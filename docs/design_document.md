# Design Document: `ensembler/function` Directory

## 1. Introduction

This document provides an analysis of the PHP files within the `ensembler/function` directory. The purpose of this document is to outline the current architecture, identify design flaws, and propose a refactoring strategy to improve the structure and reduce redundancy of the files inside the `ensembler/function` directory.

The `ensembler/function` directory contains a mix of core business logic, utility functions, and database interactions. The current design suffers from a lack of clear separation of concerns, extensive use of global variables, and duplicated code, making it difficult to maintain and extend.

## 2. Detailed File Analysis

This section provides a detailed breakdown of each file's purpose, key functions, and specific design flaws.

### 2.1. `web_function_login.php`

-   **Purpose**: Handles the core logic for user authentication and session management.
-   **Key Functions**:
    -   `Login_Flow()`: The main function that orchestrates the login process. It validates credentials, checks licenses, and sets session variables.
    -   `check_concorant_licence()`: Checks for available concurrent user licenses before allowing a user to log in.
    -   `isPasswordExpired()`: Enforces password rotation policies by checking the age of the user's password.
    -   `lockAccount()`: Prevents brute-force attacks by locking an account after multiple failed login attempts.
    -   `check_already_login()`: Checks if a user session is already active.
    -   `add_audit_log()`: A utility function to log security-sensitive actions.
-   **Design Flaws**:
    -   **Global Dependencies**: Relies heavily on global variables (`$link`, `$dbname`, `$database_name`) from the included `web_mysqlconnect.php` file.
    -   **Hardcoded Logic**: The main logic is handled in a large `Login_Flow` function, making it difficult to unit test and maintain.
    -   **SQL Injection Vulnerabilities**: Constructs SQL queries using string concatenation with user-provided data, making it vulnerable to SQL injection.

### 2.2. `web_function_define.php`

-   **Purpose**: Acts as a "catch-all" file for a vast number of unrelated utility and business logic functions.
-   **Functionality Groups**:
    -   **Database Helpers**: Contains dozens of simple functions for fetching single records or values (e.g., `assignto`, `project`, `company_name`, `database_name`).
    -   **CRUD Operations**: Includes generic functions like `insert_record`, `fetch_record`, and `maximum_id`.
    -   **Business Logic**: Contains complex functions like `RegComp` for registering complaints.
    -   **User and License Counting**: Functions like `F_Count_User` and `F_CompanyUserLicense` for counting users and checking licenses.
    -   **HTML Generation**: Includes functions that generate HTML dropdowns (`fillArrayCombo`, `fillDbCombo`).
    -   **Email/SMS**: Functions for composing and sending notifications (`mailcompose`, `include_mail_template`, `insert_smsmessages`).
-   **Design Flaws**:
    -   **Monolithic Structure**: With nearly 3000 lines and hundreds of functions, this file is a classic example of a "god object." It violates the Single Responsibility Principle, making the code extremely difficult to navigate and maintain.
    -   **Pervasive Global State**: Nearly every function depends on the global `$link` database connection.
    -   **Inconsistent Naming**: Function names are inconsistent, and many are cryptic (`F_Count_User`, `date_difff`).
    -   **Redundancy**: Contains functionality that is duplicated in other files (e.g., `failed_login`).

### 2.3. `classify_function.php`

-   **Purpose**: Handles agent-specific logic related to classification, breaks, and module licensing.
-   **Key Functions**:
    -   `agent_break()`: Records agent break start and end times.
    -   `classify_agent()`: Retrieves the classification of an agent.
    -   `module_license()`, `module_license_id()`, `module_license2()`: A set of functions to check user/group access to specific application modules.
    -   `channel_license()`: Checks which communication channels (e.g., email, chat) a user is licensed to use.
-   **Design Flaws**:
    -   **Global Dependencies**: All functions are dependent on the global `$link` and `$db` variables.
    -   **Direct Output**: The `agent_break` function echoes JSON directly, mixing business logic with presentation.
    -   **Insecure Queries**: Uses `mysqli_real_escape_string` but still builds queries via concatenation, which is not as secure as prepared statements.

### 2.4. `MultiTanentDBValidation.php`

-   **Purpose**: Provides a centralized function to resolve a company's database name in a multi-tenant environment.
-   **Key Functions**:
    -   `getDatabaseForCompany()`: Connects to a central `CampaignTracker` database to look up a company ID and return the name of the tenant's dedicated database.
-   **Design Flaws**:
    -   **Direct Connection**: Creates its own database connection instead of using a shared, reusable instance.
    -   **Error Handling**: Returns an array with a status and message, which is better than die/exit but still requires the calling code to handle the response manually.

### 2.5. `customer_function.php`

-   **Purpose**: Handles AJAX requests from the customer-facing portal for login, OTP, and service requests.
-   **Structure**: This is a script file, not a library of functions. It checks `$_POST['action']` and executes different blocks of code accordingly.
-   **Key Actions**:
    -   `login_check`: Authenticates a customer based on their phone number.
    -   `otp_check`: Verifies a customer-submitted OTP.
    -   `send_mail`: Processes a service request form and sends it as an email using PHPMailer.
-   **Design Flaws**:
    -   **Code Duplication**: The multi-tenant database validation and connection logic is duplicated for each action block.
    -   **Mixed Concerns**: Mixes request handling (processing `$_POST`), business logic, and database interaction in one file.
    -   **Security**: The `login_check` action grants a session based only on a phone number, without a password or OTP, which is highly insecure.

### 2.6. `web_function_forgot_password.php`

-   **Purpose**: Manages the logic for the "forgot password" feature.
-   **Structure**: Script-based, executing a `Forgot_Password()` function when `$_POST['action']` is set.
-   **Key Functions**:
    -   `Forgot_Password()`: The main function that handles the entire workflow.
    -   `checkEmailAndMobileExists()`: A helper to validate the user's existence.
-   **Design Flaws**:
    -   **Code Duplication**: Duplicates the multi-tenant database validation and connection logic from other files.
    -   **Lack of Abstraction**: The `Forgot_Password` function is a long, procedural script that handles validation, database updates, and email/SMS sending, making it hard to test.
    -   **Global Dependencies**: Relies on global variables and functions from included files.

### 2.7. `web_function_change_password.php`

-   **Purpose**: Handles the logic for the "change password" feature for logged-in users.
-   **Structure**: Script-based, with a main `Web_newpassword_change()` function.
-   **Key Functions**:
    -   `Web_newpassword_change()`: The main workflow function.
    -   A series of small helper functions to interact with the database (`getCompanyID`, `updateUserPassword`, `updatePasswordHistory`, `fetchUsername`).
-   **Design Flaws**:
    -   **Global Dependencies**: All helper functions rely on the global `$db` and `$link` variables.
    -   **External API Call**: Makes a `curl` call to an IP address (`http://$uc_ip/agc/executequery.php`) within the change password logic, creating a tight, hardcoded dependency.
    -   **Mixed Concerns**: The main function contains logic for database updates, external API calls, and HTML generation for the response.

### 2.8. `check_license.php`

-   **Purpose**: An endpoint for checking, updating, and removing concurrent user licenses.
-   **Structure**: Script-based, processing different `action` parameters (`check`, `remove`, `update`, `refresh`).
-   **Key Functions**:
    -   `check_concurrent_user()`: Counts currently active users.
    -   `get_max_license_count()`: Retrieves the maximum allowed licenses from the database.
    -   `insert_concurrent_user()`: Adds a user to the concurrent session tracking table.
-   **Design Flaws**:
    -   **Direct Database Access**: Directly queries the database, mixing data access logic with the licensing business logic.
    -   **Global State**: Relies on the global `$link` connection.
    -   **Security**: The `remove` action deletes a user from the concurrent list based on a username from the request, which could be insecure if not properly validated.

### 2.9. `SimpleImage.php`

-   **Purpose**: A self-contained class for performing image manipulations.
-   **Structure**: A single class, `SimpleImage`.
-   **Key Methods**:
    -   `load()`: Loads an image from a file.
    -   `save()`: Saves the modified image to a file.
    -   `resize()`, `scale()`: Methods for resizing and scaling the image.
-   **Design Flaws**:
    -   **Outdated Syntax**: Uses old PHP 4 `var` syntax for declaring properties. Modern PHP would use `public`, `private`, or `protected`.
    -   **Overall**: This file is the best-designed in the directory. It is self-contained, has a clear purpose, and does not rely on global state. It serves as a good (though simple) model for how other functionality could be encapsulated in classes.

## 3. Design Issues and Proposed Solutions

The current design of the `ensembler/function` directory has several significant issues that impact the application's quality and maintainability.

### 3.1. Lack of a Centralized Database Connection

-   **Issue**: Each file that requires a database connection includes `config/web_mysqlconnect.php` and relies on a global `$link` variable. This approach is insecure, error-prone, and makes it difficult to manage database connections.
-   **Solution**: Create a dedicated database connection class or function that can be used throughout the application. This will centralize connection logic, improve security by using prepared statements, and make the code easier to test and maintain.

### 3.2. Monolithic Function Files

-   **Issue**: `web_function_define.php` is a prime example of a "god object" file, containing hundreds of unrelated functions. This makes it nearly impossible to understand the codebase, find relevant functions, and avoid code duplication.
-   **Solution**: Refactor `web_function_define.php` and other large function files into smaller, more focused files based on their functionality. For example, create separate files for user management, complaint management, and utility functions.

### 3.3. Extensive Use of Global Variables

-   **Issue**: The code heavily relies on global variables (e.g., `$link`, `$db`, `$dbname`), which creates tight coupling between functions and makes the code difficult to reason about and test.
-   **Solution**: Eliminate the use of global variables by passing dependencies (like the database connection) as arguments to functions. This will improve code clarity, reduce side effects, and make the code more modular.

### 3.4. Code Duplication

-   **Issue**: There is significant code duplication across multiple files, especially in the areas of database connection and multi-tenant validation.
-   **Solution**: Consolidate duplicated code into a single, reusable function or class. For example, the multi-tenant database validation logic in `customer_function.php`, `web_function_forgot_password.php`, and `web_function_login.php` should be extracted into a single function.

## 4. Refactoring Proposal

I propose a two-phase refactoring process to address the issues identified in this document.

### Phase 1: Foundational Improvements

1.  **Create a `Database` class**: Implement a `Database` class that handles all database connections, queries, and transactions. This class should use prepared statements to prevent SQL injection and provide a clean, consistent API for database interactions.
2.  **Create a `Config` class**: Centralize all configuration settings (database credentials, API keys, etc.) into a `Config` class that can be easily accessed throughout the application.
3.  **Refactor `MultiTanentDBValidation.php`**: Rewrite the multi-tenant validation logic to use the new `Database` and `Config` classes.

### Phase 2: Code Reorganization

1.  **Decompose `web_function_define.php`**: Break down `web_function_define.php` into smaller, more manageable files based on functionality (e.g., `user_functions.php`, `complaint_functions.php`, `utility_functions.php`).
2.  **Refactor all function files**: Update all files in the `ensembler/function` directory to use the new `Database` and `Config` classes, and to follow the new, more organized file structure.
3.  **Update calling code**: Modify all files that include functions from the `ensembler/function` directory to use the new file structure and function signatures.

## 5. Conclusion

The `ensembler/function` directory is a critical part of the application, but its current design is unsustainable. By implementing the refactoring proposal outlined in this document, we can significantly improve the quality, maintainability, and security of the codebase, making it easier to develop and support in the future. 
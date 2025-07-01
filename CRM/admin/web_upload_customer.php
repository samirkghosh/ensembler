<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  24-april-2025
 * Description: To add multiple customer details using excel sheet
*/

include("../../config/web_mysqlconnect.php");
include("document_upload_function.php");
?>
<style>
   .upload-grid {
    display: grid;
    grid-template-columns: 200px auto auto;

    gap: 10px;
    align-items: center;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
    max-width: 1000px; /* Adjusted width to accommodate the grid */
}

.upload-grid label {
    white-space: nowrap;
    font-weight: bold;
}

.upload-grid input[type="file"] {
   padding:-100px;
}

.upload-grid .button-orange1 {
    margin-right:260px;
    background-color:#8fce00;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    cursor: pointer;
    text-align: center;
}

.upload-grid .button-orange1:hover {
    background-color:#6aa84f;
}

    </style>
   <script src="<?=$SiteURL?>CRM/admin/xlsx.full.min.js"></script>

<div class="style2-table">
    <div class="style-title">
        <h3>Upload Customer Details</h3>
    </div>

    <div id="success"></div>

    <!-- Form for uploading Excel file -->
    <form name="frmService" method="post" enctype="multipart/form-data">
    <div class="table" id="SRallview">
    <div class="upload-grid">
    <label for="customer_excel">Upload Excel File:<em>*</em></label>
    <input type="file" name="customer_excel" id="customer_excel" accept=".xlsx,xls" required class="input-style1">
    <input type="button" id="upload_button" value="Upload" class="button-orange1">
   
    <a href="admin/sample_document/customer_data.xlsx" download style="margin-left: 10px; text-decoration: none; color: #007bff;">
            📄 Download Sample File
        </a>
   
</div>

</div>

<form name="frmService" action="" method="post">
        <div class="table" id="SRallview">
            <div class="">
                <div class="div2">
                    <table class="tableview tableview-2" id="admin_table">
                        <thead>
                            <tr class="background">
                                <td align="left" width="5%">S.No.</td>
                                <td align="left">Document Name</td>
                                <td align="left">Uploaded by</td>
                                <td align="left">Uploaded On</td>
                                <td align="left">Download File</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM $db.uploaded_file WHERE type = 'customer' ";
                            $result=mysqli_query($link,$sql);
                             // Call the view_village function

                            if (mysqli_num_rows($result) == 0) {
                                echo '<tr><td colspan="5" align="center" class="contentred">No records found!</td></tr>';
                            } else {
                                $no = 1;
                                while ($res = mysqli_fetch_assoc($result)) {?>
                            <tr>
                                <td align="left"><?php echo  $no++ ?></td>
                                <td align="left"><?php echo  $res['document_name'] ?></td>
                                <td align="left">
                                    <?php
                                    $sql = "SELECT AtxUserName FROM $db.uniuserprofile WHERE AtxUserID ='" . $res['uploaded_by'] . "'";
                                    $query = mysqli_query($link, $sql);
                                    $row = mysqli_fetch_assoc($query);
                                    echo $district_name = $row['AtxUserName'];
                                    ?>
                                </td>
                                <td align="left"><?php echo $res['uploaded_date'] ?></td>
                                <td align="left">
                                <?php if (!empty($res['document_name'])): 
                                    $fileUrl = $SiteURL . "CRM/admin/uploads/customers/" . urlencode($res['document_name']);?>
                                
                                    <a href=<?=$fileUrl?> target="_blank" title="View Excel">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#45a4ed"  style="width:12px; height:12px; margin-left:-4px;" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>
                                    </a>
                                <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" name="Action" />
        <input type="hidden" name="I_ServiceID" />
    </form>

</div>
<script>document.getElementById("upload_button").addEventListener("click", uploadExcel);
// function to handle  uploading of excel file and data of the customer in the database 
function uploadExcel() {
    const fileInput = document.getElementById("customer_excel");
    const file = fileInput?.files?.[0];
// alert if  no file is uploaded 
    if (!file) {
        alert("⚠️ Please select a file first.");
        return;
    }

    // defined constant to read the content of the file 
    const reader = new FileReader();

    reader.onload = function (e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });

            const sheetName = workbook.SheetNames[0];
            const sheet = workbook.Sheets[sheetName];
            const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            // check for  the  data in the sheet 
            if (rows.length < 2) {
                alert("❌ Excel file appears to be empty or missing data rows.");
                return;
            }

            const headers = rows[0];
            const fieldIndexes = {};
            //  get the values of the  customer  to sent over 
            const allFields = {
                "Registered Mobile Number*": "mobile",
                "Alternate Mobile Number": "alternate_mobile",
                "Company Name": "company_name",
                "Company Registration Number": "company_registration",
                "First Name*": "first_name",
                "Last Name": "last_name",
                "Priority Customer(0-non priority,1-priority)": "priority",
                "County": "county",
                "Sub County": "sub_county",
                "Nationality": "nationality",
                "Gender(M/F)": "gender",
                "Email": "email",
                "Facebook Handle": "facebook",
                "X Handle": "x_handle",
                "SMS Number": "sms_number",
                "WhatsApp Number": "whatsapp",
                "Address": "address",
                "Instagram ID": "instagram",
                "Facebook Messenger ID": "messenger"
            };

            for (let field in allFields) {
                const index = headers.indexOf(field);
                if (index === -1) {
                    console.warn(`Optional column not found: ${field}`);
                }
                fieldIndexes[field] = index;
            }

            // check  for the required columns 
            const requiredFields = ["Registered Mobile Number*", "First Name*", "Gender(M/F)"];
            const errorMessages = [];

            const dataRows = rows.slice(1).filter(row => 
                row.some(cell => cell !== undefined && cell.toString().trim() !== "")
            );

            // show alert if any of the required columns is missing show the  record number 
            dataRows.forEach((row, i) => {
                const missingFields = requiredFields.filter(field => {
                    const index = fieldIndexes[field];
                    return !row[index] || row[index].toString().trim() === "";
                });
                if (missingFields.length > 0) {
                    errorMessages.push(`Record ${i + 1}: Missing ${missingFields.join(", ")}`);
                }
            });

// give alert if validation is failed 
            if (errorMessages.length > 0) {
                alert("❌ Validation Failed:\n\n" + errorMessages.join("\n"));
                return;
            }

            const customerData = dataRows.map(row => {
                const customer = {};
                for (let label in allFields) {
                    const key = allFields[label];
                    const index = fieldIndexes[label];
                    customer[key] = index !== undefined && index >= 0 ? (row[index] || "").toString().trim() : "";
                }
                return customer;
            });

            // append the data to send over the php function file 
            const formData = new FormData();
            formData.append("customer_excel", file);
            formData.append("customers", JSON.stringify(customerData));
            formData.append("action", "upload_customer_json");

            fetch("admin/document_upload_function.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                // on succes show the alert of successfull upload 
                if (result.success) {
                    alert("✅ All customers uploaded successfully!");
                    location.reload();
                } else {
                    let message = `Customer records not Added\n`;

                    if (result.failed_rows && result.failed_rows.length > 0) {
                        message += "Failed Records:\n";
                        result.failed_rows.forEach(row => {
                            message += `• Record ${row.row}: ${row.errors}\n`;
                        });
                    }

                    alert(message); 
                }
            })
            .catch(err => {
                // in canse of error  show alert
                console.error("Upload Error:", err);
                alert("❌ Error uploading data. Please try again.");
            });

        } catch (error) {
            // show alert if  error occured while reading the file 
            console.error("File Read Error:", error);
            alert("❌ An error occurred while processing the Excel file.");
        }
    };

    reader.readAsArrayBuffer(file);
}




</script>
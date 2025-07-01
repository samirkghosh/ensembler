<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  24-april-2025
 * Description: To add multiple customer details using excel sheet
*/

include("../../config/web_mysqlconnect.php");
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
        <h3>Upload Sub Category</h3>
    </div>

    <div id="success"></div>

    <!-- Form for uploading Excel file -->
    <form name="frmService" method="post" enctype="multipart/form-data">
    <div class="table" id="SRallview">
    <div class="upload-grid">
    <label for="subcategory_excel">Upload Excel File:<em>*</em></label>
    <input type="file" name="subsubcategory_excel" id="subcategory_excel" accept=".xlsx,xls" required class="input-style1">
    <input type="button" id="upload_button" value="Upload" class="button-orange1">
   
    <a href="admin/sample_document/Category-Subcategory.xlsx" download style="margin-left: 10px; text-decoration: none; color: #007bff;">
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
                            $sql = "SELECT * FROM $db.uploaded_file WHERE type = 'subcategory' ";
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
                                    $fileUrl = $SiteURL . "CRM/admin/uploads/subcategory/" . urlencode($res['document_name']);?>
                                
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
    const fileInput = document.getElementById("subcategory_excel");
    const file = fileInput?.files?.[0];

    if (!file) {
        alert("⚠️ Please select a file first.");
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });

            const sheetName = workbook.SheetNames[0];
            const sheet = workbook.Sheets[sheetName];
            const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            if (rows.length < 2) {
                alert("❌ Excel file appears to be empty or missing data rows.");
                return;
            }

            const headers = rows[0].map(h => h?.toString().trim().toLowerCase());
            const getIndex = (header) => headers.indexOf(header.toLowerCase());

            const requiredFields = ["category*", "subcategory*", "escalation time in hours*"];
            const missingRequiredHeaders = requiredFields.filter(field => getIndex(field) === -1);

            if (missingRequiredHeaders.length > 0) {
                alert(`❌ Missing required columns: ${missingRequiredHeaders.join(", ")}`);
                return;
            }

            const records = [];
            const failedRows = [];

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];

                const category = row[getIndex("category*")]?.toString().trim();
                const subcategory = row[getIndex("subcategory*")]?.toString().trim();
                const escalationTime = row[getIndex("escalation time in hours*")]?.toString().trim();

                if (!category || !subcategory || !escalationTime) {
                    failedRows.push({ row: i + 1, errors: "Missing required fields." });
                    continue;
                }

                const record = {
                    category,
                    subcategory,
                    escalation_time: escalationTime,
                    second_escalation: row[getIndex("second escalation time")]?.toString().trim() || "",
                    third_escalation: row[getIndex("third escalation time")]?.toString().trim() || "",
                    level1_users: row[getIndex("level 1 users")]?.toString().trim() || "",
                    level2_users: row[getIndex("level 2 users")]?.toString().trim() || "",
                    level3_users: row[getIndex("level 3 users")]?.toString().trim() || "",
                    description: row[getIndex("description")]?.toString().trim() || ""
                };

                records.push(record);
            }

            if (records.length === 0) {
                alert("❌ No valid records found. Please check the file.");
                return;
            }

            const formData = new FormData();
            formData.append("action", "upload_subcategory_data");
            formData.append("records", JSON.stringify(records));
            formData.append("subcategory_excel", file);

            console.log("📦 Valid data being sent:", records);

            fetch("admin/document_upload_function.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert("✅ Data uploaded successfully!");
                    location.reload();
                } else {
                    let message = `❌ Some records failed to upload.\n`;

                    if (result.failed_rows && result.failed_rows.length > 0) {
                        message += "Failed Records:\n";
                        result.failed_rows.forEach(row => {
                            message += `• Row ${row.row}: ${row.errors}\n`;
                        });
                    }
                    alert(message);
                }
            })
            .catch(err => {
                console.error("Upload Error:", err);
                alert("❌ Error uploading data. Please try again.");
            });

        } catch (error) {
            console.error("File Read Error:", error);
            alert("❌ An error occurred while processing the Excel file.");
        }
    };

    reader.readAsArrayBuffer(file);
}



</script>
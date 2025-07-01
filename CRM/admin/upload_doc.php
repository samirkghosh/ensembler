<?php
/***
    Author: Farhan Akhtar
    Last Modified on: 04 Feb 2025
    Description: To Upload Documents for Knowledge Base AI Assistant
*/
?>

<div class="style2-table">
    <div class="style-title">
        <h3>Upload Document</h3>    
    </div>

    <form id="UploadForm">
        <table class="tableview tableview-2 mb-4">
            <tbody>
                <tr>
                    <td class="left">
                        <label class="labelUpload" for="uploadDoc">
                            <input type="file" class="uploadDoc" id="uploadDoc" multiple accept="application/pdf, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, text/csv" />
                            <a title="Only PDF, Excel, Word, and CSV files Allowed">Select Files From Here
                                <i class="far fa-file-pdf text-danger"></i>
                                <i class="far fa-file-excel text-success"></i>
                                <i class="far fa-file-word text-primary"></i>
                                <i class="far fa-file-excel text-secondary"></i>
                            </a>
                        </label>
                    </td>
                    <td>
                      <textarea name="descDoc" id="descDoc" class="descDoc text-area1" placeholder="Enter Description here..." style="resize: none;"></textarea>
                    </td>
                    <td class="left">
                        <input type="submit" value="Upload" class="submitUpload btn btn-danger btn-sm" name="submit" />
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="files" style="display: none;">
            <ul class="file-list"></ul>
        </div>
    </form>
    <table class="tableview tableview-2 mt-4" id="fileTable">
        <thead>
            <tr class="background">
                <td>SNo.</td>
                <td>File Name</td>
                <td>Size</td>
                <td>Description</td>
                <td>Created by</td>
                <td>Datetime</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

</div>

<div id="processModel" class="modalprocess">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-body">
            <center>
                <h3><img src="<?= $SiteURL ?>public/images/loader.gif" style="height:30px;width: 30px;">Processing... Please wait...</h3>
            </center>
        </div>
    </div>
</div>
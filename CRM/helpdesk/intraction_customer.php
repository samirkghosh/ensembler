<?php
/***
 * Intraction Customer Details
 * Author: Aarti
 * Date: 09-04-2024
 *  This code is used in a web application to  Intraction Customer Details Display list
-->
 **/
include("web_mysqlconnect.php");  // database connection file 
include("header.php");
include("../include/function_define.php");
?>
<body>
    <div class="wrapper">
        <div class="container-fluid">
          <div class="row" style="min-height:90vh">
              <div class="col-sm-2" style="padding-left:0">
                  <?include("sidebar.php");?>
              </div>
              <div class="col-sm-10 mt-3" style="padding-left:0">
                <div class="Reports-page#" style="display: block;padding: 15px;border: #d4d4d4 1px solid;marginbottom:20px;min-height: 420px;margin-top:37px;background-color: #fff;">
                    <form method="post" name="cfrm" id="post">
                        <div  style="margin-top:-1px;background-color: #fff;">
                            <table class="tableview tableview-2 main-form new-customer">
                                <tr>
                                    <td width="95" class="left boder0-right">&nbsp;
                                        <label>Start Date </label>
                                    </td>
                                    <td width="226" class="left boder0-right">
                                        <?
                                    $startdate = ($_REQUEST['startdatetime']!='') ? $_REQUEST['startdatetime'] : date("d-m-Y 00:00:00");
                                    $enddate = ($_REQUEST['enddatetime']!='') ? $_REQUEST['enddatetime'] : date("d-m-Y 23:59:59");
                                    ?>
                                        <span class="left boder0-left">
                                            <input type="text" name="startdatetime" class="date_class dob1"
                                                value="<?=$startdate?>" id="startdatetime">&nbsp;
                                        </span>
                                    </td>
                                    <td width="112" class="left boder0-right"><label>End Date </label></td>
                                    <td width="230" align="left" class="left boder0-right"><span class="left boder0-left">
                                            <input type="text" name="enddatetime" class="date_class dob1"
                                                value="<?=$enddate?>" id="enddatetime">
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left  boder0-left" colspan="4">
                                        <input type='submit' name='sub1' value='Run Report' class="button-orange1" />
                                        <input type='button' name='reset' value='Reset' class="button-orange1" onclick="window.location.href='web_queue.php'">
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="table">
                            <table class="tableview tableview-2 example">
                                <thead>
                                <tr class="background">
                                    <td><input type="checkbox" id="checkall"></td>
                                    <td>S.No</td>
                                    <td>Date </td>
                                    <td>From email </td>
                                    <td>Subject </td>
                                    <td>Case ID</td>
                                    <td>Status</td>
                                    <td>Classification</td>
                                </tr>
                                </thead>
                                <tbody>
                                   <tr>
                                      <td align="center" valign="top" style="text-align: center;" colspan="8">No Record!</td>
                                   </tr>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
              </div>    
          </div>
        </div>
        <div class="footer">
            <? include("web_footer.php"); ?>
        </div>
    </div>
</body>
<script type="text/javascript" src="dist/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="dist/datatables/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="dist/datatables/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="dist/datatables/js/buttons.colVis.min.js" ></script>
<script>
    $(document).ready(function() {
        $('.example').DataTable({
            "aaSorting": [],
            "ordering": false,
            "pageLength": 30,
            "dom": 'rtip'
        });
    });
</script>
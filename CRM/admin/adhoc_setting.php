<?php
/***
 * Auth: Farhan Akhtar
 * Date: 02 July 2024
 * Description: To Update or view Email for Adhoc reports
 * 
*/
?>
<div id="success"><?php echo $msg; ?></div>
<form name="myform" method="post">
        <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Edit Adhoc Setitngs </span>
            <div class="style2-table">
                <div class="table">
                    <table class="tableview tableview-2 main-form">
                        <tbody>
                        <? if($msg!=''){?>
                            <tr>
                                <td colspan="2"
                                    style="color:#ffffff; width:100%; background:#00CC66; padding:5px; border:1px solid #02944b; text-align:center;">
                                    <?php echo $msg?>
                                </td>
                            </tr>
                            <? } ?>
                            <tr>
                                <td class="left">
                                    <label>Adhoc Email<em>*</em></label>
                                    <div class="log-case">
                                    <input type="email" name="emailadhoc" id="emailadhoc" class="input-style1" value="<?=get_adhoc_mail(1)?>">
                                    </div>
                                </td>
                                <td class="left">
                                    <div class="log-case">
                                    <input type="button" name="editadhoc" id="editadhoc" value="Edit" class="button-orange1" style="float:revert">
                                    <input type="submit" name="submitadhoc" id="submitadhoc"  value="Update" class="button-orange1" style="float:revert">
                                    </div>
                                </td>
                            </tr>
                        
                       
                        </tbody>
                    </table>
                </div>
            </div>
</form>

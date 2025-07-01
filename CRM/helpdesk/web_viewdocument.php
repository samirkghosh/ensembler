<?php
/***
 * Document details
 * Auth:  Aarti Ojha
 * Date: 04-10-2024
 * Description: this file use for fetch document data and insert data in database
 * Admin all module Files added and common files added
 * 
*/
	include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
   header("Cache-Control:Private");
   $uploadStatus = 1; 
   $documentid=$_GET['documentid'];
   $page_name=$_GET['page'];
   $I_section_ID=$_GET['I_section_ID'];
   $value=$_GET['value'];
   if($I_section_ID==''){
   	$I_section_ID=0;
   }
   $logged	=$_SESSION['logged'];
   $modifiedon=date("Y-m-d");
   if($_REQUEST['Active']){
   		$sql_document="update $db.web_documents set I_DOC_Status=1,ModifiedBy='$logged',ModifiedOn='$modifiedon' where I_DocumentID='$documentid'";
   			mysqli_query($link,$sql_document);
   			//print_r($sql_document);
   			$msg="Document Sucessfully Activated! ";
   }
   function get_datetime(){
	   $current_datetime=date("Y-m-d:G:i:s");
	   return $current_datetime;
   }
   if($_REQUEST['InActive']){
   	$sql_document="update $db.web_documents set I_DOC_Status=0 where I_DocumentID='$documentid'";
   	mysqli_query($link,$sql_document);
   	$msg="Document Sucessfully InActivated! ";
   }
   if($_REQUEST['Updatebtn']) {
    	$I_UploadedBY = $_SESSION['logged'];
   		$modified_on = get_datetime();
    	$I_DocumentType	=$_REQUEST['I_DocumentType'];
    	$V_Doc_Name = $_REQUEST['V_Doc_Name'];
   		$V_DOC_Description = $_REQUEST['V_DOC_Description'];
   		$I_PP = $_REQUEST['I_PP'];
     	if(isset($I_PP)){ $I_PP=1; }else{ $I_PP=0; }
       $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg','xlsx','xls','txt');
    		foreach ($_FILES['files']['tmp_name'] as $key => $value) {   
               $file_tmpname = $_FILES['files']['tmp_name'][$key]; 
               //$file_name = $_FILES['files']['name'][$key];  
               $file_name =   time().'_'.str_replace(" ", "", strtolower($_FILES['files']['name'][$key]))  ;   
               $file_size = $_FILES['files']['size'][$key]; 
               $fileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
               $targetFilePath = $file_name; 
               if(!empty($file_name)){ 
               if(in_array($fileType, $allowTypes)){ 
   				//echo '<br>'.$file_tmpname.'Target file'. $targetFilePath;
               	if(move_uploaded_file($file_tmpname, $targetFilePath)){ 
               	  
                           $uploadedFile .= $file_name.',';
                           $msg=  "File : ". $file_name ." is valid, and was ".$uploadedFile; 
                       }else{  
                           $uploadStatus = 0; 
                           $msg= "Error uploading $file_name"; 
                       }
   
               }else{
               	$msg= "Error uploading $file_name ";  
                   $msg.= "($fileType file type is not allowed)";
               }//end of allowed ext 
              }
           }//forloop
   
           if($uploadedFile)
           {
           	$uploadedFile = rtrim($uploadedFile,',');
           	$sql_document1="select * from $db.web_documents a where I_DocumentID='$documentid' ;";
   
   				//print_r($sql_document);
   				$res1=mysqli_query($link,$sql_document1) or die("Could not select1");
                   $row_doc1=mysqli_fetch_array($res1);
                   if($row_doc1['v_uploadedFile']!='')
                   {
                   	$v_uploadedFile=$row_doc1['v_uploadedFile'].",".$uploadedFile;
                   	$cond_up=" ,v_uploadedFile='$v_uploadedFile' ";
                   }else{
                   	$cond_up=" ,v_uploadedFile='$uploadedFile' ";
                   }
           	
           }
   
     $sql_document="update $db.web_documents set V_Doc_Name='$V_Doc_Name',V_DOC_Description='$V_DOC_Description',I_DocumentType='$I_DocumentType',I_PP='$I_PP',ModifiedOn='$modified_on',ModifiedBy='$logged',I_section_ID='$I_section_ID' $cond_up where I_DocumentID='$documentid'";       
   
   mysqli_query($link,$sql_document);
   
   
   if($I_DocumentType==4)
   	{
   	?>
<script>window.location.href="case_detail_backoffice.php?id=<?=$I_section_ID?>";</script>
<?
   }
   else
   if($I_DocumentType==1)
   {
   ?>
<script>window.location.href="web_customer_detail.php?customerid=<?=$I_section_ID?>";</script>
<?
   }
   
   }
   
   $selecttab=3;
   include('../includes/head.php');
   
   ?>
<body>
   <div class="wrapper">
         <div class="container-fluid">
        
            <!-- End left panel -->
            <? $uploadproductdocument=1; if($uploadproductdocument==1){ ?>


			<div class="row" style="min-height:90vh">

				<div class="col-sm-2" style="padding-left:0">

						<?php include('sidebar.php');?>
				</div>


				<div class="col-sm-10 mt-3" style="padding-left:0">
				<form name=quickcreatenc  method="post" enctype="multipart/form-data">
				<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Add new document</span>
					<div class="style2-table">
						
						<table cellpadding="3" cellspacing="2" width="90%" align="center" border="1" class="tableview tableview-2 main-form">
							<? if($msg!=''){?>
							<tr>
							<td colspan="2" style="color:#ffffff; width:100%; background:#00CC66; padding:5px; border:1px solid #02944b; text-align:center;"><?=$msg?>
							</td>
							</tr>
							<? } ?>
							<? 
							$sql_document="select * from $db.web_documents a where I_DocumentID='$documentid' ;";
							
							//print_r($sql_document);
							$res=mysqli_query($link,$sql_document) or die("Could not select");
										$row_doc=mysqli_fetch_array($res);
							
							$V_Doc_Name            = $row_doc['V_Doc_Name'];
							$I_DocumentType        = $row_doc['I_DocumentType'];
							$I_UploadedBY          = $row_doc['I_UploadedBY'];
							$I_UploadedON1         = $row_doc['I_UploadedON'];
										$I_UploadedON          = date("d-m-Y H:i:s",strtotime($I_UploadedON1));
							$V_Path    		       =$row_doc['V_Path'];
										$I_PP    		       =$row_doc['I_PP'];
							$description   		   =$row_doc['V_Doc_Description'];
										$I_Doc_Status          =$row_doc['I_Doc_Status'];
							$modifiedby =$row_doc['ModifiedBy'];
							$modifiedon =$row_doc['ModifiedOn'];
							$modifiedon= date("d-m-Y H:i:s",strtotime($modifiedon)); 
							
							?>
							
							<tr>
							<td align="left" valign="middle"><label>Owner</label><?=$logged?>
								<input type="hidden" name="I_DocumentType" value="<?=$I_DocumentType?>">
							</td>
							</tr>
							<tr>
							<td align="left" valign="middle"><label>Document Name<span class="orangeTxt">*</span></label>
								<input type="text" name="V_Doc_Name" value='<?=$V_Doc_Name?>' class="input-style1" size="23">
							</td>
							</tr>
							<tr>
							<td align="left" valign="middle"><label>Document Description</label>
								<textarea name="V_DOC_Description" rows="5" cols="40" class="text-area1"><?=$description?></textarea>	
							</td>
							</tr>
							<tr>
							<?php
								// $downloads	           ="../document/$db";
								$v_uploadedFile=$row_doc['v_uploadedFile'];
								$exp_upload=explode(",",$v_uploadedFile);
								if(count($exp_upload)>1)
								{
									for($k=0;$k<count($exp_upload);$k++)
									{
										$f='../'.$exp_upload[$k];
									$img="<img src=$SiteURL/public/images/download.jpg title='Click to download $f' border=0>";
									$downloadpath1.="<a href='JavaScript:void(0)' onClick=\"JavaScript:window.open('$f')\" class='cptext' 'view','height=350, width=550,scrollbars=0'>".$img."</a> &nbsp;&nbsp;";
									}
								
								}else if(count($exp_upload)==1)
								{
									$f= '../'.$v_uploadedFile;
									$img="<img src=$SiteURL/public/images/download.jpg title='Click to download $f' border=0>";
									$downloadpath1="<a href='JavaScript:void(0)' onClick=\"JavaScript:window.open('$f')\" class='cptext' 'view','height=350, width=550,scrollbars=0'>".$img."</a>";
								}
								
								?>
							<td align="left" valign="middle"><label>Upload Document<span class="orangeTxt">*</span></label>
								<input id="file" name="files[]" multiple  type="file" class="textbox" size="23" class="input-style1">	<?php
									if($I_Doc_Status==1 && $v_uploadedFile!="")
									{
										echo $downloadpath1;
									}
									
											?>
							</td>
							</tr>
							<tr>
							<td align="left" valign="middle"><label>Document</label>
								<? if($I_PP==1){?><input type="checkbox" id="I_PP" name="I_PP" value="check" checked><? }
									else if($I_PP==0){ ?><input type="checkbox" id="I_PP" name="I_PP" value="check"><? }?>	 
								Private</font>	
							</td>
							</tr>
							<? if($_POST['Update']) 
							{ ?>
							<tr>
							<td>&nbsp;</td>
							</tr>
							<tr bgcolor=#efefef>
							<td colspan=2 class="rec_txt_blue" align="left">&nbsp;&raquo;&nbsp;System Information</td>
							</tr>
							<tr>
							<td>&nbsp;</td>
							<td colspan=2 class="normaltextabhi">	
								Modified By&nbsp;&nbsp;</b><?=$modifiedby?>&nbsp;[<?=$modifiedon?>]</font>	
							</td>
							</font>
							</tr>
							<? } ?>
							<tr>
							<td align="left" valign="middle">
								<label>
									<!-- <input value="Update" type="button" onClick="return validation_upload(1);" class="button-orange1" style="float:none;"> -->
									<input value="Update" type="submit" name="Updatebtn" class="button-orange1" style="float:none;">
									<? if($I_Doc_Status=='0')
										{?>
									<!-- <input value="Active" type="button" onClick="return validation_upload(2);" class="button-orange1" style="float:none;"> -->
									<? }
										else if($I_Doc_Status=='1'){?>
									<!-- <input value="InActive" type="button" onClick="return validation_upload(3);" class="button-orange1" style="float:none;"> -->
									<?}?>
								</label>
							</TD>
							</TR>
						</TABLE>
						<?
							}
							
							if($uploadproductdocument==0)
							{
							?>
						<p align="center" class="permissionheading">Sorry ,&nbsp;<?=$logged_name?><br>you don't have the necessary rights to Upload documents....</p>
						<a href = "javascript:history.back()">
							<p class="permissionmsg" align="center">click here to return back.</p>
						</a>
						<? }?><br>
						</td></tr></table>
					</div>
				</form>
				</div>

				</div>
         </div>
     
   </div>
   <div class="footer">
      <? include("web_footer.php"); ?>
   </div>
</body>
<script language="javascript">
   function validation_upload(val){
   
   	var alphaExp = /^[a-z A-Z]+$/;
   	var V_Doc_Name = document.quickcreatenc.V_Doc_Name.value.match(alphaExp);
   
   	if(val=='1')
   	{
   		window.document.quickcreatenc.action="web_viewdocument.php?Update=1&documentid=<?=$documentid?>&I_section_ID=<?=$I_section_ID?>&page=<?=$page_name?>&value=<?=$value?>";
   		window.document.quickcreatenc.submit();	
   	}
   	else if(val=='2')
   	{
        window.document.quickcreatenc.action="web_viewdocument.php?Active=1&documentid=<?=$documentid?>&I_section_ID=<?=$I_section_ID?>&page=<?=$page_name?>&value=<?=$value?>";
   	window.document.quickcreatenc.submit();	
   	}
       else if(val=='3')
   	{
       window.document.quickcreatenc.action="web_viewdocument.php?InActive=1&documentid=<?=$documentid?>&I_section_ID=<?=$I_section_ID?>&page=<?=$page_name?>&value=<?=$value?>";
   	window.document.quickcreatenc.submit();	
   	}
   
   }
</script>
</html>
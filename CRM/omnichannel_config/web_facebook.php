<?php
/**
 * Auth: Vastvikta Nishad 
 * Date: 09 April 2024
 * Description: To display Data related to Facebook  and Create  Case 
 */
/* check license access or not for  this module*/
include_once("../../ensembler/function/classify_function.php"); 
$module_flag_customer = module_license('Facebook Post');
if($module_flag_customer !='1'){
  header("Location:web_admin_dashboard.php"); 
  exit();
}

/***END***/
include("../../config/web_mysqlconnect.php");
include_once("../../function/web_function_define.php");
$name= $_SESSION['logged'];

$iallstatus=(isset($_REQUEST['allstatus'])) ? $_REQUEST['allstatus'] : 4; 

$msg=$_REQUEST['mg'];
$email=$_REQUEST['email'];
$mode=$_GET['mode'];
?>
<script>
   function delUser(id)
   {
   if(confirm("Are you sure to delete?"))
   {
   location.href="<?=$_SERVER[PHP_SELF]?>?act=del&id="+id;
   }
   }
</script>
<style type="text/css">
	 .accordion-toggle .expand-button:after
  {
    position: absolute;
    left:.75rem;
    top: 50%;
    transform: translate(0, -50%);
    content: '-';
  }
  .toggle-button:before
  {
    content: '+';
    font-size: 20px;
    color: #047edf;
    padding-right: 10px;
  }
    /* Styling for the toggle button */
    .toggle-button {
        cursor: pointer;
    }
    /* Styling for hidden rows */
    .hidden-row {
        display: none;
    }
    .table-bordered > :not(caption) > * {
	    border-width: 1px 0;
	}
    thead, tbody, tfoot, tr, td, th {
	    border-color: inherit;
	    border-style: solid;
	    border-width: 0;
	}
	.tableview tr td a {
	    color: #f6e6ff;
	}
    .fil_div{
        width:20px;
        height:20px;
        border:1px solid #999999;
        border-radius:5px;
        white-space: nowrap;
    }
    /*read and unread color flow changes [Aarti][06-01-2024]*/
    .Unread{
      background:#f1f1f1;
      margin-left: -50px;
      display: inline-block;
    }

    .Read{
      background:#ffffff;
      margin-left: -73px;
    }

    .mail-row {
      cursor: pointer;
      font-weight: bold; /* Bold for unread emails */
      background-color: #f1f1f1 !important; /* Light blue background for unread emails */      
    }
    .tableview tr td a{
        color: #555;
    }
    .mail-row td a{
        color:#3974aa;    
    }

    /*END -06-01-2024*/
    .read-container {
        margin-left: 400px; /* Adjust this value to control the gap */
    }


    .flag-green {
    background:#228b22de !important; 
    color:#fff;
    }
    .channel_active{
    	box-shadow: 0 0 0 .25rem rgba(130, 138, 145, .5);
    }

</style>
<div class="col-sm-10 mt-3" style="padding-left:0">
   <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
         <div class="row">
           <div class="col-sm-12">
             <div class="row">
               <div class="col-sm-3">
                   <div class="row">
                      <div class="col-sm-7 facebook_titile">Facebook Post</div>
                    </div>
               </div>
               <div class="col-sm-2 read-container">
                       <div class="row">
                          <div class="col-sm-7 ">Unread</div>
                          <div class="col-sm-5"><div class="fil_div Unread" ></div></div>
                       </div> 
                    </div>                      
                     <div class="col-sm-2 ">
                       <div class="row">
                          <div class="col-sm-7">Read</div>
                          <div class="col-sm-5"><div class="fil_div Read" ></div></div>
                       </div> 
                </div>
             </div>
           </div>
         </div>
       </span>

	<!-- <form method="post" name="cfrm" id="post_facebook"> -->
	<form method="post" name="cfrm" id="post">
		<?php
			//if($mode==1)	{
			if($selection1==1)	{
			$selectedview='Previous Month';	}
			
			elseif($selection1==2)	{
			$selectedview='This Month';	}
			
			elseif($selection1==3)	{
			$selectedview='This Week';	}
			
			elseif($selection1==4)	{
			$selectedview='Last Week';	}
			
			elseif($selection1==6)	{
			$selectedview='ALL';	}
			
			elseif($selection1==7)	{
			$selectedview='Today';	}
			//}
		?>
		<table class="tableview tableview-2 main-form new-customer">
			<tr >
				<td width="95" class="left boder0-right">&nbsp;
					<label>Start Date </label>
				</td>
				<td width="226" class="left boder0-right">
					<?php
						$startdate = ($_REQUEST['startdatetime']!='') ? $_REQUEST['startdatetime'] : date("01-m-Y 00:00:00");
						$enddate = ($_REQUEST['enddatetime']!='') ? $_REQUEST['enddatetime'] : date("d-m-Y H:i:s");
					?>
					<span class="left boder0-left">
						<input type="text" name="startdatetime" class="date_class dob1"  value="<? if(!isset($_POST['startdatetime'])) echo date('01-m-Y 00:00:00'); else echo $_POST['startdatetime']; ?>" id="startdatetime">&nbsp;
					</span>
				</td>
				<td width="112" class="left boder0-right"><label>End Date </label></td>
				<td width="230" align="left" class="left boder0-right">
					<span class="left boder0-left">
						<input type="text" name="enddatetime" class="date_class dob1"   value="<? if(!isset($_POST['enddatetime'])) echo date('d-m-Y H:i:s'); else echo $_POST['enddatetime']; ?>" id="enddatetime">
					</span>
				</td>
				<!-- <td class="left boder0-right"> <label>Status</label></td>
				<td class="left boder0-right">
					&nbsp;<select name="allstatus" id="iallstatus" class="select-styl1">
				
					<?php if($iallstatus==3){ ?> 
					<option value="3" selected="">New Case</option>
					<?php } ?>
					<option value="4" <? if($iallstatus==4) echo "selected"; ?>>ALL</option>
					<option value="0" <? if($iallstatus==0) echo "selected"; ?>>Case Not Created</option>
					<option value="1" <? if($iallstatus==1) echo "selected"; ?>>Case Created</option>
					<option value="2" <? if($iallstatus==2) echo "selected"; ?>>Deleted</option>
				</td> -->
			</tr>
			<tr>
				<td class="left boder0-right"> <label>Channel Type</label></td>
			   	
			   <td class="left boder0-right">Facebook Post
				</td>
			   	<td class="left  boder0-left" colspan="3"><input type="submit" name="sub1" value="Run Report" class="button-orange1 set_button" onclick="UserAction('')">
			   	<input name="reset" id="reset" type="button" value="RESET" class="button-orange1 reset_button_facebook" />
			   </td>
			</tr>
		</table>
		<div class="table" id="facebook">
			<!-- <table width="100%" align="center"  valign="middle" border="0" class="tableview tableview-2" id="facebook_table">
				<thead>
					<tr class="background">
						<td align="center" width="2%" style="text-align: center;"> S.No</td>
						<td align="center"  width="8%">Comment From</td>
						<td align="center" width="12%">Post</td>
						<td align="center"  valign="middle" width="10%">Comment </td>
						<td align="center" width="8%">Attachment</td>
						<td align="center"  width="8%">Comment Date </td>
					</tr>
				<thead>
			</table> -->
			<table  class="tableview tableview-2 " id="facebookk">
				<thead>
				<tr class="background">
				   <td align="center" valign="middle" width="2%" style="text-align: center;"> S.No.</td>
				   <td align="center" valign="middle" width="8%">Comment From</td>
				   <td align="center" valign="middle" width="12%">Post</td>
				   <td align="center" valign="middle" width="10%">Comment </td>
				   <td align="center" valign="middle" width="10%">Attachment</td>
				   <td align="center" valign="middle" width="12%">Comment Date </td>
				   <td align="center" valign="middle" width="4%">View</td>
				</tr>
			</thead>
				<?php 
				$ik=1;
				$facebook_data = Fecebooki_listing();
				foreach ($facebook_data['parent'] as $key => $fbvalue){ 
					$flag_1 = $fbvalue['flag_read_unread'];
					if($flag_1==3) $clr="background:#f1a00bd4; color:#fff;"; else if($flag_1==1) $clr="background:#e34234db; color:#fff;"; else if($flag_1==2) $clr="background:#228b22de; color:#fff;";
					?>
					<tr style="<?=$clr?>">
					   <td align="center" <?php if(isset($fbvalue['child'])){?> class="toggle-button" <?php }?>  data-id="<?php echo $ik; ?>" valign="top" style="text-align: center;"><?=$ik?></td>
					   <td align="center" valign="top" style="text-align: center;"><?=$fbvalue['name']?></td>
					   <td align="center" valign="top" style="word-break: break-all;"><?=$fbvalue['post']?></td>
					   <td align="center" valign="top" style="text-align: center;">N/A</td>
						<td align="center" valign="top" style="text-align: center;">
							<?php if(!empty($fbvalue['attachment'])){?><a  href="<?=$fbvalue['attachment']?>" target="_blank" class="text-white">attachment</a><?php }?>
						</td>
					    <td align="center" valign="top" style="text-align: center;"><?=$fbvalue['createddate']?></td>
					    <td align="center" valign="top" style="text-align: center;">
					       <a style="text-decoration: none;" href="omnichannel_config/web_facebook_sent.php?comment_id=<?=$fbvalue['post_id']?>&id=<?=$fbvalue['id']?>&flag=1" class="ico-interaction2 text-white"> View </a> 
						</td>
					</tr>
					<?php
              		if(isset($fbvalue['child'])){
                		foreach($fbvalue['child'] as $key_1 => $cdrvalue_1){
                			$flag = $cdrvalue_1['flag_read_unread'];
							if($flag==3) $clr="background:#f1a00bd4; color:#fff;"; else if($flag==1) $clr="background:#e34234db; color:#fff;"; else if($flag==2) $clr="background:#228b22de; color:#fff;";
						?>
            				<tr class="hidden-row parent_child_<?php echo $ik; ?>" style="<?=$clr?> display: none;">
            					<td></td>
							   <td align="center" valign="top" style="text-align: center;"><?=$fbvalue['name']?></td>
							   <td align="center" valign="top" style="word-break: break-all;"><?=$fbvalue['post']?></td>
							   <td align="center" valign="top" style="text-align: center;">
							     <a style="text-decoration: none;" href="omnichannel_config/web_facebook_sent.php?comment_id=<?=$fbvalue['comment_id'] ?>&id=<?=$fbvalue['id']?>" class="ico-interaction2 text-white"><?=$cdrvalue_1['comment']?></a> 
								</td>
								<td align="center" valign="top" style="text-align: center;">
									<?php if(!empty($cdrvalue_1['attachment'])){?><a  href="<?=$cdrvalue_1['attachment']?>" target="_blank" class="text-white">attachment</a><?php }?>
								</td>
							   <td align="center" valign="top" style="text-align: center;"><?=$cdrvalue_1['createddate']?></td>
							   <td align="center" valign="top" style="text-align: center;">
							   <a style="text-decoration: none;" href="omnichannel_config/web_facebook_sent.php?comment_id=<?=$cdrvalue_1['comment_id'] ?>&id=<?=$cdrvalue_1['id']?>" class="ico-interaction2 text-white">View</a> 
								</td>
							   	
							</tr>
                	<?php } }?>
				<?php $ik++; }?>
				<?php if(empty($facebook_data)){	?>				
					<tr>
					   <td align="center" valign="top" style="text-align: center;" colspan="6">No Record!</td>
					</tr>
				<?php }?>
		 	</table>
		</div>
	</form>
</div>
<script type="text/javascript">
	// Add event listeners to toggle buttons
        const toggleButtons = document.querySelectorAll('.toggle-button');
        toggleButtons.forEach(button => {
            button.addEventListener('click', () => {
                const row = button.parentElement;
                const nextRow = row.nextElementSibling;
                const someAttribute = button.getAttribute('data-id'); 
                console.log(someAttribute);
                if (nextRow && nextRow.classList.contains('hidden-row')) {
                    if (nextRow.style.display === 'none') {
                        nextRow.style.display = 'table-row';
                        $('.parent_child_'+someAttribute).show();
                    } else {
                        nextRow.style.display = 'none';
                        $('.parent_child_'+someAttribute).hide();
                    }
                }
            });
        });
        
</script>
<script type="text/javascript">
$(document).ready(function() {
    $('#facebookk').DataTable({
       		 "order": [],
             "pageLength": 25,
             "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
             "searching": false,
             "paging": true
    }); // Ensure paging is enabled

	
});
$('.reset_button_facebook').click(function(event) {
		event.preventDefault(); // Prevent the form from submitting
     	localStorage.clear(); 
		console.log('reset button clicked');
		var encodedToken = btoa('facebook');
		window.location.href = "omni_channel.php?token=" + encodeURIComponent(encodedToken);
    });
</script>
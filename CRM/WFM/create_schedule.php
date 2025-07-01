<?php
/***
 * Auth: Vastvikta Nishad
 * Date:   28 Feb 2024
 * Description: Create Schedule
 * 
*/
include_once("wfm_function.php");
include("../../config/web_mysqlconnect.php");
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];

// $link_for_break = '';
// $startdatetime=date('Y-m-d');
// $wfm_break = "select * from ensembler.tbl_wfm_agent_sched_instance where i_AgentID = 246 and (d_schedStartDate>='$startdatetime 00:00:00' and  d_schedEndDate<='$startdatetime 23:59:59')";
// $res_brk = mysqli_query($link,$wfm_break);
// $rs_brk = mysqli_fetch_array($res_brk);
// $rowsbb=mysqli_num_rows($res_brk);
// if($rowsbb>0){ 
//   $query = '';
//     $shift_break=explode(",",$rs_brk['v_breakList']);
//     for($s_break=0;$s_break<count($shift_break);$s_break++){// for loop for comma (,) seperated values
//         $assigned_break_id=$shift_break[$s_break];
//         $sqlsource_abreak="select * from ensembler.tbl_wfm_mst_break where i_breakID='$assigned_break_id'";
//         $sourceresult_abreak=mysqli_query($link,$sqlsource_abreak);
//         while($row_abreak=mysqli_fetch_array($sourceresult_abreak)){  
//             if($query == ''){
//                 // $query .= ',';
//             }
//             $query .= "$row_abreak['v_breakName']";
//         }
//         print_r($query);
//     }
//     $link_for_break = "where status_name IN($query)";
// }

/*--------------------------------Delete code here [30-03-23:aarti]---------------------------*/
$act=$_GET['act'];
$id=$_GET['id']; 
if($id){
	$fetch2 = getProc($id);
	while($row2 = mysqli_fetch_array($fetch2)){
    	$v_schedName        = $row2['v_schedName'];
    	$i_noOfShift        = $row2['i_noOfShift'];
    	$v_shiftList 		= $row2['v_shiftList'];
    	$d_startDate 		= $row2['d_startDate'];
    	$d_endDate 			= $row2['d_endDate'];		
	}
}
#-----------end of delete-----------------------------------------------------------------------------------
?>
 
<style type="text/css">
  input[type=text],input[type=number], select, textarea {
    width: 90%;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    resize: vertical;
}

label {
    padding: 5px 10px 5px 10px;
    display: inline-block;
}
.row_2 {
    background-color: #ededed;
    padding: .2% .5% .5% 1%;
}
.row1 {
    background-color: #fff;
    padding: .2% .5% .5% 1%;
}

	.btn_gen_sched,#submit ,#update {
		color: #FFF;
		background-color: #060;
		text-align: center;
		padding: 1% 2%;
		border: 1px solid #CCC;
		margin: 2%;
	}
</style>
<form name="frm_schedule" action="" method="post">
    <div class="style2-table">
       <div class="style-title">
          <h3>Create Schedule</h3>
          <p> &nbsp;</p>
        </div>
 
      <div class="row row_2">
        <div class="col-25">
          <label for="txt_schedule_name">Schedule Name : </label>
        </div>
        <div class="col-50">
          <input type="text" id="txt_schedule_name" name="txt_schedule_name">
        </div>
      </div>
      <div class="row row_2 row1">
        <div class="col-25">
          <label for="txt_shift">Number of Shift : </label>
        </div>
        <div class="col-50">
        <input type="number" id="txt_shift" name="txt_shift" max="3" min="1" onblur="chk_val()">
        </div>
      </div>
      <div class="row row_2">
        <div class="col-30">
          <div class="col-25"><label for="ddl_shift1">Shift 1:</label></div>
          <div class="col-75">
            	<select id="ddl_shift1" name="ddl_shift1" disabled>
            		<option value="">Select</option>
          	<?php
      		      $sourceresult_shift=getShift();
      		      while($row_shift1=mysqli_fetch_array($sourceresult_shift)) 
      		      {
      		        ?>
      		          <option value="<?=$row_shift1['i_shiftID']?>"><?=$row_shift1['v_shiftName']?> (<?=$row_shift1['t_fromTime']?>-<?=$row_shift1['t_toTime']?>)</option>
      		    <? 
      		      }
      		?>

              </select>
          </div>
        </div>
        <div class="col-30">
          <div class="col-25"><label for="ddl_shift2">Shift 2: </label></div>
          <div class="col-75">
            	<select id="ddl_shift2" name="ddl_shift2" disabled>
            		<option value="">Select</option>
          	<?php    
      		      
      		      $sourceresult_shift=getShift();
      		      while($row_shift2=mysqli_fetch_array($sourceresult_shift)) 
      		      {
      		        ?>
      		          <option value="<?=$row_shift2['i_shiftID']?>"><?=$row_shift2['v_shiftName']?> (<?=$row_shift2['t_fromTime']?>-<?=$row_shift2['t_toTime']?>)</option>
      		    <? 
      		      }
      		?>

              </select>
          </div>
        </div>
        <div class="col-30">
          <div class="col-25"><label for="ddl_shift3">Shift 3: </label></div>
          <div class="col-75">
            	<select id="ddl_shift3" name="ddl_shift3" disabled>
            		<option value="">Select</option>
          	<?    
      		     $sourceresult_shift=getShift();
      		      while($row_shift3=mysqli_fetch_array($link,$sourceresult_shift)) 
      		      {
      		        ?>
      		          <option value="<?=$row_shift3['i_shiftID']?>"><?=$row_shift3['v_shiftName']?> (<?=$row_shift3['t_fromTime']?>-<?=$row_shift3['t_toTime']?>)</option>
      		    <? 
      		      }
      		?>

              </select>
          </div>
       </div> 
      </div>
      <div class="row row_2 row1">
        <div class="col-30">
        	<div class="col-75"><label  for="txt_agent1" >No. of Agents</label></div>
        <div class="col-25"><input name="txt_agent1" id="txt_agent1" type="text" disabled/></div>
        
        </div>
        <div class="col-30">
        	<div class="col-75"><label  for="txt_agent2" >No. of Agents</label></div>
        <div class="col-25"><input name="txt_agent2" id="txt_agent2" type="text" disabled/></div>
        
        </div>
        <div class="col-30">
        	<div class="col-75"><label  for="txt_agent3" >No. of Agents</label></div>
        <div class="col-25"><input name="txt_agent3" id="txt_agent3" type="text" disabled/></div>
        
        </div>
      </div>
      <div class="row row_2">
          <div class="col-30">
            	<div class="col-25"><label  for="break_list1">Breaks</label></div>
              <div class="col-75">
                <select id="break_list1" name="break_list1[]" multiple disabled style="height:150px;">
                  <?php 
                    $sourceresult_abreak=getBreakData();
                    while($row_break=mysqli_fetch_array($sourceresult_abreak)) 
                    {
                      ?>
                        <option value="<?=$row_break['i_breakID']?>" title="(<?=$row_break['d_startbreak']?>-<?=$row_break['d_endbreak']?>)"><?=$row_break['v_breakName']?> (<?=$row_break['d_startbreak']?>-<?=$row_break['d_endbreak']?>)</option>
                  <? 
                    }
                  ?>

                  </select>
              </div>
          </div>
          <div class="col-30">
        	    <div class="col-25"><label  for="break_list2">Breaks</label></div>
              <div class="col-75">
                  <select id="break_list2" name="break_list2[]" multiple disabled style="height:150px;">
                  <?    
                   $sourceresult_abreak=getBreakData();
                   while($row_break=mysqli_fetch_array($sourceresult_abreak)) 
                    {
                      ?>
                        <option value="<?=$row_break['i_breakID']?>" title="(<?=$row_break['d_startbreak']?>-<?=$row_break['d_endbreak']?>)"><?=$row_break['v_breakName']?> (<?=$row_break['d_startbreak']?>-<?=$row_break['d_endbreak']?>)</option>
                  <? 
                    }
                  ?>
                  </select>
              </div>
        
          </div>
          <div class="col-30">
        	    <div class="col-25"><label  for="break_list3">Breaks</label></div>
              <div class="col-75">
                  <select id="break_list3" name="break_list3[]" multiple disabled style="height:150px;">
                  <?    
                    $sourceresult_abreak=getBreakData();
                    while($row_break=mysqli_fetch_array($sourceresult_abreak)) 
                    {
                      ?>
                        <option value="<?=$row_break['i_breakID']?>" title="(<?=$row_break['d_startbreak']?>-<?=$row_break['d_endbreak']?>)"><?=$row_break['v_breakName']?> (<?=$row_break['d_startbreak']?>-<?=$row_break['d_endbreak']?>)</option>
                  <? 
                    }
                  ?>
                  </select>
              </div>
          </div>
          <div class="row" >
              <div class="col-25">
              <label for="from_time">Date</label>
              </div>
              <div class="col-50">
                <!-- <input type="text" id="from_time" name="from_time" placeholder="From"> -->
                <input type="text" name="from_time" class="dob1" value="<?=$_REQUEST['from_time']?>" id="from_time" autocomplete="off" placeholder="From" >
              </div>
              <div class="col-50">
                <!-- <input type="text" id="to_time" name="to_time" placeholder="To"> -->
                <input type="text" name="to_time" class="dob1" value="<?=$_REQUEST['to_time']?>" id="to_time" autocomplete="off" placeholder="To" >
              </div>
          </div>
      </div>
     
      <div class="botton"><center><input type='submit' name='submit' id='submit' value='Submit' class="submit_schedule" style="height: 30px;width:70px;padding: 5px;float:none;"></center></div>
          <div class="botton"><center><input type='submit' name='update' id="update" value='Update' class="update_schedule" style="height: 30px;width:70px;padding: 5px;float:none;display: none;"></center></div>
        
</form>

<!-- <form name="frm_gen_schedule"> -->
    <table class="view">
        <tr>
            <th width="17%" style="border:none;"></th>
            <th width="83%" style="border:none;" ><div class="col-100">
            <div class=" ">
              <div class="col-25"></div><div class="col-25"><label>Schedule List </label></div>
            </div>
            </div></th>
                
        </tr>
        <tr>
            <!--<td>Morning<br />
          M &nbsp;T&nbsp;W&nbsp;TH&nbsp;F&nbsp;S&nbsp;S</td>-->
            <td colspan="3">
                <div  id="div_adherence">
                    <div class="row <?=$rval?>">
                    	<div class="col-5">
                        </div>
                        <div class="col-18"><b>Schedule Name</b>
                        </div>
                        <div class="col-18"><b>No. of Shifts</b>
                        </div>
                        <div class="col-18"><b>From time</b>
                        </div>
                        <div class="col-18"><b>To Time</b>
                        </div>
                        <div class="col-18"><b>Action</b>
                        </div>
                    </div>
                    <?
                    $dcolor=0;
                    $sqlsource_sched="select * from $db.tbl_wfm_proc_schedule order by i_procSchedID	desc";
                    $sourceresult_sched=mysqli_query($link,$sqlsource_sched);
                    while($row_sched=mysqli_fetch_array($sourceresult_sched)) 
                    {
                        $rval="";
                        if($dcolor%2==0)
                        {
                          $rval="row1";
                        }
                        ?> 
                        <div class="row <?=$rval?>">
                            <div class="col-5" style="width: 6%;">
                              	<!-- <a href='javascript:void(0)' onclick="window.location.href='create_schedule.php?pid=<?=$row_sched['i_procSchedID']?>';">
                                <img src="images/edit-icon.png" border="0"  alt="Edit">
                                </a> -->
                                <a href='javascript:getSched("<?=$row_sched['i_procSchedID']?>")'><img src="WFM/images/edit-icon.png" border="0"  alt="Edit">
                                </a>
                                <a href='javascript:void(0);' data-id="<?=$row_sched['i_procSchedID']?>" class="schedule_delete">
                                    <img src="<?=$SiteURL?>public/images/delete-icon.png" border="0" alt="delete">
                                </a>
                          </div>
                            <div class="col-18"><?=$row_sched['v_schedName']?>
                            </div>
                            <div class="col-18"><?=$row_sched['i_noOfShift']?>
                            </div>
                            <div class="col-18"><?=$row_sched['d_startDate']?>
                            </div>
                            <div class="col-18"><?=$row_sched['d_endDate']?>
                            </div>
                            <div class="col-18"><span style="display: none;"><?=$row_sched['i_procSchedID']?></span>
                              	<!-- <input type='submit' name='submit' value='Generate' class="button btn_gen_sched" style="height: 30px;width:100px;padding: 5px;float:none;"> -->
                                <button class="btn_gen_sched" style="height: 30px;width:100px;padding: 5px;float:none;">Generate</button>
                                
                            </div>
                        </div>
                        <?
                        $dcolor++;
                           // shift while end
                    }   //break while end
                    ?>   
                  
            </td>
        </tr>
            </div>
    </table>
<!-- </form> -->
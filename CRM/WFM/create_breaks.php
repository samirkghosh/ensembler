<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  29 Feb 2024
 * Description: Create Breaks
 * 
*/
//file which contains funcitons used in this file 
include_once("wfm_function.php");
// echo "aarti"; die;
include("../../config/web_mysqlconnect.php");
//print_r($centralspoc);
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];

?>
	<style>
	
	.button {
		color: #FFF;
		background-color: #060;
		text-align: center;
		padding: 1% 2%;
		border: 1px solid #CCC;
		margin: 2%;
	}

	</style>
  <script>


    </script>
<form name="frmagentdashboardd" action="" method="post">
  <!-- Start Right panel -->
  <div class="style2-table">
      <div class="style-title">
         <h3>Create Break</h3>
         <p> &nbsp;</p>
      </div>
      <form action="">
          <div class="row row_2">
            <div class="col-25">
              <label for="break_name">Break Name</label>             
            </div>
            <div class="col-75">
              <select id="break_name" name="break_name">
                <?php  
                /*getting break details from asterisk database beasuse agent break and schdule break need to be same*/
                $reason_status = getReasonStatus();
                while($reason=mysqli_fetch_array($reason_status)) 
                {?>
                  <option value="<?php echo $reason['status'];?>"><?php echo $reason['status'];?></option>
                <?php }?>
                </select>
              <input type="hidden" id="break_id" name="break_id">
              <!-- <input type="text" id="break_name" name="break_name" required> -->
            </div>
          </div>
          <div class="row row_2 row1">
            <div class="col-25">
              <label for="break_type">Break Type</label>
            </div>
            <div class="col-25">
                <select id="break_type" name="break_type">
                  <option value="1">Scheduled</option>
                  <option value="2">Unscheduled</option>
                </select>
            </div>
          </div>
          <div class="row row_2 class_schedule" >
            <div class="col-25">
              <label for="from_time">Hours</label>
            </div>
            <div class="col-50">
              <!-- <input type="text" id="from_time" name="from_time" placeholder="From"> -->
              <input type="text" name="from_time" class="dob1" value="<?=$_REQUEST['from_time']?>" id="from_timebreak" autocomplete="off" placeholder="From" >
            </div>
            <div class="col-50">
              <!-- <input type="text" id="to_time" name="to_time" placeholder="To"> -->
              <input type="text" name="to_time" class="dob1" value="<?=$_REQUEST['to_time']?>" id="to_timebreak" autocomplete="off" placeholder="To" >
            </div>
          </div>
          
          <div class="botton"><center><input type='submit' name='submit' id='submit' value='Submit' class="button" style="height: 30px;width:70px;padding: 5px;float:none;"></center></div>
          <div class="botton"><center><input type='submit' name='update' id="update" value='Update' class="button" style="height: 30px;width:70px;padding: 5px;float:none;display: none;"></center></div>
        
      </form>  
      <table class="view">
        <tr>
          <th width="17%" style="border:none;"></th>
          <th width="83%" style="border:none;" >
            <div class="col-100">
              <form action="">
                <div class=" ">
                  <div class="col-25"></div>
                  <div class="col-25"><label>Break List </label>
                  </div>
                </div>
              </form>
            </div>
          </th>  
        </tr>
        <tr>
          <td colspan="3">
            <div  id="div_adherence">
            <div class="row <?=$rval?>">
              <div class="col-5">
              </div>
              <div class="col-20"><b>Type</b>
              </div>
              <div class="col-20"><b>Break Name</b>
              </div>
              <div class="col-20"><b>From time</b>
              </div>
              <div class="col-20"><b>To Time</b>
              </div>
            </div>
            <?php 
              $dcolor=0;
              $sourceresult_abreak=getBreakData();
              while($row_abreak=mysqli_fetch_array($sourceresult_abreak)) 
              {
                $rval="";
                if($dcolor%2==0)
                {
                  $rval="row1";
                }
                $btype="";
                if($row_abreak['i_breakType']==1)
                {
                  $btype="Scheduled";
                }
                else if($row_abreak['i_breakType']==2)
                {
                  $btype="Unscheduled";
                }
                else
                {
                  $btype="";
                }
              ?>  
              <div class="row row_2 <?=$rval?>">
            <div class="col-5">
            <div class="row row_2 <?=$rval?>">
                <a href='javascript:void(0)' onclick="getBreak('<?=$row_abreak['i_breakID']?>')">
                    <img src="<?=$SiteURL?>public/images/edit-icon.png" border="0" alt="Edit" />
                </a>
                <a href='javascript:void(0);' data-id="<?=$row_abreak['i_breakID']?>" class="break_delete">
                    <img src="<?=$SiteURL?>public/images/delete-icon.png" border="0" alt="delete">
                </a>
            </div>
        </div>
                <div class="col-20"><?=$btype?>
                </div>
                <div class="col-20"><?=$row_abreak['v_breakName']?>
                </div>
                <div class="col-20"><?=$row_abreak['d_startbreak']?>
                </div>
                <div class="col-20"><?=$row_abreak['d_endbreak']?>
                </div>
              </div>
                <?
              $dcolor++;
                     
            }   
            ?>   
          </td>
        </tr>
      </table>
          
  </div>
  <!-- End Right panel -->
</form>
  
                
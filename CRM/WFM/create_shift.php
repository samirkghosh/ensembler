<?php 
/***
 * Auth: Vastvikta Nishad
 * Date: 22  Feb 2024
 * Description: To Create Shift
 * 
*/
//file which contains funcitons used in this file 
include_once("wfm_function.php");
include("../../config/web_mysqlconnect.php");
//print_r($centralspoc);
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];
define("PIXCEL","4");

?>
<link rel="stylesheet" href="<?=$SiteURL?>public/css/style.css">

<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/WFM/css/wfm_common.css">
<link rel="stylesheet" href="WFM/css/erlang_style.css">
<form name="frmagentdashboardd" action="" method="post">
  <!-- Start Right panel -->
  <!-- <div class="rightpanel"> -->
    <div class="style2-table">
      <div class="style-title">
         <h3>Create Shift</h3>
         <p> &nbsp;</p>
      </div>
      <!-- <h1>Create Shift</h1> -->
      <form action="">
        <div class="row row_2">
          <div class="col-25">
            <label for="fname">Shift Name</label>
          </div>
          <div class="col-75">
            <input type="text" id="shift_name" name="shift_name" required>
          </div>
        </div>
        <div class="row row_2 row1">
          <div class="col-25">
            <label for="lname">Days of Week</label>
          </div>
          <div class="col-radio-75">        
            <input id="chk_days1" type="checkbox" value="all" />All
            <input name="chk_days[]" type="checkbox" class="chk_class" value="0" />S
            <input name="chk_days[]" type="checkbox" class="chk_class" value="1" />M
            <input name="chk_days[]" type="checkbox" class="chk_class" value="2" />T
            <input name="chk_days[]" type="checkbox" class="chk_class" value="3" />W
            <input name="chk_days[]" type="checkbox" class="chk_class" value="4" />TH
            <input name="chk_days[]" type="checkbox" class="chk_class" value="5" />F
            <input name="chk_days[]" type="checkbox" class="chk_class" value="6" />SA       
          </div>
        </div>
        <div class="row row_2">
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

        <div class="botton"><center><input type='submit' name='submit' value='Submit'  id ='insert' class="button" style="height: 30px;width:70px;padding: 5px;float:none;"></center></div>

      </form>
      <table class="view">
        <tr>
          <th width="17%" style="border:none;">Shift Name</th>
          <th width="83%" style="border:none;" >
            <div class="col-100">
              <form action="">
                <div class=" ">
                  <div class="col-25"></div><div class="col-25"><label>Shift Timeline </label></div>
                  
                </div>

              </form>
            </div>
          </th>  
        </tr>
        <tr>
          <td colspan="3">
            <div  id="div_adherence">
              <div class="row row_2">
                <div class="col-25"></div>
                <div class="col-60">
                  <div class="chart">
                     <?php for($t=0;$t<24;$t++)
                      {
                        if($t==0)
                        {
                          $margin_tick=0;
                        }
                        else
                        {
                          $margin_tick+=PIXCEL;
                        }
                        $class_margin_tick="left:".$margin_tick."%";
                        ?>
                      <div class="tick" style="<?=$class_margin_tick?>"><span><?=$t?></span></div>
                    <?php
                      }
                    ?>
                  </div>
                </div>
              </div>
              <br><br>
                  <?php
                  $dcolor=1;               
                  $sourceresult_shift = getShift();
                  while($row_shift=mysqli_fetch_array($sourceresult_shift))
                  {
                    $shift_margin=0;$shift_width=0;
                    $shift_stime=explode(":",$row_shift['t_fromTime']);
                    $shift_etime=explode(":",$row_shift['t_toTime']);

                    $assigned_start_time=$row_shift['t_fromTime'];
                    $assigned_end_time=$row_shift['t_toTime'];

                    $shift_margin=shift_margin($shift_stime[0]);
                          
                    $shiftetime= 0;              // to get the difference between hours assigned in shift : By Vipul Dwivedi
                    if($shift_stime[0]>$shift_etime[0])
                    {
                      $shiftetime=24;
                    }
                    else
                    {
                      $shiftetime=$shift_etime[0];
                    }

                    $shift_width=shift_margin($shiftetime);
                    $shift_width-=$shift_margin;

                    $class_margin_shift="margin-left:".$shift_margin."%;";
                    $class_width_shift="width:".$shift_width."%;";

                    $rval="";
                    if($dcolor%2==0)
                    {
                      $rval="row1";
                    }

                  ?>
                  <div class="row <?=$rval?>">
                    <div class="col-25">
                        <!-- <?=$agent_name?>--><?=$row_shift['v_shiftName']?><br />
                      <?php
                      $shift_days=explode(",",$row_shift['v_weekDays']);
                      // print_r($shift_days);

                      for($s_days=0;$s_days<count($shift_days);$s_days++)// for loop for comma (,) seperated values
                      {
                        if($shift_days[$s_days]!="")
                        {
                          echo days_case($shift_days[$s_days])."&nbsp";
                        }
                      }
                      ?>
                    </div>
                    <div class="col-70">
                      <div class="w3-light-grey w3-round-xlarge">
                          <div class="w3-container w3-blue  tooltip_shift" style="<?=$class_margin_shift?><?=$class_width_shift?>"><span class="tooltiptext_shift"><?=$row_shift['v_shiftName']?> : <?=$row_shift['t_fromTime']?> to <?=$row_shift['t_toTime']?></span>&nbsp;
                          </div>
                        <?
                      // ?>
                      <!--  <div class="break_assigned tooltip_break" style="<?=$class_margin_abreak?><?=$class_width_abreak?>"><span class="tooltiptext_break"><?=$row_abreak['v_breakName']?> : <?=date("H:i:s",strtotime($row_abreak['d_startbreak']))?> to <?=date("H:i:s",strtotime($row_abreak['d_endbreak']))?></span>&nbsp;</div> -->
                      <?
                      //       }   //break while end

                      //     }     //comma seperated loop end  ?>   
                      </div>   
                    </div><br />
                  </div>
                  <?$dcolor++;
                  }         // shift while end
                  ?>     
            </div>  
          </td>
        </tr>
      </table>
    </div>         
  <!-- </div> -->
  <!-- End Right panel -->
</form>
<!-- 
<script>
  $(document).ready(function () {
    // Destroy old datepickers if any
    try { $('#from_time').datepicker('destroy'); } catch (e) {}
    try { $('#to_time').datepicker('destroy'); } catch (e) {}

    // Time-only picker with seconds
    $('#from_time').datetimepicker({
      datepicker: false,
      format: 'H:i:s',
      step: 1
    });

    $('#to_time').datetimepicker({
      datepicker: false,
      format: 'H:i:s',
      step: 1
    });
  });
</script> -->

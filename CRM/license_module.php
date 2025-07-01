<?php
/* ----------------Database connect file added ---------------- */
/* this file for add new licens moduel
Aarti-29-11-23*/
include_once("../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this 
function module_license_list(){
    global $link,$db;
    if(!empty($_REQUEST['select_menu']) and $_REQUEST['select_menu'] != 'All'){
      $select_menu = $_REQUEST['select_menu'];
      $str = "and master_name='{$select_menu}'";
    }
    $query = "SELECT * FROM $db.module_license where active='1' $str";
    $res =mysqli_query($link,$query);
    return $res;
}
?>
<style type="text/css">
/* ----------------Global input---------------- */
.Toggle input[type="checkbox"],
.Radio input[type="radio"],
.Rating input[type="radio"] {
  position: absolute;
  left: -100vw;
}

.Toggle input[type="checkbox"] + label,
.Radio input[type="radio"] + label,
.Rating input[type="radio"] + label {
  position: relative;
  /*display: block;
  line-height: 3rem;
  cursor: pointer;
  white-space: nowrap;*/
}

.Toggle input[type="checkbox"] + label::before,
.Toggle input[type="checkbox"] + label::after,
.Radio input[type="radio"] + label::before,
.Radio input[type="radio"] + label::after,
.Rating input[type="radio"] + label::before,
.Rating input[type="radio"] + label::after {
  content: '';
  display: inline-block;
  position: absolute;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
  transition: .5s;
}

/* large */
.Color__large input[type="color"] + label,
.Toggle__large input[type="checkbox"] + label,
.Radio__large input[type="radio"] + label,
.Range__large input[type="range"] + label,
.Rating__large .Rating_label {
  font-size: 2rem;
  line-height: 6rem;
}
/* ----------------Toggles input---------------- */
.Toggle input[type="checkbox"] + label {
  padding-left: 5rem;
  padding-bottom: 19px;
  padding-top: 12px;
}
.Toggle input[type="checkbox"] + label::before {
  width: 4rem;
  aspect-ratio: 2 / 1;
  border-radius: 1rem;
  background: #eee;
}

.Toggle input[type="checkbox"]:checked + label::before {
  background: #ddf8eb;
}

.Toggle input[type="checkbox"] + label::after {
  left: .25rem;
  width: 1.4rem;
}

.Toggle input[type="checkbox"]:checked + label::after {
  animation: toggle 0.5s linear;
  transform: translate(125%, -50%);
}
/* ----------------
    Bubble display
   ---------------- */

.Toggle input[type="checkbox"] + label::after,
.Radio input[type="radio"] + label::after,
.Rating input[type="radio"] + label::after,
.Rating input[type="radio"]:checked ~ label > div::before,
.Rating input[type="radio"]:checked ~ label > div::after,
.Range input[type="range"]::-webkit-slider-thumb,
.Color input::-webkit-color-swatch {
  aspect-ratio: 1 / 1;
  border: 0.1rem solid #fff;
  border-radius: 50%;
  background: radial-gradient(circle at 70% 30%, #fff, rgba(0,0,0,0) 25%),
    radial-gradient(circle at 60% 55%, rgba(0,0,0,0) 60%, rgba(255, 0, 255, 0.8) 100%),
    radial-gradient(circle at 50% 50%, rgba(0,0,0,0) 40%, rgba(0, 255, 255, 0.2) 60%, rgba(0,0,0,0) 68%),
    radial-gradient(circle at 50% 55%, rgba(0,0,0,0) 35%, rgba(255, 255, 0, 0.2) 45%, rgba(0,0,0,0) 55%);
}
.customControl{
  float: unset !important;
}
.admin_role{
  pointer-events: none;
  filter: none;
  opacity: .5;
}
</style>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row" style="min-height:90vh">
                <div class="col-sm-2" style="padding-left:0">
                    <? include("includes/sidebar.php"); ?>
                </div>
                <div class="col-sm-10 mt-3" style="padding-left:0">
                    <div class="rightpanels">                       
                        <span class="breadcrumb_head" style="height:37px;padding:9px 16px">System Configuration   
                          <form name="myform" method="post" style="margin-top: -18px;padding-left: 172px;">
                            <select name="select_menu" class="select-styl1" onchange="dosubmit();">
                                <option value="All" <?php if($_REQUEST['select_menu'] == 'All'){?> selected <?php }?> >All</option>
                                <option value="menu" <?php if($_REQUEST['select_menu']=='menu'){?> selected <?php } ?> >Menu</option>
                                <option value="report" <?php if($_REQUEST['select_menu']=='report'){?> selected <?php } ?> >Report</option>
                                <option value="admin" <?php if($_REQUEST['select_menu']=='adminmaster'){?> selected <?php } ?> >Admin Master</option>
                                <option value="casecreate" <?php if($_REQUEST['select_menu']=='casecreate'){?> selected <?php } ?> >Case Create</option>
                                <option value="Notification" <?php if($_REQUEST['select_menu']=='Notification'){?> selected <?php } ?> >Notification</option>
                            </select>                      
                        </form>
                        </span>
                        <div class="style2-table">
                            <div class="style-title2">
                                <div class="table tabcontent" id="FACEBOOK">
                                    <table class="tableview tableview-2">
                                        <tbody>
                                            <tr class="background">
                                                <td align="left">S.No.</td>
                                                <td align="left">Module Name</td>
                                                <td align="left">Hide/Show</td>
                                                <td align="left">Role Mode</td>
                                            </tr>
                                           <?php
                                           $list_data = module_license_list();
                                            while($module = mysqli_fetch_array($list_data)){
                                            $no++;
                                            ?>
                                            <tr>
                                                <td align="left"><?=$no?></td>
                                                <td align="left"><?=$module['module_name']?></td>
                                                <td>
                                                  <?php 
                                                    if($module['module_flag'] == '1'){
                                                      $checked = 'checked';
                                                    }else{
                                                      $checked = '';
                                                    }
                                                  ?>
                                                    <div class="Toggle">
                                                      <input id="Checkbox2_<?php echo $no;?>" name="checkbox_<?php echo $no;?>" type="checkbox" class="checkbox" <?php echo $checked;?> data-id="<?php echo $module['id'];?>"/>
                                                      <label for="Checkbox2_<?php echo $no;?>"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                  <?php 

                                                    $group_Id = $module['group_Id'];
                                                    $group_Idarr = array();
                                                    if(!empty($group_Id)){
                                                      $group_Idarr = explode(',', $group_Id);
                                                    }
                                                    if($module['master_name'] == 'Report' || $module['master_name'] == 'admin'){
                                                    
                                                  ?>
                                                    <input type="checkbox" class="admin_role Rolecheck form-check-input" id="customControlAutosizing1" value="0000" data-id="<?php echo $module['id'];?>" data-name="admin" checked>
                                                    <label class="customControl form-check-label mb-0" for="customControlAutosizing1">Admin</label>

                                                    <input type="checkbox" class="Rolecheck form-check-input" id="customControlAutosizing1" value="080000" data-id="<?php echo $module['id'];?>" data-name="admin" <?php if(in_array('080000',$group_Idarr)){echo"checked";}?>>
                                                    <label class="customControl form-check-label mb-0" for="customControlAutosizing1">Supervisor</label>

                                                    <input type="checkbox" class="Rolecheck form-check-input" id="customControlAutosizing2" value="060000" data-id="<?php echo $module['id'];?>" data-name="Backoffice" <?php if(in_array('060000',$group_Idarr)){echo"checked";}?>>
                                                    <label class="customControl form-check-label mb-0" for="customControlAutosizing2">Backoffice Officer</label>

                                                    <input type="checkbox" class="Rolecheck form-check-input" id="customControlAutosizing3" value="070000" data-id="<?php echo $module['id'];?>"data-name="agent" <?php if(in_array('070000',$group_Idarr)){echo"checked";}?>>
                                                    <label class="customControl form-check-label mb-0" for="customControlAutosizing3">Agent</label>
                                                  <?php }?>
                                                </td>
                                            </tr>
                                            <?php  }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>           
            </div>
        </div>
    </div>
</body>
</html>
<!-- jQuery script -->
<script>
    // Handle checkbox click event
    $(document).ready(function() {
      var chkbox = $('.checkbox');
      $(".checkbox").keyup(function() {
        // Check only the clicked checkbox
        chkbox.prop('checked', this.value==1);
        console.log(this);
        // module_flag();
      });
      $('.checkbox').click(function() {
        var checked = $(this).is(':checked');
        var id = $(this).data('id');
        console.log(checked);
        $.ajax({
            type: "POST",
            url: 'common_function.php',
            data: { checked : checked,id:id,action:'license_update'},
            success: function(data) {
                location.reload();
            },
            error: function() {
                alert('somethink went wrong!');
            }
        });
    });
      $('.Rolecheck').click(function() {
        var checked = $(this).is(':checked');
        // if(checked){
          var checkedval = $(this).val();
        // }else{
          // checkedval = '';
        // }
        var id = $(this).data('id');
        var group_name = $(this).data('name');
        $.ajax({
            type: "POST",
            url: 'common_function.php',
            data: { checked : checked,id:id,groupval:checkedval,group_name:group_name,action:'license_update_role'},
            success: function(data) {
                location.reload();
            },
            error: function() {
                alert('somethink went wrong!');
            }
        });
      });
    });

    function dosubmit(){
      document.myform.action="license_module.php";
      document.myform.target="_self";
      document.myform.submit();
    }
</script>
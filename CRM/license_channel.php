  <?php
/* ----------------Database connect file added ---------------- */
/* this file for add new licens moduel
Aarti-29-11-23*/
include_once("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
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
function Channel_license_count($channel_type){
       global $db,$link;
       $sql= $link->query("SELECT count(*) as channel_count FROM $db.user_channel_assignment WHERE channel_type='$channel_type'");
       $row = $sql->fetch_assoc();
       $channel_count = $row['channel_count'];
       return $channel_count;
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
                        <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Social Media License</span>
                        <div class="style2-table">
                            <div class="style-title2">
                                <div class="table tabcontent" id="FACEBOOK">
                                  <table class="tableview tableview-2 main-form new-customer">
                                      <tbody>
                                        <tr>
                                            <td>
                                              <label>Channel Name</label>
                                              <select name="select_menu" class="select-styl1 select_menu">
                                                  <option value="">Please select channel</option>
                                                  <option value="Email">Email</option>
                                                  <option value="Twitter">Twitter</option>
                                                  <option value="SMS">SMS</option>
                                                  <option value="WhatsApp">WhatsApp</option>
                                                  <option value="Facebook Post">Facebook Post</option>
                                                  <option value="Facebook Messenger">Facebook Messenger</option>
                                                  <option value="Chat">Chat</option>
                                                  <option value="Instagram Messenger">Instagram Messenger</option>
                                                  <option value="Instagram Post">Instagram Post</option>
                                              </select> 
                                            </td> 
                                             <td class="left boder0-left">
                                              <label>License Count</label>
                                              <input name="count" id="count" maxlength="20" type="text" value="" class="input-style1">
                                            </td>   
                                        </tr>
                                        <tr>
                                          <td colspan="2" style="align-content: center;">
                                         <input name="Submit" type="submit" value="Submit" class="button-orange1 submit_Channel" style="float:inherit;">
                                       </td>
                                       </tr>
                                      </tbody> 
                                      </table>         
                                </div>
                                  <div class="col-sm-9 card mx-4">
                                    <!-- DIV 1 START -->
                                        <div class="row">
                                          <div class="col-sm-7">
                                              <span><img src="../public/images/contact_cards-128.png" width="64" alt=""></span>
                                          </div>
                                          <div class="col-sm-5 pt-3">
                                              <span class="h5">Channel License</span>
                                          </div>
                                        </div>
                                    <table class="tableview tableview-2">
                                    <tbody>
                                      <!-- added  the serial number column and changed names  of  channels in  the database table name  channel_license [vastvikta][11-12-2024] -->
                                      <tr class="background">
    <td style="width: 8%;">S. No.</td> <!-- Small column for serial number -->
    <td style="width: 20%;">Channel Type</td>
    <td style="width: 25%;" colspan="2">License</td>
    <td style="width: 25%;">Used</td>
    <td style="width: 25%;">Available</td>
</tr>

                                      <?php 
                                      $sql = "SELECT * FROM $db.channel_license";
                                      $rs = mysqli_query($link, $sql);
                                      $serial_number = 1; // Initialize the serial number

                                      while ($row = mysqli_fetch_array($rs)) {
                                          $avail = $row["count"] - Channel_license_count($row['name']);
                                          echo '<tr> 
                                              <td>' . $serial_number . '</td> 
                                              <td>' . $row["name"] . '</td> 
                                              <td colspan="2">' . $row["count"] . '</td> 
                                              <td>' . Channel_license_count($row['name']) . '</td>
                                              <td>' . $avail . '</td> 
                                          </tr>';
                                          $serial_number++; // Increment the serial number for the next row
                                      }
                                      ?>
                                  </tbody>

                                    </table>
                                    <!-- DIV 1 END -->
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
      $('.submit_Channel').click(function() {
        var count = $('#count').val();
        var select_menu = $('.select_menu').val();
        //added validation code  for the count  value [vastvikta][11-12-2024]
        // Validation for channel type and count
        if (!select_menu) {
              alert('Please select a channel type');
              return false; // Stop form submission
          }

        if (!count || isNaN(count) || parseInt(count) <= 0) {
            alert('Please enter Value of License Count');
            return false; // Stop form submission
        }
        if(select_menu){
          $.ajax({
              type: "POST",
              url: 'common_function.php',
              data: { select_menu : select_menu,count:count,action:'license_update_channel'},
              success: function(data) {
                  location.reload();
              },
              error: function() {
                alert('somethink went wrong!');
              }
          });
        }
      });
       $(document).ready(function() {
          // Restrict input to numbers only
          $('#count').on('input', function() {
              this.value = this.value.replace(/[^0-9]/g, '');  // Remove any non-numeric characters
          });
      });
</script>
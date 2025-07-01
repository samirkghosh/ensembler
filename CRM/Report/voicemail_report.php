<?php
/***
 * Voicemail Report Page
 * Author: Ritu modi
 * Date: 07-02-2024
 * 
 * This page is used to display a report of voicemails.
 * Users can filter voicemails by selecting a date range and specifying whether they are read or unread.
 * After applying filters, users can submit the form to view the voicemail report.
 * The report includes details such as caller ID, client name, voicemail time, read/unread status, associated case, and an option to play the voicemail.
**/?>
<form name="myform" method="post">
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Voice Mails</span>
    <div class="style2-table ">
        <div class="table">
            <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                    <!-- Form fields for selecting date range and read/unread status -->
                    <tr>
                        <td width="50%" class="left  boder0-right">
                        <?php
                         // Determine start and end datetime parameters[vastvikta][17-03-2025]
                                    
                            $dateRange = get_date_voicemail();

                            $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                                ? $_REQUEST['sttartdatetime'] 
                                : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                            $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                                ? $_REQUEST['enddatetime'] 
                                : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
                        ?>
                            <label>From
                                <input type="text" name="sttartdatetime" class="date_class dob1" id="sttartdatetime" value="<?=$startdatetime?>">
                            </label>
                            <label>To
                                <input type="text" name="enddatetime" class="date_class dob1" id="enddatetime" value="<?=$enddatetime?>">
                            </label>
                        </td>
                        <td width="50%" class="left  boder0-right">
                            <label>Read / Unread</label>
                                <div class="log-case">
                                    <select name="status" id="status" class="select-styl1" style="width:190px">
                                        <option value="">Select</option>
                                        <option value="1" <?=($status == '1') ? 'selected' : ''?>>Read</option>
                                        <option value="0" <?=($status == '0') ? 'selected' : ''?>>Unread</option>
                                    </select>
                                </div>
                        </td>
                       
                    </tr>
                    <tr>
                    <td class="left boder0-right" colspan="1">
                       
                            <input name="submit" id="submit_voice" type="button" value="Run Report" class="button-orange1" style="float:inherit;">
                            <?php 
                            $voicemail_report = base64_encode('voicemail_report');
                            ?>
                            <input type="button" 
                                class="button-orange1" 
                                value="Reset" 
                                onclick="window.location.href='report_index.php?token=<?php echo $voicemail_report; ?>';" 
                                style="float:inherit; color:#222; text-decoration:none;">
                        
                    </td>

                        <!-- <td colspan="3">
                           
                            <audio id="player"  controls>
                            <source id="ogg_src" type="audio/mpeg">
                            </audio>
                        </td> -->
                    </tr>
                </tbody>
            </table>
        </div>
            <form name="frmService" method="post">
                <div class="wrapper6">
                <div>
                    <table class="tableview tableview-2" id="voice_data">
                            <thead>
                                <tr style="font-size: 12px">
                                    <th>S.No.</th>
                                    <th>Caller ID</th>
                                    <th>Client Name</th>
                                    <th>Voicemail Time</th>
                                    <th>Read / Unread</th>
                                    <!-- <th>Case</th> -->
                                    <!-- <th>Play</th> -->
                                </tr>
                            </thead>
                            </table>
                        </div>
                    </div>
                </form>    
                                <!-- removed code for playing the voicemail [vastvikta][28-01-2024] -->
      <!-- <script type="text/javascript">
         $('.date_class').datetimepicker();
         function onPlay(id,sourceUrl)
         {
            console.log("called onplay");
            //alert(id+' '+file);
            var audio = $("#player");      
            $("#ogg_src").attr("src", sourceUrl);
            /****************/
            audio[0].pause();
            audio[0].load();//suspends and restores all audio element
            //audio[0].play(); changed based on Sprachprofi's comment below
            audio[0].oncanplaythrough = audio[0].play();
            $.ajax({
                method:"POST",
                url: "../voicemail/get_voicemail_play.php",
                data:{'id':id},
                dataType:'json',
                success : function(data)
                {
                    console.log('success');
                    console.log(data);
                    if(data.status == 'success')
                    { 
                       console.log('read');
                       //window.location.href="voicemail_report.php";
                    } 
                },
                error : function(error)
                {
                    console.log('error')
                    console.log(error)
                }
            });
         }
        //  added code to update status to read on playing voicemail [vastvikta ][27-01-2025]
         function updateStatus(id) {
            console.log('called1');
                $.ajax({
                    method: "POST",
                    url: "Report/report_function.php",  // PHP file that updates the status
                    data: { 'id': id , 'action' : 'updateStatus'},  // Send the voicemail ID
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 'success') {
                            console.log('Status updated to 0');
                            // Optionally, you can update the UI here, e.g., change the "Read/Unread" label
                            $('#status_' + id).text('Unread');
                        } else {
                            console.log('Error: ' + data.message);
                        }
                    },
                    error: function(error) {
                        console.log('Error updating status');
                        console.log(error);
                    }
                });
            }
            // [vastvikta ][27-01-2025]
        function onPlayWithStatusUpdate(id, sourceUrl) {
            // First update the status to 0 (unread)
            updateStatus(id);

            // Then play the voicemail
            onPlay(id, sourceUrl);
        }
      </script> -->

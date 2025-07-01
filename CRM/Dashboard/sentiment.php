<?php 
/**
 * Sidebar Page
 * Author: vastvikta nishad
 * Date: 19-03-2025
 * Description: 
 */
?>

<style>
    .border-line {
        width: 100%; /* Full width */
        height: 1px; /* Adjust thickness */
        background-color: black; /* Adjust color */
       
    }
    .chart-box {
        height: 500px; 
        margin-top: 10px;
        border: 1px solid #e5e5e5; /* Dark border */
        border-radius: 5px; /* Rounded edges */
        background-color: white; /* Background color */
    }
    .chart-box2 {
        height: 500px; 
        margin-top: 30px;
        border: 1px solid #e5e5e5; /* Dark border */
        border-radius: 5px; /* Rounded edges */
        background-color: white; /* Background color */
    }
    h2{
        margin: 30px;
    }
   
</style>
<form name="frmagentdashboard" action="" method="post">
    <div class="style2-table">
        <div class="style-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Sentiment Dashboard</h3>
                </div>
                <div class="col-sm-6 d-flex flex-row-reverse bd-highlight">
                    <a href="dashboard_index.php?token=<?php echo $web_admin_dashboard;?>">Admin Dashboard</a> 
                </div>
            </div>
            <div class="style-title2 st-title2-wth-lable main-form">
                <?php
                    $startdate = isset($_POST['startdatetime']) ? $_POST['startdatetime'] : date("01-m-Y 00:00:00");
                    $enddate = isset($_POST['enddatetime']) ? $_POST['enddatetime'] : date("d-m-Y 23:59:59");
                ?>
                From: <input type="text" name="startdatetime" class="date_class dob1" value="<?= $startdate ?>" id="startdatetime" autocomplete="off">&nbsp;&nbsp;
                To: <input type="text" name="enddatetime" class="date_class dob1" value="<?= $enddate ?>" id="enddatetime" autocomplete="off">
                <input type='submit' name='sub1' value='Run Report' class="button-orange1">
                <?php
                $sentiment = base64_encode('sentiment');
                ?>
                <input type='button' name='sub1' value='Reset' class="button-orange1" onclick="window.location.href='dashboard_index.php?token=<?php echo $sentiment; ?>'">
                </div>
            <!-- Charts -->
            <div class="row text-center">
                <div class="col-sm-6">
                    <h2 ><b>Omnichannel Sentiment</b></h2>
                    <div class="chart-box2">
                    <div id="chartContainer" ></div></div>
                </div>
                <div class="col-sm-6">
                    <h2 ><b>Sentiment</b></h2>
                    <div id="chartContainer2" class="chart-box"></div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Load Dependencies -->
<script type="text/javascript" src="<?= $SiteURL ?>public/js/jquery.min.js"></script>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

<script>
window.onload = function() {
    // First Pie Chart
   // Fetch date values from input fields
   var startDate = document.getElementById("startdatetime").value;
    var endDate = document.getElementById("enddatetime").value;

    $.ajax({
        url: 'Dashboard/sentiment_function.php', // Path to PHP script
        method: 'POST',
        data: { 
            'action': 'dashboard_pie',
            'startdatetime': startDate,
            'enddatetime': endDate
        }, // Send action parameter
        dataType: 'json',
        success: function (data) {
            console.log("Data received for Chart 1:", data); 
            // Process data and update the chart
            // Convert values to integers (handle null values with '|| 0')
            var facebookTotal = parseInt(data.facebook?.total || "0", 10);
            var twitterTotal = parseInt(data.twitter?.total || "0", 10);
            var emailTotal = parseInt(data.email?.total || "0", 10);
            var whatsappTotal = parseInt(data.whatsapp?.total || "0", 10);
            var messengerTotal = parseInt(data.messenger?.total || "0", 10);
            var chatTotal = parseInt(data.chat?.total || "0", 10);
            var recordingTotal = parseInt(data.recording?.total || "0", 10); 
            var instagramTotal = parseInt(data.instagram?.total || "0", 10);

        // Step 1: Calculate total sum of all categories
        var totalRecords = facebookTotal + twitterTotal + emailTotal + whatsappTotal +
                           messengerTotal + chatTotal + recordingTotal + instagramTotal;

        // Prevent division by zero
        if (totalRecords === 0) {
            totalRecords = 1; // Avoid NaN issues
        }

        // Ensure totalRecords is not zero to avoid division by zero
        if (totalRecords > 0) {
            var ftotal = (facebookTotal / totalRecords * 100).toFixed(2);
            var ttotal = (twitterTotal / totalRecords * 100).toFixed(2);
            var etotal = (emailTotal / totalRecords * 100).toFixed(2);
            var wtotal = (whatsappTotal / totalRecords * 100).toFixed(2);
            var mtotal = (messengerTotal / totalRecords * 100).toFixed(2);
            var ctotal = (chatTotal / totalRecords * 100).toFixed(2);
            var rtotal = (recordingTotal / totalRecords * 100).toFixed(2);
            var itotal = (instagramTotal / totalRecords * 100).toFixed(2);
        } else {
            var ftotal = 0, ttotal = 0, etotal = 0, wtotal = 0, mtotal = 0, ctotal = 0, rtotal = 0, itotal = 0;
        }

        // Step 2: Prepare chart data with already formatted values
        var chartData = [
            { y: parseFloat(ftotal), label: "Facebook", color: "#f26674" },    
            { y: parseFloat(ttotal), label: "Twitter", color: "#f7f48d" },     
            { y: parseFloat(etotal), label: "Email", color: "#6cb9ff" },       
            { y: parseFloat(wtotal), label: "WhatsApp", color: "#99da7b" },    
            { y: parseFloat(mtotal), label: "Messenger", color: "#535cfc" },   
            { y: parseFloat(ctotal), label: "Chats", color: "#fca4f2" },   
            { y: parseFloat(rtotal), label: "Recording", color: "#a587ff" },        
            { y: parseFloat(itotal), label: "Instagram", color: "#ffc489" }     
        ];

            // Initialize and render the Pie Chart
            var chart1 = new CanvasJS.Chart("chartContainer", {
                backgroundColor: "transparent",
                theme: "light2",
                indexLabel: "{label} - {y}%", // CanvasJS will now correctly use {y}
               
                animationEnabled: true,
                data: [{
                    type: "pie",
                    startAngle: 25,
                    
                    indexLabelFontSize: 16,
                    indexLabel: "{label} - {y}%", // CanvasJS will now correctly use {y}
                    toolTipContent: "<b>{label}:</b> {y}%",
                    dataPoints: chartData
                }]
            });

            chart1.render();
        },
        error: function (xhr, status, error) {
            console.error("Error fetching sentiment data:", error);
        }
    });


$.ajax({
    url: 'Dashboard/sentiment_function.php', // Path to PHP script
    method: 'POST',
    data: {'action': 'dashboard',
            'startdatetime': startDate,
            'enddatetime': endDate
        },
    dataType: 'json',
    success: function(data) {
        // Convert values to integers
        var totalPositive = (parseInt(data.recording.positive ?? 0, 10)) + 
                            (parseInt(data.email.positive ?? 0, 10)) + 
                            (parseInt(data.facebook.positive ?? 0, 10)) + 
                            (parseInt(data.twitter.positive ?? 0, 10)) + 
                            (parseInt(data.whatsapp.positive ?? 0, 10)) + 
                            (parseInt(data.messenger.positive ?? 0, 10)) + 
                            (parseInt(data.chat.positive ?? 0, 10)) + 
                            (parseInt(data.instagram.positive ?? 0, 10));

        var totalNegative = (parseInt(data.recording.negative ?? 0, 10)) + 
                            (parseInt(data.email.negative ?? 0, 10)) + 
                            (parseInt(data.facebook.negative ?? 0, 10)) + 
                            (parseInt(data.twitter.negative ?? 0, 10)) + 
                            (parseInt(data.whatsapp.negative ?? 0, 10)) + 
                            (parseInt(data.messenger.negative ?? 0, 10)) + 
                            (parseInt(data.chat.negative ?? 0, 10)) + 
                            (parseInt(data.instagram.negative ?? 0, 10));

        var totalNeutral =  (parseInt(data.recording.neutral ?? 0, 10)) + 
                            (parseInt(data.email.neutral ?? 0, 10)) + 
                            (parseInt(data.facebook.neutral ?? 0, 10)) + 
                            (parseInt(data.twitter.neutral ?? 0, 10)) + 
                            (parseInt(data.whatsapp.neutral ?? 0, 10)) + 
                            (parseInt(data.messenger.neutral ?? 0, 10)) + 
                            (parseInt(data.chat.neutral ?? 0, 10)) + 
                            (parseInt(data.instagram.neutral ?? 0, 10));

        // Calculate total sentiment count
        var totalSentiments = totalPositive + totalNegative + totalNeutral;

        // Prevent division by zero
        if (totalSentiments === 0) {
            totalSentiments = 1; // Avoid NaN issues
        }

        // Calculate percentages relative to 100%
        var positivePercentage = ((totalPositive / totalSentiments) * 100).toFixed(2);
        var negativePercentage = ((totalNegative / totalSentiments) * 100).toFixed(2);
        var neutralPercentage = ((totalNeutral / totalSentiments) * 100).toFixed(2);

        // Invisible portion to make the total sum to 200%
        var invisiblePercentage = 100; 

        // Creating the chart
        var chart2 = new CanvasJS.Chart("chartContainer2", {
            backgroundColor: "transparent",
            theme: "light2",
            animationEnabled: true,
            data: [{
                type: "doughnut",
                startAngle: 180,
                innerRadius: "80%",
                indexLabelFontColor: "black",
                indexLabelFontSize: 15,
                indexLabel: "{label} - {y}%", // CanvasJS will now correctly use {y}
                toolTipContent: "<b>{label}:</b> {y}%",
                dataPoints: [
                    { y: parseFloat(positivePercentage), label: "Positive", color: "#63d661" },
                    { y: parseFloat(neutralPercentage), label: "Neutral", color: "#ffe548" },
                    { y: parseFloat(negativePercentage), label: "Negative", color: "#f94e4f" },
                    { y: invisiblePercentage, label: "", color: "transparent", indexLabel: "", indexLabelFontColor: "white", toolTipContent: null, showInLegend: false }
                ]
            }]
        });

        chart2.render();
    },
    error: function(xhr, status, error) {
        console.error("Error fetching sentiment data:", error);
    }
});




};
</script>

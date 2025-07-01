
<?php 

/**
 * Auth: Vastvikta Nishad
 * Date: 17 May 2024
 */
include("../../config/web_mysqlconnect.php");
include("erlang_function.php");
$cal = new ErlangC();
$Erlang_Data = '';
if(!empty($_POST['from_time'])){
  $Erlang_Data = $cal->calculate();
}
// echo "<pre>";print_r($Erlang_Data);
?>
<link href="WFM/css/erlang_style.css" rel="stylesheet" type="text/css"/>
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Erlang Calculator</span>
<form>
	<div class="row">
	  	<div class="col-25">
	    	<label for="break_name"><strong>Calculation Type</strong></label>
	  	</div>
	  	<div class="col-75">
     	  <input type="radio" class="type_calculator" name="fav_calculator" checked value="1">
		  <label for="html">Manual</label>
		  <input type="radio" class="type_calculator" name="fav_calculator" value="2">
		  <label for="css">Dynamic</label><br>
	  	</div>
	</div><br/>
	<!-- <p>Calculate the number of staff required to reach an agreed service level</p><br/> -->
  <div class="Manual_div">
    	  <div class="erlang-form-caption">
            <!-- Calculate the number of staff required to reach an agreed service level -->
        </div>
    		<div class="row row_2 row1">
    	      <div class="col-25">
    	        <label for="break_type"><strong>Incoming Call</strong></label>
    	      </div>
    	      	<div class="col-75">
    	      		<div class="erlang-form-box">
    	  				<input name="timePeriodInSeconds" class="form-control input" id="timePeriodInSeconds" 
    	  				type="text" placeholder="Number Of Calls" value="400">
    	  			</div>
    	  			<!-- <div class="erlang-form-right">seconds</div> -->
    	      </div>
    	    </div>
    	    <div class="row row_2">
    		  <div class="col-25">
    		    <label for="break_name"><strong>in a period of</strong></label>
    		  </div>
    		  	<div class="col-75">
    	     	 <div class="erlang-form-box">
    	     	 	<input type="number" class="form-control input" id="totalNumberOfCalls" placeholder="Total number of calls in a time period" value="360">
                   <!-- <select name="timePeriodInSeconds" id="timePeriodInSeconds" onchange="MultipleDaysCheck(this)">
                        <option value="43200">1 Month</option>
                        <option value="10080">1 Week</option>
                        <option value="1440">24 Hours</option>
                        <option value="720">12 Hours</option>
                        <option value="600">10 Hours</option>
                        <option value="540">9 Hours</option>
                        <option value="480">8 Hours</option>
                        <option value="60">1 Hour</option>
                        <option value="360" selected="">30 Minutes</option>
                        <option value="15">15 Minutes</option>
                    </select> -->
                </div>
    		  	</div>
    		</div>
    		<div class="row row_2 row1 ">
    	      <div class="col-25">
    	        <label for="break_type"><strong>Average Handling Time (AHT)</strong></label>
    	      </div>
    	      	<div class="col-75">
    	  			<div class="erlang-form-box">
                    	<input name="averageCallDuration" id="averageCallDuration" type="text" title="Average Handling Time (seconds)" value="240">
                	</div>
                	<div class="erlang-form-right">seconds</div>
    	      </div>
    	    </div>
    	    <div class="row row_2">
    		  <div class="col-25">
    		    <label for="break_name"><strong>Number Of Agents</strong></label>
    		    <!-- <div class="erlang-form-row-label"><strong>Required Service Level </strong></div> -->
    		  </div>
    		  	<div class="col-75">
    	     	  <!-- <input type="text" id="break_name" name="break_name" value="80" required> -->
    	     	  <div class="erlang-form-box">
                    <input name="numberOfAgents" id="numberOfAgents" type="text" title="Number of agents" value="55">
                   </div>
    	     	   <!-- <div class="erlang-form-right">% Answered in</div> -->
    		  	</div>
    		</div>
    		<div class="row row_2 row1">
    	      <div class="col-25">
    	        <label for="break_type"><strong>Target Answer Time</strong></label>
    	      </div>
    	      	<div class="col-75">
    	  			<div class="erlang-form-box">
                    	<input name="targetAnswerTime" id="targetAnswerTime" type="text" title="Service Level Time (Seconds)" value="15">
                   </div>
    	     	   <div class="erlang-form-right">second</div>
    	      </div>
    	  </div>
	</div>
	<div class="Dynamic_div" style="display: none">
  		<div class="row row_2 row1">
  	      <div class="col-25">
  	        <label for="break_type"><strong>Report Date Time</strong></label>
  	      </div>
  	  </div>
     	<div class="form-group col-75">
     		<div class='col-50 input-group date_range'>
               <!-- <input type='text' class="form-control" placeholder="From" /> -->
                <input type="text" name="from_time" class="dob1" value="" id="from_time" autocomplete="off" placeholder="From" required>	
               <span class="input-group-addon">
               <span class="glyphicon glyphicon-calendar"></span>
               </span>
            </div>
            <div class='col-50 input-group date_range'>
               <!-- <input type='text' class="form-control" placeholder="to" /> -->
                <input type="text" name="to_time" class="dob1" value="" id="to_time" autocomplete="off" placeholder="To" required>
               <span class="input-group-addon">
               <span class="glyphicon glyphicon-calendar"></span>
               </span>
            </div>
     	</div>
	</div><br/>
	<div class="submit_form">
	<button class="submit_button submit_cal_button" type="button">Calculate</button></div>
</form>
<div class="table-container table_list" <?php if(empty($Erlang_Data)){?>style="display: none"<?php }?>>
    <table style="width:500px;" class="cch-table blue small text-center table table-striped table-bordered table-hover">
            <tbody><tr>
                <th colspan="2">Average Values Entered</th>
            </tr>
            <tr>
                <td>Average arrival rate (calls / sec)</td>
                <td class="average_rate"></td>
            </tr>
            <tr>
                <td>Agent occupancy</td>
                <td class="occupancy"></td>
            </tr>
            <tr>
                <td>Service level</td>
                <td class="Service_level"></td>
            </tr>
            <tr>
                <td>Average Target Answer Time (Seconds)</td>
                <td class="Answer_time"></td>
            </tr>
            <tr>
                <td>Probability of waiting (%)</td>
                <td class="waiting"></td>
            </tr>
            <tr>
                <th colspan="2">Above figures include calls and other work tasks</th>
            </tr>
    </tbody></table>
</div>
<div class="erlang_output" style="display: none;">
	<svg class="erlang-results" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 580 200" enable-background="new 0 0 450 200" xml:space="preserve">
        <style type="text/css">
            .st0{fill:#0074d9;}
            .st1{fill:none;}
            .st5{fill:#46474D;}
            .st6{fill:#DE1B53;}
            .circle_background {fill: none;  stroke: #0074d9; stroke-width: 5; }
            .pie    {fill: #9DD4F1;stroke: #9DD4F1; stroke-width: 2; }
        </style>
        <g id="agents">
            <path class="st0" d="M37.8,64.9l-9.6,0.8c-1.8,0.2-3.1,1.7-2.9,3.5l3.4,38.6c0.2,1.8,1.7,3.1,3.5,2.9l9.6-0.8
                c1.8-0.2,3.1-1.7,2.9-3.5l-3.4-38.6C41.1,66,39.5,64.7,37.8,64.9z"></path>
            <path class="st0" d="M118.5,110.7c1.8,0.2,3.3-1.2,3.5-2.9l3.4-38.6c0.2-1.8-1.2-3.3-2.9-3.5l-9.6-0.8c-1.8-0.2-3.3,1.2-3.5,2.9
                l-3.4,38.6c-0.2,1.8,1.2,3.3,2.9,3.5L118.5,110.7z"></path>
            <path class="st0" d="M134.9,74.2c0.1-15.3-5.9-29.8-17.1-40.8c-1.8-1.7-3.6-3.3-5.6-4.8c0.6-3.2-0.8-6.6-3.8-8.3
                c-10-6-21.5-9.2-33.2-9.2c-11.7,0-23.2,3.2-33.2,9.2c-3,1.8-4.4,5.3-3.7,8.5c-13.5,10.7-22.3,27-22.7,45.4
                c-3.3,5-5.1,13.1-4.6,18.9c0.8,8.9,7.2,15.6,14.3,14.9l-3.5-38.5c1.7-14.3,9.2-27,20.1-35.5c2.5,1.5,5.7,1.7,8.4,0.1
                c7.5-4.5,16.1-6.9,24.9-6.9s17.4,2.4,24.9,6.9c1.3,0.8,2.7,1.2,4.1,1.2c1.5,0,3-0.5,4.2-1.2c1.6,1.2,3.1,2.5,4.5,3.9
                c8.8,8.7,14.5,19.8,15.7,31.6l-3.3,38.5c-3.2,16-28.4,20.6-42.3,21.9c-1.8-1.6-4.6-2.7-7.9-2.7c-5.3,0-9.7,2.9-9.7,6.5
                s4.3,6.5,9.7,6.5c3.9,0,7.2-1.5,8.8-3.8c16.2-1.5,46.7-7.5,48.1-30.1c3.9-2.5,6.8-7.3,7.3-13.1C139.9,87.3,138.1,79.3,134.9,74.2z"></path>
            <text transform="matrix(1 0 0 1 75 92)" text-anchor="middle" class="erlang-results-value"></text>
             <text text-anchor="middle" transform="matrix(1 0 0 1 75 168)" class="erlang-results-caption">Agents</text>
        </g>
        <g id="serviceLevel">
            <path class="st6" d="M268.2,84.5c0,24.1-19.6,43.6-43.6,43.6c-24.1,0-43.6-19.6-43.6-43.6c0-21.4,15.5-39.2,35.9-42.9v7.4
                l22.8-12.1l-22.8-11.6v8.5c-24.7,3.7-43.6,25-43.6,50.7c0,28.3,23,51.3,51.3,51.3c28.3,0,51.3-23,51.3-51.3L268.2,84.5L268.2,84.5z
                "></path>
            <path class="st6" d="M242.9,45.6l3.2-7l-7.9,5.1C239.8,44.3,241.3,44.9,242.9,45.6z"></path>
            <path class="st6" d="M249.5,49.4l4.4-6.3c-1.6-1.1-3.3-2.2-5.1-3.1l-3.6,6.8C246.6,47.5,248.1,48.4,249.5,49.4z"></path>
            <path class="st6" d="M255.3,54.2l5.4-5.5c-1.4-1.4-2.9-2.7-4.5-3.9l-4.8,6.1C252.8,51.9,254.1,53,255.3,54.2z"></path>
            <path class="st6" d="M264,66.6l7-3.3c-0.9-1.8-1.8-3.5-2.8-5.2l-6.5,4.1C262.5,63.6,263.3,65.1,264,66.6z"></path>
            <path class="st6" d="M266.6,73.7l7.4-2c-0.5-1.9-1.1-3.8-1.9-5.7l-7.1,2.9C265.6,70.5,266.2,72.1,266.6,73.7z"></path>
            <path class="st6" d="M260.2,60.1l6.3-4.5c-1.1-1.6-2.4-3.2-3.7-4.6l-5.7,5.1C258.1,57.4,259.2,58.7,260.2,60.1z"></path>
            <path class="st6" d="M268,81.2l7.7-0.7c-0.2-2-0.5-4-0.9-5.9l-7.5,1.6C267.6,77.8,267.8,79.5,268,81.2z"></path>
            <text text-anchor="middle" transform="matrix(1 0 0 1 225 92)" class="erlang-results-value"></text>
            <text text-anchor="middle" transform="matrix(1 0 0 1 225 168)" class="erlang-results-caption">20 Seconds</text>
        </g>
        <g id="occupancy">
            <path class="pie" d="M 365,83 v-50 a 50,50 0 1,0 42.22,23.21 z"></path>
               <circle class="circle_background" r="50" cx="365" cy="83"></circle>
            <text text-anchor="middle" transform="matrix(1 0 0 1 366 168)" class="erlang-results-caption">Occupancy</text>
            <text text-anchor="middle" transform="matrix(1 0 0 1 366 92)" class="erlang-results-value"></text>
        </g>
        <g id="contacts">
            <path class="st5" d="M454.8,45c-9.3,12.9-5.6,36,10.1,57.4c15.7,21.4,36.7,31.8,51.8,26.8c1.1-0.4,2.1-0.8,3.1-1.3l2.6-2.3l4.9-4.6
                l-16.7-19l-9,8.2c-6.4-2.8-14.5-8.9-21.7-18.7c-7.2-9.8-10.7-19.4-11.4-26.3l10.5-6.1l-13.2-21.6l-5.8,3.3l-3,1.8
                M454.8,45z"></path>
            <text transform="matrix(1 0 0 1 490 92)" class="erlang-results-value"></text>
            <text text-anchor="middle" transform="matrix(1 0 0 1 485 168)" class="erlang-results-caption">Calls</text>
        </g>
    </svg>
</div>
<div class="erlang_output1" style="display: none;">
	<div class="col-sm-6">
        <label>Calculated Values</label>
        <div class="form-group">
          <label for="averageArrivalRate">Average arrival rate (calls / sec)</label>
          <input type="text" class="form-control" id="averageArrivalRate"
          placeholder="Average arrival rate (calls per second)" readonly />
        </div>
        <div class="form-group">
          <label for="trafficIntensity">Traffic intensity</label>
          <input type="text" class="form-control" id="trafficIntensity"
          placeholder="Traffic intensity" readonly />
        </div>
        <div class="form-group">
          <label for="agentOccupancy">Agent occupancy (utilitization, %)</label>
          <input type="text" class="form-control" id="agentOccupancy"
          placeholder="Agent occupancy" readonly />
        </div>
        <div class="form-group">
          <label for="probabilityOfWaiting">Probability of waiting (%)</label>
          <input type="text" class="form-control" id="probabilityOfWaiting"
          placeholder="Probability of waiting" readonly />
        </div>
        <div class="form-group">
          <label for="averageSpeedOfAnswer">Average speed of answer (response time, s)</label>
          <input type="text" class="form-control" id="averageSpeedOfAnswer"
          placeholder="Average speed of answer (response time)" readonly />
        </div>
        <div class="form-group">
          <label for="serviceLevel">Service level (%)</label>
          <input type="text" class="form-control" id="serviceLevel"
          placeholder="Service level (requires a target answer time)" readonly />
        </div>
    </div>
</div>
<script type="text/javascript" src="WFM/js/wfm_script.js"></script>
<script type="text/javascript">
  $('#from_time').datepicker();
  $('#to_time').datepicker();
</script>
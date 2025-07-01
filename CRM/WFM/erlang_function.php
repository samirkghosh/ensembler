<?php

include("../../config/web_mysqlconnect.php");
class ErlangC
{   
    // average waiting time fixed
    public $average_waiting = 2;

    public $forcast_agent = 0; 

    // There were 360 calls in a half hour.
    public $totalNumberOfCalls = 22;

    // The total time duration in seconds.
    // 1800 seconds = 30 minutes.
    public $totalTimeDuration = 3600;

    // In this example, 360 calls / half hour =
    // 0.2 calls / second
    private $averageArrivalRate = 0;

    // Average call duration in seconds
    public $averageCallDuration = 240;

    // 55 agents
    public $numberOfAgents = 2;

    // Traffic intensity = volume of calls
    // volume of calls = average arrival rate * average call duration
    private $volumeOfCalls = 0;

    private $agentOccupancy = 0;

    // Ec(m,u) = probability that a call is not answered immediately and has to
    // wait. This will be a decimal between 0 and 1.
    // Numerator in this formula is:
    // (traffic intensity ^ number of agents / number of agents factorial)
    // Sigma notation means for every number k from 0 to (number of agents - 1),
    // add (traffic intensity ^ k) / (k factorial)
    private $probabilityOfWaiting = 0;

    // Average speed of answer (wait time, response time)
    // Tw = (probability of waiting * average call duration) /
    // (number of agents * (1 - agent occupancy))
    private $averageSpeedOfAnswer = 0;

    private $serviceLevel = 0;

    // Needed for calculating service level.
    // Target speed of answer in seconds.
    private $targetAnswerTime = 15;

    /**
    * Specifying an array of calls where each element value is the duration
    * of the call in minutes, calculates the average call duration.
    * @param array $calls array of integers.
    * @return void
    */
    

    /**
    * Uses recursion to calculate the factorial of a given integer.
    * @param int $number A number.
    * @return int
    */
    public function calculate_getdata(){ 
        global $link,$db;
        /*-----offered calls - number of call-----------*/
        if(!empty($_POST['from_time']) && !empty($_POST['to_time'])){
            $start_date = $_POST['from_time'];
            $end_date = $_POST['to_time'];
        }else{
            $start_date = '2023-04-21 00:00:00';
            $end_date = '2023-04-22 00:00:00';
        }
        $query = "SELECT users.v_department,log.campaign_id,users.full_name,users.user,TIMESTAMPDIFF(SECOND,d_createdOn,NOW()) as TermCheck from asterisk.autodial_users as users inner join asterisk.recording_log as log on users.user=log.extension where log.start_time between '" . $start_date . "' and '" . $end_date . "' $campCond group by user";
        
        $query = mysqli_query($link, $query);   
        $count = 1;    
        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $cond_usr = "  and user='" . $row['user'] . "'";
                $stmt = "SELECT * FROM ( select * from asterisk.autodial_closer_log where user!='' and (call_date >= '$start_date' and call_date <= '$end_date' $cond_usr) order by call_date desc ) AS sub group by v_SessionId";
                $q1 = mysqli_query($link, $stmt);
                $NumberOfCalls = mysqli_num_rows($q1);
                // $numberOfAgentsCount = $numberOfAgentsCount + $count;
                $count++;

                $query03 = "select sum(talk_sec) as totalTalkTime from asterisk.autodial_agent_log where event_time between '" . $start_date . "' and '" . $end_date . "' $campCond  and user='" . $row['user'] . "'";
                $query03Data = mysqli_query($link, $query03) or die(mysqli_error($link) . "<21111111111");
                $query03Row = mysqli_fetch_assoc($query03Data);

                $query06 = "select sum(dispo_sec) as totalWrapUpTime from asterisk.autodial_agent_log where event_time between '" . $start_date . "' and '" . $end_date . "' $campCond and user='" . $row['user'] . "'";
                $query06Data = mysqli_query($link, $query06) or die(mysqli_error($link) . "<5");
                $query06Row = mysqli_fetch_assoc($query06Data);
                

                $query08 = "select sum(hold_sec) as totalHoldTime from asterisk.autodial_agent_log where event_time between '" . $start_date . "' and '" . $end_date . "' $campCond and user='" . $row['user'] . "'";
                $query08Data = mysqli_query($link, $query08) or die(mysqli_error($link) . "<6");
                $query08Row = mysqli_fetch_assoc($query08Data);
                

                $query021 = "select count(*) as totalADRAttempted from asterisk.autodial_closer_log where call_date between '" . $start_date . "' and '" . $end_date . "' and user='" . $row['user'] . "' and status in ('DONE','INCALL')";
                $query02Data1 = mysqli_query($link, $query021) or die(mysqli_error($link) . "<11");
                $query02Row1 = mysqli_fetch_assoc($query02Data1);
                

                $totalAHTTime = (($query03Row['totalTalkTime'] + $query06Row['totalWrapUpTime'] + $query08Row['totalHoldTime']) / $query02Row1['totalADRAttempted']);
            }
            $this->totalNumberOfCalls = $NumberOfCalls;
            $this->averageCallDuration = $totalAHTTime;
            $this->numberOfAgents = $NumberOfCalls;
        }

    }

    public function calculate()
    {   
        global $link,$db;
        $this->calculate_getdata();

        // 1. Average arrival rate
        $this->averageArrivalRate = $this->totalTimeDuration > 0 ? ($this->totalTimeDuration / $this->totalNumberOfCalls) : 0;

        
        // 4. Traffic intensity (volume of calls)
        $this->volumeOfCalls =  $this->averageCallDuration * $this->averageArrivalRate;

        
        // 5. Agent occupancy (utlitization)
        $this->agentOccupancy = $this->numberOfAgents > 0 ? ($this->volumeOfCalls / $this->numberOfAgents) : 0;
        
        // 7. Probability of waiting
        $numerator = pow($this->volumeOfCalls, $this->numberOfAgents) /
        self::factorial($this->numberOfAgents);

        $denominator =
        $numerator + ((1 - $this->agentOccupancy) * self::erlangSigma());

        $this->probabilityOfWaiting = $this->numberOfAgents > 0 ? ($numerator / $denominator) : 0;

        // 8. Average speed of answer (response time)
        $this->averageSpeedOfAnswer =
        $this->numberOfAgents > 0 && $this->agentOccupancy !== 1 ? ($this->probabilityOfWaiting * $this->averageCallDuration ) /
        ($this->numberOfAgents * (1 - $this->agentOccupancy)) :
        0;

        // 9. Service level
        // Calculate the exponent of e first:
        $exponent = $this->averageCallDuration > 0 ?
        -($this->numberOfAgents - $this->volumeOfCalls) *
        ($this->targetAnswerTime / $this->averageCallDuration) :
        0;
        $this->serviceLevel = 1 - ($this->probabilityOfWaiting) * exp($exponent);

        $Erlang_Data = array();
        $Erlang_Data['averageArrivalRate'] = $this->averageArrivalRate;
        $Erlang_Data['agentOccupancy'] = $this->agentOccupancy;
        $Erlang_Data['serviceLevel'] = $this->serviceLevel;
        $Erlang_Data['averageSpeedOfAnswer'] = $this->averageSpeedOfAnswer;
        $Erlang_Data['probabilityOfWaiting'] = $this->probabilityOfWaiting;
        return $Erlang_Data;
    }
    public function forcast_calculate(){   
        global $link,$db;
        /*-----offered calls - number of call-----------*/
        if(!empty($_POST['sttartdatetime']) && !empty($_POST['from_time'])){
            $sttartdate = $_POST['sttartdatetime'];
            $sttartdatetime = date('Y-m-d', strtotime("$sttartdate"));
            $time_s = $_POST['from_time'];
            $time_e = $_POST['to_time'];
            $start_date = date('Y-m-d H:i:s', strtotime("$sttartdatetime $time_s]"));
            $end_date = date('Y-m-d H:i:s', strtotime("$sttartdatetime $time_e"));
        }else{
            $start_date = '2023-04-21 10:00:00';
            $end_date = '2023-04-21 12:00:00';
        }
        
        $startTime = date('H:i:s',strtotime($time_s));
        $endTime = date('H:i:s',strtotime($time_e));
        $time_start_1 = '';
        $date =  date('Y-m-d',strtotime($sttartdate));
        $date_end =  date('Y-m-d',strtotime($sttartdate));
        $start_Time = strtotime($startTime);
        $end_Time   = strtotime($endTime);

        $arrInterval = [];
        $arrInterval_1 = [];
        // print_r($end_Time);

        while($end_Time >= $start_Time){
            // echo "<br/>";print_r($end_Time);echo "<br/>";
              // echo "<br/>";print_r($start_Time);echo "<br/>";
            if($end_Time == $start_Time){
                break;
            }
            
              $from_times  = date("H:i:s", $start_Time);
              $arrInterval['start'][] = date('Y-m-d H:i:s', strtotime("$date $from_times"));
              // echo "<br/>";print_r(date("H:i:s", $start_Time));

              $start_Time = strtotime('+60 minutes', $start_Time);
              $to_times  = date("H:i:s", $start_Time);
              $arrInterval_1['end'][] = date('Y-m-d H:i:s', strtotime("$date_end $to_times"));           
        }
        $i=0;
        $target_waiting = 0;
        $return_array = array(); 
        foreach ($arrInterval['start'] as $key => $date_time_value) {
           $start_from_time = $date_time_value;
           $end_to_time = $arrInterval_1['end'][$key];

            $query = "SELECT log.start_time,users.v_department,log.campaign_id,users.full_name,users.user,TIMESTAMPDIFF(SECOND,d_createdOn,NOW()) as TermCheck from asterisk.autodial_users as users inner join asterisk.recording_log as log on users.user=log.extension where log.start_time between '" . $start_from_time . "' and '" . $end_to_time . "' group by user limit 1"; 

            $query = mysqli_query($link, $query);       
            $count = 1;
            $agnet = 0;          
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    $cond_usr = "  and user='" . $row['user'] . "'";
                    $stmt = "SELECT * FROM ( select * from asterisk.autodial_closer_log where user!='' and (call_date >= '$start_from_time' and call_date <= '$end_to_time') order by call_date desc ) AS sub group by v_SessionId";
                    $q1 = mysqli_query($link, $stmt);                
                    $NumberOfCalls = mysqli_num_rows($q1);
                    // echo "<br/><br/>"; echo "NumberOfCalls : "; print_r($NumberOfCalls);
                    $numberOfAgentsCount = $numberOfAgentsCount + $count;
                    $count++;

                    $query03 = "select sum(talk_sec) as totalTalkTime from asterisk.autodial_agent_log where event_time between '" . $start_from_time . "' and '" . $end_to_time . "' $campCond  and user='" . $row['user'] . "'";
                    $query03Data = mysqli_query($link, $query03) or die(mysqli_error($link) . "<21111111111");
                    $query03Row = mysqli_fetch_assoc($query03Data);

                    $query06 = "select sum(dispo_sec) as totalWrapUpTime from asterisk.autodial_agent_log where event_time between '" . $start_from_time . "' and '" . $end_to_time . "' $campCond and user='" . $row['user'] . "'";
                    $query06Data = mysqli_query($link, $query06) or die(mysqli_error($link) . "<5");
                    $query06Row = mysqli_fetch_assoc($query06Data);
                    

                    $query08 = "select sum(hold_sec) as totalHoldTime from asterisk.autodial_agent_log where event_time between '" . $start_from_time . "' and '" . $end_to_time . "' $campCond and user='" . $row['user'] . "'";
                    $query08Data = mysqli_query($link, $query08) or die(mysqli_error($link) . "<6");
                    $query08Row = mysqli_fetch_assoc($query08Data);
                    

                    $query021 = "select count(*) as totalADRAttempted from asterisk.autodial_closer_log where call_date between '" . $start_from_time . "' and '" . $end_to_time . "' and user='" . $row['user'] . "' and status in ('DONE','INCALL')";
                    $query02Data1 = mysqli_query($link, $query021) or die(mysqli_error($link) . "<11");
                    $query02Row1 = mysqli_fetch_assoc($query02Data1);
                    
                    $totalAHTTime = round(($query03Row['totalTalkTime'] + $query06Row['totalWrapUpTime'] + $query08Row['totalHoldTime']) / $query02Row1['totalADRAttempted']);

                    // echo "<br/>"; echo "AHTTime : "; print_r($totalAHTTime);

                    $this->totalNumberOfCalls = $NumberOfCalls;
                    $this->averageCallDuration = $totalAHTTime;

                    $this->numberOfAgents = 1;

                    // 1. Average arrival rate
                    $this->averageArrivalRate = $this->totalTimeDuration > 0 ? ($this->totalNumberOfCalls / $this->totalTimeDuration) : 0;
                    // echo "<br/>"; echo "averageArrivalRate : "; print_r($this->averageArrivalRate);

                    // 4. Traffic intensity (volume of calls)
                    $this->volumeOfCalls = $this->averageArrivalRate * $this->averageCallDuration;
                    // echo "<br/>"; echo "volumeOfCalls : "; print_r($this->volumeOfCalls);

                    // 5. Agent occupancy (utlitization)
                    $this->agentOccupancy = $this->numberOfAgents > 0 ? ($this->volumeOfCalls / $this->numberOfAgents) : 0;
                    // echo "<br/>"; echo "agentOccupancy : "; print_r($this->agentOccupancy);

                    // 7. Probability of waiting
                    $numerator = pow($this->volumeOfCalls, $this->numberOfAgents) / self::factorial($this->numberOfAgents);
                    $denominator = $numerator + ((1 - $this->agentOccupancy) * self::erlangSigma());
                    $this->probabilityOfWaiting = $this->numberOfAgents > 0 ? ($numerator / $denominator) : 0;

                    $probabilityOfWaiting = $this->probabilityOfWaiting;
                    // echo "<br/>"; echo "probabilityOfWaiting : "; print_r($probabilityOfWaiting); 

                    $numberOfAgents = '1'; 
                     // echo "<br/>"; echo "numberOfAgents : "; print_r($this->numberOfAgents);

                    if($this->probabilityOfWaiting>0){
                        for($k=1; $k<=$numberOfAgents; $k++){                   
                            if($this->probabilityOfWaiting <= $target_waiting){
                                // echo "<br/>";echo "<br/>"; echo "numberOfAgents - New : "; print_r($numberOfAgents);
                                // echo "<br/>"; echo "probabilityOfWaiting - New : "; print_r($this->probabilityOfWaiting);
                                break;
                            }else{
                                $numberOfAgents++;

                                 // 5. Agent occupancy (utlitization)
                                $this->agentOccupancy = $numberOfAgents > 0 ? ($this->volumeOfCalls / $numberOfAgents) : 0;
                                // echo "<br/>"; echo "<br/>"; echo "agentOccupancy New : "; print_r($this->agentOccupancy);

                                // 7. Probability of waiting
                                $numerator = pow($this->volumeOfCalls, $numberOfAgents) / self::factorial($numberOfAgents);
                                $denominator = $numerator + ((1 - $this->agentOccupancy) * self::erlangSigmaNew($numberOfAgents));
                                $this->probabilityOfWaiting = round($numberOfAgents > 0 ? ($numerator / $denominator) : 0);
                                
                                // echo "<br/>"; echo "numberOfAgents - New : "; print_r($numberOfAgents);
                                // echo "<br/>"; echo "probabilityOfWaiting - New : "; print_r($this->probabilityOfWaiting);
                                
                                
                            }
                        }
                    }else{

                    }
                    
                                   
                    $return_array[$i]['interval'] =  date("H:i:s", strtotime($start_from_time))." - ".date("H:i:s", strtotime($end_to_time)); 
                    $return_array[$i]['call_offered'] =  $this->totalNumberOfCalls;
                    $probabilityOfWaiting = $this->probabilityOfWaiting;
                    // echo "<br/>"; echo "probabilityOfWaiting : "; print_r($probabilityOfWaiting);     
                    $return_array[$i]['Forecast_value'] =  $numberOfAgents;                  
                } 
                $i++;               
            }
        }
        return $return_array;
    }
    private function erlangSigmaNew($numberOfAgents){
        $output = 0;
        // echo "<br/>"; echo "erlangSigma numberOfAgents : "; echo $numberOfAgents;
        for ($k = 0; $k < $numberOfAgents; $k++) {
            $output += (pow($this->volumeOfCalls, $k) / self::factorial($k));
        }
        return $output;
    }
    private function erlangSigma(){
        $output = 0;
        for ($k = 0; $k < $this->numberOfAgents; $k++) {
            $output += (pow($this->volumeOfCalls, $k) / self::factorial($k));
        }
        return $output;
    }
    // get seconds in time format start
    function getTimeInFormated($seconds)
    {
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        $secs = floor($seconds % 60);
        return $timeFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
    }

    public function probabilityOfWaiting()
    {
        self::calculate();
        return $this->probabilityOfWaiting;
    }

    public function averageSpeedOfAnswer()
    {
        self::calculate();
        return $this->averageSpeedOfAnswer;
    }

    public function waitTime()
    {
        return self::getAverageSpeedOfAnswer();
    }

    public function serviceLevel($targetAnswerTime = 0)
    {
        $this->targetAnswerTime = $targetAnswerTime;
        self::calculate();
        return $this->serviceLevel;
    }

    public function averageArrivalRate()
    {
        self::calculate();
        return $this->averageArrivalRate;
    }

    public function volumeOfCalls()
    {
        self::calculate();
        return $this->volumeOfCalls;
    }

    public function trafficIntensity()
    {
        return self::volumeOfCalls();
    }

    public function agentOccupancy()
    {
        self::calculate();
        return $this->agentOccupancy;
    }

    public function targetAnswerTime()
    {
        self::calculate();
        return $this->targetAnswerTime;
    }

    private function factorial($number = 0){
        if ($number === 0) {
            return 1;
        }
        return $number * self::factorial($number - 1);
    }
    public function specifyCallDurationInMinutes(array $calls){

        $this->totalNumberOfCalls = count($calls);
        $timeInMinutes = array_sum($calls);

        if ($this->totalNumberOfCalls === 0 || $timeInMinutes === 0) {
            $this->averageCallDuration = 0;
        }

        // change minutes to seconds
        $this->totalTimeDuration = $timeInMinutes * 60;

        // calculate the average call duration in seconds by dividing
        // the total call durationg by the number of calls
        $this->averageCallDuration = ($this->totalTimeDuration / $this->totalNumberOfCalls );

    }
}
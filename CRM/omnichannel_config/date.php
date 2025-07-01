<?php
/****************************************************************************************************
Last Modification Made By :: RG :: Incorrect date was showing for previous month and enddate for last week
Date of Modification :: 09-02-2008
*****************************************************************************************************/

error_reporting(0);

$yesterday = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
$lastyear  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")-1);
$thisyear  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
$Nextyear  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")+1);//Next financial year.
$ritumonth=date("m",$thisyear);// gives the value of this month.Use this to check several conditions.
$m=date('m');
$yest=date("Y-m-d", $yesterday);

$lyear=date("Y", $lastyear);
$tyear=date("Y", $thisyear);
$nextyear=date("Y", $Nextyear);

if($m<4)
{
	$year=$lyear;
	$nextyear=$tyear;
			
}
else if($m>=4)
{
	$year=$tyear;
	$nextyear=$nextyear;

}

$thisfinancialyearstartdate=$year.'-'.'04'.'-'.'01';
//print $thisfinancialyearstartdate."this financial year start date"."<br>";
$thisfinancialyearenddate=$nextyear.'-'.'03'.'-'.'31';
//print $thisfinancialyearenddate."this financial year end date"."<br>";



$lastfysd=($year-1).'-'.'04'.'-'.'01'; //last financial year start date.
//print $lastfysd."last finan year start date"."<br>";

$lastfyenddate=$year.'-'.'03'.'-'.'31';//last financial year end date.
//print $lastfyenddate."last finan year end date"."<br>";

$yest=date("Y-m-d", $yesterday);
$todaysdate=date("Y-m-d");
$datetoday=substr($todaysdate,8,2);
$yeartoday=substr($todaysdate,0,4);
$monthtoday=substr($todaysdate,5,2);
if($monthtoday>6)
{
$halfyearstart =  mktime(0, 0, 0, date("m")-6  , date("d"), date("Y"));
$sixmonthbefore=$monthtoday-6;
}
else
{
	if($monthtoday=='01')
	{
		$sixmonthbefore='08';
	}
	else if($monthtoday=='02')
	{
		$sixmonthbefore='09';
	}
	else if($monthtoday=='03')
	{
		$sixmonthbefore='10';
	}
	else if($monthtoday=='04')
	{
		$sixmonthbefore='11';
	}
	else if($monthtoday=='05')
	{
		$sixmonthbefore='12';
	}
	else if($monthtoday=='06')
	{
		$sixmonthbefore='01';
	}
}
$halfyearstart=
$halfmonth=date("m", $halfyearstart);
$halfyearstartdate=$yeartoday."-".$sixmonthbefore.'-'.'01';
$halfyearenddate=$yeartoday.'-'.$monthtoday.'-'.'31';


$testlastmonth=$monthtoday-1;//calculating last month

if($testlastmonth <10)
{
$lastmonth2='0'.$testlastmonth;
}

if($testlastmonth >=10)
{
$lastmonth2=$testlastmonth;
}



/* calculating dates for this month.....    */

$todayMonth = date('m'); //prints todays month 

if($todayMonth='3')
{
$monthtoday=='03';
}
if($todayMonth=='1')
{
$monthtoday='01';
}
if($todayMonth='2')
{
$monthtoday=='02';
}
if($todayMonth='4')
{
$monthtoday=='04';
}
if($todayMonth='5')
{
$monthtoday=='05';
}
if($todayMonth='6')
{
$monthtoday=='06';
}
if($todayMonth='7')
{
$monthtoday=='07';
}
if($todayMonth='8')
{
$monthtoday=='08';
}

if($todayMonth='9')
{
$monthtoday=='09';
}

$startdatefortodaysmonth=$yeartoday.'-'.$monthtoday.'-'.'01';
$enddatefortodaysmonth=$yeartoday.'-'.$monthtoday.'-'.'31';

$daytoday =date("D", mktime(0, 0, 0, date("m"), date("d"), date("Y")));//prints the current day of 																			the week.
if($daytoday=="Mon")
{
$lastmonth = mktime(0, 0, 0, date("m"), date("d")-7,  date("Y"));
$mdate = date("m d Y", $lastmonth);
$mdate_in_mysql_format = implode('-',array_reverse(explode (' ',$mdate)));
$prevyear=substr($mdate_in_mysql_format,0,4);
$prevday=substr($mdate_in_mysql_format,5,2);
$prevmonth=substr($mdate_in_mysql_format,8,2);
$prevweekstartdate=$prevyear.'-'.$prevmonth.'-'.$prevday; $prevweekstartdate."previous week start date in yy/mm/dd format"."<br>";
$prevweekenddate				=	date("Y-m-d", strtotime($prevweekstartdate . "+6 day"));

}

if($daytoday=="Tue")
{
$lastmonth = mktime(0, 0, 0, date("m"), date("d")-8,  date("Y"));
$mdate = date("m d Y", $lastmonth);
$mdate_in_mysql_format = implode('-',array_reverse(explode (' ',$mdate)));
$prevyear=substr($mdate_in_mysql_format,0,4);
$prevday=substr($mdate_in_mysql_format,5,2);
$prevmonth=substr($mdate_in_mysql_format,8,2);
$prevweekstartdate=$prevyear.'-'.$prevmonth.'-'.$prevday;
$prevweekenddate				=	date("Y-m-d", strtotime($prevweekstartdate . "+7 day"));

//print $prevweekstartdate."previous week start date in yy/mm/dd format"."<br>";
}

if($daytoday=="Wed")
{
$lastmonth = mktime(0, 0, 0, date("m"), date("d")-9,  date("Y"));
$mdate = date("m d Y", $lastmonth);
//$mdate_in_mysql_format = implode('-',array_reverse(explode ('[/]',$mdate)));
$mdate_in_mysql_format = implode('-',array_reverse(explode (' ',$mdate)));
$prevyear=substr($mdate_in_mysql_format,0,4);
$prevday=substr($mdate_in_mysql_format,5,2);
$prevmonth=substr($mdate_in_mysql_format,8,2);
$prevweekstartdate=$prevyear.'-'.$prevmonth.'-'.$prevday;
//print $prevweekstartdate."previous week start date in yy/mm/dd format"."<br>";
$prevweekenddate				=	date("Y-m-d", strtotime($prevweekstartdate . "+7 day"));


}

if($daytoday=="Thu")
{
$lastmonth = mktime(0, 0, 0, date("m"), date("d")-10,  date("Y"));
$mdate = date("m d Y", $lastmonth);
$mdate_in_mysql_format = implode('-',array_reverse(explode (' ',$mdate)));
$prevyear=substr($mdate_in_mysql_format,0,4);

$prevday=substr($mdate_in_mysql_format,5,2);
$prevmonth=substr($mdate_in_mysql_format,8,2);
$prevweekstartdate=$prevyear.'-'.$prevmonth.'-'.$prevday;
//print $prevweekstartdate."previous week start date in yy/mm/dd format"."<br>";
$prevweekenddate				=	date("Y-m-d", strtotime($prevweekstartdate . "+7 day"));

}

if($daytoday=="Fri")
{
$lastmonth = mktime(0, 0, 0, date("m"), date("d")-11,  date("Y"));
$mdate = date("m d Y", $lastmonth);
$mdate_in_mysql_format = implode('-',array_reverse(explode (' ',$mdate)));
$prevyear=substr($mdate_in_mysql_format,0,4);
$prevday=substr($mdate_in_mysql_format,5,2);
$prevmonth=substr($mdate_in_mysql_format,8,2);
$prevweekstartdate=$prevyear.'-'.$prevmonth.'-'.$prevday;
$prevweekenddate				=	date("Y-m-d", strtotime($prevweekstartdate . "+7 day"));

//print $prevweekstartdate."previous week start date in yy/mm/dd format"."<br>";

}



if($daytoday=="Sat")
{
$lastmonth = mktime(0, 0, 0, date("m"), date("d")-12,  date("Y"));
$mdate = date("m d Y", $lastmonth);
$mdate_in_mysql_format = implode('-',array_reverse(explode (' ',$mdate)));
$prevyear=substr($mdate_in_mysql_format,0,4);
$prevday=substr($mdate_in_mysql_format,5,2);
$prevmonth=substr($mdate_in_mysql_format,8,2);
$prevweekstartdate=$prevyear.'-'.$prevmonth.'-'.$prevday;
$prevweekenddate				=	date("Y-m-d", strtotime($prevweekstartdate . "+7 day"));

//print $prevweekstartdate."previous week start date in yy/mm/dd format"."<br>";
//print $daytoday."<br>";
}

if($daytoday=="Sun")
{
$lastmonth = mktime(0, 0, 0, date("m"), date("d")-13,  date("Y"));
$mdate = date("m d Y", $lastmonth);
$mdate_in_mysql_format = implode('-',array_reverse(explode (' ',$mdate)));
$prevyear=substr($mdate_in_mysql_format,0,4);
$prevday=substr($mdate_in_mysql_format,5,2);
$prevmonth=substr($mdate_in_mysql_format,8,2);
$prevweekstartdate=$prevyear.'-'.$prevmonth.'-'.$prevday;
$prevweekenddate				=	date("Y-m-d", strtotime($prevweekstartdate . "+7 day"));


}

// To get  starting date of current week.............codes starts here.
if($daytoday=="Mon")
{

$thisweekstartdate=$todaysdate."<br>";

}

if($daytoday=="Tue")
{
	$lastmonth1 = mktime(0, 0, 0, date("m"), date("d")-1,  date("Y"));
	$mdate1 = date("m d Y", $lastmonth1);
	$mdate_in_mysql_format1 = implode('-',array_reverse(explode (' ',$mdate1)));
	$thisyear=substr($mdate_in_mysql_format1,0,4);
	$thisday=substr($mdate_in_mysql_format1,5,2);
	$thismonth=substr($mdate_in_mysql_format1,8,2);
	$thisweekstartdate=$thisyear.'-'.$thismonth.'-'.$thisday;
 
}

if($daytoday=="Wed")
{
	$lastmonth1 = mktime(0, 0, 0, date("m"), date("d")-2,  date("Y"));
	$mdate1 = date("m d Y", $lastmonth1);
	$mdate_in_mysql_format1 = implode('-',array_reverse(explode (' ',$mdate1)));
	$thisyear=substr($mdate_in_mysql_format1,0,4);
	$thisday=substr($mdate_in_mysql_format1,5,2);
	$thismonth=substr($mdate_in_mysql_format1,8,2);
	$thisweekstartdate=$thisyear.'-'.$thismonth.'-'.$thisday;
}

if($daytoday=="Thu")
{
	$lastmonth1 = mktime(0, 0, 0, date("m"), date("d")-3,  date("Y"));
	$mdate1 = date("m d Y", $lastmonth1);
	$mdate_in_mysql_format1 = implode('-',array_reverse(explode (' ',$mdate1)));
	$thisyear=substr($mdate_in_mysql_format1,0,4);
	$thisday=substr($mdate_in_mysql_format1,5,2);
	$thismonth=substr($mdate_in_mysql_format1,8,2);
	$thisweekstartdate=$thisyear.'-'.$thismonth.'-'.$thisday;
	//print $thisweekstartdate."This weeks start date in yy/mm/dd format"."<br>";
}

if($daytoday=="Fri")
{
	$lastmonth1 = mktime(0, 0, 0, date("m"), date("d")-4,  date("Y"));
	$mdate1 = date("m d Y", $lastmonth1);
	$mdate_in_mysql_format1 = implode('-',array_reverse(explode (' ',$mdate1)));
	$thisyear=substr($mdate_in_mysql_format1,0,4);
	$thisday=substr($mdate_in_mysql_format1,5,2);
	$thismonth=substr($mdate_in_mysql_format1,8,2);
	$thisweekstartdate=$thisyear.'-'.$thismonth.'-'.$thisday;
	//print $thisweekstartdate."this week start date in yy/mm/dd format"."<br>";

}


if($daytoday=="Sat")
{
	$lastmonth1 = mktime(0, 0, 0, date("m"), date("d")-5,  date("Y"));
	$mdate1 = date("m d Y", $lastmonth1);
	$mdate_in_mysql_format1 = implode('-',array_reverse(explode (' ',$mdate1)));
	$thisyear=substr($mdate_in_mysql_format1,0,4);
	$thisday=substr($mdate_in_mysql_format1,5,2);
	$thismonth=substr($mdate_in_mysql_format1,8,2);
	$thisweekstartdate=$thisyear.'-'.$thismonth.'-'.$thisday;

}


if($daytoday=="Sun")
{
	$lastmonth1 = mktime(0, 0, 0, date("m"), date("d")-6,  date("Y"));
	$mdate1 = date("m d Y", $lastmonth1);
	$mdate_in_mysql_format1 = implode('-',array_reverse(explode (' ',$mdate1)));
	$thisyear=substr($mdate_in_mysql_format1,0,4);
	$thisday=substr($mdate_in_mysql_format1,5,2);
	$thismonth=substr($mdate_in_mysql_format1,8,2);
	$thisweekstartdate=$thisyear.'-'.$thismonth.'-'.$thisday;
//print $thisweekstartdate."this week start date in yy/mm/dd format..today is sunday"."<br>";

}


//$startdateforlastmonth	=	$yeartoday.'-'.$lastmonth2.'-'.'01';
//$enddateforlastmonth	=	$yeartoday.'-'.$lastmonth2.'-'.'31';




if($ritumonth != '01')
{
	
$lx	=date('m')-1;
if($lx <10)
	{
	$lastmonthoftheyear='0'.$lx;
	}
	else
	{
	$lastmonthoftheyear=$lx;
	}


$startdateforlastmonth	=	$yeartoday.'-'.$lastmonthoftheyear.'-'.'01';
$enddateforlastmonth	=	$yeartoday.'-'.$lastmonthoftheyear.'-'.'31';
}

else if($ritumonth=='01')
{
	//print "yes".'<br>';
	$lastm  = mktime(0, 0, 0, date("m")-1 , date("d"), date("Y")-1);
	$lm=date("m", $lastm);
	//print $lm."<br>";

$startdateforlastmonth	=	$lyear.'-'.$lm.'-'.'01';

$enddateforlastmonth	=	$lyear.'-'.$lm.'-'.'31';
}

// fumction to change the date format included by SM as on 12 feb,2007

// FUNCTION TO CHANGE THE DATE TIME FORMAT 







function view_dateformat($issue_date)
{
    if(empty($issue_date))
    {
	$issue_date='0000-00-00';
    }
	else
    {
	$issue_date     =    dateformat($issue_date);
    }
	
	return $issue_date;

}
function view_dateformat1($issue_date2)
{
	if($issue_date2=='0000-00-00')
    {
	$issue_date2='';
    }
    else
    {
	//echo "issuedate";
	$issue_date2     =    dateformat($issue_date2);
    }
	//echo $issue_date1;
	return $issue_date2;

}





// fumction to change the date format included by SM as on 12 feb,2007

function dateformat($date)
{
	$r_date=explode('-',$date);
	$result=$r_date[2].'-'.$r_date['1'].'-'.$r_date[0];
	return $result;

}

function dateformat1($date)
{
	$r_date=explode('-',$date);
	$result=$r_date[2].'-'.$r_date['1'].'-'.$r_date[0];
	return $result;

}
// FUNCTION TO CHANGE THE DATE TIME FORMAT 
function datetimeformat($datetime)
{
     $cdate1=explode(' ',$datetime);  // to separate the date and time
	 $cdate=$cdate1[0];  #######   date
	 $ctime=$cdate1[1];  #######   time
     $cdateexplode=explode('-',$cdate);
     $cdateexp=$cdateexplode[2].'-'.$cdateexplode[1].'-'.$cdateexplode[0];
	 //print $cdateexp;
     $cdatetime=$cdateexp.' '.$ctime;
	 return($cdatetime);
}
##################Function to Change the time format #############
function date_time($datetime)
{
     $cdate1=explode(' ',$datetime);  // to separate the date and time
	 $cdate=$cdate1[0];  #######   date
	 $ctime=$cdate1[1];  #######   time
    
	 return($ctime);
}

function datetimeformat1($datetime)
{
    if(empty($datetime))
    {
	$cdatetime='0000-00-00:00-00-00';
    }
    else
	{
	 $cdate1=explode(' ',$datetime);  // to separate the date and time
	 $cdate=$cdate1[0];  #######   date
	 $ctime=$cdate1[1];  #######   time
     $cdateexplode=explode('-',$cdate);
     $cdateexp=$cdateexplode[2].'-'.$cdateexplode[1].'-'.$cdateexplode[0];
	 //print $cdateexp;
     $cdatetime=$cdateexp.' '.$ctime;
	}
	 return($cdatetime);
}

// comment by Deepak date convert to database format.
function StoreDateIntoDataBase($date0)
{
	$date1=explode("-",$date0);
	$date2=$date1[2]."-".$date1[1]."-".$date1[0];
	return $date2;

}

?>

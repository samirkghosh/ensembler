<!DOCTYPE html>
<head>
	<title>Dashboard</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<script type="text/javascript" src="WFM/js/chart.js"></script>
	<script type="text/javascript" src="WFM/js/jquery.canvasjs.min.js"></script>
	
	<!-- SmartMenus core CSS (required) -->
	<link href="WFM/css/sm-core-css.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="WFM/css/agent_styles.css"/>
	<link rel="stylesheet" type="text/css" href="WFM/css/styles.css"/>
	<link href="WFM/css/sm-simple.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="WFM/css/according-menu.css"/>
	<link href="WFM/css/colorbox.css" rel="stylesheet" type="text/css"/>
	<link href="WFM/css/calender-time.css" rel="stylesheet" type="text/css"/>
	<link href="WFM/css/slicknav.css" rel="stylesheet" type="text/css"/>

<!-- <link href="templates/fullcalendar/fullcalendar.css" rel="stylesheet" /> -->
	<!-- <link href="templates/fullcalendar/fullcalendar.print.css" rel="stylesheet" media="print" /> -->
	
	<style>
	    .counter1,
	  .counter2{font-size: 14px!important ;
	  background: #e3e3e3;
	  border-left: #cecece 1px solid;
	  padding: 10px!important;
	  float: right;
	  text-align: center;
	  margin: -10px -10px 0 0;}
	  .imgBALL0,
	  .imgBALL0{ cursor:move}
	  
	  
	#AssignedArea SELECT, 
	#AssignedArea INPUT[type="text"] {
	    width: 160px;
	    box-sizing: border-box;
	}
	#AssignedArea SECTION {
	    padding: 8px;
	    background-color: #f0f0f0;
	    overflow: auto;
	  display:block;
	  width: 670px;
	  margin-bottom: 5px;
	  border: 1px solid #d4d4d4;
	}
	#AssignedArea SECTION > DIV {
	    float: left;
	    padding: 4px;
	}
	#AssignedArea SECTION > DIV + DIV {
	    width: 40px;
	    text-align: center;
	}
	#AssignedArea #Assigned-btn{margin-top: 30px;}
	.container-wrap{  width: 1000px!important;
	    margin: 0 auto!important;}
	.rightpanel{width:720px!important; margin-right:0%!important} 
	.style-title{     margin-right:0%!important; }
	option{padding: 5px;}

	/* Tooltip container */
	/* Tooltip text */
	.tooltip .tooltiptext {
	    visibility: hidden;
	    /*width: 120px;*/
	    margin-top: -20px;    
	    /*margin-left: -60px; /* Use half of the width (120/2 = 60), to center the tooltip */
	    background-color: #fff;
	    color: #000;
	    opacity:0.9;
	    text-align: center;
	    padding: 2px 5px;
	    border-radius: 6px;
	 
	    /* Position the tooltip text - see examples below! */
	    position: absolute;
	    z-index: 1;
	}
	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltip:hover .tooltiptext {
	    visibility: visible;
	}
	/* Tooltip text for break */
	.tooltip_break .tooltiptext_break {
	    visibility: hidden;
	    /*width: 120px;*/
	    margin-top: -20px;    
	    /*margin-left: -60px; /* Use half of the width (120/2 = 60), to center the tooltip */
	    background-color: #666;
	    color: #fff;
	    opacity:0.9;
	    text-align: center;
	    padding: 2px 5px;
	    border-radius: 6px;
	 
	    /* Position the tooltip text - see examples below! */
	    position: absolute;
	    z-index: 1;
	}
	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltip_break:hover .tooltiptext_break {
	    visibility: visible;
	}
	/* Tooltip text for shift */
	.tooltip_shift .tooltiptext_shift {
	    visibility: hidden;
	    /*width: 120px;*/
	    margin-top: -20px;    
	    /*margin-left: -60px; /* Use half of the width (120/2 = 60), to center the tooltip */
	    background-color: #CCFFFF ;
	    color: #000;
	    opacity:0.9;
	    text-align: center;
	    padding: 2px 5px;
	    border-radius: 6px;
	 
	    /* Position the tooltip text - see examples below! */
	    position: absolute;
	    z-index: 1;
	}
	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltip_shift:hover .tooltiptext_shift {
	    visibility: visible;
	}

	.submit_wfm{
		height: 29px;
    	width: 82px;
    	padding: 5px;
    	background-color: #45a049 !important;
    	color: white;
	}
	.col-2 {
	    float: left;
	    width: 2%;
	    margin-top: 6px;
	}
	.col-5 {
	    float: left;
	    width: 5%;
	    margin-top: 6px;
	}
	.col-15 {
	    float: left;
	    width: 15%;
	    margin-top: 6px;
	}
	.col-18 {
	    float: left;
	    width: 18%;
	    margin-top: 6px;
	}
	.col-25 {
	    float: left;
	    width: 25%;
	    margin-top: 6px;
	}
	.col-20 {
	    float: left;
	    width: 20%;
	    margin-top: 6px;
	}
	.col-22 {
	    float: left;
	    width: 22%;
	    margin-top: 6px;
	}

	.col-75 {
	    float: left;
	    width: 75%;
	    margin-top: 6px;
	}

	.col-70 {
	    float: left;
	    width: 70%;
	    margin-top: 6px;
	}

	.col-50 {
	    float: left;
	    width: 35%;
	    margin-top: 6px;
	}
	.col-radio-75 {
	    float: left;
	    width: 75%;
	    margin-top: 6px;

	}
	.row {
		/*background-color: #ededed;*/
		/*padding:.2% .5% .5% 1%;*/
	}
	.row1 {
		background-color: #fff;
		padding:.2% .5% .5% 1%;
	}
	.button {
		color: #FFF;
		background-color: #060;
		text-align: center;
		padding: 1% 2%;
		border: 1px solid #CCC;
		margin: 2%;
	}
	.botton {
		text-align: center;
			
	}
	.button-orange1{
	background-color: #45a049 !important;
	}
	</style>
	
	<script type="text/javascript" src="WFM/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="WFM/js/jquery.validate.min.js"></script>
	<script src="WFM/js/common.js"></script>
	<script src="WFM/js/form-validation.js"></script>
	<script type="text/javascript" src="WFM/js/jquery.smartmenus.js"></script>
	<script language='javascript' src='WFM/js/md5.js'></script>
	<script src="templates/js/jquery.mousewheel.min.js"></script>
	<script src="WFM/js/jquery.colorbox.js"></script>
	<script type="text/javascript" src="WFM/js/jquery-ui.min2.js"></script>
	<script type="text/javascript" src="templates/js/jquery-ui-timepicker-addon2.js"></script>
	<script src="WFM/js/jquery.slicknav.js"></script>
	<script src="WFM/js/script.js"></script>

	<link href="templates/fullcalendar/fullcalendar.css" rel="stylesheet" />
	<link href="templates/fullcalendar/fullcalendar.print.css" rel="stylesheet" media="print" />
	<script src="templates/fullcalendar/moment.min.js"></script>
	<script src="templates/fullcalendar/fullcalendar.min.js"></script>
</head>
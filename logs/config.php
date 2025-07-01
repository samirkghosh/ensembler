<?php
//echo "inside config.php<br>";

/************************************************************************
* FileName: config.php
* Purpose: System-wide falgs and constants are defined.
* @Auth: Samir Ghosh
* @Date: 30-08-2016
*************************************************************************/
/*
* __CONSOLE_DBG__: 0: OFF, 1:ON, This output the dbg out to Console, Press F12 to view it
*/

define ("__CONSOLE_DBG__", 0);

/**
* define the path for gen function
**/

define ("__UTILS", "utils");


/* Define if the logging to file is enabled */
define ("__DBGLOG__", 1);

/* define the path */
define ("__LOGPATH__", "/var/www/html/log");

//echo __LOGPATH__ . "<br>";

define ("__TOKEN__","ICMS");
define("__OmniChannel__", "OmniChannel"); // for omnichannel logs file create[Aarti][03-10-2024]

define ("__LOGLEVEL__","3");

define ("_LOG_ERROR", "0");

define ("_LOG_WARNING", "1");

define ("_LOG_INFO", "2");

define ("_LOG_DTL", "3");

define ("_USE_SYSTEM_LOG_", "1");

/******************************** Cmmon includes *****************************/
//echo "ends config.php<br>";

?>



<?php

/**************************************************************************
** FieName: logconsole.php
** Purpose: Logs the values on the console
**
***************************************************************************/

/***************************************************************************
** FNNAME: DbgCon: outputs to console
** @param: $szLevel: Log level
** @param: $szToken: Token used to identify log filename
** @param: $szLine: Line number in the File
** @param: $szMethod: method name
** @param: $szMsg: Message String to be output
** @Auth: Samir ghosh
** @Date: 30-11-2016
****************************************************************************/
function DbgLog($szLevel,$szLine, $szMethod, $szMsg)
{
	
	if ( $szLevel > __LOGLEVEL__)
		return;

	/* Create a folder as [DDMMMYY] */
 	$szFolder = date("dMY");

//echo "Folder: ". $szFolder ."<br>";

	$szPath=__LOGPATH__ . "/" . date("dMY");
	if ( !file_exists($szPath))
	{
//	echo "LogPath Not found.$szPath<br>";
		mkdir($szPath);
	}

	/* Create a FileName [BasePath]/[Folder]/[Token]_logfile.log */
	// for omnichannel logs file create[Aarti][03-10-2024]
	if(!empty($pathname)){
		/* Create a FileName [BasePath]/[Folder]/[Token]_logfile.log */
		$szLogFileName= $szPath . "/". __OmniChannel__ . "_logfile.log";
	}else{
		/* Create a FileName [BasePath]/[Folder]/[Token]_logfile.log */
		$szLogFileName= $szPath . "/". __TOKEN__ . "_logfile.log";
	}
	
	// echo "LogFileName:".$szLogFileName."<br>";
	
	$szLogMsg = $szLevel . " :: " . date("H:i:s") . " :: " . $szMsg . " :: " . $szLine . " :: " . $szMethod . "\r\n";
// echo "Msg:". $szLogMsg ."<br>";

	file_put_contents($szLogFileName, $szLogMsg, FILE_APPEND | LOCK_EX);

	return;
}


?>

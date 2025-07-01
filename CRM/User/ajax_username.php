<?php 
/***
    * USER Name 
    * Author: Aarti Ojha
    * Date: 27-03-2024
    * This file is handling for check user name already available or not
   */
session_start();
include("../../config/web_mysqlconnect.php"); 
   	if(!empty($_REQUEST["q"]))
	{
		$username = $_REQUEST["q"];
		$SQL="SELECT count(*) as cou FROM $db.uniuserprofile WHERE AtxUserName = '$username' and AtxUserStatus='1'";
		$rs=mysqli_query($link,$SQL);
		$row=mysqli_fetch_array($rs);
		$cou=$row['cou'];
		if($cou>=1){ $response='<font face="Tahoma" color="#FF3333" size="1">This User Name is already in use.</font>'; }else{ $response='<font face="Tahoma" color="#00FF00" size="1">User Name Available</font>';}
		echo $response;
		
	}
?>


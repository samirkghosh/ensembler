<?php 
/***
 * IM Page
 * Author: Aarti Ojha
 * Date: 07-11-2024
 * Description: This file handles IM Realtime Chat chat flow
 */
  if(isset($_SESSION['userid'])){
    $_SESSION['unique_id'] = $_SESSION['userid'];
    //header("location: chatview.php");
    include_once("chatview.php");
  }
?>
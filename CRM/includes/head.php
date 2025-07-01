<?php
/**
 * Header Page
 * Author: Aarti Ojha
 * Date: 16-01-2024
 * Description: This file handles the head section, including JavaScript and CSS files,
 *              and includes all necessary project configurations.
 */

include("../config/web_mysqlconnect.php");  // Includes MySQL connection configuration file

// Constants defined in included file
?>
<!-- Dynamic css -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ENSEMBLER</title>
    <!-- Bootstrap CSS -->
    <link href="<?=$SiteURL?>public/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?=$SiteURL?>public/datatables/css/dataTables.dataTables.css" />
    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/select2.min.css" />
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/select2-bootstrap.min.css">
    <!-- SmartMenus core CSS -->
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/facebook.css" />
    <link href="<?=$SiteURL?>public/css/sm-core-css.css" rel="stylesheet" type="text/css" />
    <link href="<?=$SiteURL?>public/css/sm-simple.css" rel="stylesheet" type="text/css" />
    <link href="<?=$SiteURL?>public/css/colorbox.css" rel="stylesheet" type="text/css" />
    <link href="<?=$SiteURL?>public/css/calender-time.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome and Ionicons -->
    <link rel="stylesheet" href="<?=$SiteURL?>public/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?=$SiteURL?>public/css/ionicons.min.css">
    <!-- Dropify CSS -->
    <link rel="stylesheet" href="<?=$SiteURL?>public/css/dropify.min.css">
    <!-- NProgress CSS -->
    <link href="<?=$SiteURL?>public/css/nprogress.css" rel="stylesheet">
    <!-- DataTables Additional CSS -->
    <link href="<?=$SiteURL?>public/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="<?=$SiteURL?>public/datatables/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="<?=$SiteURL?>public/datatables/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <!-- Responsive DataTables CSS -->
    <link href="<?=$SiteURL?>public/datatables/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="<?=$SiteURL?>public/datatables/css/rowReorder.dataTables.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="<?=$SiteURL?>public/css/fullcalendar.css" rel="stylesheet" />
    <!-- DateTimePicker CSS -->
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/jquery.datetimepicker.css"/>
    <!-- Font Awesome (5.15.3) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Common CSS -->
    <link href="<?=$SiteURL?>public/css/common.css" rel="stylesheet" />

    <!-- jQuery Scripts -->
    <script type="text/javascript" src="<?=$SiteURL?>public/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="<?=$SiteURL?>public/js/jquery.validate.min.js"></script>

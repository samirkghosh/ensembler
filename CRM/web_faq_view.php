<?php
/**
* Auth: Vastvikta Nishad
* Date: 04-03-2024
* This file is used to display a knowledge base where users can search for questions and answers. It includes the necessary PHP files for handling functions related to web and report functionalities. The file consists of a search form, a table for displaying FAQ data, and JavaScript code to highlight search results.
*/
include("web_function.php");
include("Report/report_function.php");
?>
<style type="text/css">
    span.highlighted {
      color: red;
    }
    .text_ans {
      text-align: justify;
      text-justify: inter-word;
    }
    </style>

<!-- Fix code no need to be change -->
<body>
<div class="wrapper">
<div class="container-fluid">
    <div class="row" style="min-height:90vh;">
        <div class="col-sm-2" style="padding-left:0">
            <?php include('includes/sidebar.php');?>
        </div>
        <div class="col-sm-10 mt-3" style="padding-left:0">
            <form name=loginfrm method=post>
                <div id="searchResult"></div>
                <div class="style2-table">
                    <div class="style-title">
                        <h3>Knowledge Base</h3>
                    </div>
                    <div class="style-title2 st-title2-wth-lable main-form">
                                <form action="" method="post" style="float:left">
                                    <input type="text" name="queryString" class="input-style1"
                                        value="<?=$_POST['queryString']?>" id="seabutton">&nbsp;&nbsp;
                                    <input type="submit" name="submit" class="button-orange1" value="Search"
                                         id="new_submit_form">
                                </form>
                            </div>
                    <div class="table" id="SRallview">
                    <table class="tableview tableview-2 new_table" style="border-collapse: none !important;" cellspacing="0" cellpadding="0" id="admin_table">
                                <thead>
                                <tr class="background">
                                    <td align="center" width="5%">S.No.</td>
                                    <td align="left" width="35%">Question</td>
                                    <td align="center">Answer</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                 $faqData = getFaqData();
                                 $numrow = count($faqData);
                                 foreach ($faqData as $index => $row) {
                             ?>
                                     <tr>
                                         <td align="center"><?= $index + 1; ?></td>
                                         <td align="center">
                                             <p class="contains"><?= wordwrap($row['v_qus'], 150, "<br>") ?></p>
                                         </td>
                                         <td align="center">
                                             <p class="contains text_ans"><?= $row['v_ans'] ?></p>
                                         </td>
                                     </tr>
                             <?php
                                 }
                             
                                 if ($numrow == 0){
                             ?>
                                     <tr>
                                         <td align="center" class="select" colspan="3">No Records Found</td>
                                     </tr>
                             <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
 <div class="footer">
<? include("includes/web_footer.php"); ?>
</div>
      <script type="text/javascript" src="<?=$SiteURL?>public/js/faq.js" ></script>

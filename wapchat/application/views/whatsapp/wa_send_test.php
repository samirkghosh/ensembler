<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

  <!-- Header start -->
  <?php  $this->load->view('layout/header'); ?>
  <!-- Header End  -->
<style>
  body {
    font-family: "Lato", sans-serif;
  }

  .sidenav {
      height: 100%;
      left: 0;
      background-color: #11111161;
      width: 52px
    
  }

  .sidenav a {
    padding: 6px 8px 6px 16px;
    text-decoration: none;
    font-size: 18px;
    color: #818181;
    display: block;
  }

  .sidenav a:hover {
    color: coral;
  }
  .card {
    border-radius: 0;
  }

  .direct-chat-text{
    color: #566069;
      font-size: 13px;
      line-height: 21px;
      letter-spacing: 0.3px;
      outline: none;
      line-height: 3;
  }
  .direct-chat-img {
      border-radius: 0; 
      float: left;
      height: 25px;
      width: 25px;
  }
  .direct-chat-timestamp{
    font-size: 11px;
    letter-spacing: 0.5px;
    line-height: 16px;
  }
  .direct-chat-messages {
      -webkit-transform: translate(0,0);
      transform: translate(0,0);
      /* height: 500px;
      overflow: auto; */
      height:auto;
      padding: 10px;
  }

</style>


<!-- Content page  -->
<div class="content">
  <!-- Content Header (Page header) -->
   

      <div class="d-flex p-1">

          <!-- Options -->

          <!-- <div class="p-0">
            
                <div class="sidenav">
                <a href="#about">
                      
                    </a>
                    <a href="#about">
                      
                    </a>
                <a href="#about">
                      <i class="fas fa-comments"></i>
                    </a>
                    <a href="#about">
                      <i class="fas fa-comments"></i>
                    </a>
                    <a href="#services"><i class="fab fa-whatsapp"></i></a>
                    <a href="#clients"><i class="fas fa-envelope-square"></i></a>
                </div>

          </div> -->

          <!-- left -->
          <div class="p-0">
            <!-- <div class="card card-info  card-outline" style="max-width: 400px;height:600px"> -->
            <div class="card card-info  card-outline" style="max-height:538px">
                      <div class="card-header">
                        <h3 class="card-title">Contacts</h3>
                      </div>
                      <!-- /.card-header -->
                      <div class="card-body" style="overflow: auto;">
                        <ul class="nav nav-pills flex-column">
                          <li class="nav-item" style="width: 300px;">
                            <a href="#" class="nav-link" style="padding: 0;">
                              <i class="fas fa-user-circle"></i> Farhan Akhtar
                            </a>
                            <p style="font-size: 13px;
                              line-height: 20px;
                              opacity: 0.8;
                              height: 35px;
                              overflow: hidden;
                              letter-spacing: 0.3px;">We will provide you all the details via email within 48 hours</p>
                          
                          </li>
                          <li class="nav-item" style="width: 300px;">
                            <a href="#" class="nav-link" style="padding: 0;">
                              <i class="fas fa-user-circle"></i> Vijay Pippal
                            </a>
                              <p style="font-size: 13px;
                              line-height: 20px;
                              opacity: 0.8;
                              height: 35px;
                              overflow: hidden;
                              letter-spacing: 0.3px;">See you soon. If you have further questions don't hesitate to contact me.</p>
                          
                          </li>
                          <li class="nav-item" style="width: 300px;">
                            <a href="#" class="nav-link" style="padding: 0;">
                              <i class="fas fa-user-circle"></i> Faieza Khan
                            </a>
                              <p style="font-size: 13px;
                              line-height: 20px;
                              opacity: 0.8;
                              height: 35px;
                              overflow: hidden;
                              letter-spacing: 0.3px;">[email]</p>
                          
                          </li>
                          <li class="nav-item" style="width: 300px;">
                            <a href="#" class="nav-link" style="padding: 0;">
                              <i class="fas fa-user-circle"></i> Samir ghosh
                            </a>
                              <p style="font-size: 13px;
                              line-height: 20px;
                              opacity: 0.8;
                              height: 35px;
                              overflow: hidden;
                              letter-spacing: 0.3px;">
                            
                              Jennifer:Muller:jenny@email.com,Al ...
                            </p>
                          
                          </li>
                          <li class="nav-item" style="width: 300px;">
                            <a href="#" class="nav-link" style="padding: 0;">
                              <i class="fas fa-user-circle"></i> Ajay kumar
                            </a>
                              <p style="font-size: 13px;
                              line-height: 20px;
                              opacity: 0.8;
                              height: 35px;
                              overflow: hidden;
                              letter-spacing: 0.3px;">
                              hey there
                            </p>
                          
                          </li>
                          <li class="nav-item" style="width: 300px;">
                            <a href="#" class="nav-link" style="padding: 0;">
                              <i class="fas fa-user-circle"></i> Rahul Jain
                            </a>
                              <p style="font-size: 13px;
                              line-height: 20px;
                              opacity: 0.8;
                              height: 35px;
                              overflow: hidden;
                              letter-spacing: 0.3px;">
                              hello
                            </p>
                          
                          </li>
                          
                        </ul>
                      </div>
                      <!-- /.card-body -->
            </div>

          </div>

          <!--Mid  -->
          <div class="p-0">
                  <!-- <div class="card card-info  card-outline" style="width:669px;height:600px"> -->
                  <div class="card card-info  card-outline" style="width: 556px;height:538px"> 
                    <div class="card-header">
                      <h3 class="card-title">Send SMS</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" style="overflow: auto;">
                      <!-- Conversations are loaded here -->
                      <div class="direct-chat-messages">
                        <!-- Message. Default to the left -->
                        <div class="direct-chat-msg">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                            <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:#f5f5f5">
                          We have an offer for you! 🤑
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->

                        <!-- Message to the right -->
                        <div class="direct-chat-msg right">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-right" style="font-weight:100;color:#6c757d">Sarah Bullock</span>
                            <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:aliceblue">
                          What is this offer? Can you explain me?
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->
                        <!-- Message. Default to the left -->
                        <div class="direct-chat-msg">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                            <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:#f5f5f5">
                          We have an offer for you! 🤑
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->

                        <!-- Message to the right -->
                        <div class="direct-chat-msg right">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-right" style="font-weight:100;color:#6c757d">Sarah Bullock</span>
                            <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:aliceblue">
                          What is this offer? Can you explain me?
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->
                        <!-- Message. Default to the left -->
                        <div class="direct-chat-msg">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                            <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:#f5f5f5">
                          We have an offer for you! 🤑
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->

                        <!-- Message to the right -->
                        <div class="direct-chat-msg right">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-right" style="font-weight:100;color:#6c757d">Sarah Bullock</span>
                            <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:aliceblue">
                          What is this offer? Can you explain me?
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->
                        <!-- Message. Default to the left -->
                        <div class="direct-chat-msg">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                            <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:#f5f5f5">
                          We have an offer for you! 🤑
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->

                        <!-- Message to the right -->
                        <div class="direct-chat-msg right">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-right" style="font-weight:100;color:#6c757d">Sarah Bullock</span>
                            <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:aliceblue">
                          What is this offer? Can you explain me?
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->
                        <!-- Message. Default to the left -->
                        <div class="direct-chat-msg">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                            <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:#f5f5f5">
                          We have an offer for you! 🤑
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->

                        <!-- Message to the right -->
                        <div class="direct-chat-msg right">
                          <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-right" style="font-weight:100;color:#6c757d">Sarah Bullock</span>
                            <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                          </div>
                          <!-- /.direct-chat-infos -->
                          <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                          <!-- /.direct-chat-img -->
                          <div class="direct-chat-text" style="background:aliceblue">
                          What is this offer? Can you explain me?
                          </div>
                          <!-- /.direct-chat-text -->
                        </div>
                        <!-- /.direct-chat-msg -->
                      </div>
                      <!--/.direct-chat-messages-->

                    
                      <!-- /.direct-chat-pane -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                      <form action="#" method="post">
                        <div class="input-group">
                          <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                          <span class="input-group-append">
                            <button type="submit" class="btn btn-primary">Send</button>
                          </span>
                        </div>
                      </form>
                    </div>
                  </div>

          </div>

            <!--right  -->
            <div class="p-0">

                    <!-- <div class="card card-info  card-outline" style="height:600px;width:388px"> -->
                    <div class="card card-info  card-outline" style="width:440px;height:538px">
                      <div class="card-header">
                        <h3 class="card-title">Last interaction 360°</h3>
                      </div>
                      <!-- /.card-header -->
                      <div class="card-body" style="overflow: auto;">
                        <!-- Conversations are loaded here -->
                        <div class="direct-chat-messages">
                          <!-- Message. Default to the left -->
                          <div class="direct-chat-msg">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-left" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            We have an offer for you! 🤑
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->

                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->

                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->
                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->

                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->
                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->

                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->
                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->

                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->
                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->

                          <!-- Message to the right -->
                          <div class="direct-chat-msg left">
                            <div class="direct-chat-infos clearfix">
                              <span class="direct-chat-name float-lrft" style="font-weight:100;color:#6c757d">Farhan Akhtar</span>
                              <span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span>
                            </div>
                            <!-- /.direct-chat-infos -->
                            <img class="direct-chat-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyWp28M_ivxI629RNf9M3GjgUvjtVR_huaqu32zGKNKj6CvtTgowcrz4hUywbJKCloI0M&usqp=CAU" alt="Message User Image">
                            <!-- /.direct-chat-img -->
                            <div class="direct-chat-text" style="background:#f5f5f5">
                            What is this offer? Can you explain me?
                            </div>
                            <!-- /.direct-chat-text -->
                          </div>
                          <!-- /.direct-chat-msg -->
                        </div>
                        <!--/.direct-chat-messages-->

                      
                        <!-- /.direct-chat-pane -->
                      </div>
                      <!-- /.card-body -->
                    </div>


            </div>
          
      </div>

</div>
     

        <!-- Main Footer --> 
        <?php $this->load->view('layout/footer');?>
        <!-- End of footer -->

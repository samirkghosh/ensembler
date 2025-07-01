<section class="content-header" style="padding: 3px .5rem;">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-3">
        <strong  style="font-family: sans-serif;    color: coral;">
            UC-2000 Notification Platform
           </strong>
      </div>
      <div class="col-sm-5">
        <div class="row" style="font-size:small">
          <div> SMS GateWay Status : <span id="indecator" class="brand-text">Connecting... &nbsp;&nbsp;&nbsp;</span> </div>
          <div >SMS Sender Status : <span id="sender_status" class="brand-text">Connecting... &nbsp;&nbsp;&nbsp;</span> &nbsp;<span id="sendreason"></span> </div>
        </div>
      </div>
       <div class="col-sm-1">
         <button type="button" class="btn btn-info form-control-sm" onclick="reset_sms_sender_status()">Reset Sender Status </button>
       </div>
      <div class="col-sm-3">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a class="link" href="#">Home</a></li>
          <li class="breadcrumb-item active"><?php echo $breadcrumb; ?></li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
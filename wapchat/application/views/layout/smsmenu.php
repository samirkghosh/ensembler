<style>
  .nav-pills .menu-link.active, .nav-pills .show>.menu-link {
    color: coral;
    background-color: #fff;
    border: 1px solid coral;
}
.menu-link{
  display: block;
  padding: .5rem 1rem;
}
</style>
<div class="col-md-2">
  <a href="<?php echo site_url('smsbox/new_message')?>" id="compose" class="btn btn-block mb-3">Compose</a>

  <div class="card">
    <div class="card-body p-0">
      <ul class="nav nav-pills flex-column">
        <li class="nav-item active">
          <a href="<?php echo site_url('smsbox/inbox')?>" id="inbox" class="menu-link link  <?php echo ($this->uri->segment(2) == 'inbox')?'active':''; ?>">
            <i class="fas fa-inbox"></i> Inbox
            <span class="badge bg-primary float-right"><?php echo $total_unread_count; ?></span>
            
          </a>
        </li>
        <li class="nav-item">
          <a  href="<?php echo site_url('smsbox/outbox')?>" id="outbox" class="menu-link link  <?php echo ($this->uri->segment(2) == 'outbox')?'active':''; ?>">
            <i class="far fa-envelope-open"></i> Outbox
            <span class="badge bg-primary float-right"><?php echo $total_outbox_count; ?></span>
          </a>
        </li>
        <li class="nav-item">
          <a  href="<?php echo site_url('smsbox/sent')?>" id="sent" class="menu-link link  <?php echo ($this->uri->segment(2) == 'sent')?'active':''; ?>">
            <i class="far fa-envelope"></i> Sent
          </a>
        </li>
        <!-- <li class="nav-item">
          <a href="<?php echo site_url('smsbox/delivered')?>" id="delivered" class="menu-link link  <?php echo ($this->uri->segment(2) == 'delivered')?'active':''; ?>">
            <i class="fas fa-file-import"></i> Delivered
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo site_url('smsbox/undelivered')?>" id="undelivered" class="menu-link link <?php echo ($this->uri->segment(2) == 'undelivered')?'active':''; ?>">
            <i class="fas fa-file-excel"></i> Undelivered
          </a>
        </li> -->
      </ul>
    </div>
    <!-- /.card-body -->
  </div>
<?php if($this->uri->segment(2,0) == 'inbox--ss'):?>
  <div class="card">
    <div class="card-header">
      <h6>Message Details</h6>
    </div>
      
    <div class="card-body p-0">
      <!-- Show Message Details  -->
      <table width="100%">
        <tr>
          <th style="padding-left: 5px" >Sent By :</th>
          
        </tr>
        <tr>
          <td style="padding-left: 5px" ><span id="sent_by"></span></td>
        </tr>

        <tr>
          <th style="padding-left: 5px">Action By :</th>
        </tr>
        <tr>
          <td style="padding-left: 5px"><span id="replied_by"></span></td>
        </tr>

        <tr>
          <th style="padding-left: 5px">Action Date :</th>
        </tr>

        <tr>
          <td style="padding-left: 5px"><span id="replied_date"></span></td>
        </tr>

        <tr>
          <th style="padding-left: 5px">Content :</th>
        </tr>
        <tr>
          <td style="padding-left: 5px"><span id="content"></span></td>
        </tr>
      </table>
    </div>
  </div>

<?php endif;?>
 
</div>
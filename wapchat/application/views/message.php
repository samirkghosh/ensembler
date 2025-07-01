<? if($this->session->flashdata('fail')){?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
       <? echo $this->session->flashdata('fail')?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <? }?>
    <? if($this->session->flashdata('success')){?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <? echo $this->session->flashdata('success')?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <? }?>
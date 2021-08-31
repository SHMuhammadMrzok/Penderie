 <div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('address_list');?></li>
      </ul>
    </div>
  </div>
</div>
<main>
  <div class="container">

    <div class="row">
      <?php $this->load->view('site/user_menu', $this->data);?>
      <div class="col-md-8">
        <div class="user-address-container setting-container mt-0">
          <h3 class="title"><?php echo lang('address_list');?> <a href="<?php echo base_url()."users/user_address/address/";?>" class="button-link"><?php echo lang('add').' '.lang('user_address');?></a></h3>
          <div class="row m-0">
            <div class="address-area">
              <?php if(isset($_SESSION['list_error'])){
                if($_SESSION['list_error'] == 1){?>
                  <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['list_msg'];?>
                  </div>
                <?php }else{?>
                  <div class="alert alert-success" role="alert">
                    <?php echo $_SESSION['list_msg'];?>
                  </div>
                <?php }
              }?>
                <?php if(count($list) == 0){?>
                  <div class="address-container">
                    <div class="info">
                      <label for="ad-1"><?php echo lang('no_data');?></label>
                    </div>
                  </div>
                <?php
                }else{
                    foreach($list as $key=>$row){ ?>
                    <div class="address-container">
                      <?php /*<input type="radio" name="address" value="a-1" id="ad-1" checked="checked" />*/?>
                      <div class="info">
                        <label for="ad-1">
                          <?php echo $row->title;?>
                          <?php if($row->default_add){?>
                             ( <?php echo lang('default_address');?>)
                          <?php }?>
                        </label>
                        <?php if($row->default_add){?>
                          <label for="ad-1"><?php echo lang('default_address');?></label>
                        <?php }?>
                        <p><?php echo $row->address;?></p>
                        <div class="link-area">
                          <ul>
                            <li><a href="<?php echo base_url()."users/user_address/address/".$row->id;?>"><?php echo lang('edit');?></a></li>
                            <li>|</li>
                            <li><a href="<?php echo base_url()."users/user_address/delete_address/".$row->id;?>"><?php echo lang('delete');?></a></li>
                        </div>

                      </div>
                    </div>
                  <!--End address-container-->
                <?php }
              }?>
                <!--End address-container-->


            </div>


          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<div class="breadcrumb">
<div class="container">
  <div class="breadcrumb-inner">
    <ul class="list-inline list-unstyled">
      <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
      <li class='active'><?php echo lang('user_address');?></li>
    </ul>
  </div>
</div>
</div>

<main>
<div class="container">

  <div class="row">
    <?php $this->load->view('site/user_menu', $this->data);?>
    <div class="col-md-8">
      <div class="user-address-container setting-container  mt-0">

        <h3 class="title"><?php echo lang('new_address');?></h3>
        <div class="row ml-0 mr-0">
          <div class="add-address">
            <form action="" method="post" enctype="multipart/form-data">
              <?php if($this->session->flashdata('message')){?>
                  <div class="success-alert"><?php echo $this->session->flashdata('message');?></div><!--success_message-->
              <?php }?>
              <?php if($this->session->flashdata('error')){?>
                  <div class="error-messege"><?php echo $this->session->flashdata('error');?></div><!--fail_message-->
              <?php }?>

              <div class="add-address-map">
                  <?php echo form_error('lat'); // form_error('lng')  // Mrzok Edit ?> 
                  <div class="title-map">
                      <h2><?php echo lang('map_location');?></h2>
                  </div>
                  <div class="add-lcation">
                       <div id="map" class="map-location"></div>
                       <div class="input-search-area">
                          <input type="text" id="autocomplete" class="form-control">
                          <button class="getcurrentLocation">

                            <span> <?php echo lang('get_current_location');?> </span>
                          </button>

                       </div>
                       <div id="map_address"></div>
                  </div>
                </div>

                <input type="hidden" name="lat" id="lat_input" />
                <input type="hidden" name="lng" id="lng_input" />

              <div class="form-group">
                <label><?php echo lang('address_title');?></label>
                <?php
                 $title_att = array(
                                    'name'  => 'title',
                                    'id'    => 'title',
                                    'placeholder' => lang('name'),
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'value' => isset($general_data->title)? $general_data->title : set_value('title')
                                  );
                 echo form_input($title_att);
                 echo form_error('title');
                ?>
              </div>

              <div class="form-group">
                <label><?php echo lang('city_name');?></label>
                <?php
                 $city_att = array(
                                    'name'  => 'city',
                                    'id'    => 'city',
                                    'placeholder' => lang('city_name'),
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'value' => isset($general_data->city)? $general_data->city : set_value('city')
                                  );
                 echo form_input($city_att);
                 echo form_error('city');
                ?>
              </div>

              <div class="form-group">
                  <label><?php echo lang('address');?></label>
                   <?php
                    $address = array(
                                     'name'   => 'address',
                                     'id'     => 'address',
                                     'placeholder' => lang('address'),
                                     'class'  => 'form-control',
                                     //'required' => 'required',
                                     'value'  => isset($general_data->address)? $general_data->address : set_value('address')
                                   );
                    echo form_textarea($address);
                    echo form_error('address');
                   ?>
                </div>

                <div class="form-group">

                  <label class="control-label">
                      <?php echo lang('default_address');?>
                  </label>
                  <label class="checkbox">
                      <?php
                      echo form_error('default_add');
                      $default_add   = true;
                      if(isset($general_data->default_add))
                      {
                        if($general_data->default_add == 1)
                        {
                          $default_add   = true;
                        }
                        else {
                          $default_add   = false;
                        }
                      }
                      $default_add_data  = array(
                                             'name'           => 'default_add',
                                             'id'             => 'default_add',
                                             'value'          => 1,
                                             'checked'        => set_checkbox('default_add', $default_add, $default_add),
                                             'data-on-text'   => lang('yes'),
                                             'data-off-text'  => lang('no'),
                                             'class'          => ''
                                          );
                      echo form_checkbox($default_add_data);
                      ?>
                      <span></span>
                  </label>

                </div><!--row-form-->



                <div class="form-group">
                  <div class="row">
                    <div class="col">
                        <button class="button"><?php echo lang('save');?></button>
                    </div>
                    <div class="col">
                        <button  class="button gray-bg" type="reset"><?php echo lang('cancle');?></button>
                    </div>
                  </div>

                </div>
            </form>
          </div>
        </div>
      </div>


    </div>
  </div>
</div>
</main>

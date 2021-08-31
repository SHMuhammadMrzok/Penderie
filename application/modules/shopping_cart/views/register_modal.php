<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="memberModalLabel"></h4>
        <div id="reg_validation"></div>
      </div>
      <div class="modal-body">
        <form class="cd-form" id="register_form" method="post" action="#">
            <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label for="email_reg"><?php echo lang('email');?></label>
                </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php
                      $email_att = array('name'=>'email', 'placeholder'=>lang('email'), 'id'=>'email_reg', 'class'=>'form-control', 'readonly'=>'readonly');
                      echo form_input($email_att);
                    ?>
                </div><!--col-->
                <?php echo form_error('email');?>
            </div><!--Email row-->

            <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label for="phone_reg"><?php echo lang('phone');?></label>
                </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php
                      $phone_att = array('name'=>'phone', 'placeholder'=>lang('phone'), 'id'=>'phone_reg', 'class'=>'form-control', 'readonly'=>'readonly');
                      echo form_input($phone_att);
                    ?>
                </div><!--col-->
                <?php echo form_error('phone');?>
            </div><!--Phone row-->

            <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label for="username_reg"><?php echo lang('first_name');?></label>
                </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php
                      $username_att = array('name'=>'username', 'placeholder'=>lang('username'), 'id'=>'username_reg', 'class'=>'form-control', 'readonly'=>'readonly');
                      echo form_input($username_att);
                    ?>
                </div><!--col-->
                <?php echo form_error('username');?>
            </div><!--Username row-->

            <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label for="last_name_reg"><?php echo lang('last_name');?></label>
                </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php
                      $last_name_att = array('name'=>'last_name', 'placeholder'=>lang('last_name'), 'id'=>'last_name_reg', 'class'=>'form-control', 'value'=>set_value('last_name'));
                      echo form_input($last_name_att);
                    ?>
                </div><!--col-->
                <?php echo form_error('last_name');?>
            </div><!--last name row-->

             <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label for="reg"><?php echo lang('country');?></label>
                </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php /*?>
                    <select id="user_nationality" class="form-control" name="user_nationality">
                        <?php foreach($user_countries as $country_id => $country){ 
                            if($country_id == 191) $selected ='selected';else $selected='';?>
                            <option value="<?php echo $country_id;?>" <?php echo $selected?>><?php echo $country;?></option>
                        <?php }?>
                    </select>
                    <?php */?>
                    
                    <input type="text" readonly="readonly" id="user_nationality" class="form-control" />
                    <input type="hidden" readonly="readonly" id="country_id_reg" class="form-control" />
                </div><!--col-->
            </div><!--Country row-->

            <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label for="reg"><?php echo lang('region');?></label>
                </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="city" name="city"></select>
                </div><!--col-->
            </div><!--City row-->

            <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <label for="password"><?php echo lang('password');?></label>
            </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php
                      $password_att = array('name'=>'password', 'id'=>'password_reg', 'placeholder'=>'', 'class'=>'form-control password_field');
                      echo form_password($password_att); 
                    ?>
                </div><!--col-->
                <?php echo form_error('password');?>
            </div><!--Password row-->

            <div class="row no-margin margin-bottom-10px">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label for="conf_password"><?php echo lang('confirm_password');?></label>
                </div><!--col-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php 
                      $conf_password_att = array('name'=>'conf_password', 'id'=>'conf_password', 'placeholder'=>'', 'class'=>'form-control confirm_field');
                      echo form_password($conf_password_att);
                    ?>
                </div><!--col-->
                <?php echo form_error('conf_password');?>
            </div><!--row-->

            <div class="modal-footer">
                <button class="btn btn-primary full-width submit_finish_data"><?php echo lang('submit');?> </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo lang('close');?></button>
              </div>


            </form>
      </div>

      
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $(document).on({
    ajaxStart: function() { $('body').addClass("loading");    },
    ajaxStop: function() { $('body').removeClass("loading"); }    
});
   var user_nationality_id = $("#country_id_reg").val();
   
   /*
   
   postData = {id: user_nationality_id};

    $.post('<?php echo base_url()?>users/register/get_country_cities/', postData, function(result){
        $("#city").html(result);
    });
   */

    /// Get Regions in Completing signing up 
    $("#user_nationality").change(function(){
       var nationality_id = $(this).val();

       $.ajax({
            type:'post',
            data:{ id: $(this).val()},
            url :"<?php echo base_url()?>users/register/get_country_cities/"+nationality_id,

            success:function(info){
             $("#city").html(info);
            }
       });
    });

    ////////////////////////////////
    
    $('.submit_finish_data').click(function(e){
        e.preventDefault();
        
        var email      = $("#email_reg").val();
        var last_name  = $("#last_name_reg").val();
        //var country_id = $("#user_nationality").val();
        var city_id    = $("#city").val();
        var password   = $("#password_reg").val();
        var conf_pass  = $("#conf_password").val();
        
        var postData = { 
                            email       : email      ,
                            last_name   : last_name  ,
                            //country_id  : country_id ,
                            city_id     : city_id    ,
                            password    : password   ,
                            conf_pass   : conf_pass
                       }
                       
        $.post('<?php echo base_url()."users/register/update_sign_up_data";?>', postData, function(result){
            <?php
              /*
                result[0] = errors_exist
                result[1] = validation errors
                result[2] = msg view
              */ 
            ?>
            
            if(result[0] == 'true')
            {
                $('#reg_validation').html(result[1]);
            }
            else if(result[0] == 'false')
            {
                window.location.href = '<?php echo base_url().'users/register/view_first_msg';?>';
                //$('.message').html(result[1]);
            }
        }, 'json');
    });
    
});
</script>
<?php #File Path application/modules/stores/views/stores_register_form.php?>
<style>
    @font-face{
    font-family:'Glyphicons Halflings';
    /*src:url(../fonts/glyphicons-halflings-regular.eot);*/
}
    .wizard {
    margin: 20px auto;
    background: #fff;
}

    .wizard .nav-tabs {
        position: relative;
        margin: 40px auto;
        margin-bottom: 0;
        border-bottom-color: #e0e0e0;
    }

    .wizard > div.wizard-inner {
        position: relative;
    }

.connecting-line {
    height: 2px;
    background: #e0e0e0;
    position: absolute;
    width: 80%;
    margin: 0 auto;
    left: 0;
    right: 0;
    top: 50%;
    z-index: 1;
}

.wizard .nav-tabs > li.active > a, .wizard .nav-tabs > li.active > a:hover, .wizard .nav-tabs > li.active > a:focus {
    color: #555555;
    cursor: default;
    border: 0;
    border-bottom-color: transparent;
}

span.round-tab {
    width: 70px;
    height: 70px;
    line-height: 70px;
    display: inline-block;
    border-radius: 100px;
    background: #fff;
    border: 2px solid #e0e0e0;
    z-index: 2;
    position: absolute;
    left: 0;
    text-align: center;
    font-size: 25px;
}
span.round-tab i{
    color:#555555;
}
.wizard li.active span.round-tab {
    background: #fff;
    border: 2px solid #5bc0de;
    
}
.wizard li.active span.round-tab i{
    color: #5bc0de;
}

span.round-tab:hover {
    color: #333;
    border: 2px solid #333;
}

.wizard .nav-tabs > li {
    width: 20%;
}

.wizard li:after {
    content: " ";
    position: absolute;
    left: 46%;
    opacity: 0;
    margin: 0 auto;
    bottom: 0px;
    border: 5px solid transparent;
    border-bottom-color: #5bc0de;
    transition: 0.1s ease-in-out;
}

.wizard li.active:after {
    content: " ";
    position: absolute;
    left: 46%;
    opacity: 1;
    margin: 0 auto;
    bottom: 0px;
    border: 10px solid transparent;
    border-bottom-color: #5bc0de;
}

.wizard .nav-tabs > li a {
    width: 70px;
    height: 70px;
    margin: 20px auto;
    border-radius: 100%;
    padding: 0;
}

    .wizard .nav-tabs > li a:hover {
        background: transparent;
    }

.wizard .tab-pane {
    position: relative;
    padding-top: 50px;
}

.wizard h3 {
    margin-top: 0;
}

@media( max-width : 585px ) {

    .wizard {
        width: 90%;
        height: auto !important;
    }

    span.round-tab {
        font-size: 16px;
        width: 50px;
        height: 50px;
        line-height: 50px;
    }

    .wizard .nav-tabs > li a {
        width: 50px;
        height: 50px;
        line-height: 50px;
    }

    .wizard li.active:after {
        content: " ";
        position: absolute;
        left: 35%;
    }
}
    </style>

<div class="container">
	<div class="row">
        <div class="col-lg-12 col-md-12 about-info">
            <h2 class="about-title"><?php echo $info->title;?></h2>
            <span><?php echo $info->page_text;?></span>
        </div>
        <br /><br /><br /><br />
		<section>
        <div class="wizard">
            <div class="wizard-inner">
                <div class="connecting-line"></div>
                <ul class="nav nav-tabs" role="tablist">

                    <li role="presentation" class="active">
                        <a href="#pick_pack" data-toggle="tab" aria-controls="step_pack" role="tab" title="<?php echo lang('package_data');?>">
                            <span class="round-tab">
                                <i class="glyphicon">1</i>
                            </span>
                        </a>
                    </li>
                    
                    <li role="presentation" class="disabled">
                        <a href="#general_info" data-toggle="tab" aria-controls="step1" role="tab" title="<?php echo lang('follow_us');?>">
                            <span class="round-tab">
                                <i class="glyphicon">2</i>
                            </span>
                        </a>
                    </li>

                    <?php foreach(array_reverse($data_languages) as $index=>$lang){?>
                        <li role="presentation" class="disabled">
                            <a href="#lang_<?php echo $lang->id;?>" data-toggle="tab" aria-controls="lang_<?php echo $lang->id;?>" role="tab" title="<?php echo $lang->name;?>">
                                <span class="round-tab">
                                    <i class="glyphicon glyphicon-pencil2"><?php echo $index+3;?></i>
                                </span>
                            </a>
                        </li>
                    <?php }?>
                    <li role="presentation" class="disabled">
                        <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
                            <span class="round-tab">
                                <i class="glyphicon">5</i>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <form role="form" action="" method="post" id="form_pack">
                <div class="tab-content">
                
                    <div class="tab-pane active" role="tabpanel" id="pick_pack">
                        <h3><?php echo lang('package_data');?></h3>
                        <fieldset id="package_info">
        					<legend></legend>
        					
                            <div class="table-responsive">
        						<table class="table table-hover">
        							<thead>
        								<tr>
        									<th>#</th>
        									<th><?php echo lang('name');?></th>
        									<th><?php echo lang('description');?></th>
        									<th><?php echo lang('products_count_limit');?></th>
                                            <th><?php echo lang('users_limit');?></th>
                                            <th><?php echo lang('cost');?></th>
                                            <th><?php echo lang('value');?></th>
                                            <th class="choose-pack-input"><?php echo lang('select');?></th>
        								</tr>
        							</thead>
        							<tbody>
                                        <?php foreach($packages_data as $key=>$package){?>
            								<tr style="text-align: center;">
            									<th scope="row"><?php echo $key+1;?></th>
            									<td><?php echo $package->name;?></td>
            									<td><?php echo $package->description;?></td>
            									<td><?php echo $package->products_limit;?></td>
                                                <td><?php echo $package->users_limit;?></td>
                                                <td><?php echo $package->type;?></td>
                                                <td><?php echo $package->cost;?></td>
                                                <td><input type="radio" class="pack_id" name="package_id" value="<?php echo $package->id;?>" /></td>
            								</tr>
                                        <?php }?>
        							
        							</tbody>
        						</table>
        					</div>
                            
        					
        				</fieldset>
                        <ul class="list-inline pull-right"  style="margin: 10px;">
                            <li><button type="button" class="btn btn-primary next-step"><?php echo lang('continue');?></button></li>
                        </ul>
                    </div>
                    
                    <div class="tab-pane" role="tabpanel" id="general_info">
                        <h3><?php echo lang('follow_us');?></h3>
                        <p></p>
                        <fieldset id="account">
        					<legend></legend>
        					
                            <div class="form-group required">
        						<label class="col-sm-2 control-label" for="input-telephone"><?php echo lang('phone');?></label>
        						<div class="col-sm-10">
                                    <?php 
                                        $phone_atts = array(
                                                                'name'          => 'phone',
                                                                'placeholder'   => lang('phone'),
                                                                'id'            => 'input-phone',
                                                                'class'         => 'form-control',
                                                                'value'         => set_value('phone')
                                                            );
                                        echo form_input($phone_atts);
                                        echo form_error('phone');
                                    ?>
        						</div>
        					</div>
                            
        					<div class="form-group">
        						<label class="col-sm-2 control-label" for="input-fb"><?php echo lang('facebook');?></label>
        						<div class="col-sm-10">
        							<?php 
                                        $facebook_atts = array(
                                                                'name'          => 'facebook',
                                                                'placeholder'   => lang('facebook'),
                                                                'id'            => 'input-facebook',
                                                                'class'         => 'form-control',
                                                                'value'         => set_value('facebook')
                                                            );
                                        echo form_input($facebook_atts);
                                        echo form_error('facebook');
                                    ?>
        						</div>
        					</div>
                            
        					<div class="form-group ">
        						<label class="col-sm-2 control-label" for="input-twitter"><?php echo lang('twitter');?></label>
        						<div class="col-sm-10">
        							<?php 
                                        $twitter_atts = array(
                                                                'name'          => 'twitter',
                                                                'placeholder'   => lang('twitter'),
                                                                'id'            => 'input-twitter',
                                                                'class'         => 'form-control',
                                                                'value'         => set_value('twitter')
                                                            );
                                        echo form_input($twitter_atts);
                                        echo form_error('twitter');
                                    ?>
        						</div>
        					</div>
                            
        					<div class="form-group ">
        						<label class="col-sm-2 control-label" for="input-instagram"><?php echo lang('instagram');?></label>
        						<div class="col-sm-10">
        							<?php 
                                        $instagram_atts = array(
                                                                'name'          => 'instagram',
                                                                'placeholder'   => lang('instagram'),
                                                                'id'            => 'input-instagram',
                                                                'class'         => 'form-control',
                                                                'value'         => set_value('instagram')
                                                            );
                                        echo form_input($instagram_atts);
                                        echo form_error('instagram');
                                    ?>
        						</div>
        					</div>
                            
        					<div class="form-group ">
        						<label class="col-sm-2 control-label" for="input-youtube"><?php echo lang('youtube');?></label>
        						<div class="col-sm-10">
        							<?php 
                                        $youtube_atts = array(
                                                                'name'          => 'youtube',
                                                                'placeholder'   => lang('youtube'),
                                                                'id'            => 'input-youtube',
                                                                'class'         => 'form-control',
                                                                'value'         => set_value('youtube')
                                                            );
                                        echo form_input($youtube_atts);
                                        echo form_error('youtube');
                                    ?>
        						</div>
        					</div>
        				</fieldset>
                        <ul class="list-inline pull-right" style="margin: 10px;">
                            <li><button type="button" class="btn btn-default prev-step"><?php echo lang('previous');?></button></li>
                            <li><button type="button" class="btn btn-primary next-step"><?php echo lang('continue');?></button></li>
                        </ul>
                    </div>
                    
                    <?php foreach($data_languages as $lang){?>
                        <div class="tab-pane" role="lang_<?php echo $lang->id;?>" id="lang_<?php echo $lang->id;?>">
                            <h3><?php echo lang('store_data').' '.$lang->name;?> <img src="<?php echo base_url();?>assets/template/admin/global/img/flags/<?php echo $lang->flag ;?>" /></h3>
                            <fieldset id="address">
            					<legend></legend>
                                
                                <div class="form-group required">
            						<label class="col-sm-2 control-label" for="input-name_<?php echo $lang->id;?>"><?php echo lang('name_of_store');?></label>
            						<div class="col-sm-10">
            							<?php 
                                            $name_atts = array(
                                                                    'name'          => 'name['.$lang->id.']',
                                                                    'placeholder'   => lang('name').' '.$lang->name,
                                                                    'id'            => 'input-name'.$lang->id,
                                                                    'class'         => 'form-control',
                                                                    'value'         => set_value('name['.$lang->id.']')
                                                                );
                                            echo form_input($name_atts);
                                            echo form_error('name['.$lang->id.']');
                                        ?>
            						</div>
            					</div>
                                
                                <div class="form-group required">
            						<label class="col-sm-2 control-label" for="input-address<?php echo $lang->id;?>"><?php echo lang('address');?></label>
            						<div class="col-sm-10">
            							<?php 
                                            $address_atts = array(
                                                                    'name'          => 'address['.$lang->id.']',
                                                                    'placeholder'   => lang('address').' '.$lang->name,
                                                                    'id'            => 'input-address'.$lang->id,
                                                                    'class'         => 'form-control',
                                                                    'value'         => set_value('address['.$lang->id.']')
                                                                );
                                            echo form_input($address_atts);
                                            echo form_error('address['.$lang->id.']');
                                        ?>
            						</div>
            					</div>
                                
                                 <div class="form-group required">
            						<label class="col-sm-2 control-label" for="input-description<?php echo $lang->id;?>"><?php echo lang('description');?></label>
            						<div class="col-sm-10">
                                        <?php 
                                          $des_atts = array(
                                                            'name'          => 'description['.$lang->id.']',
                                                            'rows'          => 10,
                                                            'placeholder'   => lang('description').' '.$lang->name,
                                                            'id'            => 'input-description'.$lang->id ,
                                                            'class'         => 'form-control',
                                                            'value'         => set_value('description['.$lang->id.']')
                                                        );
                                          echo form_textarea($des_atts);
                                          echo form_error('description['.$lang->id.']');
                                        ?>
            						</div>
            					</div>
            				</fieldset>
                            
                            <?php echo form_hidden('lang_id[]', $lang->id);?>
                            <ul class="list-inline pull-right" style="margin: 10px;">
                                <li><button type="button" class="btn btn-default prev-step"><?php echo lang('previous');?></button></li>
                                <li><button type="button" class="btn btn-primary next-step"><?php echo lang('continue');?></button></li>
                            </ul>
                        </div>
                    <?php }?>
                    
                    <div class="tab-pane" role="tabpanel" id="complete">
                        <h3><?php echo lang('process_completed');?></h3>
                        <p><?php echo lang('finish_store_msg');?></p>
                        <p class="form-msg"></p>
                        
                        <input type="hidden" name="user_store" value="<?php echo $new_user_id;?> " />
                        <button type="button" class="btn btn-default prev-step"><?php echo lang('previous');?></button>
                        <button type="button" id="finish" class="btn btn-primary green"><?php echo lang('save');?></button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </section>
   </div>
</div>

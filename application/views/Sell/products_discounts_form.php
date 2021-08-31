<div class="right-content">
    <?php if(isset($error_msg)){?>
        <span class="error"><?php echo $error_msg;?></span>
    <?php }else{?> 
        <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;};?></span>
        <?php
        if(!empty($countries)){
            echo form_open_multipart($form_action);
        ?>
            <span style="margin: 5px;"><?php echo lang('products_discounts_tabs');?></span>
            <div class="list">
                <?php
                $index=0; 
                foreach($countries as $key=> $country)
                { ?>
                    <div class="relate">
                        <a href="#addA_<?php echo $country->country_id; ?>" <?php echo $index==0?'class="active"':'';?> >
                            <img alt="" style="width:20px;height:15px;"  src="<?php echo base_url();?>assets/uploads/<?php echo $country->flag; ?>" />
                            <span class="langname"><?php echo $country->name; ?> </span>
                        </a>
                    </div>
                <?php
                    $index++;
                } ?>
            </div>
            
            
            <div class="add">
                <?php 
                $index=0;
                foreach($countries as $key=> $country){
                    echo isset($general_data->id) ? form_hidden("product_discount_id[$general_data->id]", $general_data->id) : '';
                ?>
                <div id="addA_<?php echo $country->country_id; ?>" class="relateDiv row <?php echo ($key==0)?'active':'';?>" style="display: flex!important;<?php //echo $index==0?'display: flex;':'';?>">
                    <div class="col-md-12">
                    
                        <div class="form-group">
                            <div class="row no-gutters">
                                <label class="col-md-2"><?php echo lang('product_name');?></label>
                                <?php
                                    $name_data = array('name'=>"name" , 'class'=>"form-control col-md-10" ,'readonly'=>'true' , 'value'=> isset($product_name)? $product_name : set_value("name"));
                                    echo form_input($name_data);
                                ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row no-gutters">
                                <label class="col-md-2"><?php echo lang('current_quantity');?></label>
                                <?php
                                    $current_quantity_data = array('name'=>"current_quantity[$country->country_id]" , 'class'=>"form-control col-md-10" ,'readonly'=>'true' , 'value'=> isset($country->available_serials)? $country->available_serials : 00);
                                    echo form_input($current_quantity_data);
                                ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row no-gutters">
                                <label class="col-md-2"><?php echo lang('price_before_discount');?></label>
                                <?php
                                    $price_data = array('name'=>"price[$country->country_id]" , 'class'=>"form-control col-md-10" ,'readonly'=>'true' , 'value'=> isset($country->country_price)? $country->country_price : 00);
                                    echo form_input($price_data);
                                ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row no-gutters">
                                <label class="col-md-2"><?php echo lang('currency');?></label>
                                <?php
                                    $currency_data = array('name'=>"currency[$country->country_id]" , 'class'=>"form-control col-md-10" ,'readonly'=>'true' , 'value'=> isset($country->currency )? $country->country_symbol  : set_value("currency"));
                                    echo form_input($currency_data);
                                ?>
                            </div>
                        </div>
                        
                        <script type="text/javascript">
                        
                        $(function(){
                            $(".price_spinner_<?php echo $country->country_id; ?>").TouchSpin({          
                                buttondown_class: 'btn green',
                                buttonup_class: 'btn green',
                                //min: 0,
                                max: 1000000000,
                                stepinterval: 1,
                                maxboostedstep: 1,
                                step: .1
                               
                            }); 
                        })
                        
                        </script>
                        
                        <div class="form-group">
                            <div class="row no-gutters">
                                <label class="col-md-2">
                                    <?php echo lang('price_after_discount');?><span class="required">*</span>
                                </label>
                                <div class="col-md-10">
                                    <?php
                                        $price_after_data = array('name'=>"price[$country->country_id]" , 'required'      => 'required', 'class'=>"form-control price_spinner_". $country->country_id , 'value'=> isset($country->price)? $country->price : set_value("price[$country->country_id]"));
                                        echo form_input($price_after_data);
                                        echo form_error("price[$country->country_id]");
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row no-gutters">
                                <label class="col-md-2">
                                    <?php echo lang('discount_period');?><span class="required">*</span>
                                </label>
                                <div class="input-group input-large date-picker input-daterange" data-date="11-10-2012" data-date-format="dd-mm-yyyy">
        								
                                    <?php 
                                        $date_start_data = array('name' => "from[$country->country_id]" ,'class'=>"form-control" , 'value'=>isset($country->discount_start_unix_time)?date('d-m-Y',$country->discount_start_unix_time) : set_value("from[$country->country_id]") );
                                        echo form_input($date_start_data);
                                    ?>
    								
                                    <span class="input-group-addon"><?php echo lang('to');?></span>
                                    
                                    <?php 
                                        $date_end_data = array('name' => "to[$country->country_id]" , 'required'      => 'required','class'=>"form-control" , 'value'=>isset($country->discount_end_unix_time) ? date('d-m-Y', $country->discount_end_unix_time) : set_value("to[$country->country_id]") );
                                        echo form_input($date_end_data);
                                    ?>
    								
    							</div>
    							<?php 
                                    echo form_error("from[$country->country_id]");
                                    echo form_error("to[$country->country_id]");
                                ?>
                            </div>
                        </div>
                        
                        <script type="text/javascript">
                                    
                             $(function(){
                                $(".spinner1_<?php echo $country->country_id; ?>").TouchSpin({          
                                    buttondown_class: 'btn main-button',
                                    buttonup_class: 'btn main-button',
                                    //min: 0,
                                    max: 1000000000,
                                    stepinterval: 1,
                                    maxboostedstep: 1
                                }); 
                             })
                        </script>
                        
                        <div class="form-group">
                            <div class="row no-gutters">
                                <label class="col-md-2">
                                    <?php echo lang('max_units_customers');?><span class="required">*</span>
                                </label>
                                <div class="col-md-10">
                                    <?php
                                        $spinner_data = array('name'=>"max_units_customers[$country->country_id]" , 'required'      => 'required', 'class'=>"form-control spinner1_". $country->country_id , 'value'=> isset($country->max_units_customers) ? $country->max_units_customers : set_value("max_units_customers[$country->country_id]"));
                                        echo form_input($spinner_data);
                                        echo form_error("max_units_customers[$country->country_id]");
                                    ?>
                                    <span class="error"><?php echo lang('zero_hint');?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-item">
                                <label><?php echo lang('active');?><span class="required">*</span></label>
                                <div class="checkbox kuwait-div">
                                    <label for="active">
                                        <?php 
                                        echo form_error("active[$country->country_id]");
                                        $active_value     = true ;
                                        
                                        if(isset($country->active)) 
                                        {
                                            if($country->active == 1)
                                            {
                                                $active_value = true;
                                            }
                                            if($country->active == 0)
                                            {
                                                $active_value = false;
                                            }
                                        }  
                                        
                                        $active_data = array(
                                                                'name'           => "active[$country->country_id]",
                                                                'class'          => 'form-control',
                                                                'value'          => 1,
                                                                'data-toggle'    => 'toggle'                        ,
                                                                'checked'        => set_checkbox("active[$country->country_id]", $active_value, $active_value)
                                                            );  
                                                    
                                        echo form_checkbox($active_data);
                                        
                                            ?>
                                        
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        </div>
                        </div>
                            
                    <?php 
                        echo form_hidden('country_id[]', $country->country_id);
                        $index++;
                    } ?>
                    
                    <div class="form-group">
                        <div class="row no-gutters align-items-left">
                            <div class="col-md-12">
                                <?php echo form_hidden('product_id' ,  $product_id) ; ?>
                                <?php echo isset($last_updated) ? form_hidden('last_updated', $last_updated) : ''; ?>
                                <button class="button"><?php echo lang('submit');?></button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
    
        <?php echo form_close();
        }else{?>
            <div class="title">
                <h3><?php echo lang('no_data');?></h3>
            </div>
        <?php }
        } ?>
</div>
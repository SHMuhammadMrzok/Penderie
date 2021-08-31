<div class="form">
    <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>

    <?php
        $att = array('class'=> 'form-horizontal form-bordered');
        echo form_open_multipart($form_action, $att);
    ?>
    <div class="tabbable-custom form">

    	<div class="tab-content">
            <div class="form-group">
               <label class="control-label col-md-3"><?php echo lang('free');?></label>
               <div class="col-md-4">
                 <?php

                    $free_value = true ;
                    if($mode == 'edit' && !isset($validation_msg))
                    {
                        if($general_data->free == 1)
                        {
                            $free_value = true;
                        }
                        if($general_data->free == 0)
                        {
                            $free_value = false;
                        }
                    }

                    if($mode == 'add' && !isset($validation_msg))
                    {
                        $free_value = true;
                    }

                    $free_data = array(
                                'name'           => "free",
                                'class'          => 'make-switch',
                                'data-on-color'  => 'danger',
                                'data-off-color'  => 'default',
                                'value'          => 1,
                                'checked'        => set_checkbox("free", 1, $free_value),
                                'data-on-text'   => lang('yes'),
                                'data-off-text'  => lang('no'),
                                );
                    echo form_checkbox($free_data);
                 ?>
                </div>
            </div>

            <?php /*<div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('is_moquette');?></label>
                <div class="col-md-4">
                 <?php
                    $is_moquette_value = false ;

                    if(isset($general_data->is_moquette))
                    {
                        if($general_data->is_moquette == 1)
                        {
                            $is_moquette_value = true;
                        }
                        if($general_data->is_moquette == 0)
                        {
                            $is_moquette_value = false;
                        }
                    }

                    $is_moquette_data = array(
                                'name'           => "is_moquette",
                                'class'          => 'make-switch',
                                'data-on-color'  => 'danger',
                                'data-off-color' => 'default',
                                'value'          => 1,
                                'checked'        => set_checkbox("is_moquette", $is_moquette_value, $is_moquette_value),
                                'data-on-text'   => lang('yes'),
                                'data-off-text'  => lang('no'),
                                );
                    echo form_checkbox($is_moquette_data);
                 ?>
                </div>
            </div>
            */?>

            <div class="form-group">
                <label class="control-label col-md-3"><?php echo lang('has_weight');?></label>
                <div class="col-md-4">
                 <?php
                    $has_weight_value = false ;

                    if(isset($general_data->has_weight))
                    {
                        if($general_data->has_weight == 1)
                        {
                            $has_weight_value = true;
                        }
                        if($general_data->has_weight == 0)
                        {
                            $has_weight_value = false;
                        }
                    }

                    $has_weight_data = array(
                                'name'           => "has_weight",
                                'class'          => 'make-switch',
                                'data-on-color'  => 'danger',
                                'data-off-color' => 'default',
                                'value'          => 1,
                                'checked'        => set_checkbox("has_weight", $has_weight_value, $has_weight_value),
                                'data-on-text'   => lang('yes'),
                                'data-off-text'  => lang('no'),
                                );
                    echo form_checkbox($has_weight_data);
                 ?>
                </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3"><?php echo lang('has_qty');?></label>
            <div class="col-md-4">
             <?php
                $has_qty_value = false ;

                if(isset($general_data->has_qty))
                {
                    if($general_data->has_qty == 1)
                    {
                        $has_qty_value = true;
                    }
                    if($general_data->has_qty == 0)
                    {
                        $has_qty_value = false;
                    }
                }

                $has_qty_data = array(
                            'name'           => "has_qty",
                            'class'          => 'make-switch',
                            'data-on-color'  => 'danger',
                            'data-off-color' => 'default',
                            'value'          => 1,
                            'checked'        => set_checkbox("has_qty", $has_qty_value, $has_qty_value),
                            'data-on-text'   => lang('yes'),
                            'data-off-text'  => lang('no'),
                            );
                echo form_checkbox($has_qty_data);
             ?>
            </div>
        </div>

<?php /*
            <div class="form-group">
               <label class="control-label col-md-3"><?php echo lang('secondary');?></label>
               <div class="col-md-4">
                 <?php

                    $secondary_value = false ;
                    if($mode == 'edit' && !isset($validation_msg))
                    {
                        if($general_data->secondary == 1)
                        {
                            $secondary_value = true;
                        }
                        if($general_data->secondary == 0)
                        {
                            $secondary_value = false;
                        }
                    }

                    if($mode == 'add' && !isset($validation_msg))
                    {
                        $secondary_value = false;
                    }

                    $secondary_data = array(
                                'name'           => "secondary",
                                'class'          => 'make-switch',
                                'data-on-color'  => 'danger',
                                'data-off-color'  => 'default',
                                'value'          => 1,
                                'checked'        => set_checkbox("secondary", 1, $secondary_value),
                                'data-on-text'   => lang('yes'),
                                'data-off-text'  => lang('no'),
                                );
                    echo form_checkbox($secondary_data);
                 ?>
                </div>
            </div>
            */?>
            <?php foreach($data_languages as $key=> $lang){?>

                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('label');?>
                     <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" />
                     <span class="required">*</span>
                   </label>
                   <div class="col-md-4">
                      <?php
                        echo form_error("label[$lang->id]");
                        $label_data = array('name'=>"label[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->label)? $data[$lang->id]->label : set_value("label[$lang->id]"));
                        echo form_input($label_data);
                      ?>
                    </div>
                </div>

                <div class="form-group">
                   <label class="control-label col-md-3">
                     <?php echo lang('text');?>
                     <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" />
                     <span class="required"></span>
                   </label>
                   <div class="col-md-4">
                      <?php
                        echo form_error("text[$lang->id]");
                        $text_data = array('name'=>"text[$lang->id]" , 'class'=>"form-control" , 'value'=> isset($data[$lang->id]->text)? $data[$lang->id]->text : set_value("text[$lang->id]"));
                        echo form_textarea($text_data);
                      ?>
                    </div>
                </div>
                <input type="hidden" name="lang_id[]" value="<?php echo $lang->id;?>" />
            <?php }?>

            <div class="">
    	      <div class="form-body">

                    <div class="form-group">
                       <label class="control-label col-md-3">
                         <?php echo lang('priority');?>
                       </label>
                       <div class="col-md-4">
                          <?php
                            echo form_error("priority");
                            $priority_data = array('name'=>"priority" , 'class'=>"form-control" , 'value'=> isset($general_data->priority)? $general_data->priority : set_value("priority"));
                            echo form_input($priority_data);
                          ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">
                          <?php echo lang('type');?><span class="required">*</span>
                        </label>
                       <div class="col-md-4">

                           <select class="form-control select2" id="types" name="type_id">
                               <option>----------------------</option>
                               <?php
                                foreach($types as $type)
                                {
                                    $selected = '';
                                    if(isset($general_data->field_type_id) &&($type->id == $general_data->field_type_id))
                                    {
                                        $selected = 'selected';
                                    }
                                    elseif(isset($_POST['type_id']) && $type->id == $_POST['type_id'])
                                    {
                                        $selected = 'selected';
                                    }
                                    ?>

                                    <option value="<?php echo $type->id;?>" <?php echo $selected;?> data-options="<?php echo $type->has_options;?>" data-value="<?php echo $type->has_value;?>"><?php echo $type->type_name;?></option>
                               <?php }?>
                           </select>
                           <?php echo form_error('type_id');?>
                       </div>
                       <div class="option_input"><?php if(isset($general_data)){?><input type="hidden" name="has_options" value="<?php echo $general_data->has_options;?>" /><?php }?></div>
                       <div class="value_input"><?php if(isset($general_data)){?><input type="hidden" name="has_value" value="<?php echo $general_data->has_value;?>" /><?php }?></div>
                     </div>

                     <div><?php echo form_error('option_value');?></div>
                     <div id="options_div" style="display: <?php echo (isset($validation_msg) && isset($has_options))|| (isset($option_options) && count($option_options) != 0) ? '' : 'none';?>;">
                        <div class="table-responsive">
            				<table class="table table-striped table-hover table-bordered" id="products_table">
                				<thead>
                				    <tr>
                                        <th colspan="2"><?php echo lang('option_value');?></th>
                                        <th><?php echo lang('priority');?></th>
                                        <th></th>
                                    </tr>
                				</thead>
                				<tbody id="purchase_orders_products">
                                    <?php //echo '<pre>'; print_r($option_options); die();
                                     if(isset($option_options) && count($option_options) != 0)
                                     {
                                         foreach($option_options as $row){?>

                                            <tr class="option_row_data">
                                                <?php foreach ($data_languages as $key=> $lang){?>
                                                    <td>
                                                        <img alt="" src="<?php echo base_url();?>/assets/template/admin/global/img/flags/<?php echo $lang->flag; ?>" /><span class="required">*</span>

                                                        <?php
                                                            echo form_error("option_value[$lang->id][".$row[$lang->id]->id."]");
                                                            $option_value_data = array(
                                                                                        'name'        => "option_value[$lang->id][".$row[$lang->id]->id."]" ,
                                                                                        'class'       => "form-control" ,
                                                                                        'placeholder' => $lang->name,
                                                                                        'value'       => isset($row[$lang->id]->field_value)? $row[$lang->id]->field_value : set_value("option_value[$lang->id][]")
                                                                                      );

                                                            echo form_input($option_value_data);
                                                        ?>
                                                    </td>
                                                <?php }?>


                                                <td>
                                                    <?php
                                                        echo form_error("sort[]");
                                                        $sort_data = array(
                                                                            'name'  => "sort[".$row[$lang->id]->id."]" ,
                                                                            'class' => "form-control" ,
                                                                            'value' => isset($row[$lang->id]->priority) ? $row[$lang->id]->priority : set_value("sort[]")
                                                                          );

                                                        echo form_input($sort_data);
                                                    ?>
                                                </td>

                                                <td><button class="btn btn-sm red filter-cancel btn-warning remove_option" data-opt_id="<?php echo $row[$lang->id]->id;?>"  data-toggle="confirmation" id="bs_confirmation_demo_1" ><i class="fa fa-times"></i> <?php echo lang('delete');?></button></td>
                                            </tr>
                                        <?php }
                                     } ?>

                                    <tr class="add_option_row">
                                        <td colspan="4" style="text-align: center;"><button class="btn btn-sm blue btn-info add_option"  data-toggle="confirmation"><i class="fa fa-plus-square-o"></i> <?php echo lang('add_option');?></button></td>
                                    </tr>
                                </tbody>
            				</table>
            			</div>
                    </div>

                </div>
             </div>

            <?php echo isset($id) ? form_hidden('id', $id) : '';?>
            <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
    					<button type="submit" class="btn green"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
    				</div>
    			</div>
            </div>

    	</div>
    </div>
    <?php echo form_close();?>
</div>

<script>

    // show options table
    $('#types').change(function(){

        var hasOptions  = $(this).find(':selected').data('options');
        var hasValue    = $(this).find(':selected').data('value');

        var optionInput = '<input type="hidden" value ='+hasOptions+' name="has_options"/>';
        var valueInput  = '<input type="hidden" value ='+hasValue+' name="has_value"/>';

        if(hasOptions == 1)
        {
            $('#options_div').show();
        }
        else
        {
            $('#options_div').hide();
        }

        $('.option_input').html(optionInput);
        $('.value_input').html(valueInput);
    });

    /**************************************/
    // optioal fields options rows

    $('.add_option').click(function(event){
        event.preventDefault();

        optionRow = '<tr class="option_row"><?php foreach ($data_languages as $key=> $lang){?>
            <td><img alt="" src="<?php echo base_url();?>assets/template/admin/global/img/flags/<?php echo $lang->flag?>" /><input type="text" name="new_option_value[<?php echo $lang->id;?>][]" value="" class="form-control" placeholder="<?php echo $lang->name;?>"></td><?php }?>
            <td><input type="text" name="new_sort[]" value="" class="form-control"></td><td><button class="btn btn-sm red filter-cancel btn-warning remove_option" data-toggle="confirmation"><i class="fa fa-times"></i><?php echo lang('delete');?></button></td></tr>';

        $('.add_option_row').before(optionRow);
    });

    /**************************************/
    // remove row
    $('body').on('click', '.remove_option', function(event){
        event.preventDefault();

        <?php if($mode == 'add'){?>
            $(this).closest('tr').remove();
        <?php }else if($mode == 'edit'){?>

            var tr_id = $(this).data('opt_id');

            if(typeof tr_id != 'undefined')
            {
                postData = {optional_field_option_id : tr_id}
                //delete exist row
                $.post('<?php echo base_url().'optional_fields/optional_fields/delete_option_option';?>', postData, function(result){

                    if(result == "1")
                    {
                        //$(this).closest('tr').remove();
                        location. reload(true);
                    }
                    else
                    {
                        alert('<?php echo lang('delete_option_error');?>');
                    }
                });
                //alert(tr_id);
            }
            else
            {
                //delete new added row
               $(this).closest('tr').remove();
            }

        <?php }?>
    });

</script>

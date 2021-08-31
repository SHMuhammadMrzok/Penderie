<style>
    .loading_modal {
    display:     none;
    position:    fixed;
    z-index:     1000;
    top:         0;
    left:        0;
    height:      750px;/*100%;*/
    width:       900px;/*100%;*/
    margin-left: 300px;
    background:  rgba( 255, 255, 255, .8 )
                 url('<?php echo base_url().'assets/ajax-loader.gif';?>')
                 50% 50%
                 no-repeat;
}

body.loading {
    overflow: hidden;
}

body.loading .loading_modal {
    display: block;
}
</style>
<div class="form">
    <span class="error"><?php if(isset($validation_msg)) echo $validation_msg.'<br />'.validation_errors();?></span>
    <?php $att=array('class'=> 'form-horizontal form-bordered cmxform','id'=>'order_form' );
          echo form_open_multipart($form_action, $att);?>

    <div class="form-body">

        <div class="form-group">
            <label class="control-label col-md-3">
              <?php  echo lang('vendor');?><span class="required">*</span>
            </label>
           <div class="col-md-4" >
                <?php
                    echo form_error("vendor_id");
                    $vendor_id = isset($purchase_orders_data->vendor_id) ? $purchase_orders_data->vendor_id : set_value('vendor_id') ;
                    echo form_dropdown('vendor_id', $vendors_array,$vendor_id,'class="form-control select2" id="vendor_id"');
               ?>
           </div>
           <div id="vendor_message" style="display: none; color: red;font-weight: bold;"><?php echo lang('choose_vendor');?></div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3">
                <?php echo lang('order_number');?><span class="required">*</span>
            </label>
           <div class="col-md-4">
              <?php
                  $order_number_data = array('name'=>'order_number','class'=>"form-control order_number" , 'value'=> isset($purchase_orders_data->order_number)? $purchase_orders_data->order_number : set_value('order_number'));
                  echo form_input($order_number_data);
                  echo form_error('order_number');
              ?>
           </div>
        </div><!--Purchase Order Number-->

        <div class="form-group">
            <label class="control-label col-md-3">
              <?php echo lang('addproduct');?><span class="required">*</span>
            </label>
           <div class="col-md-4">
               <a class="btn default" data-toggle="modal" href="#responsive" id="modal_popup" ><?php echo lang('add_product');?></a>
           </div>
           <div class="col-md-4">
             <label class="control-label col-md-3">
               <?php echo lang('upload');?><span class="required"></span>
             </label>
             <input type="file" name="userfile" class="upload_field"  />
           </div>
        </div>



        <div class="form-group">
           <label class="control-label col-md-3"><?php echo lang('products');?></label>
           <div class="col-md-12">
           <div style="<?php if($mode == 'edit' && isset($purchase_orders_products_data) && count ($purchase_orders_products_data) != 0){?>display: none;<?php }?>" id="no_product_data">
                <?php echo lang('no_products');?>
            </div>

            <div id="product_data" style="<?php if($mode == 'edit'&& isset($purchase_orders_products_data) && count ($purchase_orders_products_data) != 0){?>display: block;<?php }else{?>display: none;<?php }?>">
                <div class="table-responsive">
          				<table class="table table-striped table-hover table-bordered" id="products_table">
              				<thead>
              				    <tr>
                                      <th><?php echo lang('product_name');?></th>
                                      <th><?php echo lang('country_name');?></th>
                                      <th><?php echo lang('quantity');?></th>
                                      <th><?php echo lang('name_of_store');?></th>
                                      <th><?php echo lang('price_per_unit');?></th>
                                      <th></th>
                                  </tr>
              				</thead>
              				<tbody id="purchase_orders_products">
                                  <?php if(isset($purchase_orders_products_data) && count ($purchase_orders_products_data) != 0){ ?>
                                    <?php foreach($purchase_orders_products_data as  $row){?>
                                      <tr id="product_row_<?php echo $row->product_id."_".$row->country_id."_".$row->store_id ; ?>" class="product_row_class">
                                          <td><label><?php echo $row->title ; ?></label></td>
                                          <td><label><?php $country = isset($row->name) && $row->name != '' ? $row->name : lang('global_quantitiy'); echo $country;  ; ?></label></td>
                                          <td><label><?php echo $row->quantity ; ?></label></td>
                                          <td><label><?php echo $row->price_per_unit ."  ".$purchase_orders_data->currency_symbol ; ?></label></td>

                                          <input type="hidden" name="purchase_order_id" value="<?php echo $row->purchase_order_id ; ?>" />
                                          <input type="hidden" name="product_id[]" value="<?php echo $row->product_id ; ?>" />
                                          <input type="hidden" name="quantity[]" value="<?php echo $row->quantity ; ?>" />
                                          <input type="hidden" name="price_per_unit[]" value="<?php echo $row->price_per_unit ; ?>" />
                                          <input type="hidden" name="country_id[]" value="<?php echo $row->country_id ; ?>" />
                                          <input type="hidden" name="store_id[]" value="<?php echo $row->store_id ; ?>" />

                                          <td><button class="btn btn-sm red filter-cancel btn-warning product_row_<?php echo $row->product_id ; ?>"  data-toggle="confirmation" id="bs_confirmation_demo_1" ><i class="fa fa-times"></i> <?php echo lang('delete');?></button></td>
                                      </tr>
                                  <?php } }?>
                              </tbody>
          				</table>
    			    </div>
            </div>
           </div>
        </div> <!-- form_group -->

        <input type="hidden" name="order_store_id" class="order_store_id" />

         <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                <?php  echo isset($id) ? form_hidden('purchase_order_id', $id) : ''; ?>
                <?php  echo isset($purchase_orders_data->order_number) ? form_hidden('purchase_order_number', $purchase_orders_data->order_number) : ''; ?>
                <input type="hidden" value="0" name="draft" id="draft" />
                <button type="submit" name="order"  id="form_submit_button" class="btn green" value="0"><i class="fa fa-check"></i><?php echo lang('submit');?></button>
                <?php /* 
                <button type="submit" name="draft"  id="form_draft_button" class="btn defaulte" value="1"><?php echo lang('save_draft');?></button> 
                */ ?>

            </div>
    			</div>
        </div>

    </div>
    <?php echo form_close();?>
    <!-- /.modal -->
    <?php $att=array('class'=> 'form-horizontal form-bordered cmxform','id'=>'modal_form' );
      echo form_open_multipart('',$att);?>
        <div id="responsive" class="modal fade" tabindex="-1" aria-hidden="true">
        	<div class="modal-dialog">
        		<div class="modal-content">
                    <style>
                .loading_modal2 {
                display:     none;
                position:    fixed;
                z-index:     1000;
                top:         0;
                left:        0;
                height:      100%;/*750px;*/
                width:       100%;/*900px;*/
                background:  rgba( 255, 255, 255, .8 )
                             url('<?php echo base_url().'assets/ajax-loader.gif';?>')
                             50% 50%
                             no-repeat;
            }

            body.loading2 {
                overflow: hidden;
            }

            body.loading2 .loading_modal2 {
                display: block;
            }
            </style>
            <div class="loading_modal2"><!-- Place at bottom of page --></div>
            <script>
                    $(document).on({
                ajaxStart: function() { $('body').addClass("loading2");   },
                ajaxStop : function() { $('body').removeClass("loading2"); }
            });
            </script>
        			<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        				<h4 class="modal-title"><?php echo lang('products');?></h4>
        			</div>
        			<div class="modal-body">
        			  <div class="scroller" style="height:400px" data-always-visible="1" data-rail-visible1="1">
        				 <div class="row">
                            <div class="form-body">
                                <div id="product_data_message">
                                   <!-- <p id="required_quantity" style="display: none;color: red;font-weight: bold;  text-align: center;"><?php echo lang("required_quantity"); ?></p>
                                    <p id="required_price_per_unit" style="display: none;color: red;font-weight: bold;  text-align: center;"><?php echo lang("required_price"); ?></p>
                                    <p id="required_product" style="display: none;color: red;font-weight: bold;  text-align: center;"><?php echo lang("required_product"); ?></p>
                                    <p id="required_country_id" style="display: none;color: red;font-weight: bold;  text-align: center;"><?php echo 'country required ';// lang("required_country"); ?></p>-->

                                    <p id="msg_span" style="color: red;font-weight: bold;  text-align: center;"></p>
                                </div>

                                <div class="form-group">
                                   <label class="control-label col-md-3">
                                     <?php echo lang('name_of_store');?>
                                   </label>
                                   <div class="col-md-4" id="stores_div">
                                      <?php
                                       /*  echo form_error("store_id");
                                        $store_id = 0;//set_value('store_id') ;
                                        echo form_dropdown('store_id', $stores, $store_id, 'class="form-control select2" id="store_id"');
                                        */
                                      ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                   <label class="control-label col-md-3">
                                     <?php echo lang('quantity');?>
                                   </label>
                                   <div class="col-md-4">
                                      <?php
                                            echo form_error("quantity");
                                            $quantity_data = array('name'=>"quantity" , 'class'=>"form-control quantity_spinner");
                                            echo form_input($quantity_data);
                                      ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                   <label class="control-label col-md-3">
                                     <?php echo lang('price_per_unit');?>
                                   </label>
                                   <div class="col-md-4">
                                      <?php
                                            echo form_error("price_per_unit");
                                            $price_per_unit_data = array('name'=>"price_per_unit" , 'class'=>"form-control price_spinner");
                                            echo form_input($price_per_unit_data);
                                      ?>

                                    </div>
                                    <div class="col-md-4">
                                        <div >
                                        <?php if($mode == 'edit'){?>
                                        <input type="text" class="form-control" name="default_currency" value="<?php echo $purchase_orders_data->currency_symbol;?>" readonly="true" />
                                        <?php }else{?>
                                            <input type="text" class="form-control" name="default_currency" value="<?php echo $currency_symbol;?>" readonly="true" />
                                        <?php }?>
                                        </div>
                                    </div>
                                </div>


                                 <div class="form-group">
                                   <label class="control-label col-md-3">
                                     <?php echo lang('category');?>
                                   </label>
                                   <div class="col-md-4" id="cats_div">
                                      <?php
                                     //    echo form_error("category");
                                     //   $cat_id = set_value('cat_id') ;
                                     //   echo form_dropdown('cat_id', $cats_array, $cat_id, 'class="form-control select2" id="cat_id"');
                                      ?>
                                    </div>
                                </div>
                                <div class="form-group" id="products_div" style="display: none;">
                                    <label class="control-label col-md-3">
                                      <?php echo lang('products');?>
                                    </label>
                                   <div class="col-md-4">

                                    <?php
                                        echo form_error("product_id");
                                        $product_id = set_value('product_id') ;
                                        echo form_dropdown('product_id', $products_options, $product_id, 'class="form-control select2" id="products"');
                                    ?>
                                 </div>
                                 <label id="product_serials" style="margin: 20px;color: red;font-weight: bold;display: none;"></label>
                                </div>

                                <div class="" id="optional_fields_div" style="display: none;">
                                    <?php /*<label class="control-label col-md-3">
                                      <?php echo ('optional_fields');?>
                                    </label>
                                   <div class="col-md-4">

                                    <?php
                                        echo form_error("optional_fields");
                                        $optional_fields = set_value('optional_fields') ;
                                        echo form_dropdown('optional_fields', array(), $optional_fields, 'class="form-control select2" id="optional_fields"');
                                    ?>
                                 </div>
                                 */?>

                                </div>

                                <div class="form-group" id="country_div" style="display: none;">
                                    <label class="control-label col-md-3">
                                      <?php echo lang('country');?>
                                    </label>
                                   <div class="col-md-4">

                                    <?php
                                        echo form_error("country_id");
                                        $country_id = set_value('country_id') ;
                                        echo form_dropdown('country_id', $countries_options,$country_id,'class="form-control select2" id="country_id"');
                                    ?>
                                 </div>
                                </div>


                            </div><!-- form-body -->
        			      </div><!-- row -->
        			  </div>
        			</div>
        			<div class="modal-footer">
        				<button type="button" data-dismiss="modal" class="btn default" id="close_modal"><?php echo lang('close');?></button>
        				<button type="button" class="product_modalbtn green" id="product_modal"><?php echo lang('save');?></button>
        			</div>
        		</div>
        	</div>
        </div>
    <?php echo form_close();?>
    <!-- modal -->
</div>
<div class="loading_modal"><!-- Place at bottom of page --></div>
<script>
$(function(){
    $(".quantity_spinner").TouchSpin({
        buttondown_class: 'btn green',
        buttonup_class: 'btn green',
        min: 1,
        max: 1000000000,
        stepinterval: 1,
        maxboostedstep: 1,

    });
});
 ///////////////////////////////////////////
  $(function(){
    $(".price_spinner").TouchSpin({
        buttondown_class: 'btn red',
        buttonup_class: 'btn red',
        min: .1,
        max: 1000000000,
        stepinterval: 0.00001,
        step: .1,
        decimals: 1,
        maxboostedstep: 1,

    });
})
//////////////////////////////////////////////////////
$('#modal_popup').bind('click', function(){
    var vendor_id = parseInt($("#vendor_id").val());
    var store_id  = parseInt($("#store_id").val());

    if(vendor_id == 0 || vendor_id == '' || isNaN(vendor_id))
    {
        $('#vendor_message').css('display','block');
        return false;
    }
    else
    {
      // $('#modal_form').resetForm();
        /////reset select tags
       $('#quantity').val(0);
       $('#price_per_unit').val(0);
       //$('#cat_id').prop('selectedIndex',0).change();
       //$('#products').select2('data', null);
       //$('#country_id').select2('data', null);
       //$('#store_id').select2('data', null);
       $('#cat_id').hide();
       $('#products_div').hide();
       $('#country_div').hide();
       /////////////////////
       $('#vendor_message').css('display','none');
       $('#product_serials').hide();

       postData = {
                    store_id : store_id
                  };

       $.post('<?php echo base_url()?>stores/admin_stores/get_store_cats_options/', postData, function(result){
        $("#cats_div").html(result);

        $('#country_div').hide();
        $('#product_serials').hide();
        $('#products_div').hide();
    });
    }

 });

///////////////////////////////////////////////////
var old_vendor_id = $("#vendor_id").val();

$("#vendor_id").change(function(e){
    e.preventDefault();

    var vendor_id = $(this).val();

    postData = {vendor_id : vendor_id}

    $.post('<?php echo base_url()?>products/admin_purchase_orders/get_vendor_store/', postData, function(result){
        $("#stores_div").html(result);
    });

    if($(".product_row_class").length)
    {
        bootbox.confirm('<?php echo lang('confirm_change_vendor_msg');?>', function(result) {
            if($.trim(result) == 'true')
            {
                old_vendor_id = $("#vendor_id").val();

                $("#products_table tr").remove();
            }
            else
            {
                $("#vendor_id").val(old_vendor_id)
                $("#vendor_id").select2();
            }
        });
    }
    else
    {
        var old_vendor_id = vendor_id;
    }


   $('#modal_popup').unbind('click', false);
   return false;
});
////////////////////////////////////////////
/*
var old_store_id  = $("#store_id").val();

$( "body" ).on( "change", "#store_id", function(){

    postData = {'store_id' : $(this).val()};
    $.post('<?php echo base_url()?>products/admin_purchase_orders/get_store_vendors/', postData, function(result){
        $("#vendors_div").html(result);

        if($(".product_row_class").length)
        {
            bootbox.confirm('<?php echo lang('confirm_change_vendor_msg');?>', function(result) {
                if($.trim(result) == 'true')
                {alert('111');
                    old_store_id = $("#store_id").val();

                    $("#products_table tr").remove();
                }
                else
                {alert(old_store_id);
                    $("#store_id").val(old_store_id)
                    $("#store_id").select2();
                }
            });
        }
        else
        {
            var old_store_id = store_id;
        }
    });
 });
*/
$( "body" ).on( "change", "#store_id", function(){

    postData = {'store_id' : $(this).val()};
    $.post('<?php echo base_url()?>stores/admin_stores/get_store_cats_options/', postData, function(result){
        $("#cats_div").html(result);

        $('#country_div').hide();
        $('#product_serials').hide();
        $('#products_div').hide();
    });
 });

////////////////////////////////////////////
$( "body" ).on( "change", "#cat_id", function(){

    postData = {
                'cat_id' : $(this).val(),
                'store_id': $("#store_id").val()
               };
    $.post('<?php echo base_url()?>products/admin_purchase_orders/get_products', postData, function(result){
        $('#country_div').hide();
        $('#product_serials').hide();
        $('#products').select2('data', null);

        $("#products_div").show();
        $("#products").html(result);
        $('#optional_fields_div').css('display', 'none');
        });
 });

////////////////////////////////
$(document).ready(function() {
  $('#products').on('change', function(){

    $('#optional_fields_div').css('display', 'none');

    var product_id = $(this).val();
    postData = {
                  id : $(this).val()
               };
    $.post('<?php echo base_url()?>products/admin_purchase_orders/get_product_details/'+product_id, postData, function(result){
      countries = result[0];
      optional_fields = result[1];

      if(countries == 'none')
      {
          $("#country_div").hide();
          $("#country_id").html('<option value="0"></option>');

          var product_id = $("select[name=product_id]").val();
          var country_id = 0;
          var post_data = {
                              product_id : product_id,
                              country_id : country_id
                          };

          $.post('<?php echo base_url();?>products/admin_purchase_orders/get_product_serials/', post_data, function(result){
              $("#product_serials").show();
              $("#product_serials").html('<?php echo lang('current_quantity');?> : '+result);
          });

      }
      else
      {
          $('#country_id').select2('data', null);
          $("#country_div").show();
          $("#country_id").html(countries);
          $("#product_serials").hide();
      }

      //optional fields
      if(optional_fields != '')
      {
        //$('#optional_fields').select2('data', null);
        $("#optional_fields_div").show();
        $("#optional_fields_div").html(optional_fields);
      }
    }, 'json');
  });
});
<?php /*$("#products").change(function(){
   var product_id = $(this).val();

   $.ajax({
    type :'post',
    data :{ id: $(this).val()},
    url  :"<?php echo base_url()?>products/admin_purchase_orders/get_product_details/"+product_id,
    dataType: "json",

    success:function(info)
    {
        if(info == 'none')
        {
            $("#country_div").hide();
            $("#country_id").html('<option value="0"></option>');

            var product_id = $("select[name=product_id]").val();
            var country_id = 0;
            var post_data = {
                                product_id : product_id,
                                country_id : country_id
                            };

            $.post('<?php echo base_url();?>products/admin_purchase_orders/get_product_serials/', post_data, function(result){
                $("#product_serials").show();
                $("#product_serials").html('<?php echo lang('current_quantity');?> : '+result);
            });

        }
        else
        {
            $('#country_id').select2('data', null);
            $("#country_div").show();
            $("#country_id").html(info);
            $("#product_serials").hide();
        }
    }
   });

   return false;
});
*/?>
////////////////////////////////
$("#country_id").change(function(){
   var product_id = $("select[name=product_id]").val();
   var country_id = $(this).val();

   postData = {
                product_id : product_id,
                country_id : country_id
              };

   $.post('<?php echo base_url();?>products/admin_purchase_orders/get_product_serials/', postData, function(result){
       $("#product_serials").show();
       $("#product_serials").html('<?php echo lang('current_quantity');?> : '+result);
   });

   return false;
});
/////////////////////////////////////////
$("#product_modal").click(function(){
   var product_id      = $("select[name=product_id]").val();
   var product_name    = $("select[name=product_id] option:selected").text();
   var quantity        = $("input[name=quantity]").val();
   var price_per_unit  = $("input[name=price_per_unit]").val();
   var vendor_id       = $("#vendor_id").val();

   var country_id      = $("select[name=country_id]").val();
   var country_name    = $("select[name=country_id] option:selected").text();

   var store_id        = $("#store_id").val();//$("select[name=store_id]").val();
   var store_name      = $("#store_name").val();//$("select[name=store_id] option:selected").text();
   var op_fields       = new Array();
   var op_fields_values = new Array();
   //var op_fields        = $(".op_fields").val();
   //var op_fields_values = $(".op_fields_value").val();

   $('.op_fields').each(function(){
     op_fields.push($(this).val());
   });

   $('.op_fields_value').each(function(){

     op_fields_values.push($("option:selected", this).val());//($(this).val());
   });

   if(quantity == 0 || price_per_unit == 0 || product_id ==0 || product_name == '' || store_id == 0 || store_name == '' )
   {
        if(quantity == 0)
        {
            $('#msg_span').html('<?php echo lang("required_quantity"); ?>');
        }
        if(price_per_unit == 0)
        {
            $('#msg_span').html('<?php echo lang("required_price"); ?>');
        }
        if(product_id == 0 || product_name == '')
        {
            $('#msg_span').html('<?php echo lang("required_product"); ?>');
        }

        if(store_id == 0 || store_name == '')
        {
            $('#msg_span').html('<?php echo lang('required').' '.lang("name_of_store"); ?>');
        }

        if( country_id == 0)
        {
           //$('#msg_span').html('<?php echo lang("country_required"); ?>');
        }
   }
   else
   {
       //if($("#product_row_" + product_id + "_" + country_id+'_'+store_id).length == 0)
       {

           var html  = '<tr id="product_row_'+product_id+'_'+country_id+'_'+store_id+'" class="product_row_class"><td><a href="#">'+product_name+'</a></td><td><a href="#">'+country_name+'</a></td><td>'+quantity+'</td><td>'+store_name+'</td><td>'+price_per_unit+' '+'<?php echo $currency_symbol;?>'+'</td><td><button class="btn btn-sm red filter-cancel btn-success remove_product" data-product_id="product_row_'+product_id+'_'+country_id+'_'+store_id+'"  data-toggle="confirmation" id="bs_confirmation_demo_1" ><i class="fa fa-times"></i> <?php echo lang('delete');?></button></td>';
           html     += '<input type="hidden" name="product_id[]" value="'+product_id+'"/> <input type="hidden" name="quantity[]" value="'+quantity+'"/> <input type="hidden" name="price_per_unit[]" value="'+price_per_unit+'"/> <input type="hidden" name="country_id[]" value="'+country_id+'"/> <input type="hidden" name="store_id[]" value="'+store_id+'"/><input type="hidden" name="option_fields[]" value="'+op_fields+'" /><input type="hidden" name="op_fields_values[]" value="'+op_fields_values+'" /></tr>';

           $("#purchase_orders_products").append(html);

           $('#product_data').css('display','block');
           $('#no_product_data').css('display','none');
           $('#optional_fields_div').css('display', 'none');

           $('#msg_span').html('  ');
           //$('#store_id').val('0');
           //alert($('#store_id').val());

           $('.order_store_id').val(store_id);

           $("#modal_form")[0].reset();
           $("#close_modal").click();
       }
       /*else
       {
            $('#msg_span').html('<?php echo lang('product_added_before');?>');
       }*/
   }


});
///////////////////////////////////////////////////
var valid = function () {
    //Validation Function - Sample, just checks for empty fields
     var valid;
    $("input").each(function () {
        if ($(this).val() === "") {
            var a = $(this).val();
            valid = false;
        }
    });
    if (valid !== false) {
        return true;
    }
    else {
        return false;
    }
}
/////////////////Delete Row/////////////////////////
$('body').on('click','.remove_product',function(e){
     e.preventDefault();

     var product_row = $(this).data('product_id');

     bootbox.confirm('<?php echo lang('confirm_delete_msg');?>', function(result)
     {
        if($.trim(result) == 'true')
        {
          $('#'+product_row).remove();

           var table_count = $('#products_table >tbody >tr').length;
           if(table_count == 0 )
           {
              $('#product_data').css('display','none');
              $('#no_product_data').css('display','block');
           }
        }

    });

})
////////////////////////////////////
$('#form_submit_button').click(function(e){
    e.preventDefault();

    if($('.order_number').val() == '')
    {
        showToast('<?php echo lang('required').lang('order_number');?>','<?php echo lang('error');?>','warning');
    }
    else if($('.product_row_class').length == 0 && $('.upload_field').val() == '')
    {
         showToast('<?php echo lang('empty_puroducts_message');?>','<?php echo lang('empty_puroducts');?>','warning');
    }
    else
    {
        $('#order_form').submit();
    }



})
////////////////////////////////////
$('#form_draft_button').click(function(e){
    e.preventDefault();

    if($('.order_number').val() == '')
    {
        showToast('<?php echo lang('required').lang('order_number');?>','<?php echo lang('error');?>','warning');
    }
    else if($('.product_row_class').length == 0)
    {
         showToast('<?php echo lang('empty_puroducts_message');?>','<?php echo lang('empty_puroducts');?>','warning');
    }
    else
    {
        $("#draft").val('1');
        $('#order_form').submit();
    }


    if($('.product_row_class').length > 0)
    {
        $("#draft").val('1');
        $('#order_form').submit();
    }
    else
    {
        showToast('<?php echo lang('empty_puroducts_message');?>','<?php echo lang('empty_puroducts');?>','warning');
    }


});

$(document).on({
  //  ajaxStart: function() { $('body').addClass("loading");   },
  //  ajaxStop : function() { $('body').removeClass("loading"); }
});


</script>

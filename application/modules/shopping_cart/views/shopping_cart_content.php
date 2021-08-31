<script>
$("#products_form").submit(function(e){
      e.preventDefault();
  });
</script>

<div class="container cart_container_div">
      <div class="container">
          <div class="row">
              <div class="col-md-8">
                  <div class="header-shooping-cart">
                      <div class="title-page">
                          <h2><?php echo lang('shopping_cart');?> <span>(<?php echo $cart_items_count;?>)</span></h2>
                      </div>
                  </div>
                  
                  <?php if(isset($quantity_status_error)){?>
                    <div class="alert-area">
                        <?php echo $quantity_status_error;?>
                    </div>
                  <?php }?>
                  <?php if(isset($no_products_msg)) {
                    echo $no_products_msg;
                  } else {?>
                    <form id="products_form" action="<?php echo base_url();?>orders/order/insert_order" method="post">
                      <?php foreach($cart_stores as $store)
                      { unset($before_discount_price); ?>
                        <div class="store-order">
                          <div class="store-name-title">
                              <div class="form">
                                <?php if($store->checked == 1){
                                  $checked = "checked";
                                  } else {
                                      $checked = "";
                                  } ?>
                                  <?php if($this->config->item('business_type') == 'b2b'){?>
                                    <input id='store-<?php echo $store->store_id;?>' name="checkd_stores[]" type="checkbox" placeholder="" value="<?php echo $store->store_id;?>" <?php echo $checked;?> />
                                    <label for="store-<?php echo $store->store_id;?>"><?php echo $store->store_name;?></label>
                                  <?php }?>
                                  <?php /*<span class="collapce">
                                    <b class="plus show-icon">+</b>
                                    <b class="min">-</b>
                                  </span>
                                  */?>
                              </div>
                          </div>

                          <div class="shopping-cart-container">
                            <?php foreach($store->products as $details){?>
                              <div class="item-shop-container" id="row_<?php echo $details->product_id; ?>">
                                  <div class="row no-gutters">
                                      <div class="col-md-3">
                                        <div class="item-img">
                                          <a href="<?php echo $details->product_id != 0 ? base_url().$product_route.$details->route : '#';?>">
                                            <?php if($details->type =='recharge_card'){ ?>
                                                <img width="182" height="167" src="<?php echo base_url(); ?>assets/template/site/images/wallet.jpg" alt="wallet" title="wallet" />
                                            <?php }elseif($details->type =='package'){ ?>
                                                <img width="182" height="167" src="<?php echo $images_path . $details->image; ?>" alt="<?php echo $details->name; ?>" title="<?php echo $details->name; ?>" />
                                            <?php }
                                            elseif($details->type =='product'){ ?>
                                                <img width="182" height="167" src="<?php echo $images_path . $details->image; ?>" alt="<?php echo $details->name; ?>" title="<?php echo $details->name; ?>" />
                                            <?php } ?>
                                          </a>
                                        </div>
                                      </div>
                                      <div class="col-md-9">
                                          <div class="info-container">
                                            <div class="item-info">
                                              <h3><?php echo $details->name;?></h3>
                                              <p class="price">
                                                <span><?php echo lang('price_per_unit');?></span>
                                                <span class="new-price"> ( <?php echo $details->final_price; ?>  <?php echo $cart_data->currency_symbol; ?> ) </span>
                                                <?php if(($details->price+$details->optional_fields_cost) != $details->final_price) { ?>
                                                  <span class="old-price"><?php echo $details->price; ?> <?php echo $cart_data->currency_symbol; ?></span>
                                                <?php }?>
                                              </p>

                                              <?php if($details->product_id != 0){?>
                                                <p class="price">
                                                  <span><?php echo lang('total_amount');?></span> <span class="new-price" id="field_total_<?php echo $details->product_id; ?>"> ( <?php echo $details->final_price * $details->qty; ?> <?php echo $cart_data->currency_symbol; ?> ) </span>
                                                </p>

                                                <?php if($details->vat_value != 0){?>
                                                  <p class="price">
                                                    <span><?php echo lang('vat_value');?></span> <span class="new-price" id="field_total_<?php echo $details->product_id; ?>">  <?php echo $details->vat_value .' '. $cart_data->currency_symbol.' <br /> ( '.$details->vat_percent.' % ) '; ?> <?php echo '<br />'; echo ($details->vat_type == 1) ? lang('inclusive_vat'):lang('exclusive_vat'); ?>  </span>
                                                  </p>
                                                <?php }?>

                                                <p class="brand">
                                                  <a href="<?php echo base_url().$sub_category_route.$details->cat_route.'/0';?>"><?php echo $details->cat_name;?></a>
                                                </p>
                                              <?php }?>



                                              <?php
                                              if(isset($details->user_optional_fields) && count($details->user_optional_fields) != 0){?>
                                              <?php foreach($details->user_optional_fields as $field){?>

                                                      <p class="brand">
                                                          <span class="new-price"><?php echo $field->label;?> :
                                                          <?php echo $field->product_optional_field_value;?>
                                                        </span>
                                                      </p>

                                                        <?php if($field->has_qty == 1){?>
                                                            <p class="brand">( <?php echo lang('quantity').' : '.$field->qty;?>) </p>
                                                        <?php }?>

                                                  <?php }
                                                  }
                                              ?>

                                              <a class="action-item close_button" id="delete_btn_<?php echo $details->product_id; ?>" data-product_id="<?php echo $details->product_id; ?>" data-cart_product_id="<?php echo $details->cart_product_id; ?>">
                                                <div  class="delet-item">
                                                  <svg>
                                                    <use xlink:href="#rebbish"></use>
                                                  </svg>
                                                  <span><?php echo lang('delete');?></span>
                                                </div>
                                              </a>
                                            </div>
                                            <?php if($details->type == 'product'){ ?>
                                              <div class="">
                                                  <div class="">
                                                      <div class="">
                                                        <?php /** Basic Code
                                                        ** <input type="number" name="qty[<?php echo $details->product_id; ?>]" class="qty form-control qty_<?php echo $details->product_id;?>" data-rowid="<?php echo $details->id; ?>" data-product_id="<?php echo $details->product_id; ?>" value="<?php echo $details->qty; ?>"  />
                                                          */?>
                                                          <?php /** Mrzok Edit */ ?>
                                                          <input type="number" name="qty[<?php echo $details->product_id.'-'.$details->id; ?>]" class="qty form-control qty_<?php echo $details->product_id;?>" data-rowid="<?php echo $details->id; ?>" data-product_id="<?php echo $details->product_id; ?>" value="<?php echo $details->qty; ?>"  />
                                                          <?php /** End Edit */ ?>

                                                          <?php /*<div class="inc button" onclick="increaseValue()">+</div>
                                                          <div class="dec button" onclick="decreaseValue()">-</div>
                                                          */?>
                                                      </div>
                                                  </div>
                                              </div>
                                              <?php }?>

                                          </div>
                                      </div>
                                  </div>

                              </div>
                            <?php }?>

                          </div>

                      </div>
                      <?php }?>
                    </form>
                  <?php }?>
              </div>
              <div class="col-md-4">

                  <div class="deliver">
                    <?php /*
                      <form action="#">
                          <label>Deliver To:</label>
                          <select>
                            <option>Cairo</option>
                            <option>Giza</option>
                          </select>
                      </form>
                      <p>Delivered by <span>Sunday, Sep 8 </span></p>
                      */?>
                  </div>

                  <?php $this->load->view('cart_total', $this->data);?>
                  <div class="gift" style="border-right: 4px solid #0c0c0c!important; border-left: 2px dashed #0c0c0c!important; border: 2px dashed #0c0c0c!important;">
                    <form action="#">
                      <div class="checkbox">
                        <?php
                        echo form_error('send_as_gift');
                        if($cart_data->send_as_gift == 1)
                        {
                            $checked = 'checked' ;
                        } else {
                            $checked = '' ;
                        }

                        ?>
                        <input type="checkbox" class="send_gift" id="send_gift_check" name="send_as_gift" <?php echo $checked;?> />
                        <label for="send_gift_check" style="color: #0c0c0c!important;">
                          <?php echo lang('gift_note');?>
                        </label>
                      </div>
                    </form>
                  </div>

                  <div class="button-checkout">
                    <a href="<?php echo base_url();?>Cart_Address"><?php echo lang('proceed_to_checkout');?></a>
                  </div>

                  <div class="continue-shopping">
                    <a href="<?php echo base_url();?>"><?php echo strtoupper(lang('continue_shopping'));?></a>
                  </div>
              </div>
          </div>
      </div>
</div>

<script>
  function increaseValue() {
      var value = parseInt(document.getElementById('partridge').value, 10);
      value = isNaN(value) ? 0 : value;
      value++;
      //alert(value);
      document.getElementById('partridge').value = value;
    }

    function decreaseValue() {
      var value = parseInt(document.getElementById('partridge').value, 10);
      value = isNaN(value) ? 0 : value;
      value < 1 ? value = 1 : '';
      value--;
      //alert(value);
      document.getElementById('partridge').value = value;
    }
  </script>

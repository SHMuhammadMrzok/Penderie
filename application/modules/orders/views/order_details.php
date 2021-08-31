<link href="<?php echo base_url();?>assets/template/rating.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url();?>assets/template/rating.js"></script>

<script language="javascript" type="text/javascript">

function processRating(val, attrVal){

    $.ajax({
        type: 'POST',
        url: '<?php echo base_url();?>products/products/add_rate/',
        data: 'product_id='+attrVal+'&ratingPoints='+val,
        dataType: 'json',
        success : function(data) {
            //alert(data);
            if(data[0] == 'login')
            {
                window.location = '<?php echo base_url().'User_login';?>';
            }
            else if(data[0] == 'rated_before')
            {
                $('.rate_msg_'+attrVal).html('<?php echo lang('you_rated_product_before');?>');

            }
            else
            {
                $('#avgrat').text(data.average_rating);
                $('#totalrat').text(data.rating_number);
            }

        }
    });
}
</script>
<script>
    <?php if(isset($_SESSION['pay_msg'])){ ?>
        showToast('<?php echo $_SESSION['pay_msg'];?>','<?php echo lang('payment_reply');?>','warning');
    <?php }?>

    <?php if(isset($_SESSION['success'])){ ?>
        showToast('<?php echo $_SESSION['success'];?>','<?php echo lang('success');?>','success');
    <?php }?>

    <?php if(isset($_SESSION['failure'])){ ?>
        showToast('<?php echo $_SESSION['failure'];?>','<?php echo lang('error');?>','errror');
    <?php }?>


</script>

<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('orders_log');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
  <div class="container">
    <div class="row">
      <?php $this->load->view('site/user_menu', $this->data);?>
      <div class="col-md-8">
        <div class="order-page">
            <a href="<?php echo base_url();?>Orders_Log" class="back-orders">
                <svg>
                    <use xlink:href="#arrow-line-left"></use>
                  </svg>
              <span><?php echo lang('Back').' '.lang('to').' '.lang('orders_log');?></span>
            </a>
          <div class="order-container">

            <div class="header-order">
              <div class="row">
                <div class="col-md-6">
                  <p><?php echo lang('order_date');?>: <span><?php echo date('Y-m-d', $order_details->unix_time);?></span></p>
                  <p><?php echo lang('order_number');?>: <span>#<?php echo $order_details->order_id;?></span></p>
                  <p><?php echo lang('order_status');?>: <span><?php echo $order_details->status;?></span></p>
                  <p><?php echo lang('payment_method');?>: <span><?php echo $order_details->payment_method;?></span></p>
                  <p><?php echo lang('address');?>: <span><?php echo $order_details->address;?></span></p>

                </div>
                <div class="col-md-6">

                  <div class="total-price-order">
                    <p><?php echo lang('total');?>: </p>
                    <h2><?php echo $order_details->total;?> <?php echo $order_details->currency_symbol;?></h2>
                  </div>

                  <?php if($order_details->discount != 0){?>
                    <div class="total-price-order">
                      <p><?php echo lang('total_discount');?>: </p>
                      <h2> - <?php echo $order_details->discount;?> <?php echo $order_details->currency_symbol;?></h2>
                    </div>
                  <?php }?>

                  <?php if($order_details->coupon_discount != 0){?>
                    <div class="total-price-order">
                      <p><?php echo lang('coupon_discount');?>: </p>
                      <h2> - <?php echo $order_details->coupon_discount;?> <?php echo $order_details->currency_symbol;?></h2>
                    </div>
                  <?php }?>

                  <?php if($order_details->shipping_cost != 0){?>
                    <div class="total-price-order">
                      <p><?php echo lang('shipping_cost');?>: </p>
                      <h2> <?php echo $order_details->shipping_cost;?> <?php echo $order_details->currency_symbol;?></h2>
                    </div>
                  <?php }?>

                  <?php if($order_details->tax != 0){?>
                    <div class="total-price-order">
                      <p><?php echo lang('tax');?>: </p>
                      <h2> <?php echo $order_details->tax;?> <?php echo $order_details->currency_symbol;?></h2>
                    </div>
                  <?php }?>

                  <?php if($order_details->wrapping_cost != 0){?>
                    <div class="total-price-order">
                      <p><?php echo lang('wrapping_cost');?>: </p>
                      <h2> <?php echo $order_details->wrapping_cost;?> <?php echo $order_details->currency_symbol;?> </h2>
                    </div>
                  <?php }?>

                  <?php if($order_details->vat_value != 0){?>
                    <div class="total-price-order">
                      <p><?php echo lang('vat_value');?>: </p>
                      <h2> <?php echo $order_details->vat_value;?> <?php echo $order_details->currency_symbol;?> </h2>
                    </div>
                  <?php }?>

                  <div class="total-price-order">
                    <p><?php echo lang('final_total');?>: </p>
                    <h2><?php echo $order_details->final_total;?> <?php echo $order_details->currency_symbol;?> </h2>
                  </div>

                </div>
              </div>
            </div>

            <div class="stages-area">
              <div class="col p-0">
                  <div class="stage-container <?php echo $order_details->order_status_id==1||$order_details->order_status_id==10||$order_details->order_status_id==12?'active':'';?>">
                      <span class="num">1</span>
                      <span><?php echo lang('ready_for_shipping');?></span>
                  </div>
              </div>
              <div class="col p-0">
                  <div class="stage-container <?php echo $order_details->order_status_id==10||$order_details->order_status_id==12?'active':'';?>">
                      <span class="num">2</span>
                      <span><?php echo lang('out_for_delivery');?></span>
                  </div>
              </div>

              <div class="col p-0">
                  <div class="stage-container <?php echo $order_details->order_status_id==10?'active':'';?>">
                      <span class="num">3</span>
                      <span><?php echo lang('delivered');?></span>
                  </div>
              </div>
            </div>

            <div class="main-order">
              <?php foreach($products_with_serials as $product){?>
                <script>
                $(function() {
                    $(".rating_star_"+'<?php echo $product->product_id;?>').codexworld_rating_widget({
                        starLength: '5',
                        initialValue: '',
                        callbackFunctionName: 'processRating',
                        imageDirectory: 'images/',
                        inputAttr: 'postID_'+'<?php echo $product->product_id;?>'
                    });
                });
              </script>
                <div class="row m-0">

                  <div class="item-container">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="item-img">
                          <a href="<?php echo base_url().$product_route.$product->route;?>">
                            <img src="<?php echo $images_path.$product->image;?>" alt="<?php echo $product->title;?>">
                          </a>
                        </div>
                      </div>
                      <div class="col-md-9">
                        <div class="item-info">
                          <div class="stat">
                            <p><?php echo lang('status');?>: <span>
                                <?php if($order_details->order_status_id == 10){//delivered orders?>
                                  <svg>
                                    <use xlink:href="#check"></use>
                                  </svg>
                                <?php }?>
                                <span><?php echo $order_details->status;?> </span>
                            </p>
                          </div>
                          <h3><?php echo $product->title;?></h3>
                          <p class="price">
                            <span class="new-price"><?php echo lang('price').' '.$product->final_price.' '.$order_details->currency_symbol;?> </span>
                            <?php if(($product->price+$product->optional_fields_cost) != $product->final_price){?>
                              <span class="old-price"><?php echo $product->price.' '.$order_details->currency_symbol; ?></span>
                            <?php }?>
                          </p>
                          <p class="price">
                            <span class="new-price"><?php echo lang('amount').' ('.$product->qty.')';?> </span>
                          </p>
                          <p class="price">
                            <span class="new-price"><?php echo lang('final_total').' '. $product->final_price*$product->qty.' '.$order_details->currency_symbol;?> </span>
                          </p>
                          <p class="brand"><a href="<?php echo base_url().$product_route.$product->route;?>"><?php echo $product->cat_name;?></a></p>

                          <?php
                              if(isset($product->user_optional_fields) && count($product->user_optional_fields) != 0){?>
                              <?php foreach($product->user_optional_fields as $field){?>

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
                          <?php if($product->allow_return){//allow return product?>
                            <div class="stat">
                                <p><a href="<?php echo base_url().'orders/return_order/return_order_product/'.$product->order_product_id;?>"><?php echo lang('return_product');?></a> <span>

                            </p>
                          </div>
                          <?php }?>

                          <?php if($product->return_msg != ''){//return message?>
                            <div class="stat">
                                <p><?php echo $product->return_msg;?> <span>

                            </p>
                          </div>
                          <?php }?>

                          <?php /*<div class="rate-this">
                            <p><?php echo lang('rate_product');?>:</p>
                            <div class="rated">
                                <input name="rating" value="0" class="rating_star_<?php echo $product->product_id;?>" type="hidden" postID_<?php echo $product->product_id;?>="<?php echo $product->product_id;?>" data-product_id="<?php echo $product->product_id;?>" />
                                <div class="overall-rating">
                                    <?php /*( <?php echo lang('avg_rating');?>
                                    <span id="avgrat"> <?php echo $product->rating_avg;?> </span>
                                    <?php echo lang('base_on');?>
                                    <span id="totalrat"> <?php echo $product->rating_times; ?> </span>
                                    <?php echo lang('rating');?> )*/
                           /*      </div>
                            </div>
                        </div>*/?>

                      </div>
                    </div>
                  </div>


                </div>
                </div>
              <?php }?>

              <?php if(count($cards_data) != 0){
                 foreach($cards_data as $item){?>
                <div class="row m-0">
                  <div class="item-container">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="item-img">
                          <a href="">
                            <?php if($item->type == 'recharge_card'){?>
                              <img src="<?php echo base_url();?>assets/template/site/images/wallet.jpg"  alt="">
                            <?php }elseif($item->type == 'package'){?>
                              <img src="<?php echo $images_path.$item->image;?>"  alt="">
                            <?php }?>
                          </a>
                        </div>
                      </div>
                      <div class="col-md-9">
                        <div class="item-info">
                          <div class="stat">
                            <p><?php echo lang('status');?>: <span>
                                <?php if($order_details->order_status_id == 10){//delivered orders?>
                                  <svg>
                                    <use xlink:href="#check"></use>
                                  </svg>
                                <?php }?>
                                <span><?php echo $order_details->status;?> </span>
                            </p>
                          </div>
                          <h3>
                            <?php if($item->type == 'recharge_card'){
                              echo lang('recharge_card');
                            }elseif($item->type == 'package'){
                              echo $item->title;
                            }?></h3>
                          <p class="price">
                            <span class="new-price"><?php echo $item->final_price.' '.$order_details->currency_symbol;?> </span>
                            <?php if(($item->price+$item->optional_fields_cost) != $item->final_price && $item->product_id != 0){?>
                              <span class="old-price"><?php echo $item->price.' '.$order_details->currency_symbol; ?></span>
                            <?php }?>
                          </p>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php }
            }?>

          </div>
        </div>


      </div>
    </div>
  </div>
  </div>
</main>

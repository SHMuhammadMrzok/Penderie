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
          <?php foreach($orders_data as $key=>$order){ ?>
          <div class="order-container">
            <div class="header-order">
              <div class="row">
                <div class="col-md-6">
                  <p><?php echo lang('order_date');?>: <span><?php echo date('Y-m-d', $order->unix_time);?></span></p>
                  <p><?php echo lang('order_number');?>: <span>#<?php echo $order->id;?></span></p>
                  <a href="<?php echo base_url()."orders/order/view_order_details/".$order->id;?>"><?php echo lang('order_details');?></a>
                </div>
                <div class="col-md-6">
                  <div class="total-price-order">
                    <p><?php echo lang('final_total');?>: </p>
                    <h2><?php echo $order->final_total;?><span><?php echo $order->currency_symbol;?></span></h2>
                  </div>
                </div>
              </div>
            </div>
            <div class="main-order">
              <?php if(isset($order_products[$order->id]))
              {
                  foreach($order_products[$order->id] as $product)
                  {?>
                    <div class="row m-0">
                      <div class="item-container">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="item-img">
                              <a href="<?php echo base_url().$product_route.$product->route;?>">
                                <img src="<?php echo base_url().'assets/uploads/products/'.$product->image;?>" alt="">
                              </a>
                            </div>
                          </div>
                          <div class="col-md-9">
                            <div class="item-info">
                              <div class="stat">
                                  <p>Status: <span>
                                      <svg>
                                        <use xlink:href="#check"></use>
                                      </svg>
                                      <span><?php echo $order->status;?> </span>
                                  </p>
                              </div>
                              <h3><?php echo $product->title;?></h3>
                              <p class="price">
                                <span class="new-price"><?php echo $product->final_price." ".$order->currency_symbol;?> </span>
                                <?php if($product->final_price != $product->price){?>
                                  <span class="old-price"><?php echo $product->price." ".$order->currency_symbol;?></span>
                                <?php }?>
                              </p>
                              <p class="brand">
                                <a href="<?php echo base_url().$sub_category_route.$product->cat_route.'/0';?>"><?php echo $product->cat_name;?></a>
                              </p>
                              <?php /*
                              <div class="rate-this">
                                <p>Rate this product:</p>
                                <div class="rated">
                                  <ul>
                                    <li>
                                      <a href="#"><svg>
                                          <use xlink:href="#star"></use>
                                        </svg>
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#"><svg>
                                          <use xlink:href="#star"></use>
                                        </svg>
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#"><svg>
                                          <use xlink:href="#star"></use>
                                        </svg>
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#"><svg>
                                          <use xlink:href="#star"></use>
                                        </svg>
                                      </a>
                                    </li>
                                    <li>
                                      <a href="#"><svg>
                                          <use xlink:href="#star"></use>
                                        </svg>
                                      </a>
                                    </li>

                                  </ul>
                                </div>
                              </div>
                              */?>

                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
              <?php }
              }?>
              </div>
            </div>
          <?php }?>

        </div>
      </div>
    </div>
  </div>
</main>

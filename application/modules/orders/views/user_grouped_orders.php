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

              <?php foreach($orders_data as $key=>$row){?>
                  <div class="order-container-area">
                      <div class="order-head">
                          <div class="row">
                              <div class="col">
                                <p><?php echo lang('order_id');?>:
                                    <span>#<?php echo $row->id;?></span>
                                </p>
                              </div>
                              <div class="col">
                                <p>
                                    <?php echo lang('order_date');?>:
                                    <span><?php echo date('Y-m-d', $row->unix_time);?></span>
                                </p>
                              </div>
                              <div class="col">
                                <a target="_blank" href="<?php echo base_url()."orders/order/get_grouped_orders_reciept/".$row->orders_number;?>">
                                    <svg>
                                      <use xlink:href="#bill"></use>
                                    </svg>
                                    <span><?php echo lang('order_receipt');?></span>
                                </a>
                              </div>
                          </div>
                      </div>
                      <div class="order-main-ar">

                        <?php if($row->detailed_orders[0]->payment_method_id == 3 && $row->detailed_orders[0]->bank_statement == ''){ //if payment method is bank , then upload bank statement?>
                          <div class="store-order">
                            <div class="store-name-title">
                                <div class="form">
                                  <form method="post" action="<?php echo base_url().'orders/order/upload_order_bank_statement';?>" enctype="multipart/form-data">
                                    <label for=""><?php echo lang('upload_bank_statement');?> </label>
                                    <input type="file" name="userfile" />
                                    <input type="hidden" name="grouped_order_id" value="<?php echo $row->orders_number;?>" />
                                    <button><?php echo lang('upload');?></button>
                                  </form>
                                </div>
                            </div>
                          </div>
                        <?php }?>

                        <?php foreach($row->detailed_orders as $order){?>
                          <div class="store-order">
                              <div class="store-name-title">
                                  <div class="form">
                                      <label for="store-nike"><?php echo $order->store_name;?></label>
                                      <span class="collapce">
                                        <b class="plus">+</b>
                                        <b class="min show-icon">-</b>
                                      </span>
                                  </div>
                              </div>
                              <div class="shopping-cart-container">
                                  <div class="header-order">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <p><?php echo lang('order_date');?>: <span><?php echo date('Y-m-d', $order->unix_time);?></span></p>
                                        <p><?php echo lang('order_number');?>: <span>#<?php echo $order->id;?></span></p>
                                        <a href="<?php echo base_url().'orders/order/view_order_details/'.$order->id;?>"><?php echo lang('order_details');?></a>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="total-price-order">
                                          <?php /*
                                          <p><?php echo lang('total');?>: </p>
                                          <h2><?php echo $order->total;?> <?php echo $order->currency_symbol;?></h2>
                                          */ ?>
                                          <p><?php echo lang('final_total');?>: </p>
                                          <h2><?php echo $order->final_total;?> <?php echo $order->currency_symbol;?></h2>
                                        </div>

                                      </div>
                                    </div>
                                  </div>
                                  <div class="stages-area">
                                      <div class="col p-0">
                                          <div class="stage-container <?php echo $order->order_status_id==1||$order->order_status_id==10||$order->order_status_id==12?'active':'';?>">
                                              <span class="num">1</span>
                                              <span><?php echo lang('ready_for_shipping');?></span>
                                          </div>
                                      </div>
                                      <div class="col p-0">
                                          <div class="stage-container <?php echo $order->order_status_id==10||$order->order_status_id==12?'active':'';?>">
                                              <span class="num">2</span>
                                              <span><?php echo lang('out_for_delivery');?></span>
                                          </div>
                                      </div>

                                      <div class="col p-0">
                                          <div class="stage-container <?php echo $order->order_status_id==10?'active':'';?>">
                                              <span class="num">3</span>
                                              <span><?php echo lang('delivered');?></span>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                        <?php }?>
                      </div>
                  </div>
                <?php }?>
            </div>

            <?php if(isset($pagination) && !empty($pagination)){?>
              <div class="pagination-container">
                <ul>
                  <?php echo $pagination;?>
                </ul>
            </div>
          <?php }?>

        </div>
    </div>
    </div>
</main>

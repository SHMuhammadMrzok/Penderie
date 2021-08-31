<link rel="stylesheet" href="<?php echo base_url();?>assets/template/site/css/style.min.css">
<script src="<?php echo base_url();?>assets/template/site/js/scripts.js"></script>
<script src="<?php echo base_url();?>assets/template/site/js/un-min-js/svg.js"></script>
<body style="direction:<?php echo $_SESSION['direction'];?>">
  <style>
  @media print
  {
  .info-inv {
    font-weight: 900;
    font-size: 20px;
    }
  .table{
    font-weight: 900;
    font-size: 20px;
  }
  }
  
  .bg-total{
    
    background: #fff!important;
  }
  </style>
  <div class="invoice-container">
      <div class="container">
          <div class="row no-gutters border-bottom">
              <div class="title">
                  <h1><?php echo lang('order_receipt');?></h1>

                  <a href="#" class="button" onclick="myFunction()">
                      <svg>
                          <use xlink:href=#print></use>
                      </svg>
                      <span><?php echo lang('print_all');?></span>
                  </a>
              </div>

          </div>
          <div class="info-inv border-bottom no-gutters">
            <div class="col-md-6 ">
                <div class="invoice-logo">
                    <img src="<?php echo $images_path.$this->config->item('logo');?>" alt=""/>
                </div>
            </div>
            <div class="col-md-6 ctrl-dir">
                <?php /*<div class="cell">
                    <p>
                        <span><?php echo lang('order_id');?>.:</span>
                        <span><?php echo $order_details->id;?></span>
                    </p>
                </div>*/?>

                <div class="cell">
                    <p>
                        <span><?php echo lang('order_date');?>:</span>
                        <span><?php echo date('Y/m/d',$order_details->unix_time);?> | <?php echo date('H:i a',$order_details->unix_time);?></span>
                    </p>
                </div>

                <div class="cell">
                    <p>
                        <span><?php echo lang('from');?>:</span>
                        <span><?php echo $order_details->first_name.' '.$order_details->last_name;?></span>
                    </p>
                </div>

                <div class="cell">
                    <p>
                        <span><?php echo lang('address');?>:</span>
                        <span><?php echo $order_details->address;?></span>
                    </p>
                </div>


                <?php if($this->config->item('tax_number') != ''){?>
                  <div class="cell">
                      <p>
                          <span><?php echo lang('tax_number');?>:</span>
                          <span><?php echo $this->config->item('tax_number');?></span>
                      </p>
                  </div>
              <?php }?>

                <?php if($order_details->shipping_company != ''){?>
                    <div class="cell">
                        <p>
                            <span><?php echo lang('shipping_company');?>:</span>
                            <span><?php echo $order_details->shipping_company;?></span>
                        </p>
                    </div>
                <?php }?>

                <?php if($order_details->tracking_number != ''){?>
                <div class="cell">
                    <p>
                        <span><?php echo lang('tracking_number');?>:</span>
                        <span><?php echo $order_details->tracking_number;?></span>
                    </p>
                </div>
                <?php }?>

            </div>
          </div>
          <div class="info-table">
                <table class="table">
                        <thead class="header-table">
                          <tr>
                            <th scope="col"><?php echo lang('product_name');?></th>
                            <th scope="col"><?php echo lang('code');?></th>
                            <th scope="col"><?php echo lang('price');?></th>
                            <?php if($this->config->item('vat_percent') != 0){?>
                              <th scope="col"><?php echo lang('vat_value');?></th>
                            <?php }?>
                            <th scope="col"><?php echo lang('quantity');?></th>
                            <th scope="col"><?php echo lang('final_total');?>"<?php echo $order_details->currency_symbol;?>"</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php if(count($orders_products)!=0){
                           foreach($orders_products as $product){?>
                            <tr>
                              <td>
                                <?php echo $product->title;?><br />
                                <?php echo $product->vat_type == 1 ? lang('inclusive_vat'):lang('exclusive_vat');?>
                              </td>
                              <td><?php echo $product->code;?></td>
                              <td><?php echo $product->final_price .' '.$order_details->currency_symbol;?></td>
                              <?php if($product->vat_percent != 0){?>
                                <td><?php echo '('.$product->vat_percent.'%)'.$product->vat_value.' '.$order_details->currency_symbol;?></td>
                              <?php }?>
                              <td><?php echo $product->qty;?></td>
                              <td><?php echo $product->final_price * $product->qty .' '.$order_details->currency_symbol;?></td>
                            </tr>
                          <?php }
                          }?>


      						<tr class="bg-total">
      							<td colspan="4"><?php echo lang('total');?></td>
      							<td><?php echo round($order_details->total, 2)." ".$order_details->currency_symbol;?></td>
      						</tr>

                          <?php if($order_details->discount != 0){?>
                              <tr class="bg-total">
      							<td colspan="4"><p><strong><?php echo lang('total_discount');?></strong></p></td>
      							<td><p><strong> - <?php echo round($order_details->discount, 2)." ".$order_details->currency_symbol;?></p></strong></td>

      						</tr>
                          <?php }?>

                          <?php if($order_details->coupon_discount != 0){?>
                  						<tr class="bg-total">
                  							<td colspan="4"><?php echo lang('coupon_discount');?></td>
                  							<td>- <?php echo round($order_details->coupon_discount, 2)." ".$order_details->currency_symbol;?></td>

                  						</tr>
                          <?php }?>

                          <?php if($order_details->tax != 0){?>
                  						<tr class="bg-total">
                  							<td colspan="4"><?php echo lang('tax');?></td>
                  							<td><?php echo round($order_details->tax, 2)." ".$order_details->currency_symbol;?></td>
                  						</tr>
                          <?php }?>

                          <?php if($order_details->shipping_cost != 0){?>
                  						<tr class="bg-total">
                  							<td colspan="4"><?php echo lang('shipping_cost');?></td>
                  							<td><?php echo round($order_details->shipping_cost, 2)." ".$order_details->currency_symbol;?></td>
                  						</tr>
                          <?php }?>

                          <?php if($order_details->wrapping_cost != 0){?>
                  						<tr class="bg-total">
                  							<td colspan="4"><?php echo lang('wrapping_cost');?></td>
                  							<td><?php echo round($order_details->wrapping_cost, 2)." ".$order_details->currency_symbol;?></td>
                  						</tr>
                          <?php }?>

                          <?php if($order_details->vat_value != 0){?>
                            <tr class="bg-total">
                                <td colspan="4"><?php echo lang('vat_value');?></td>
                                <td><?php echo $order_details->vat_value;?></td>
                            </tr>
                          <?php }?>

                          <tr class="bg-total">
                              <td colspan="4"><?php echo lang('final_total');?></td>
                              <td><?php echo round($order_details->final_total, 2)." ".$order_details->currency_symbol;?></td>
                          </tr>
                        </tbody>
                </table>
          </div>
      </div>
  </div>
</body>

  <script>
    function myFunction() {
      window.print();
    }

    if(document.querySelector("body").style.direction === "rtl"){
      document.querySelector(".ctrl-dir").style.textAlign = "right";
    }
  </script>

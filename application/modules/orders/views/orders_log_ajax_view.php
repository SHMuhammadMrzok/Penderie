<?php 
  if($orders_data)
  {
       foreach($orders_data as $key=>$order){?>
        <tr class="table_content">
        	<td align="right">#<?php echo $order->id;?></td>
            <td align="right"><?php echo date('Y-m-d h:i', $order->unix_time);?></td>
            <td align="right">
                <?php $i=0;
                  foreach($order->products as $product)
                  {
                      echo $product->title." --- ".$product->qty.'<br/>';
                      $i++;
                      if($i == 3)
                      {
                          break;
                      }
                  }
                  foreach($order->charge_card as $card)
                  {
                     echo $card->price." ".lang('recharge_card').'<br/>';
                  }
                ?>
                <?php if(count($order->products) > 3){?>
                    <a href="#" class="label label-primary" data-toggle="modal" data-target="#pop_<?php echo $key;?>"><?php echo lang('more');?></a>
                     <div class="modal fade" id="pop_<?php echo $key;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel"><?php echo lang('products');?></h4>
                          </div><!--modal-header-->
                          <div class="modal-body text-center">
                            <?php 
                             foreach($orders_data[$key]->products as $product)
                             {
                                echo $product->title." --- ".$product->qty."<br/>";
                             }
                             foreach($orders_data[$key]->charge_card as $card)
                             {
                                echo $card->price.' '.lang('recharge_card').'<br/>';
                             }
                            ?>
                          </div><!--modal-body-->
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('close');?></button>
                          </div><!--modal-footer-->
                        </div><!--modal-content-->
                      </div><!--modal-dialog-->
                    </div><!--modal fade-->
                <?php }?>
            </td>
            <td align="right"><?php echo $order->final_total." ".$order->currency_symbol;?></td>
            <td align="right"><span class="label label-<?php echo $order->label;?>"><?php echo $order->status;?> </span></td>
            <td align="right"><a href="<?php echo base_url()."orders/order/view_order_details/".$order->id;?>" class="btn bg-primary"><?php echo lang('details');?></a></td>
        </tr>
    
    <?php }
    }else{?>
        <tr><td colspan="6"><?php echo lang('no_data');?></td></tr>
    <?php }?>


<link href="<?php echo base_url();?>assets/template/new_site/css/easyzoom.css?v=1" rel="stylesheet" />
<div class="breadcrumb">
    <div class="container-fluid">
      <div class="breadcrumb-inner">
        <ul class="list-inline list-unstyled">
          <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
          <?php if(count($parent_data) != 0){?>
            <li><a href="<?php echo base_url().$main_category_route.$parent_data->route;?>/0"><?php echo $parent_data->name;?></a></li>
          <?php }?>
          <li class="active"><a href="<?php echo base_url().$sub_category_route.$cat_data->route;?>/0"><?php echo $cat_data->name;?></a></li>

        </ul>
      </div>
    </div>
</div>

<div class="body-content outer-top-xs">
    <div class='container-fluid'>
      <div class="row">
        <div class="col-md-9 rht-col">
          <div class="detail-block">
            <h1 class="name"><?php echo $product->title;?></h1>
            <div class="rating-reviews">
                <div class="row">

                  <div class="pull-left">
                    <div class="rating rateit-small rateit">
                      <button id="rateit-reset-5" data-role="none" class="rateit-reset" aria-label="reset rating"
                        aria-controls="rateit-range-5" style="display: none;"></button>
                      <div id="rateit-range-5" class="rateit-range" tabindex="0" role="slider" aria-label="rating"
                        aria-owns="rateit-reset-5" aria-valuemin="0" aria-valuemax="5" aria-valuenow="4"
                        aria-readonly="true" style="width: 70px; height: 14px;">
                        <div class="rateit-selected" style="height: 14px; width: 56px;"></div>
                        <div class="rateit-hover" style="height:14px"></div>
                      </div>
                    </div>
                  </div>
                  <div class="pull-left">
                    <div class="reviews">
                      <a href="#" class="lnk">(13 Reviews)</a>
                    </div>
                  </div>

                </div>
              </div>
            <div class="row">
              <div class="col-md-5 gallery-holder">
                <div class="product-item-holder size-big single-product-gallery small-gallery">
                  <div id="owl-single-product" class="owl-single-product">

                    <div class="single-product-gallery-item" id="slide1">
                      <a class="image-1 magnifier-thumb-wrapper " data-title="Gallery">
                        <img class="img-responsive img" alt="" src="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>"
                          data-echo="<?php echo base_url();?>assets/uploads/products/<?php echo $product->image;?>" />
                      </a>
                    </div><!-- /.single-product-gallery-item -->

                  <?php if(count($product_images) != 0){
                    foreach($product_images as $i=>$image){ ?>
                        <div class="single-product-gallery-item" id="slide<?php echo $i+2;?>">
                          <a class="image-1 magnifier-thumb-wrapper" data-title="Gallery">
                            <img class="img-responsive img" alt="" id="thumb" src="<?php echo base_url();?>assets/uploads/products/<?php echo $image->image;?>"
                              data-echo="<?php echo base_url();?>assets/uploads/products/<?php echo $image->image;?>" />
                          </a>

                        </div><!-- /.single-product-gallery-item -->
                    <?php }
                    }?>


                  </div>

                  <div class="single-product-gallery-thumbs gallery-thumbs">
                    <div id="owl-single-product-thumbnails" class="owl-single-product-thumbnails">
                      <div class="item">
                        <a class="horizontal-thumb active" data-target="#owl-single-product" data-slide="1">
                          <img class="img-responsive " alt="" src="assets/images/blank.gif"
                            data-echo="assets/images/products/p1.jpg" />
                        </a>
                      </div>
                      <div class="item">
                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="2">
                          <img class="img-responsive " alt="" src="assets/images/blank.gif"
                            data-echo="assets/images/products/p2.jpg" />
                        </a>
                      </div>
                      <div class="item">
                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="3">
                          <img class="img-responsive " alt="" src="assets/images/blank.gif"
                            data-echo="assets/images/products/p3.jpg" />
                        </a>
                      </div>
                      <div class="item">
                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="4">
                          <img class="img-responsive " alt="" src="assets/images/blank.gif"
                            data-echo="assets/images/products/p4.jpg" />
                        </a>
                      </div>
                      <div class="item">
                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="5">
                          <img class="img-responsive " alt="" src="assets/images/blank.gif"
                            data-echo="assets/images/products/p5.jpg" />
                        </a>
                      </div>
                      <div class="item">
                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="6">
                          <img class="img-responsive " alt="" src="assets/images/blank.gif"
                            data-large-img-url="assets/images/products/p6.jpg" />
                        </a>
                      </div>
                      <div class="item">
                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="7">
                          <img class="img-responsive " alt="" src="assets/images/blank.gif"
                            data-large-img-url="assets/images/products/p7.jpg" />
                        </a>
                      </div>
                      <div class="item">
                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="8">
                          <img class="img-responsive " alt="" src="assets/images/blank.gif"
                            data-large-img-url="assets/images/products/p8.jpg" />
                        </a>
                      </div>
                      <div class="item">
                        <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="9">
                          <img class="img-responsive " alt="" src="assets/images/blank.gif"
                            data-large-img-url="assets/images/products/p9.jpg" />
                        </a>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="col-md-7">
                <div class="product-info-block">
                    <div class="product-info">

                        <div class="row">
                         <div class="magnifier-preview" id="preview" style="width: 100%;
          height: 400px;position:absolute;visibility:hidden;    background: rgb(255, 255, 255);
          z-index: 44;"></div>


                              <div class="stock-container">
                                <div class="row m-0">
                                  <div class="pull-left">
                                    <div class="stock-box">
                                       <?php echo lang('cat_name');?> :
                                    </div>
                                  </div>
                                  <div class="pull-left">
                                    <div class="stock-box">
                                       <a href="<?php echo base_url().$sub_category_route.$cat_data->route;?>/0"><?php echo $cat_data->name;?></a>
                                    </div>
                                  </div>

                                </div>

                                <div class="row m-0">
                                  <div class="pull-left">
                                    <div class="stock-box">
                                     <?php echo lang('code');?> :
                                    </div>
                                  </div>
                                  <div class="pull-left">
                                    <div class="stock-box">
                                      <?php echo $product->code;?>
                                    </div>
                                  </div>

                                </div>

                                <div class="row m-0">
                                  <div class="pull-left">
                                    <div class="stock-box">
                                   <?php echo lang('reward_points');?> :
                                    </div>
                                  </div>
                                  <div class="pull-left">
                                    <div class="stock-box">
                                      <?php echo $product->reward_points;?>
                                    </div>
                                  </div>
                                </div>

                                <div class="row m-0">
                                  <div class="pull-left">
                                    <div class="stock-box">
                                      <?php echo lang('name_of_store');?> :
                                    </div>
                                  </div>
                                  <div class="pull-left">
                                    <div class="stock-box">
                                       <a href="<?php echo base_url().'Store_details/'.$product->store_id;?>"><?php echo $product->store_name;?></a>
                                    </div>

                                  </div>
                                </div>


                                <?php if($product->brand_name != ''){?>
                                    <div class="row m-0">
                                      <div class="pull-left">
                                        <div class="stock-box">
                                            <?php echo lang('brand_name');?> :
                                        </div>
                                      </div>
                                      <div class="pull-left">
                                        <div class="stock-box">
                                            <a href="<?php echo base_url().'All_Brands';?>"><?php echo $product->brand_name;?></a>
                                        </div>
                                      </div>
                                    </div>
                                <?php }?>


                              </div>

                            </div>
                            <form id="optional_fields_form" enctype="multipart/form-data">
                        <?php if(isset($product_optional_fields) && count($product_optional_fields) != 0){
                        foreach ($product_optional_fields as $field)
                        {
                            if($field->field_type_id == 2) //radio
                            {
                                $required       = '';
                                $required_span  = '';

                                if($field->required == 1)
                                {
                                    $required       = 'required';
                                    $required_span  = " <span class='required' style='color: red'> * </span>";
                                }?>
                                <div class="info-container m-t-30">
                                  <div class="row m-0">
                                      <h5><?php echo $field->label.$required_span;?>:</h5>
                                    <ul class="product-color-list row col-sm-12 col-xs-12 " id="product-color-list">

                                      <?php foreach($field->options as $key=>$option)
                                    {?>
                                        <li data-id="<?php echo $key;?>" >
                                      <input type="radio" name="optional_field[<?php echo $field->id;?>]" value="<?php echo $option->id;?>" <?php echo $required;?> <?php echo $key==0?'selected':'';?>/>
    
                                        <?php if($option->image != ''){?>

										   <img src="<?php echo base_url();?>assets/uploads/products/<?php echo $option->image;?>" alt=""  />

                                      <?php }?>
                                      <?php echo $option->field_value;?>
                                      </li>
                                      <?php }?>

                                    </ul>
                                  </div>
                                </div>
                        <?php }
                        }
                        }?>

                      </div>
                </div>

              </div>

            </div>
          </div>

          <div class="product-tabs">
            <div class="row m-0">
              <ul class="nav nav-tabs w-100" id="myTab" role="tablist">

                <li class="nav-item">
                  <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
                    aria-selected="true"><?php echo lang('detials');?></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                    aria-controls="profile" aria-selected="false"><?php echo lang('customers_review');?></a>
                </li>

              </ul>
              <div class="tab-content w-100" id="myTabContent">


                <div class="tab-pane fade  show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                  <div class="descrip w-100">
                    <article class="text"><?php echo $product->description?></article>
                  </div>

                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                  <div class="product-tab w-100">
<?php /*
                    <div class="product-reviews">
                      <h4 class="title">Customer Reviews</h4>

                      <div class="reviews">
                        <div class="review">
                          <div class="review-title"><span class="summary">We love this product</span><span
                              class="date"><i class="fa fa-calendar"></i><span>1 days ago</span></span></div>
                          <div class="text">"Lorem ipsum dolor sit amet, consectetur adipiscing elit.Aliquam
                            suscipit."</div>
                        </div>

                      </div><!-- /.reviews -->
                    </div><!-- /.product-reviews -->
*/?>


                    <div class="product-add-review">
                    <?php /*
                      <h4 class="title">Write your own review</h4>
                      <div class="review-table">
                        <div class="table-responsive">
                          <table class="table">
                            <thead>
                              <tr>
                                <th class="cell-label">&nbsp;</th>
                                <th>1 star</th>
                                <th>2 stars</th>
                                <th>3 stars</th>
                                <th>4 stars</th>
                                <th>5 stars</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td class="cell-label">Quality</td>
                                <td><input type="radio" name="quality" class="radio" value="1"></td>
                                <td><input type="radio" name="quality" class="radio" value="2"></td>
                                <td><input type="radio" name="quality" class="radio" value="3"></td>
                                <td><input type="radio" name="quality" class="radio" value="4"></td>
                                <td><input type="radio" name="quality" class="radio" value="5"></td>
                              </tr>
                              <tr>
                                <td class="cell-label">Price</td>
                                <td><input type="radio" name="quality" class="radio" value="1"></td>
                                <td><input type="radio" name="quality" class="radio" value="2"></td>
                                <td><input type="radio" name="quality" class="radio" value="3"></td>
                                <td><input type="radio" name="quality" class="radio" value="4"></td>
                                <td><input type="radio" name="quality" class="radio" value="5"></td>
                              </tr>
                              <tr>
                                <td class="cell-label">Value</td>
                                <td><input type="radio" name="quality" class="radio" value="1"></td>
                                <td><input type="radio" name="quality" class="radio" value="2"></td>
                                <td><input type="radio" name="quality" class="radio" value="3"></td>
                                <td><input type="radio" name="quality" class="radio" value="4"></td>
                                <td><input type="radio" name="quality" class="radio" value="5"></td>
                              </tr>
                            </tbody>
                          </table><!-- /.table .table-bordered -->
                        </div><!-- /.table-responsive -->
                      </div><!-- /.review-table -->
*/?>
                      <div class="review-form">
                        <div class="form-container">
                          <form action="<?php echo base_url();?>products/products/add_product_comment" method="post">

                            <div class="row">
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="exampleInputName"><?php echo lang('name');?> <span class="astk">*</span></label>
                                  <input type="text" name="username" class="form-control txt" id="exampleInputName" placeholder="" required="required" />
                                </div><!-- /.form-group -->
                                <div class="form-group">
                                  <label for="exampleInputSummary"><?php echo lang('add_review');?> <span class="astk">*</span></label>
                                  <textarea placeholder="" name="comment" class="form-control txt" required="required"></textarea>
                                </div><!-- /.form-group -->
                              </div>

                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="exampleInputReview">Review <span class="astk">*</span></label>
                                  <textarea class="form-control txt txt-review" id="exampleInputReview" rows="4"
                                    placeholder=""></textarea>
                                </div><!-- /.form-group -->
                              </div>
                            </div><!-- /.row -->
                            <input type="hidden" name="product_id" value="<?php echo $product->id;?>" />
                            <input type="hidden" name="route" value="<?php echo $product->route;?>" />
                            <div class="action text-right">
                              <button class="btn btn-primary btn-upper"><?php echo lang('add');?></button>
                            </div><!-- /.action -->

                          </form><!-- /.cnt-form -->
                        </div><!-- /.form-container -->
                      </div><!-- /.review-form -->

                    </div><!-- /.product-add-review -->

                  </div><!-- /.product-tab -->

                </div>
              </div>



            </div><!-- /.row -->
          </div><!-- /.product-tabs -->


        </div>



            <div class="col-md-3">

                <div class="short-action">
                <?php if(isset($product_optional_fields) && count($product_optional_fields) != 0){
                        foreach ($product_optional_fields as $field)
                        {
                            if($field->field_type_id == 3) //check box
                            {
                                $required       = '';
                                $required_span  = '';

                                if($field->required == 1)
                                {
                                    $required       = 'required';
                                    $required_span  = " <span class='required' style='color: red'> * </span>";
                                }?>
                                  <div class="qty">
                                    <h5><?php echo $field->label.$required_span;?> </h5>
                                    <?php foreach($field->options as $key=>$option)
                                        {
                                            if($key == 0){
                                                $selected_op_cost = $option->cost;
                                            }
                                            ?>
                                        <div class="size-box">

                                            <input type="checkbox" class="op_cost op_c_<?php echo $field->id.'_'.$key;?>" data-op_cost="<?php echo $option->cost;?>" data-op_index="<?php echo $key;?>" data-op_id="<?php echo $field->id;?>" name="optional_field[<?php echo $field->id;?>][<?php echo $key;?>]" value="<?php echo $option->id;?>" <?php echo $required;?> <?php echo $key==0 ? 'checked':'';?> />

                                            <label> <?php if($option->cost != 0){?>
                                               ( <?php echo lang('cost').' : '.$option->cost.' '.$product->currency;?> )
                                           <?php }?></label>
                                           <?php if($field->has_qty == 1){?>

                                            <input type="number" name="op_qty[<?php echo $field->id;?>][<?php echo $key;?>]" value="1" min="1" class="op_qty_<?php echo $field->id.'_'.$key;?> op_q" data-op_cost="<?php echo $option->cost;?>" data-op_index="<?php echo $key;?>" data-op_id="<?php echo $field->id;?>" <?php echo $key!=0 ? 'disabled="true"' : '';?>/>
                                            <?php }?>
                                        </div>
                                    <?php }?>







                                  </div>
                  <?php }
                    }
                  }?>

                  <div class="add-btn w-100">
                    <a href="#" class="btn btn-primary w-100 <?php echo (isset($product_optional_fields) && count($product_optional_fields) != 0) ? 'add_optional_fields add-cart-bt' : 'cart';?>" data-product_id="<?php echo $product->id;?>">
                      <?php echo lang('add_to_cart');?>
                    </a>
                  </div>


                  <div class="favorite-button m-t-5">
                      <a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="" href="#"
                        data-original-title="Wishlist">
                        <i class="fa fa-heart"></i>
                      </a>
                      <a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="" href="#"
                        data-original-title="Add to Compare">
                        <i class="fa fa-signal"></i>
                      </a>
                      <a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="" href="#"
                        data-original-title="E-mail">
                        <i class="fa fa-envelope"></i>
                      </a>
                    </div>

                </div>

            </div>


          </form>


      </div>

      <?php if(count($related_products) != 0){?>
          <div class="row m-0 related-product">
            <div class="latest-container">
              <div class="title">
                <h2> <?php echo lang('related_products');?>
                </h2>
              </div>
              <div class="row m-0 m-t-15">
                <?php foreach($related_products as $key=>$r_product){
                    if($key<6){?>
                    <div class="col-md-2">
                      <div class="product-container-2">
                      <?php if($r_product->price_before != $r_product->price){?>
                        <div class="discound-label">
                          <?php echo lang('deduct');?>
                        </div>
                        <?php }?>

                        <div class="add-fav">
                          <a href="#" title="<?php echo lang('add_to_wishlist');?>" class="wishlist_product" data-product_id="<?php echo $r_product->id;?>">
                            <svg>
                              <use xlink:href="#fav"></use>
                            </svg>
                          </a>

                        </div>
                        <div class="image">
                          <img style="width: 179px!important; height: 194px!important;" src="<?php echo base_url();?>assets/uploads/products/<?php echo $r_product->image;?>" alt="" class="re-image">
                          <img style="width: 179px!important; height: 194px!important;" src="<?php echo base_url();?>assets/uploads/products/<?php echo $r_product->hover_image;?>" alt="" class="hover-image">

                        </div>
                        <div class="info-container">
                          <h3><a href="<?php echo base_url().$product_route.$r_product->route;?>"><?php echo $r_product->title;?></a></h3>

                        </div>
                      </div>
                    </div>
                <?php }
                }?>


              </div>



            </div>
          </div>

      <?php }?>

    </div>

  </div>
  
   <script>
 //calculate product price with optional fields
 $('.op_cost ,.op_q').change(function() {
    //var sList = "";
    var product_price = 0;
     $('input[type=checkbox]').each(function () {


        if ($(this).is(':checked')) {
            var op_cost = $(this).attr("data-op_cost");
            var id      = $(this).attr("data-op_id");
            var index   = $(this).attr("data-op_index");

            $('.op_qty_'+id+'_'+index).prop('disabled', false);

            //var product_price = $('.product_price').text();
            var qty = $('.op_qty_'+id+'_'+index).val() ;

            cost = Number(op_cost) * Number(qty);

            product_price += cost;
        }
        else
        {
            $('.op_qty_'+id+'_'+index).prop('disabled', true);

        }

    });

    $('.product_price').html(product_price);

    //console.log (sList);
 });

 </script>

<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <?php if(isset($parent_cat_data) && count($parent_cat_data) !=0 ){?>
            <li><a href="<?php echo base_url().$main_category_route.$parent_cat_data->route.'/0';?> "><?php echo $parent_cat_data->name;?></a></li>
        <?php }?>

        <?php if(isset($cat_data) && count($cat_data) != 0){?>
          <li class='active'><?php echo $cat_data->name;?></li>
        <?php }else{?>
          <li class='active'><?php echo $page_title;?></li>
        <?php }?>
      </ul>
    </div>
  </div>
</div>

<main class="margin-bottom-30px">
  <div class="container">
    <div class="row no-gutters">
      <div class="col-lg-3 col-md-4">
        <div class="mobile-action">
          <svg>
            <use xlink:href="#filter"></use>
          </svg>
          <span>
            Filter
          </span>
        </div>

        <div class="filter-left-area">

          <div class="close-filter">
            <svg>
              <use xlink:href="#close"></use>
            </svg>

          </div>
          <div class="filter-ocard">
            <div class="title">
              <h3><?php echo lang('sub_categories');?></h3>
              <span class="toggle-filter">
                <span class="min">-</span>
                <span class="plus">+</span>
              </span>
            </div>
            <ul class="filter-items">
              <li><a href="<?php echo base_url().'products/products/cat_products/1';?>" class="active"><?php echo lang('all_prducts');?> <span>(<?php echo $all_products_count;?>)</span></a></li>
              <?php /*<li><a href="#">New Arrivals <span>(1018)</span></a></li>*/?>

              <?php foreach($categories_array[0] as $key=>$cat){
                if($key != 'products_count'){?>
                  <li><a href="<?php echo base_url().$main_category_route.$cat->route.'/0';?>"><?php echo $cat->name;?> <span><?php echo $categories_array[$cat->category_id]['products_count'];?></span></a>
                  <?php if(isset($categories_array[$cat->id]) && count($categories_array[$cat->id]) != 0 && $key != 'products_count'){?>
                    <ul class="sub-menu">
                       <?php foreach ( $categories_array[ $cat->id ] as $index => $category ) {
                         if(is_object($category)){?>
                           <li><a href="<?php echo base_url().$sub_category_route.$category->route.'/0';?>" class="<?php echo $category->products_count==0?'no-items':'';?>" ><?php echo $category->name;?> <span>(<?php echo $category->products_count;?>)</span></a></li>
                       <?php }
                     }?>
                       <li><a href="<?php echo base_url().'products/products/all_offers/1/'.$cat->id;?>"><?php echo lang('sale');?> <span></span></a></li>
                    </ul>
                <?php }?>
              </li>
            <?php }
          }?>

            </ul>
            <?php /*<div class="link-show-more"><?php echo lang('see_more');?></div>*/?>
          </div>

          <?php if(isset($cat_brands) && count($cat_brands) != 0){?>
            <div class="filter-ocard">
              <div class="title">
                <h3><?php echo lang('brands');?></h3>
                <span class="toggle-filter">
                  <span class="min">-</span>
                  <span class="plus">+</span>
                </span>
              </div>
              <div class="check-list-area filter-items">
                <?php foreach($cat_brands as $brand){?>
                  <div class="form-group">
                    <input type="checkbox" name="brand" id="brand_<?php echo $brand->brand_id;?>" class="brands-filter" value="<?php echo $brand->brand_id;?>" <?php if(in_array($brand->brand_id, $brand_filter['ids'])){echo 'checked';}?> />
                    <label for="brand_<?php echo $brand->brand_id;?>"><?php echo $brand->name;?></label>
                    <span>(<?php echo $brand->products_count;?>)</span>
                  </div>
                <?php }?>

              </div>
              <div class="link-show-more">See More</div>

            </div>
            <!--filter-ocar-->
          <?php }?>

          <div class="filter-ocard">
            <div class="title">
              <h3><?php echo lang('price');?></h3>
              <span class="toggle-filter">
                <span class="min">-</span>
                <span class="plus">+</span>
              </span>
            </div>
            <div class="price-area filter-items">
              <div class="w-100">
                <div class="row no-gutters mr-5px ml-5px">
                  <div class="col-md-6">
                    <input name="price_from" value="<?php if(isset($price_from)){echo $price_from;}?>" class="price_from" type="text" placeholder="<?php echo lang('from_by');?>" />
                  </div>
                  <div class="col-md-6">
                    <input name="price_to" value="<?php if(isset($price_to)){echo $price_to;}?>" class="price_to" type="text" placeholder="<?php echo lang('to_by');?>" />
                  </div>
                  <div class="col-md-12">
                    <button class="button price-form"><?php echo lang('Apply');?></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--filter-ocar-->

          <?php /*<div class="filter-ocard">
            <div class="title">
              <h3><?php echo lang('condition');?></h3>
              <span class="toggle-filter">
                <span class="min">-</span>
                <span class="plus">+</span>
              </span>
            </div>
            <div class="condition filter-items">
              <div class="container-item-list condtion_new" <?php echo isset($condition_filter)&&$condition_filter==1?'style="display:none;"' : '';?>>

                <input class="condtion_filter" id="new_cond" type="checkbox" name="condition" value="2" <?php echo isset($condition_filter)&&$condition_filter==2?'checked':'';?> />
                <label><?php echo lang('new');?> <span>(<?php echo $new_used_products_count['new_products_count'];?>)</span></label>
                <svg>
                  <use xlink:href="#check-non-box"></use>
                </svg>
              </div>

              <div class="container-item-list condtion_used" <?php echo isset($condition_filter)&&$condition_filter==2?'style="display:none;"' : '';?>>

                <input class="condtion_filter" id="used_cond" type="checkbox" name="condition" value="1" <?php echo isset($condition_filter)&&$condition_filter==1?'checked':'';?> />
                <label><?php echo lang('old');?> <span>(<?php echo $new_used_products_count['used_products_count'];?>)</span></label>
                <svg>
                  <use xlink:href="#check-non-box"></use>
                </svg>
              </div>

            </div>
          </div>
          */?>

          <!--filter-ocar-->


          <div class="filter-ocard">
            <div class="title">
              <h3><?php echo lang('rating');?></h3>
              <span class="toggle-filter">
                <span class="min">-</span>
                <span class="plus">+</span>
              </span>
            </div>

            <div class="radio filter-items">
              <div class="form-group">
                <input type="radio" <?php echo isset($rating_filter) && $rating_filter==0 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="0" />
                <label for="s"><?php echo lang('all');?></label>
                <span></span>
              </div>

              <div class="form-group rated">
              <input type="radio" <?php echo isset($rating_filter) && $rating_filter==5 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="5" />
              <ul>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <span>& Up (64)</span>
                </li>
              </ul>
            </div>

              <div class="form-group rated">
              <input type="radio" <?php echo isset($rating_filter) && $rating_filter==4 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="4" />
              <ul>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <span>& Up (64)</span>
                </li>
              </ul>
            </div>

              <div class="form-group rated">
              <input type="radio" <?php echo isset($rating_filter) && $rating_filter==3 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="3" />
              <ul>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <span>& Up (64)</span>
                </li>
              </ul>
            </div>

              <div class="form-group rated">
              <input type="radio" <?php echo isset($rating_filter) && $rating_filter==2 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="2" />
              <ul>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <span>& Up (64)</span>
                </li>
              </ul>
            </div>

              <div class="form-group rated">
              <input type="radio" <?php echo isset($rating_filter) && $rating_filter==1 ? 'checked': '';?> class="rating-filter" name="rating_filter" value="1" />
              <ul>
                <li class="active">
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <svg>
                    <use xlink:href="#star"></use>
                  </svg>
                </li>
                <li>
                  <span>& Up (64)</span>
                </li>
              </ul>
            </div>

            </div>

          </div>

          <!--filter-ocar-->


        </div>
      </div>
      <div class="col-lg-9 col-md-8">
        <div class="search-results">
          <div class="row  w-100 no-gutters">
            <div class="col flex-grow-0 p-0">
              <div class="title">
                <h2>
                  <?php if(isset($cat_data)){
                    echo $cat_data->name;
                  }
                  elseif(isset($store_data)){
                    echo $store_data->name;
                  }
                  else{
                    echo $page_title;
                  }
                ?></h2>
                <p>(<?php echo $products_count.' '.lang('products_count');?>)</p>
                <?php if(isset($searched_stores)){?>
                    <p>(<?php echo count($searched_stores).' '.lang('stores');?>)</p>
                <?php }?>
              </div>
            </div>

            <div class="col flex-grow-1 p-0">
              <div class="arr-sort">
                <div class="sort-by">
                  <form action="#" class="flex-lg-row justify-content-start  flex-md-column d-md-flex">
                    <label class="mb-0"><?php echo lang('order_by');?></label>
                    <select id="input-sort">
                      <option value="0" >-------------</option>
                      <option value="1" <?php echo isset($sort) && $sort == 1 ? 'selected' : '';?>><?php echo lang('name_asc');?></option>
                      <option value="2" <?php echo isset($sort) && $sort == 2 ? 'selected' : '';?>><?php echo lang('name_desc');?></option>
                      <option value="3" <?php echo isset($sort) && $sort == 3 ? 'selected' : '';?>><?php echo lang('price_asc');?></option>
                      <option value="4" <?php echo isset($sort) && $sort == 4 ? 'selected' : '';?>><?php echo lang('price_desc');?></option>
                      <option value="5" <?php echo isset($sort) && $sort == 5 ? 'selected' : '';?>><?php echo lang('rating_desc');?></option>
                      <option value="6" <?php echo isset($sort) && $sort == 6 ? 'selected' : '';?>><?php echo lang('rating_asc');?></option>
                    </select>
                  </form>
                </div>
                <div class="arrangement">
                  <ul>
                    <li><span class="larg">
                        <svg>
                          <use xlink:href="#larg"></use>
                        </svg>
                      </span></li>

                    <li><span class="list">
                        <svg>
                          <use xlink:href="#list"></use>
                        </svg>
                      </span></li>
                  </ul>
                </div>
              </div>

            </div>

            <div class="search-filter-tags">
              <ul>
                <?php if(count($brand_filter['data']) !=0){
                  foreach($brand_filter['data'] as $row){?>
                    <li>
                      <div class="tag-area">
                        <span ><?php echo $row->name;?></span>
                        <a href="#" class="remove_filter" data-filter_id="<?php echo $row->id;?>" data-filter_type="brands" >
                          <svg>
                            <use xlink:href="#close"></use>
                          </svg>
                        </a>
                      </div>
                    </li>
                <?php }
                }?>

                <?php if(isset($price_from)){?>
                  <li>
                    <div class="tag-area">
                      <span><?php echo lang('from_by').' '.$price_from;?></span>
                      <a href="#" class="remove_filter" data-filter_type="price_from" >
                        <svg>
                          <use xlink:href="#close"></use>
                        </svg>
                      </a>
                    </div>
                  </li>
                <?php }?>

                <?php if(isset($price_to)){?>
                  <li>
                    <div class="tag-area">
                      <span><?php echo lang('to_by').' '.$price_to;?></span>
                      <a href="#" class="remove_filter" data-filter_type="price_to" >
                        <svg>
                          <use xlink:href="#close"></use>
                        </svg>
                      </a>
                    </div>
                  </li>
                <?php }?>

                <?php if(isset($condition_filter) && ($condition_filter == 1 || $condition_filter == 2)){?>
                  <li>
                    <div class="tag-area">
                      <span><?php echo $condition_filter == 1 ? lang('old') : lang('new');?></span>
                      <a href="#" class="remove_filter" data-filter_type="condition" >
                        <svg>
                          <use xlink:href="#close"></use>
                        </svg>
                      </a>
                    </div>
                  </li>
                <?php }?>


                <?php if(isset($rating_filter) && $rating_filter >= 1){?>
                  <li>
                    <div class="tag-area">
                      <span><?php echo lang('rating').' '.$rating_filter;?></span>
                      <a href="#" class="remove_filter" data-filter_type="ratings" >
                        <svg>
                          <use xlink:href="#close"></use>
                        </svg>
                      </a>
                    </div>
                  </li>
                <?php }?>
                <?php if(count($brand_filter['data']) !=0||isset($price_from)||isset($price_to)||isset($condition_filter) && ($condition_filter == 1 || $condition_filter == 2)||isset($rating_filter)&&$rating_filter>=1 ){?>
                  <li><a href="#" class="remove_filter" data-filter_type="clear_all">Clear All</a></li>
                <?php }?>
              </ul>
            </div>
          </div>
        </div>

        <div class="row no-gutters search-iteams">
          <?php 
          /* if( (count($cat_products) == 0) || (count($cat_products) == 0 && isset($searched_stores) && count($searched_stores) == 0 ) )
           //if( (count($cat_products)) )
           {?>
            <div class="alert-area" style="width: 100%"><?php echo $error_msg;?></div>
          <?php }
          else 
          */
          if(count($cat_products) != 0 || (isset($searched_stores) && count($searched_stores) != 0))
          {
             foreach($cat_products as $product){
              echo $product;
            }
            
            if(isset($searched_stores) && count($searched_stores))
            {
                
                foreach($searched_stores as $store){?>
                    <div class="col-xl-4 col-lg-6 col-md-6 p-10">
                      <div class="product-container ">
                        <div class="images-product">
                          <img src="<?php echo $images_path.$store->image;?>" alt="<?php echo $store->name;?>" />
                          <a href="<?php echo base_url().'Store_details/'.$store->id;?>" title="<?php echo $store->name;?>" class="hover-img">
                            <img src="<?php echo $images_path.$store->image;?>" alt="<?php echo $store->name;?>" />
                          </a>
                    
                          <div class="action">
                            <ul>
                              <li></li>
                            </ul>
                          </div>
                    
                          <div class=labels></div>
                        </div>
                    
                        <div class="info-product">
                          
                    
                          <p class="price"></p>
                          
                          <h3><a href="<?php echo base_url().'Store_details/'.$store->id;?>" title="<?php echo $store->name;?>"> <?php echo $store->name;?></a></h3>
                    
                          <div class="rated">
                            <ul>
                              
                              
                            </ul>
                          </div>
                    
                          <a href="<?php echo base_url().'Store_details/'.$store->id;?>" class="add-to-cart ">
                            <svg>
                                  
                            </svg>
                            <?php echo $store->name;?>
                          </a>
                    
                        </div>
                      </div>
                    </div>

                <?php }
            }
            
            
          }
          else
          {?>
            <div class="alert-area" style="width: 100%"><?php echo $error_msg;?></div>
          <?php }
         ?>

        </div>
        <?php if(isset($page_links)){?>
          <div class="pagination-container">
            <ul>
              <?php echo $page_links;?>
            </ul>
          </div>
        <?php }?>
      </div>
    </div>
  </div>
</main>

<?php if(isset($product_name)){?>
  <input type="hidden" name="product_name" value="<?php echo $product_name;?>" class="product_name" />
<?php }?>

<script>
  $( "body" ).on( "click", ".remove_filter", function(event){
    event.preventDefault();

    var filter_id   = $(this).data("filter_id");
    var filter_type = $(this).data("filter_type");

    if(filter_type == 'brands' || filter_type == 'clear_all')
    {
      //uncheck brand input
      $('#brand_'+filter_id).prop('checked', false);
      if(filter_type == 'clear_all')
      {
        $('.brands-filter').prop('checked', false);
      }
    }

    if(filter_type == 'price_from' || filter_type == 'clear_all')
    {
      //reset price filter
      $('.price_from').val('');
    }

    if(filter_type == 'price_to' || filter_type == 'clear_all')
    {
      //reset price filter
      $('.price_to').val('');
    }

    if(filter_type == 'condition' || filter_type == 'clear_all')
    {
      $('.condtion_filter').prop('checked', false);
    }

    if(filter_type == 'ratings' || filter_type == 'clear_all')
    {
      //$('.rating-filter').attr('checked', true);
      $('input:radio[name=rating_filter][value=0]').attr('checked', true);
    }


    $(this).parents('li').remove();
    filters();
  });

</script>

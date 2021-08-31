<!DOCTYPE html>
<html dir="<?php echo $_SESSION['direction'];?>" style="direction: <?php echo $_SESSION['direction'];?>;">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="robots" content="all">
  <title><?php echo isset($page_title) ? $page_title : $this->config->item('site_name');?></title>
	<?php if(isset($meta_keywords)){ ?>
		<meta name="keywords" content="<?php echo isset($meta_keywords) ? $meta_keywords : '';?>"/>
	<?php } else{ ?>
		<meta name="keywords" content="<?php  echo $this->config->item('keywords');?>"/>
	<?php }
  if(isset($meta_description)){ ?>
		<meta name="description" content="<?php echo isset($meta_description) ? $meta_description : '';?>"/>
	<?php } else{ ?>
		<meta name="description" content="<?php echo $this->config->item('description');?>"/>
	<?php } ?>

	<link rel="shortcut icon" href="<?php echo $images_path.$this->config->item('fav_ico');?>"/>

  <link rel="stylesheet" href="<?php echo base_url();?>assets/template/site/css/style.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/template/design/style.css">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-toastr/toastr.min.css"/>

  <script src="<?php echo base_url();?>assets/template/site/js/scripts.js"></script>
  <script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />





</head>

<body>

  <style type="text/css">
  .loading_modal {
      display:     none;
      position:    fixed;
      z-index:     1000;
      top:         0;
      left:        0;
      height:      100%;
      width:       100%;
      margin-left: 0px;
      background:  rgba( 255, 255, 255, .8 )
                   url('<?php echo base_url().'assets/loading.gif';?>')
                   50% 50%
                   no-repeat;
  }

  body.loading {
      overflow: hidden;
  }

  body.loading .loading_modal {
      display: block;
  }

  .modal-footer .btn{
      width: 100px!important;
      border: 1px solid #ccc;
  }

  @media print
  {
  .no_print {display:none;}
  @page { margin: 0; }
  body { margin: 1.6cm; }
  }
  <?php if($_SESSION['direction']=='rtl'){?>
    .menu-image{
      width: 32px;
      float: left;
    }
  <?php }else {?>
    .menu-image{
      width: 32px;
      float: right;
    }
  <?php }?>

  .whatsapp {
overflow: hidden;
height: auto;
width: 100%;
display: flex;
margin-top: 15px;
align-items: center;
svg {
  width: 35px!important;
  height: 35px;
  margin: 0 5px;
  margin-top: -2px;
  display: inline-flex;
}
p {
  color: #fff !important;
  font-size: 1.1rem;
  display: inline-flex;
}
}

.whatsapp svg{
width: 35px!important;
height: 35px;
margin: 0 5px;
margin-top: -2px;
display: inline-flex;
}



  </style>

  <header>
    <div class=top-header>
        <div class=container>
            <div class=row>
              <?php if(isset($is_cart)){?>
                <div class="col flex-grow-1">
                  <div class="logo shop-cart-logo">
                    <a href="<?php echo base_url().'Shopping_Cart';?>">
                      <svg>
                        <use xlink:href="#arrow-line-left"></use>
                      </svg>
                      <span><?php echo lang('Back').' '.lang('to').' '.lang('cart');?></span>
                    </a>
                    <a href="<?php echo base_url();?>"><img src="<?php echo $images_path.$this->config->item('logo');?>" alt="" /></a>
                  </div>
                </div>
              <?php }else{?>
                <div class="col flex-grow-1">
                    <div class="row d-flex no-gutters">
              
                               
                      <form action="<?php echo base_url();?>front_end_global/change_lang_country" id="count_lang_form" class="d-flex site-setting" method="post">
                         <div class="lang">  
                            <div class="select-image drop-down">
                                  <?php /*<label><?php echo lang('country');?></label>*/ ?>
                                  <select name="country_id" id="countries">
                                    <?php foreach($countries as $country) {?>
                                        <option value='<?php echo $country->id?>' data-title="<?php echo $country->name;?>" <?php echo $country->id==$_SESSION['country_id'] ? 'selected':'';?> >
                                                <?php echo $country->name;?>
                                        </option>
                                       <!-- <option value='<?php echo $country->id?>' data-image="https://www.marghoobsuleman.com/mywork/jcomponents/image-dropdown/samples/images/msdropdown/icons/blank.gif" data-imagecss="flag <?php echo strtolower($country->country_symbol);?>" data-title="<?php echo $country->name;?>" <?php echo $country->id==$_SESSION['country_id'] ? 'selected':'';?> ><?php echo $country->name;?></option>-->
                                    <?php }?>
                                  </select>
                                  <svg>
                                    <use xlink:href="#arrow-down"></use>
                                  </svg>
                            </div><!--select-image drop-down-->
                              
                            <span class="m-10px"></span>

                            <div class="select-image drop-down ">
                            <label><?php echo lang('lang-label');?></label>
                                <select id="lang" name="lang_id">
                                          <?php foreach($languages as $lang){?>
                                            <option value="<?php echo $lang->id?>" <?php echo $lang->language == $active_lang? 'selected':''; ?>><?php echo $lang->name?></option>
                                          <?php }?>
                                </select>
                                <svg><use xlink:href="#arrow-down"></use> </svg>
                            </div><!--select-image drop-down-->
                          </div>
                      </form>
                      </div>
                </div>
              <?php }?>
              <div class="col flex-grow-0">
                <div class="links-top d-flex">
                  <ul class="d-flex flex-nowrap">
                    <li>
                      <a href="<?php echo base_url();?>Wishlist">
                       
                        <span><?php echo lang('wishlist')?></span>
                      </a>
                    </li>
                    <?php /*
                    <li>
                      <a href="<?php echo base_url();?>Compare_Products">
                        <svg>
                          <use xlink:href="#compare"></use>
                        </svg><span><?php echo lang('compare_products')?></span>
                      </a>
                    </li>
                    */?>

                  </ul>
                </div>
              </div>
            </div>
        </div>
    </div>

    <?php if(!isset($hide_menu)){?>
      <div class="mid-header">
      <div class="container">
        <div class="row no-gutters">
          <div class="col flex-grow-0">
            <div class="logo">
              <a href="<?php echo base_url();?>" title="<?php echo lang('home');?>"><img src="<?php echo $images_path.$this->config->item('logo');?>" alt="<?php echo $this->config->item('site_name');?>" /></a>
            </div>

            <div class=user-area>
                                        <div class=dropdown>
                                            <button class=dropbtn>
                                                <svg> <use xlink:href=#user-icon></use></svg>
                  </button>
                <div class=dropdown-content>
                
                    <?php if(! $is_logged_in){?>
        							<a href="<?php echo base_url();?>User_login">
        								<?php echo lang('login');?>
        							</a>
        							<a href="<?php echo base_url();?>Register">
        								<?php echo lang('new_user');?>
        							</a>

                      <?php if($this->config->item('business_type') == 'b2b'){?>
                        <a target="_blank" href="<?php echo base_url();?>sell"><?php echo lang('continue_as_seller');?></a>
                      <?php }?>

						      <?php }else{?>
                    <a href="<?php echo base_url();?>Orders_Log">
      								<?php echo lang('orders_log');?>
      							</a>
      							<a href="<?php echo base_url();?>Addresses_List">
      								<?php echo lang('user_address');?>
      							</a>
                                <a href="<?php echo base_url();?>Wishlist">
                                    <span><?php echo lang('wishlist')?></span>
                                </a>
                    <?php /*<a href="<?php echo base_url();?>Balance_Recharge">
      								<?php echo lang('recharge_pocket');?>
      							</a>
                    <a href="<?php echo base_url();?>Payment_Log">
      								<?php echo lang('balance_details');?>
      							</a>
                    */?>
                    <a href="<?php echo base_url();?>Edit_Profile">
      								<?php echo lang('my_personal');?>
      							</a>

      							<?php
                    /*<a href="<?php echo base_url();?>Wishlist">
      								<?php echo lang('wishlist')?>
      							</a>
      							<a href="<?php echo base_url();?>Compare_Products">
      								<?php echo lang('compare_products')?>
      							</a>
                    */?>
      							<a href="<?php echo base_url();?>User_logout">
      								<?php echo lang('logout');?>
      							</a>
                                <?php } ?>                                                                                                                                
                                            </div>
                                        </div>
            </div>

           
            <div class="shop-cart-icon">
              <div class="icon">
                <a href="<?php echo base_url();?>Shopping_Cart">
                  <span class="cart_items_count"><?php echo $cart_items_count;?></span>
                  <svg>
                    <use xlink:href="#shopping-cart"></use>
                  </svg>
                </a>

              </div>
            </div>

            <div class="search-mobile">
              <svg>
                <use xlink:href="#search"></use>
              </svg>
            </div>

          </div>
          <?php /*<div class="col flex-grow-1">
            <div class="search-area search">
              <form action="<?php echo base_url(); ?>products/products/search" method="post">
                  <input class="search_auto_complete select2 js-data-example-ajax" type="text" value="<?php echo isset($product_name)? $product_name : ''; ?>" placeholder="<?php echo isset($product_name)? $product_name : lang ('search_word'); ?>" name="product_name"/>
                  <button>
                    <svg>
                      <use xlink:href="#search"></use>
                    </svg>
                  </button>
                </form>
            </div>*/ ?>
          </div>
          <div class="col flex-grow-0">
               <div class="d-flex align-items-center justify-content-center">
           
              <div class="gray-bgusershop d-flex justify-content-center align-items-center flex-row">
              <div class="user-area">
                <div class="dropdown">
                    <button class="dropbtn space-nowrap">
                     <div class="icon-user">
                        <svg>
                          <use xlink:href=#user-icon></use>
                        </svg>
                      </div>
                       <span class="space-nowrap">
                    <?php if(! $is_logged_in){
                           echo lang('register');
                         }else{
                           echo lang('welcome')."  ".$user->first_name;
                         }?>
                         </span>
                    <svg>
                      <use xlink:href="#arrow-down"></use>
                    </svg>
                  </button>
                  <div class="dropdown-content">
                    <?php if(! $is_logged_in){?>
        							<a href="<?php echo base_url();?>User_login">
        								<?php echo lang('login');?>
        							</a>
        							<a href="<?php echo base_url();?>Register">
        								<?php echo lang('new_user');?>
        							</a>

                      <?php if($this->config->item('business_type') == 'b2b'){?>
                        <a target="_blank" href="<?php echo base_url();?>sell"><?php echo lang('continue_as_seller');?></a>
                      <?php }?>

						      <?php }else{?>
                    <a href="<?php echo base_url();?>Orders_Log">
      								<?php echo lang('orders_log');?>
      							</a>
      							<a href="<?php echo base_url();?>Addresses_List">
      								<?php echo lang('user_address');?>
      							</a>
                                <a href="<?php echo base_url();?>Wishlist">
                                    <span><?php echo lang('wishlist')?></span>
                                </a>
                    <?php /*<a href="<?php echo base_url();?>Balance_Recharge">
      								<?php echo lang('recharge_pocket');?>
      							</a>
                    <a href="<?php echo base_url();?>Payment_Log">
      								<?php echo lang('balance_details');?>
      							</a>
                    */?>
                    <a href="<?php echo base_url();?>Edit_Profile">
      								<?php echo lang('my_personal');?>
      							</a>

      							<?php
                    /*<a href="<?php echo base_url();?>Wishlist">
      								<?php echo lang('wishlist')?>
      							</a>
      							<a href="<?php echo base_url();?>Compare_Products">
      								<?php echo lang('compare_products')?>
      							</a>
                    */?>
      							<a href="<?php echo base_url();?>User_logout">
      								<?php echo lang('logout');?>
      							</a>
      						<?php }?>

                  </div>
                </div>
              </div>
              <?php /*<div class="shop-cart-icon">
                <div class="icon">
                  <a href="<?php echo base_url();?>Shopping_Cart">
                    <span class="cart_items_count">
                        <?php echo $cart_items_count;?>
                    </span>
                    <svg>
                      <use xlink:href="#shopping-cart"></use>
                    </svg>
                  </a>
                </div>
                
                <?php /*if($this->data['is_logged_in']){?>
                  <div class="balance-num">
                     <?php echo lang('balance');?>: <?php echo $user_balance_new.' '.$currency;?>
                  </div>
                <?php }* 
              </div> */?>
              </div>
              <?php /*<div class="shop-cart-icon">
                <div class="icon">
                  <a href="<?php echo base_url();?>Shopping_Cart">
                    <span class="cart_items_count"><?php echo $cart_items_count;?></span>
                    <svg>
                      <use xlink:href="#shopping-cart"></use>
                    </svg>
                  </a>

                </div>
              </div>
              */?>
            </div>
          </div>
        </div>

      </div>
    </div>
  <?php }?>
  </header>
  <div class="search-area form-search-mobile">
        <form action=#>
            <input placeholder=Search>
            <button>
                <svg>
                    <use xlink:href=#search></use>
                </svg>
            </button>
        </form>
    </div>  
  <?php if(!isset($hide_menu)){?>
  
    <?php /*<div class="search-area form-search-mobile search">
      <form action="<?php echo base_url(); ?>products/products/search" method="post">
        <input class="search_auto_complete select2 js-data-example-ajax" type="text" value="<?php echo isset($product_name)? $product_name : ''; ?>" placeholder="<?php echo isset($product_name)? $product_name : lang ('search_word'); ?>" name="product_name"/>
        <button>
          <svg>
            <use xlink:href="#search"></use>
          </svg>
        </button>
      </form>
    </div> */?>

    <section class="menu-section">
    <div class="container">
      <div class="row">
        <nav id="navigation1" class="navigation">

          <div class="nav-header">

            <div class="nav-toggle"></div>
          </div>

          <div class="nav-menus-wrapper">
            <ul class="nav-menu">
                <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
                <?php
                if(isset($categories_array[0])){
                  foreach($categories_array[0] as $key=>$cat){
                  if($key < $cats_vertical_limit){?>
                    <li><a href="<?php echo base_url().$main_category_route.$cat->route.'/0';?>" class="all-cartegory"><?php echo $cat->name;?></a>

                    <?php if(isset($categories_array[$cat->id]) && count($categories_array[$cat->id]) != 0){?>
                      <ul class="nav-dropdown">
                        <?php foreach ( $categories_array[ $cat->id ] as $index => $category ) {
                          if(is_int($index)){ //$index != 'products_count'){?>
                            <li><a href="<?php echo base_url().$sub_category_route.$category->route.'/0';?>"><?php echo $category->name;?></a></li>
                          <?php }else{continue;}

                        }?>
                      </ul>
                    <?php }?>
                  </li>
                <?php }
                    }
                  }?>

                  <li><a href="<?php echo base_url();?>All_Offers"><?php echo lang('last_offers');?> </a>
            <?php if($this->config->item('business_type') == 'b2b'){?>
                  <li><a href="<?php echo base_url();?>All_stores"><?php echo lang('all_stores');?> <i class="fa fa-caret-down fa-indicator"></i> </a>
                    <!-- drop down -->
                    <ul class="nav-dropdown">
                        <?php foreach($menu_stores as $store){?>
                            <li><a href="<?php echo base_url().'Store_details/'.$store->store_id;?>"><?php echo $store->store_name;?><img class="menu-image" src="<?php echo $images_path.$store->image;?>" /></a></li>
                        <?php }?>
                        <div class="divider"></div>
                        <li><a href="<?php echo base_url().'All_stores/';?>"><?php echo lang('more_stores');?></a></li>
                    </ul>
                </li>
            <?php } ?>
            
            <li style="margin-right: 530px;">
                <div class="search-area search">
                  <form action="<?php echo base_url(); ?>products/products/search" method="post">
                    <input style="width: 180px;" class="search_auto_complete select2 js-data-example-ajax" type="text" value="<?php echo isset($product_name)? $product_name : ''; ?>" placeholder="<?php echo isset($product_name)? $product_name : lang ('search_word'); ?>" name="product_name"/>
                    <button>
                      <svg>
                        <use xlink:href="#search"></use>
                      </svg>
                    </button>
                  </form>
                </div>
            </li>
            
            <?php
                if($this->data['lang_id'] == 1)
                {
                    $dir = 'left';
                }
                else if($this->data['lang_id'] == 2)
                {
                    $dir = 'right';
                }
            ?>
            <li style="margin-<?php echo $dir; ?>: 530px;">
                <div class="shop-cart-icon">
                  <div class="icon">
                    <a href="<?php echo base_url();?>Shopping_Cart">
                      <span class="cart_items_count"><?php echo $cart_items_count;?></span>
                      <svg>
                        <use xlink:href="#shopping-cart"></use>
                      </svg>
                    </a>
    
                  </div>
                </div>
            </li>

            </ul>
            <div class="area-mobile">
                <div class="lang">
                    <h1><?php echo ('language');?></h1>
                     <select id="lang" name="lang_id" class="form-control">
                          <?php foreach($languages as $lang){?>
                            <option value="<?php echo $lang->id?>" <?php echo $lang->language == $active_lang? 'selected':''; ?>><?php echo $lang->name?></option>
                          <?php }?>
                      </select>
                </div>
                <div class="country">
                    <h1><?php echo lang('country');?></h1>
                     <select name="country_id" id="countries" class="form-control" style=width:100%>
                    <?php foreach($countries as $country) {?>
                        <option value='<?php echo $country->id?>' data-image="https://www.marghoobsuleman.com/mywork/jcomponents/image-dropdown/samples/images/msdropdown/icons/blank.gif" data-imagecss="flag <?php echo strtolower($country->country_symbol);?>"
                                data-title="<?php echo $country->name;?>" <?php echo $country->id==$_SESSION['country_id'] ? 'selected':'';?> >
                                <?php echo $country->name;?>
                        </option>
                    <?php }?>
                  </select>
                </div>
            </div>



          </div>

        </nav>
      </div>
    </div>
  </section>
<?php }?>

<?php echo $content;?>

<section class="mail-list">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <p><?php echo lang('join_mail_list_msg');?></p>
        <span> <?php //echo lang('join_mail_list_msg');?></span>
      </div>
      <div class="col-md-6">
        <?php echo isset($mail_list_error)?$mail_list_error:'';?>
        <form method="post" action="#" id="mail_list_form">
          <input type="email" placeholder="example@example.com" name="email" required="required" id="mail_list_email"/>
          <button id="submit_mail_list"><?php echo lang('send');?></button>
        </form>
      </div>

    </div>
  </div>
</section>

<section class="popup-area" style="display:none;">
  <div class="click-to-browes"></div>
  <div class="pop-content">
    <div class="white-row">
      <div class="add-cart-row">
        <div class="row no-gutters align-item-center w-100">
          <div class="image-product">
            <a class="cart_product_link" href="#"><img class="cart_product_img" height="124" src="" alt=""></a>
          </div>
          <div class="summary">
              <div class="congratulate">
                <svg>
                  <use xlink:href="#check"></use>
                </svg>
                <span class="cart_msg"></span>
              </div>
              <div class="cart-sum">
                <p><?php echo lang('final_total');?><span> (<span class="cart_count"></span><?php echo lang('items');?>)</span>:<span class="num cart_total_price"></span></p>
              </div>
          </div>

        </div>
      </div>
      <div class="action">
        <ul>
          <li><a href="<?php echo base_url();?>Shopping_Cart" class="go-cart"><?php echo lang('cart');?></a></li>
          <li><a href="<?php echo base_url();?>Cart_Address" class="proceed"><?php echo lang('proceed_to_checkout');?><span> (<span class="cart_count"></span> <?php echo lang('items');?>)</span></a></li>
        </ul>
      </div>
    </div>

    <div class="other-product-area">
      <div class="title">
        <h3><?php echo lang('frequently_bought_together');?></h3>
      </div>
      <div class="related_products_div"></div>
    </div>

  </div>
</section>

<?php if($this->config->item('whats_app_number') != ''){?>
<div class="wats-fixed">
  <div class="whatsapp">
      <a href="http://wa.me/<?php echo $this->config->item('whats_app_number');?>" target="_blank">
        <svg>
            <use xlink:href="#whatsapp"></use>
        </svg>
      </a>
    </div>
</div>
<?php }?>



  <footer>
    <?php if(!isset($hide_menu)){?>
      <div class="footer-top">
        <div class="container new-container-padding">
          <div class="row">

            <div class="col">
            <div class="footer-contact-info">
                <ul>
                  <li>
                    <p><?php echo lang('phone');?>:</p>
                    <a href="#"><?php echo $site_phones;?></a>
                  </li>
                  <li>
                    <p><?php echo lang('support');?>:</p>
                    <a href="#"><?php echo $site_emails;?></a>
                  </li>
                  <li>
                      <p><?php echo lang('download');?></p>
                      <div class="apps">
                          <ul>
                             <li><a target="_blank" href="<?php echo $this->config->item('ios_app_link');?>"><img src="<?php echo base_url();?>assets/template/site/images/ios.png"/></a></li>
                             <li><a target="_blank" href="<?php echo $this->config->item('android_app_link');?>"><img src="<?php echo base_url();?>assets/template/site/images/android.png"/></a></li>
                          </ul>
                      </div>
                  </li>
                  <?php if($this->config->item('whats_app_number') != ''){?>
                    <li>
                        <div class="whatsapp">
                          <a href="http://wa.me/<?php echo $this->config->item('whats_app_number');?>" target="_blank">
                            <svg>
                                <use xlink:href="#whatsapp"></use>
                            </svg>
                          </a>
                          <p><?php echo $this->config->item('whats_app_number');?></p>
                        </div>

                    </li>
                  <?php }?>
                </ul>
            </div>
          </div>


            <div class="col-md-3">
              <div class="module-heading">
                <h4 class="module-title"><?php echo lang('public_policy');?></h4>
              </div>
              <!-- /.module-heading -->

              <div class="module-body">
                <ul class='list-unstyled'>
                  <?php /*<li class="first"><a href="<?php echo base_url();?>Page_Details/9" title="<?php echo lang('warranty_policy');?>"><?php echo lang('warranty_policy');?></a></li>*/?>
                  <li class="first"><a href="<?php echo base_url();?>Page_Details/8" title="<?php echo lang('return_policy');?>"><?php echo lang('return_policy');?></a></li>
                  <li class="first"><a href="<?php echo base_url();?>Page_Details/4" title="<?php echo lang('terms_conditions');?>"><?php echo lang('terms_conditions');?></a></li>
                  <li class="last"><a href="<?php echo base_url();?>Page_Details/3" title="<?php echo lang('privacy_policy');?>"><?php echo lang('privacy_policy');?></a></li>
                </ul>
              </div>
              <!-- /.module-body -->
            </div>


            <div class="col-md-3">
              <div class="module-heading">
                <h4 class="module-title"><?php echo lang('about').' '.lang('store_name');?></h4>
              </div>

              <div class="module-body">
                <ul class='list-unstyled'>
                  <li class="first"><a title="<?php echo lang('contact_us');?>" href="<?php echo base_url();?>Contact_US"><?php echo lang('contact_us');?></a></li>
                  <li class=""><a title="<?php echo lang('careers');?>" href="<?php echo base_url();?>OurCareers"><?php echo lang('careers');?></a></li>
                  <li class="last"><a title="<?php echo lang('faqs');?>" href="<?php echo base_url().'faq/faq';?>"><?php echo lang('faqs');?></a></li>
                  <?php if($this->config->item('business_type') == 'b2b' && ! $is_logged_in){?>
                      <li class=""><a href="<?php echo base_url();?>sell"><?php echo lang('continue_as_seller');?></a></li>
                      <li class="last"><a href="<?php echo base_url();?>Page_Details/9" title="<?php echo lang('terms_conditions');?>"><?php echo lang('terms_conditions');?></a></li>
                  <?php }?>
                </ul>
              </div>
            </div>

            <div class="col-md-3">
              <div class="module-heading">
                <h4 class="module-title"><?php echo lang('my_personal');?></h4>
              </div>

              <div class="module-body">
                <ul class='list-unstyled'>
                  <li class="first"><a href="<?php echo base_url().'Shopping_Cart';?>" title="<?php echo lang('shopping_cart');?>"><?php echo lang('shopping_cart');?></a></li>
                  <li><a href="<?php echo base_url().'Orders_Log'?>" title="<?php echo lang('orders_log');?>"><?php echo lang('orders_log');?></a></li>
                  <li><a href="<?php echo base_url().'Addresses_List'?>" title="<?php echo lang('user_address');?>"><?php echo lang('user_address');?></a></li>
                  <li><a href="<?php echo base_url().'Wishlist'?>" title="<?php echo lang('wishlist');?>"><?php echo lang('wishlist');?></a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="row no-gutters">
              <div class="payment-method">
                  <ul>
                    <?php foreach($payment_methods_images as $method){?>
                      <li><a href="#"><img src="<?php echo $images_path.$method->image;?>"/></a></li>
                    <?php }?>
                  </ul>
              </div>
          </div>
        </div>
      </div>
    <?php }?>
    <div class="copyright-bar">
      <div class="container">
        <div class="row">
          <div class="col-md-6 no-padding social">
            <ul class="link">
              <?php if($this->config->item('facebook') != ''){?>
                <li class="fb pull-left"><a target="_blank" href="<?php echo $this->config->item('facebook');?>" title="Facebook">
                    <svg>
                      <use xlink:href="#facebook"></use>
                    </svg>
                  </a>
                </li>
              <?php }?>
              <?php if($this->config->item('twitter') != ''){?>
                <li class="tw pull-left"><a target="_blank" href="<?php echo $this->config->item('twitter');?>" title="Twitter">
                    <svg>
                      <use xlink:href="#twitter"></use>
                    </svg>
                  </a>
                </li>
              <?php }?>

              <?php if($this->config->item('instagram') != ''){?>
                <li class="insta pull-left"><a target="_blank" href="<?php echo $this->config->item('instagram');?>" title="instagram">
                    <svg>
                      <use xlink:href="#instagram"></use>
                    </svg>
                  </a>
                </li>
              <?php }?>
                <?php if($this->config->item('linkedin') != ''){?>
                  <li class="linkedin pull-left"><a target="_blank" href="<?php echo $this->config->item('linkedin');?>" title="Linkedin">
                      <svg>
                        <use xlink:href="#linkedin"></use>
                      </svg>
                    </a>
                  </li>
                <?php }?>
                <?php if($this->config->item('youtube') != ''){?>
                  <li class="youtube pull-left"><a target="_blank" href="<?php echo $this->config->item('youtube');?>" title="Youtube">
                      <svg>
                        <use xlink:href="#youtube"></use>
                      </svg>
                    </a>
                  </li>
            <?php }?>
            <?php /*if($this->config->item('whats_app_number') != ''){?>
              <li class="whats_app pull-left"><a target="_blank" href="http://wa.me/<?php echo $this->config->item('whats_app_number');?>" title="whats_app">
                <svg>
                    <use xlink:href="#whatsapp"></use>
                </svg>
                </a>
              </li>
            <?php }*/?>

            </ul>
          </div>


          <div class="col-md-6">
            <div class="copy-right-text">
             <?php echo lang('copy_rights') .' '. lang('store_name'). ' &copy; '.' '.date('Y');?>

            </div>
          </div>


        </div>
      </div>
    </div>
  </footer>
<div class="loading_modal"><!-- Place at bottom of page --></div>

  <script src="<?php echo base_url();?>assets/template/site/js/un-min-js/svg.js"></script>
  <script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/5f0adcd867771f3813c0e101/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->

</body>


<script>
  $( "#lang" ).change( function () {
    this.form.submit();
  } );
  ////////////////////////////////////////////////////////////
  $( "#countries" ).change( function () {
  $( "#count_lang_form" ).submit();

  } );

  /////////////////////////////////////////////////////////////
  <?php if(isset($_SESSION['not_allow'])){ die('111');?>
    showToast('<?php echo $_SESSION['not_allow'];?>', '<?php echo lang('not_allow');?>', 'error' );
  <?php }?>
  /////////////////////////////////////////////////////////////
  <?php if(isset($_SESSION['message'])){ ?>
    showToast('<?php echo $_SESSION['message'];?>', '<?php echo lang('message');?>', 'success' );
  <?php }?>

  <?php if(isset($_SESSION['error_message'])){ ?>
    showToast( '<?php echo $_SESSION['error_message'];?>', '<?php echo lang('message');?>', 'error' );

  <?php $this->session->unset_userdata('error_message');}?>
  <?php if(isset($_SESSION['pay_msg'])){ ?>
  //showToast('<?php echo $_SESSION['pay_msg'];?>','<?php echo lang('payment_reply');?>','warning');

  <?php }?>
  <?php if(isset($_SESSION['notification_error'])){ ?>
  showToast( '<?php echo $_SESSION['notification_error'];?>', '', 'warning' );

  <?php }?>

  <?php if(isset($_SESSION['first_order_inserted'])){?>
  showToast( '<?php echo $_SESSION['first_order_inserted'];?>', '<?php echo lang('success ');?>', 'success' );
  <?php }
  if(isset($_SESSION['success_msg'])){?>
  showToast( '<?php echo $_SESSION['success_msg'];?>', '<?php echo lang('success ');?>', 'success' );
  <?php }


    $this->session->unset_userdata('first_order_inserted');
   ?>

  //Toast Notifications
  ////////////////////////////////////////////////////////////
  function showToast( $msg, $title, $type ) {
    var msg = $msg;
    var title = $title;
    var shortCutFunction = $type;
    var showDuration = parseInt('<?php echo $this->config->item('toaster_seconds');?>') * 1000;

    toastr.options = {
      "closeButton": true,
      "debug": false,
      "positionClass": "toast-bottom-full-width",
      "onclick": null,
      "showDuration": "10000",
      "hideDuration": "1000",
      "timeOut": showDuration,
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"

    }

    var $toast = toastr[ shortCutFunction ]( msg, title ); // Wire up an event handler to a button in the toast, if it exists
    $toastlast = $toast;
    if ( $toast.find( "#okBtn" ).length ) {
      $toast.delegate( "#okBtn", "click", function () {
        alert( "you clicked me. i was toast #" + toastIndex + ". goodbye!" );
        $toast.remove();
      } );
    }
    if ( $toast.find( "#surpriseBtn" ).length ) {
      $toast.delegate( "#surpriseBtn", "click", function () {
        alert( "Surprise! you clicked me. i was toast #" + toastIndex + ". You could perform an action here." );
      } );
    }

    $( "#clearlasttoast" ).click( function () {
      toastr.clear( $toastlast );
    } );
  }

  ////////////////////////////////////////////////////////
  //when click add to wishlist
  $( '.wishlist_product' ).click( function ( event ) {
    event.preventDefault();

    var product_id = $( this ).data( 'product_id' );
    var postData = {
      product_id: product_id
    };
    $.post( '<?php echo base_url()."products/products/add_to_wishlist/";?>', postData, function ( data ) {

      /*
      result :
          product_required
          login
          already_exist
          success
      */

      if ( data == 'product_required' ) {
        showToast( '<?php echo lang('no_product_details');?>', '<?php echo lang('error');?>', 'error' );
      } else if ( data == 'login' ) {
        window.location = "<?php echo base_url().'User_login';?>";
      } else if ( data == 'already_exist' ) {
        showToast( '<?php echo lang('product_exist_in_wishlist');?>', '<?php echo lang('sorry');?>', 'warning' );
      } else if ( data == 'success' ) {
        showToast( '<?php echo lang('added_to_wishlist_successfully');?>', '<?php echo lang('success');?>', 'success' );
      }
    } );
  } );

  ////////////////////////////////////////////////////////
  //when click remove from wishlist
  $( '.remove_wishlist' ).click( function ( event ) {
    event.preventDefault();

    var product_id = $( this ).data( 'product_id' );
    var postData = {
      product_id: product_id
    };
    $.post( '<?php echo base_url()."products/products/remove_from_wishlist/";?>', postData, function ( data ) {


      if ( data[ 0 ] == 'product_required' ) {
        showToast( '<?php echo lang('no_product_details');?>', '<?php echo lang('error');?>', 'error' );
      } else if ( data[ 0 ] == 'login' ) {
        window.location = "<?php echo base_url().'User_login';?>";
      } else if ( data[ 0 ] == 'product_not_exist' ) {
        showToast( '<?php echo lang('product_not_exist_in_wishlist');?>', '<?php echo lang('sorry');?>', 'warning' );
      } else if ( data[ 0 ] == 'success' ) {
        showToast( '<?php echo lang('product_removed_successfully_from_wishlist');?>', '<?php echo lang('success');?>', 'success' );
        $( '.product_' + data[ 1 ] ).remove();
      }

    }, 'json' );
  } );

  ////////////////////////////////////////////////////////
  //when click add to cart
  $( '.cart' ).click( function ( event ) {
    event.preventDefault();

    var product_id  = $(this).data('product_id');
    var product_qty = $('.product_qty').val();
    var package_id  = $(this).data('package_id');
    var type        = $(this).data('type');

    if (product_qty === undefined)
    {
      product_qty = 1;
    }
    var postData = {
      product_id    : product_id,
      product_qty   : product_qty,
      package_id    : package_id,
      type          : type
    };
    $.post( '<?php echo base_url()."shopping_cart/cart/add_to_cart/";?>', postData, function ( data ) {

      if ( data[ 0 ] == 'no_stock' ) {
        showToast( '<?php echo lang('no_stock_for_this_product');?>', '<?php echo lang('sorry');?>', 'error' );
      } else if ( data[ 0 ] == 'max_per_discount' ) {
        showToast( '<?php echo lang('max_qty_per_user_discount_reached');?>', '<?php echo lang('warning');?>', 'warning' );
      } else if ( data[ 0 ] == 'max_products_per_order' ) {
        showToast( '<?php echo lang('max_products_per_order_reached');?>', '<?php echo lang('sorry');?>', 'error' );
      } else if ( data[ 0 ] == 'product_exist' ) {
        showToast( '<?php echo lang('product_exist_in_your_shopping_cart');?>', '<?php echo lang('sorry');?>', 'error' );
      } else if ( data[ 0 ] == 'qty_error' ) {
        showToast( '<?php echo lang('shopping_cart_quantity_error');?>', '<?php echo lang('sorry');?>', 'error' );
      } else if ( data[ 0 ] == 'optional_fields_required' ) {
        window.location = "<?php echo base_url().$product_route.'/';?>" + data[ 1 ];
      } else {
        var cur_count = $( '.cart_items_count' ).html();

        var new_count = Number( cur_count ) + Number( product_qty ); //'<?php echo $cart_items_count + 1;?>';
        $( '.cart_items_count' ).html( new_count );
        $( '.cart_items_count' ).addClass( "badge badge-primary" );
        //showToast( data[ 0 ], 'success', 'success' );
        $('.cart_msg').html('<?php echo lang('added_to_cart');?>')//(data[ 0 ]);
        var img_src  = '<?php echo base_url().'assets/uploads/products/';?>' + data[ 2 ];
        var product_link  = '<?php echo base_url().$product_route;?>' + data[ 1 ];

        $(".cart_product_img").attr("src", img_src);
        $('.cart_product_link').attr("href", product_link);
        $( '.cart_count' ).html( new_count );
        $('.related_products_div').html(data[ 4 ]);
        $('.cart_total_price').html(data[3])
        $('.popup-area').show();
      }
    }, 'json' );
  } );

  ////////////////////////////////////////////////////////
  //when click buy now
  $( '.buy_now, .buy_product' ).click( function ( event ) {
    event.preventDefault();



    var product_id  = $(this).data('product_id');
    var product_qty = $('.product_qty').val();
    var package_id  = $(this).data('package_id');
    var type        = $(this).data('type');

    if (product_qty === undefined)
    {
      product_qty = 1;
    }
    var postData = {
      product_id    : product_id,
      product_qty   : product_qty,
      package_id    : package_id,
      type          : type
    };

    $.post( '<?php echo base_url()."shopping_cart/cart/add_to_cart/";?>', postData, function ( data ) {

      if ( data[ 0 ] == 'no_stock' ) {
        showToast( '<?php echo lang('no_stock_for_this_product');?>', '<?php lang('sorry');?>', 'error' );
      } else if ( data[ 0 ] == 'max_per_discount' ) {
        showToast( '<?php echo lang('max_qty_per_user_discount_reached');?>', '<?php echo lang('warning');?>', 'warning' );
      } else if ( data[ 0 ] == 'max_products_per_order' ) {
        showToast( '<?php echo lang('max_products_per_order_reached');?>', '<?php echo lang('sorry');?>', 'error' );
      } else if ( data[ 0 ] == 'product_exist' ) {
        showToast( '<?php echo lang('product_exist_in_your_shopping_cart');?>', '<?php echo lang('sorry');?>', 'error' );
      } else if ( data[ 0 ] == 'qty_error' ) {
        showToast( '<?php echo lang('shopping_cart_quantity_error');?>', '<?php echo lang('sorry');?>', 'error' );
      } else if ( data[ 0 ] == 'optional_fields_required' ) {
        window.location = "<?php echo base_url().$product_route.'/';?>" + data[ 1 ];
      } else {
        window.location = "<?php echo base_url()."Shopping_Cart";?>";
      }
    }, 'json' );
  } );

  ////////////////////////////////////////////////////////
  // insert user optional fields
  // Add To Cart
  $( "body" ).on( "click", ".add_optional_fields", function ( event ) {
    event.preventDefault();
    //var postData = $.param( $( '#optional_fields_form' ).find( ':input option:selected' ).not( $( this ) ) );

    //$( "#myselect option:selected" ).val();
    var postData = $( '#optional_fields_form' ).serialize();

    $.post( '<?php echo base_url()."shopping_cart/cart/submit_product_optional_fields";?>', postData, function ( result ) {
      if ( result[ 0 ] == 1 ) {
        $( "#optional_fields_form" ).trigger( 'reset' );
        showToast( result[ 1 ], 'success', 'success' );
      } else {
        showToast( result[ 1 ], 'error', 'error' );
      }
    }, 'json' );
  } );
  // Buy Now
  $( "body" ).on( "click", ".buy_optional_fields", function ( event ) {
    event.preventDefault();
    //var postData = $.param( $( '#optional_fields_form' ).find( ':input' ).not( $( this ) ) );
    var postData = $( '#optional_fields_form' ).serialize();
    $.post( '<?php echo base_url()."shopping_cart/cart/submit_product_optional_fields";?>', postData, function ( result ) {
      if ( result[ 0 ] == 1 ) {
        window.location = "<?php echo base_url()."Shopping_Cart";?>";
      } else {
        showToast( result[ 1 ], 'error', 'error' );
      }
    }, 'json' );
  } );
  ////////////////////////////////////////////////////////
  //when click compare products
  $( "body" ).on( "click", ".compare_products", function ( event ) {
    event.preventDefault();

    var product_id = $( this ).data( 'product_id' );
    var postData = {
      product_id: product_id
    };

    $.post( '<?php echo base_url();?>products/products/add_compare_product', postData, function ( result ) {

      //check if more than one product to show compare table
      if ( result == 1 ) {
        showToast( '<?php echo lang('product_added_successfully_to_comparison');?>', '<?php echo lang('success');?>', 'success' );
      } else {
        //show table
        window.location = "<?php echo base_url();?>Compare_Products";
      }
    } );

  } );

  /////////////////////////////////////////////////////////////
  //When click remove compare product

  $( "body" ).on( "click", ".remove_compare_product", function ( event ) {
    event.preventDefault();

    var product_id = $( this ).data( 'product_id' );
    var postData = {
      product_id: product_id
    };

    $.post( '<?php echo base_url();?>products/products/remove_compare_product', postData, function ( result ) {
      if ( result == 1 ) {
        $( ".compare_product_" + product_id ).remove();
        showToast( '<?php echo lang('product_removed_from_compare');?>', '<?php echo lang('success');?>', 'success' );
      }
    } );
  } );

  ////////////////////////////////////////////////////////////
  //////Products Sorting
  $( "body" ).on( "change", "#input-sort, .cat-filter, .rating-filter, .op-filter, .cities-filter, .brands-filter, .price_from, .price_to, .condtion_filter", function (e) {
    e.preventDefault();
    filters();
  } );

  function filters()
  {

    var catFilterValls  = [];
    var opFilterValls   = [];
    var brandFilterValls= [];
    //var priceFilterVal  = 0;
    var ratingFilterVal = 0;
    var citiesFilterVal = 0;
    var conditionFilterVal = 0;
    var sortVal         = $('#input-sort').val();
    var productNameVal  = $('.product_name').val();
    var priceFromFilter = $('.price_from').val();
    var priceToFilter   = $('.price_to').val();

    //var priceFilter  = $( "input[type=radio][name=price_filter]:checked" );
    var ratingFilter = $( "input[type=radio][name=rating_filter]:checked" );
    var citiesFilter = $( "input[type=radio][name=cities_filter]:checked" );
    var conditionFilter = $( ".condtion_filter:checked");

    /*if ( priceFilter.length > 0 ) {
      priceFilterVal = priceFilter.val();
    }*/

    if ( ratingFilter.length > 0 ) {
      ratingFilterVal = ratingFilter.val();
    }

    if ( citiesFilter.length > 0 ) {
      citiesFilterVal = citiesFilter.val();
    }

    if ( conditionFilter.length > 0 ) {
      conditionFilterVal = conditionFilter.val();
    }

    if ( $( '.cat-filter:checkbox:checked' ).length > 0 ) {
      $( '.cat-filter:checkbox:checked' ).each( function () {
        catFilterValls.push( $( this ).val() );
      } );
    } else {
      catFilterValls = 0;
    }

    if ( $( '.op-filter:checkbox:checked' ).length > 0 ) {
      $( '.op-filter:checkbox:checked' ).each( function () {
        opFilterValls.push( $( this ).val() );
      } );
    } else {
      opFilterValls = 0;
    }

    if ( $( '.brands-filter:checkbox:checked' ).length > 0 ) {
      $( '.brands-filter:checkbox:checked' ).each( function () {
        brandFilterValls.push( $( this ).val() );
      } );
    } else {
      brandFilterValls = 0;
    }

    postData = {
      sort: sortVal,
      cat_filter: catFilterValls,
      //price_filter: priceFilterVal,
      rating_filter: ratingFilterVal,
      op_filter: opFilterValls,
      cities_filter: citiesFilterVal,
      product_name: productNameVal,
      brands_filter: brandFilterValls,
      price_from : priceFromFilter,
      price_to : priceToFilter
    }



    window.location = '<?php echo base_url();?><?php echo isset($_SERVER['argv'][0])?$_SERVER['argv'][0]:'';?>' +'?sort='+sortVal + '&cat_filter=' + catFilterValls + '&rating_filter=' + ratingFilterVal + '&op_filter=' + opFilterValls+ '&cities_filter=' + citiesFilterVal+ '&product_name=' + productNameVal+'&brands_filter='+brandFilterValls+'&price_from='+priceFromFilter+'&price_to='+priceToFilter+'&condition_filter='+conditionFilterVal;
    //window.location = '<?php echo base_url();?><?php echo isset($_SERVER['argv'][0])?$_SERVER['argv'][0]:'';?>' +'?sort='+sortVal + '&cat_filter=' + catFilterValls + '&price_filter=' + priceFilterVal + '&rating_filter=' + ratingFilterVal + '&op_filter=' + opFilterValls+ '&cities_filter=' + citiesFilterVal+ '&product_name=' + productNameVal+'&brands_filter='+brandFilterValls+'&price_from='+priceFromFilter+'&price_to='+priceToFilter+'&condition_filter='+conditionFilterVal;

  }
  /////////////////////////////////////////////////////////////

  $(document).on({
      ajaxStart: function() { $('body').addClass("loading");    },
      ajaxStop: function() { $('body').removeClass("loading"); }
  });
</script>

<?php if(isset($user_add_page)){?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->config->item('googleapi_key');?>&libraries=places&callback=initMap" async defer></script>
  <script>
    var map, autocomplete, places, marker, geocoder;

    function initMap() {
      geocoder = new google.maps.Geocoder();
      var lat_var = <?php echo (isset($general_data) && $general_data->lat != '')?$general_data->lat:$this->config->item('map_country_lat');?>;
      var lng_var = <?php echo (isset($general_data) && $general_data->lng != '')?$general_data->lng:$this->config->item('map_country_lng');?>;

      map = new google.maps.Map(document.getElementById('map'), {
        zoom: 8,
        center: {
            lat: lat_var,
            lng: lng_var },
        mapTypeControl: false,
        panControl: false,
        zoomControl: false,
        streetViewControl: false
      });

      marker = new google.maps.Marker({ position: {
        lat: lat_var,
        lng: lng_var },
        map: map, draggable: true });


      autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(
          document.getElementById('autocomplete')), {
        types: ['(cities)']
      });
      places = new google.maps.places.PlacesService(map);

      autocomplete.addListener('place_changed', onPlaceChanged);

      google.maps.event.addListener(marker, 'dragend', function () {
        // updateMarkerStatus('Drag ended');
        geocodePosition(marker.getPosition());
        map.panTo(marker.getPosition());

        console.log(marker.getPosition().lat())
        console.log(marker.getPosition().lng())
        $("#lat_input").val(marker.getPosition().lat());
        $("#lng_input").val(marker.getPosition().lng());
      });
    }

    function onPlaceChanged() {
      var place = autocomplete.getPlace();
      if (place.geometry) {
        map.panTo(place.geometry.location);
        map.setZoom(15);
        marker.setPosition(place.geometry.location)
        search();
      } else {
        document.getElementById('autocomplete').placeholder = 'Enter a city';
      }
    }
    function search() {
      var search = {
        bounds: map.getBounds(),
        types: ['lodging']
      };
    }
    function geocodePosition(pos) {
      geocoder.geocode({
        latLng: pos
      }, function (responses) {
        if (responses && responses.length > 0) {
          updateMarkerAddress(responses[0].formatted_address);
        } else {
          updateMarkerAddress('Cannot determine address at this location.');
        }
      });
    }
    function updateMarkerAddress(str) {
      //document.getElementById('autocomplete').innerHTML = str;
      $("#autocomplete").val(str);
    }

    function getLocation(e) {
      e.preventDefault();
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(renderMapInCurrentPosition);
      } else {
        alert("browser doesn't support Geo Location")
      }
    }

    function renderMapInCurrentPosition(position) {
      userLocation = {lat:position.coords.latitude,lng:position.coords.longitude}

      $("#lat_input").val(position.coords.latitude);
      $("#lng_input").val(position.coords.longitude);

      map.panTo(userLocation);
        map.setZoom(15);
        marker.setPosition(userLocation)
    }
    document.querySelector(".getcurrentLocation").addEventListener("click",getLocation);
  </script>
<?php }?>

<script>
  //add user to mail_list script
  $( "body" ).on( "click", "#submit_mail_list", function(event){
    event.preventDefault();

    var postData = {
        email : $("#mail_list_email").val()
      }
      $.post('<?php echo base_url();?>users/mail_list/insert_member', postData, function(result){
        if(result['error'] == 0)
        {
          showToast(result['message'], ' ', 'success');
        }
        else {
          showToast(result['message'], ' ', 'error');
        }
      }, 'json');
  });


  </script>
  
<script type="text/javascript">
  window.onload = function(){
    if(document.querySelector("html").style.direction === 'ltr'){
      document.querySelector("body").lastChild.getElementsByTagName("iframe")[1].style.left="10px";
      document.querySelector("body").lastChild.getElementsByTagName("iframe")[1].style.right="auto"
      document.querySelector(".wats-fixed").classList.add("left-social");
    }
  }
</script>

</html>

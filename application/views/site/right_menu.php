<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
    <?php if ($this->ion_auth->logged_in()){
            $user = $this->ion_auth->user()->row();
    ?>
    <div class="info_account">
        <div class="title_area"><span><?php echo lang('account_info')?>معلومات الحساب</span></div><!--title_area-->
        <div class="account_area_left">
            <div class="padding_account">
                <h3><?php echo lang('welcome')."  ".$user->user_name;?> </h3>
                <div class="account">
                    <div class="wallet"><h3>رصيد المحفظة</h3><p>5800 ريال سعودي</p></div><!--wallet-->
                    <div class="point"><h3>النقاط المكتسبة</h3><p>200 نقطة</p></div><!--point-->
                </div><!--account-->

            </div><!--padding_account-->

        </div><!--account_area_left-->

    </div><!--info_account-->

    <div class="tools_account">

    	<div class="title_area">

        	<span>أدوات الحساب</span>

        </div><!--title_area-->

        <div class="account_links">

        	<ul>

            	<li><a href="#"><i class="fa fa-square"></i> طلباتي</a></li>

                <li><a href="#"><i class="fa fa-square"></i> تفاصيل الرصيد</a></li>

                <li><a href="#"><i class="fa fa-square"></i> معلوماتي</a></li>

                <li><a href="#"><i class="fa fa-square"></i> التذاكر</a></li>

                <li><a href="#"><i class="fa fa-square"></i> المنتجات المفضلة</a></li>

                <li><a href="#"><i class="fa fa-square"></i> طلب شحن رصيد</a></li>

            </ul>

        </div><!--account_links-->

        <div class="last_visit">
            <h3><?php echo lang('last_login_date')."  ".$user->user_name;?></h3><p><?php echo date('Y/m/d H:i',$user->last_login);?></p>
        </div><!--last_visit-->

    </div><!--tools_account-->
    <?php } ?>
    <div class="main_links">

    	<div class="title_area">

        	<span><?php echo lang('main_menu'); ?></span>

        </div><!--title_area-->

        <div class="links">
        	<ul>
                <?php foreach($categories as $category) {?>
                    <li><a href="<?php echo base_url();?>products/products/index/<?php echo $category->id; ?>"><?php echo $category->name;?></a></li>
                <?php }?>
            </ul>
        </div><!--links-->
    </div><!--main_links-->
    
    <?php foreach($right_advertisments as $adv){?>
    <div class="ads">
        <a href="#"><img src="<?php echo base_url();?>assets/uploads/<?php echo $adv->image;?>" alt="ads"/></a>
    </div><!--ads-->
    <?php }?>
</div>
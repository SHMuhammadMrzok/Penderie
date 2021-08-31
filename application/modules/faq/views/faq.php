<div class="breadcrumb">
  <div class="container">
    <div class="breadcrumb-inner">
      <ul class="list-inline list-unstyled">
        <li><a href="<?php echo base_url();?>"><?php echo lang('home');?></a></li>
        <li class='active'><?php echo lang('faqs');?></li>
      </ul>
    </div>
  </div>
</div>

<main>
<div class="container">
	<div class="checkout-box faq-page">
		<div class="row">
			<div class="col-md-12">
				<h2 class="heading-title"><?php echo lang('frequently_asked_questions');?></h2>
				<?php /*<span class="title-tag">Last Updated on November 02, 2014</span>*/?>
				<div class="panel-group checkout-steps" id="accordion">
          <?php foreach($faqs as $key=>$faq){?>
					     <!-- checkout-step-01  -->
            <div class="panel panel-default checkout-step-0<?php echo $faq->id;?>">

            <!-- panel-heading -->
            	<div class="panel-heading">
              	<h4 class="unicase-checkout-title">
                    <a data-toggle="collapse" class="" data-parent="#accordion" href="#collapse_<?php echo $key;?>">
                      <span><?php echo $key+1;?></span><?php echo $faq->question;?>
                    </a>
                 </h4>
              </div>
              <!-- panel-heading -->

              <div id="collapse_<?php echo $key;?>" class="panel-collapse collapse in">
              	<!-- panel-body  -->
                  <div class="panel-body">
                  	<?php echo $faq->answer;?>
              	</div>
              	<!-- panel-body  -->

              </div><!-- row -->
            </div> <!-- checkout-step-01  -->
          <?php }?>

				</div><!-- /.checkout-steps -->
			</div>
		</div><!-- /.row -->
	</div><!-- /.checkout-box -->
</div>

</main>

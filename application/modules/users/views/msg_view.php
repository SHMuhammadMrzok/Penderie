<main class="no-padding-top">
    <div class="container no-padding">
        <div class="row">
            <div class="center-div">
                <div class="title-page">
                    <h4><?php echo lang('text_message');?></h4>
                </div><!--title-page-->

                <div class="registration">
                    <div class="text">
                        <article><?php echo isset($reg_message) ? $reg_message : '';?></article>
                        <article><?php echo isset($msg) ? $msg : '';?></article>
                    </div>
                </div><!--registration-->
            </div><!--col-->
       </div><!--row-->
    </div><!--container-->
</main>

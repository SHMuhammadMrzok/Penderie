<section class="login-page" style="background:url(<?php echo base_url().'assets/uploads/'.$this->config->item('login_background');?>) center center no-repeat;">
  <div class="container">
    <div class="row">
      <div class="col-md-5 mx-auto">
        <div class="login-container">
          <form class="w-100" method="post" action="<?php echo base_url();?>users/users/login">
            <div class="form-logo">
              <a href="<?php echo base_url();?>"  title="<?php echo lang('home');?>">
                <img src="<?php echo base_url();?>assets/uploads/<?php echo $this->config->item('logo');?>" alt="<?php echo $this->config->item('site_name');?>" />
              </a>
              <span><?php echo lang('login');?></span>
            </div>

            <div class="signup-link">
              <p><?php echo lang('not_a_member');?> <a href="<?php echo base_url();?>Register"> <?php echo lang('join_now');?></a></p>
              <?php  if(isset($_SESSION['login_error']))   { ?>
                  <div class="error-messege"><?php echo $_SESSION['login_error'];?></div>
              <?php } ?>

              <?php  if(isset($_SESSION['error_message']))   { ?>
                  <div class="error-messege"><?php echo $_SESSION['error_message'];?></div>
              <?php } ?>
            </div>
            <div class="form-group">
              <label for="username"><?php echo lang('email');?></label>
              <input type="text" name="email" required id="username" placeholder="example@example.example" class="form-control" autofocus />
            </div>

            <div class="form-group">
              <label for="password"><?php echo lang('password');?></label>
              <span class="show-password">
                <div class="svg-eye">
                  <svg>
                    <use xlink:href="#show-password"></use>
                  </svg>
                </div>

                <div class="svg-uneye">
                  <svg>
                    <use xlink:href="#un-show-password"></use>
                  </svg>
                </div>
              </span>
              <input required name="password" type="password" id="password" placeholder="<?php echo lang('password');?>" class="form-control" />
            </div>

            <div class="form-group">
              <div class="checkbox">
                <input type="checkbox" id="keepme" name="remember" />
                <label for="keepme"><?php echo lang('remember');?></label>
              </div>
            </div>

            <?php /*
            <div class="form-group">
              <p class="agree"><?php echo lang('agree_by_logging');?> <a href="<?php echo base_url();?>Page_Details/3"><?php echo lang('privacy_policy');?> </a>&
                <a href="<?php echo base_url();?>Page_Details/4"><?php echo lang('terms_conditions');?></a></p>
            </div>
            */?>

            <div class="form-group">
               <button class="button" formaction="<?php echo base_url();?>users/users/login"><?php echo lang('login');?></button>
            </div>

            <div class="form-group">
              <a href="<?php echo base_url();?>users/users/forget" class="forgotten"><?php echo lang('forget');?></a>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</section>

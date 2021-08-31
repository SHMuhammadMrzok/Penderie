<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title><?php echo ('site_name');?></title>
</head>

<body style="direction:rtl">

<div style="overflow: hidden; height: auto; width: 750px; margin: 20px auto; display: block">
	<header style="overflow:hidden;height:auto;width:100%; display:block; padding:20px 0; background:#434048; text-align:center;">
    	<img src="<?php echo base_url();?>assets/template/site/img/logo.png" alt=""/>    
    </header>
    <aside style="overflow: hidden; height: auto; width: 96.999%; display: block; background: #eee; padding: 10px 11px;" >
   	    <div style="overflow: hidden; height: auto; width: 100%; display: block; direction: ltr;" >
            <h1 style="color: #333; font-size: 22px; font-family: Tahoma, Geneva, sans-serif; line-height: 1.7;" ><?php echo sprintf(lang('email_forgot_password_heading'), $identity);?></h1>
            <p style="color:#333; font-size:14px; font-family:Tahoma, Geneva, sans-serif; line-height:1.5;" ><?php echo sprintf(lang('email_forgot_password_subheading'), anchor('users/users/reset_password/'. $forgotten_password_code, lang('email_forgot_password_link')));?></p>
        </div><!--row-->
    </aside>
    <footer style="overflow: hidden; height: auto; width: 100%; display: block; background: #434048; padding: 10px; text-align: center;">
    	<span style="color: #fff; font-size: 14px; font-family: Tahoma, Geneva, sans-serif;"><?php echo lang('copyrights');?> &copy; <?php echo date('Y');?></span>
    </footer>
</div><!--wrapper-->

</body>
</html>

<div class="col-md-4">
  <div class="menu-side-bar">
    <ul>

      <li>
        <a href="<?php echo base_url();?>Addresses_List" <?php echo isset($user_address)?'class="active"':'';?>>
          <svg><use xlink:href="#location"></use></svg><?php echo lang('user_address');?>
        </a>
      </li>
      <li>
        <a href="<?php echo base_url();?>Wishlist" <?php echo isset($wishlist)?'class="active"':'';?> >
          <svg><use xlink:href="#wishlist"></use></svg><?php echo lang('wishlist');?>
        </a>
      </li>
      <?php /*<li><a href="<?php echo base_url();?>Compare_Products" <?php echo isset($compare)?'class="active"':'';?>><?php echo lang('compare_products');?></a></li>*/?>
      <li>
        <a href="<?php echo base_url();?>Orders_Log" <?php echo isset($orders_log)?'class="active"':'';?>>
          <svg><use xlink:href="#order"></use></svg><?php echo lang('orders_log');?>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url();?>Balance_Recharge">
          <svg><use xlink:href="#recharge-balance"></use></svg><?php echo lang('recharge_pocket');?>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url();?>Payment_Log" <?php echo isset($balance_page)?'class="active"':'';?>>
          <svg><use xlink:href="#order"></use></svg><?php echo lang('balance_operations');?>
        </a>
      </li>

      <?php /*<li><a href="#">Support tickets</a></li>*/?>
      <li>
        <a href="<?php echo base_url();?>Edit_Profile" <?php echo isset($edit_profile)?'class="active"':'';?>>
          <svg><use xlink:href="#edit"></use></svg>
          <?php echo lang('edit_mydata');?>
        </a>
      </li>

      <?php if($this->config->item('allow_account_upgrading') == 'true'){?>
        <li>
        <a href="<?php echo base_url();?>UpgradeAccount">
          <svg><use xlink:href="#order"></use></svg><?php echo lang('renew_membership');?>
        </a>
      </li>
      <?php }?>

        <li>
          <a href="<?php echo base_url();?>User_logout">
            <svg><use xlink:href="#logout"></use></svg><?php echo lang('logout');?>
          </a>
        </li>


    </ul>
  </div>
</div>

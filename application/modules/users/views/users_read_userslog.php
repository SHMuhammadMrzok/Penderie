<div class="portlet-body">
    <div class="table-scrollable">
        <table class="table table-hover">
            <thead>
            <tr>
                <td ><?php echo lang('action');?></td>
                <td ><?php echo lang('module');?></td>
                <td ><?php echo lang('controller');?></td>
                <td ><?php echo lang('ip_address');?></td>
                <td ><?php echo lang('unix_time');?></td>
            </tr>
            </thead>
            <tbody>
            <?php if(count($user_log_data) !=0 && !empty($user_log_data)){
                     foreach($user_log_data as $row){
            ?>
            <tr>
                <td><?php echo $row->action_name;?></td>
                <td><?php echo $row->module;?></td>
                <td><?php echo $row->controller;?></td> 
                <td><?php echo $row->ip_address;?></td>
                <td><?php echo date('Y-d-m H:i', $row->unix_time);?></td>
            </tr>
            <?php }
            }else{?>
            <tr><td colspan="7" style="text-align: center;"><?php echo lang('no_data');?></td></tr>
            <?php }?>
           </tbody>
        </table>
    </div>
    <?php if(count($user_log_data) !=0 && !empty($user_log_data)){?>
        <ul class="pagination"><?php echo $page_links; ?></ul>
    <?php }?>
</div>
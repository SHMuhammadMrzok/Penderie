<div class="portlet-body">
    <div class="table-scrollable">
        <table class="table table-hover">
        <thead>
            <tr>
                <td ><?php echo lang('module');?></td>
                <td ><?php echo lang('controller');?></td>
                <td ><?php echo lang('method');?></td>
                <td ><?php echo lang('ip_address');?></td>
                <td ><?php echo lang('url');?></td>
                <td ><?php echo lang('date');?></td>
            </tr>
        </thead>
        <tbody>
        <?php 
        if(count($visits_log_data) != 0)
        {
            foreach($visits_log_data as $visit_log)
            {
        ?>
        <tr>
            <td><?php echo $visit_log->module;?></td>
            <td><?php echo $visit_log->controller;?></td>
            <td><?php echo $visit_log->method;?></td> 
            <td><?php echo long2ip($visit_log->ipaddress_long);?></td>
            <td><?php echo $visit_log->url;?></td>
            <td><?php echo date('Y-m-d H:i', $visit_log->unix_time);?></td>
        </tr>
        <?php
            }
        }
        else
        {
        ?>
          <tr><td colspan="7" style="text-align: center;"><?php echo lang('no_data');?></td></tr>
        <?php
        }
        ?>
        </tbody>
        </table>
    </div>
    <?php if(count($visits_log_data) != 0){?>
        <ul class="pagination"><?php echo $page_links; ?></ul>
    <?php }?>
</div>
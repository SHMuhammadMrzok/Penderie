<div class="form">
    <?php if(isset($error_msg)){?>
        <span class="error"><?php echo $error_msg;?></span>
    <?php }else{?>
        <span class="error"><?php if(isset($validation_msg)){echo $validation_msg;}?></span>
        <div class="tabbable-custom form">
    	   <div class="tab-content">
            
            <div class="tab-pane active" id="tab_cats">
    	      <div class="form-body">
                <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/admin/global/plugins/jstree/dist/themes/default/style.min.css"/>
                <script src="<?php echo base_url();?>assets/template/admin/global/plugins/jstree/dist/jstree.min.js"></script>
                <style>
                    .jstree-wholerow {
                      width: 1065px !important;
                      margin-left: 6px !important;
                    }
                </style>
                <script>
                $(function(){
                    
                   /*************************************************************************************/
                    $('#tree_2').jstree({
                            'plugins': ["wholerow", "checkbox", "types"]
                            
                        });
                   /*************************************************************************************/ 
                 $('#submit_form_cats').click(function(e){
                        //e.preventDefault();
                        var selectedElmsIds = $('#tree_2').jstree("get_selected");
                        
                        $.ajax({
                    		type: 'post',
                    		data:{ checked_nodes: selectedElmsIds, store_id: <?php echo $store_id?>},
                   			url:  $("#store_cats_form").attr("action"),
                     		success:function(data){
                     		  $("#message").html(data);
                     		}
                   		});
                 })
                   
                 /*************************************************************************/
                 
                })
                
                </script>
                <div class='alert alert-success alert-dismissable'><?php echo lang('name_of_store').' :  '.$store_data->name;?></div>
                <form id="store_cats_form" method="post" action="<?php echo base_url()?>stores/admin_stores/save_store_cats" class="form-horizontal form-bordered">
                    <div class="form-group last">
                    	<div id="tree_2" role="tree">
                           <ul>
                                <li role="treeitem" aria-expanded="true" id="check_all" aria-selected="false" data-jstree='{ "icon" : "fa fa-folder-open icon-state-warning ","opened" : true }'>
                                    <a class="jstree-anchor" href="#"><?php echo lang('check_all')?></a>
                                    <ul>
                                    <?php foreach($cats[0] as $cat){?>
                                   
                                        <li role="treeitem" aria-expanded="true" id="m_<?php echo $cat->id; ?>" aria-selected="false" data-jstree='{ "icon" : " icon-state-success ","opened" : true }'>
                                           <a class="jstree-anchor" href="#"> <?php echo $cat->name; ?> </a>
                                            
                                            <ul role="group" class="jstree-children">
                                                <?php  $sub_cat_name = '';
                                                        foreach($cats[$cat->id] as $sub_cat){ 
                                                            if($sub_cat->parent_id== $cat->id){
                                                                 $cat_check="";
                                                                 $cat_checked = '';
                                                                if(isset($store_old_cats) && in_array($sub_cat->id,$store_old_cats))
                                                                {
                                                                    $cat_check="checked";
                                                                    $cat_checked=',"selected" : true';
                                                                }
                                                                
                                                                $sub_cat_name=$sub_cat->name;
                                                                
                                                    ?>
                                                    
                                                    <li role="treeitem" id="c_<?php echo $sub_cat->id.'_'.$sub_cat->parent_id; ?>" aria-selected="false" aria-expanded="true" data-jstree='{ "icon" : "icon-state-danger ","opened" : true <?php echo $cat_checked;?>}'>
                                                        <a class="jstree-anchor" href="#"><?php echo $sub_cat_name; ?> </a>
                                                        <ul role="group" class="jstree-children" style="">
                                                        <?php
                                                            foreach($cats[$cat->id] as $sub_cat)
                                                            {
                                                                $cat_check="";
                                                                if(isset($store_old_cats) && in_array($sub_cat->id,$store_old_cats))
                                                                {
                                                                        $cat_check=',"selected" : true';
                                                                    }
                                                                }
                                                                    
                                                                $perm_name = $sub_cat->name;
                                                            ?>
                                                                  
                                                        
                                                    <?php } ?>
                                                    </ul>
                                                </li>
                                               
                                              <?php  }?>
                                            </ul>
                                        </li>
                                    
                                    <?php }?>
                                    </ul>
                            </li><!--check all-->
                            </ul>
                            <input type="hidden" name="cat_id" value="<?php echo $cat->id?>" />
                            <input type="hidden" name="checked_nodes" id="checked_nodes" value="" />
                         </div><!-- tree-->
                     </div><!-- form group-->
                  <div id="message"></div>
               </form>  
                
            </div>  
         
    	</div>
               
            <?php  echo isset($store_id) ? form_hidden('store_id', $store_id) : ''; ?>
            <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn green" id="submit_form_cats"><i class="fa fa-check"></i> <?php echo lang('submit');?></button>
    				</div>
    			</div>
            </div>
            
    	</div>
    </div>
  <?php }?>  		

</div>


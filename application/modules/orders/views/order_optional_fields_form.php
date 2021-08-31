<div class="form">
    <?php echo validation_errors();?>
    <?php $att=array('class'=> 'form-horizontal form-bordered', 'id'=>'products_form');
                      echo form_open_multipart($form_action, $att);?>
    <div class="tabbable-custom form">
    
	<div class="tab-content">
        <div class="tab-pane active" id="tab_general">
	      <div class="form-body">
              <?php
               if(isset($products_optional_fields) && count($products_optional_fields) != 0)
               {
                  foreach ($products_optional_fields as $product_options)
                  {
                    foreach ($product_options as $field)
                    {
                        echo form_hidden('product_id[]', $field->product_id);
                        $required       = '';
                        $required_span  = '';
                        
                        if($field->required == 1)
                        {
                            $required       = 'required';
                            $required_span  = " <span class='required'>*</span>";
                        }
                   ?>
                  
                    <div class="form-group">
                    
                       <label class="control-label col-md-3"><?php echo $field->product_name.' / '.$field->label.$required_span?></label>
                       <div class="col-md-4">
                          <?php
                             if($field->field_type_id == 2) // radio  
                             {
                                foreach($field->options as $option)
                                {?>
                                    <label><?php echo $option->field_value;?></label><?php echo $required_span;?>
                                    <input type="radio" name="optional_field[<?php echo $field->id;?>]" value="<?php echo $option->id;?>" <?php echo $required;?> />
                             <?php }
                             }
                             else if($field->field_type_id == 3) //check box
                             {
                                foreach($field->options as $option)
                                {?>
                                   <label><?php echo $option->field_value;?></label><?php echo $required_span;?>
                                    <input type="checkbox" name="optional_field[<?php echo $field->id;?>]" value="<?php echo $option->id;?>" <?php echo $required;?> /> 
                             <?php }
                             }
                             else if($field->field_type_id == 8) //select
                             {?>
                                
                               <select class="select2" name="optional_field[<?php echo $field->id;?>]" <?php echo $required;?>>
                                   <?php foreach($field->options as $option){?>
                                       <option value="<?php echo $option->id;?>"><?php echo $option->field_value;?></option> 
                                   <?php }
                                ?> 
                               </select>
                                     
                             <?php 
                             }
                             else if($field->field_type_id == 9) //file
                             {?>
                                
                                
                                <form id="fileupload" action="<?php echo base_url();?>uploadHandler2/do_upload" method="POST" enctype="multipart/form-data">
                                    <!-- Redirect browsers with JavaScript disabled to the origin page -->
                                    <noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>
                                    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                                    <div class="row fileupload-buttonbar">
                                        <div class="col-lg-7">
                                            <!-- The fileinput-button span is used to style the file input field as button -->
                                            <span class="btn btn-success fileinput-button">
                                                <i class="glyphicon glyphicon-plus"></i>
                                                <span><?php echo lang('add_files');?>...</span>
                                                <input type="file" name="files[]" accept=".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2" multiple >
                                            </span>
                                            <button type="submit" class="btn btn-primary start">
                                                <i class="glyphicon glyphicon-upload"></i>
                                                <span><?php echo lang('start_upload');?></span>
                                            </button>
                                            <button type="reset" class="btn btn-warning cancel">
                                                <i class="glyphicon glyphicon-ban-circle"></i>
                                                <span><?php echo lang('cancel_upload');?></span>
                                            </button>
                                            <button type="button" class="btn btn-danger delete">
                                                <i class="glyphicon glyphicon-trash"></i>
                                                <span><?php echo lang('delete');?></span>
                                            </button>
                                            <input type="checkbox" class="toggle">
                                            <!-- The global file processing state -->
                                            <span class="fileupload-process"></span>
                                        </div>
                                        <!-- The global progress state -->
                                        <div class="col-lg-5 fileupload-progress fade">
                                            <!-- The global progress bar -->
                                            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                            </div>
                                            <!-- The extended global progress state -->
                                            <div class="progress-extended">&nbsp;</div>
                                        </div>
                                    </div>
                                    <!-- The table listing the files available for upload/download -->
                                    <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
                                </form>
                                
                                
                                <!-- The template to display files available for download -->
                                <script id="template-download" type="text/x-tmpl">
                                {% for (var i=0, file; file=o.files[i]; i++) { %}
                                    <tr class="template-download fade">
                                        <td>
                                            <span class="preview">
                                                {% if (file.thumbnailUrl) { %}
                                                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                                                {% } %}
                                            </span>
                                        </td>
                                        <td>
                                            <p class="name">
                                                {% if (file.url) { %}
                                                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                                                {% } else { %}
                                                    <span>{%=file.name%}</span>
                                                {% } %}
                                            </p>
                                            {% if (file.error) { %}
                                                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                                            {% } %}
                                        </td>
                                        <td>
                                            <span class="size">{%=o.formatFileSize(file.size)%}</span>
                                        </td>
                                        <td>
                                            {% if (file.deleteUrl) { %}
                                                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                    <span><?php echo lang('delete');?></span>
                                                </button>
                                                <input type="hidden" name="optional_field[<?php echo $field->id;?>][]" value={%=file.name%} />
                                                <input type="checkbox" name="delete" value="1" class="toggle">
                                            {% } else { %}
                                                <button class="btn btn-warning cancel">
                                                    <i class="glyphicon glyphicon-ban-circle"></i>
                                                    <span>Cancel</span>
                                                </button>
                                            {% } %}
                                        </td>
                                    </tr>
                                {% } %}
                                </script>
                                
                                
                             <?php }
                             else
                             {?>
                             
                                <input name="optional_field[<?php echo $field->id;?>]" <?php echo $required;?> type="<?php echo $field->type_name;?>" class='form-control' placeholder="<?php echo $field->default_value;?>" />
                             
                             <?php 
                             }
                             echo form_error('optional_field['.$field->id.']'); 
                          ?>
                        </div>
                    </div>
                  <?php }
                    }
              ?>
                 
              <?php }?>  
                
                
                 
                 
             </div>
                 
           </div>
           
           <?php  echo isset($order_id) ? form_hidden('order_id', $order_id) : ''; ?>
            <div class="form-actions">
    			<div class="row">
    				<div class="col-md-offset-3 col-md-9">
                        <?php
                            $submit_att= array('class'=>"btn green");
                        ?>
    					<button type="submit" class="btn green"><i class="fa fa-check"></i> Submit</button>
    				 
    				</div>
    			</div>
            </div>
         </div>
        
	</div>
</div>
    		
<?php echo form_close();?>
</div>
  	
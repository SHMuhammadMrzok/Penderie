<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Article extends CI_Controller
{
    var $data = array();
    function __construct()
    {
        parent::__construct();
    }

    public function _output_data($output)
    {
        $this->data['content'] = $this->load->view('Admin/crud',$output,true);
        $this->load->view('Admin/main_frame',$this->data);
        
    }

    public function management()
    {
        $crud = new grocery_CRUD();
        $crud->set_table('articles');
        
        $crud->display_as('name',lang('arabic'));
        $crud->display_as('name_en','Title in English');
        $crud->display_as('source', 'Source');
        $crud->display_as('image', 'Image');
	    $crud->display_as('text', 'Text in arabic');
        $crud->display_as('text_en','Text in english');
        $crud->display_as('active','Activate');
        
        $crud->set_subject('article');
	
        //$crud->set_language(lang('Adminlang'));

        $crud->required_fields('name', 'text');
        $crud->set_field_upload('image', 'assets/uploads/');
        
        $crud->columns('name','name_en', 'source','image','text','text_en','active');
        $crud->fields('name','name_en', 'source','image','text','text_en','active');
        
        $crud->callback_add_field('active',array($this,'add_field_callback_pvalid'));
        $crud->callback_edit_field('active',array($this,'edit_field_callback_pvalid'));
        //$crud->callback_column('active',array($this,'activevalue'));
	   
      
        $output = $crud->render();
        $this->_output_data($output); 
    }
    
    function add_field_callback_pvalid()
    {
        return ' <input style="float: right;width: 15px;margin-left: 10px;" type="radio" name="pvalid" value="1" checked="checked" /> <span style="float: right;">Active</span>
            <input style="float: right;width: 15px;margin-left: 10px;" type="radio" name="pvalid" value="0" /><span style="float: right;">Not Active</span>';
    }
    
    function edit_field_callback_pvalid($value)
    {
        if($value == 1)
        {
            $check='checked="checked"';
            $asc='';
        }elseif($value == 0){
            $asc='checked="checked"';
            $check='';
        }
        return ' <div style="display:block;overflow:hidden;width:100%;height:auto;"><input style="float: right;width: 15px;margin-left: 10px;" type="radio" name="pvalid"'. $check .'value="1" /> <span style="float: right;">Active</span></div>
            <div style="display:block;overflow:hidden;width:100%;height:auto;"><input style="float: right;width: 15px;margin-left: 10px;" type="radio" name="pvalid"'. $asc .'  value="0" /><span style="float: right;">Not Active</span></div>';
    }
    

  


}
/* End of file add.php */
/* Location: ./application/modules/Importance/controllers/Add.php */
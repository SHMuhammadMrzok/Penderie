<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Upload_image extends CI_Controller
{
    public $data = array();
    public $crud;

    public function __construct()
    {
        parent::__construct();

        $this->crud = new grocery_CRUD();
        $params     = array($this->crud);

        $this->load->library('amazon_s3_uploads');


    }

    public function _output_data($output)
    {

        $this->data['content'] = $this->load->view('Admin/crud',$output,true);
        $this->load->view('Admin/main_frame',$this->data);

    }

    public function _site_output_data($output)
    {
        $this->data['content'] = $this->load->view('crud',$output,true);
        $this->load->view('site/main_frame',$this->data);

    }

    public function product_uploads()
    {

      require(APPPATH . 'includes/global_vars.php');

      $this->crud->set_table('gallery');

       $this->crud->display_as('image',lang('image'));

       $this->crud->set_field_upload('image','assets/uploads/products');
       $this->crud->callback_after_upload(array($this, 'upload_product_images_on_amazon_s3'));

       $output = $this->crud->render();

       $this->_output_data($output);
    }

    public function image_uploads()
    {
      require(APPPATH . 'includes/global_vars.php');

       $this->crud->set_table('gallery');

       $this->crud->display_as('image',lang('image'));

       $this->crud->set_field_upload('image','assets/uploads/');
       $this->crud->set_field_upload('image2','assets/uploads/');
       $this->crud->set_field_upload('image3','assets/uploads/');
       $this->crud->set_field_upload('image4','assets/uploads/');
       $this->crud->set_field_upload('image5','assets/uploads/');
       $this->crud->set_field_upload('video','assets/uploads/');

       $this->crud->callback_after_upload(array($this, 'upload_images_on_amazon_s3'));

       $output = $this->crud->render();
       $this->_output_data($output);
    }

    public function flag_uploads()
    {
      require(APPPATH . 'includes/global_vars.php');

        $this->crud->set_table('gallery');

       $this->crud->display_as('image',lang('image'));

       $this->crud->set_field_upload('image','assets/template/admin/global/img/flags/');

       $output = $this->crud->render();
       $this->_output_data($output);
    }

    public function upload_images_on_amazon_s3($uploader_response,$field_info, $files_to_upload)
    {
       $file_uploaded = $field_info->upload_path.'/'.$uploader_response[0]->name;
       $file_name = $uploader_response[0]->name;
       
       $this->amazon_s3_uploads->upload_to_o3($file_name);
    }

    public function upload_product_images_on_amazon_s3($uploader_response,$field_info, $files_to_upload)
    {
       $file_uploaded = $field_info->upload_path.'/'.$uploader_response[0]->name;
       $file_name     = $uploader_response[0]->name;
       $folder_name   = 'products/';

       $this->amazon_s3_uploads->upload_to_o3($file_name, $folder_name);
    }

    public function site_uploads()
    {
      require(APPPATH . 'includes/front_end_global.php');

       $this->crud->set_table('gallery');

       $this->crud->display_as('image',lang('image'));

       $this->crud->set_field_upload('image','assets/uploads/');
       $this->crud->set_field_upload('image2','assets/uploads/');
       $this->crud->set_field_upload('image3','assets/uploads/');
       $this->crud->set_field_upload('image4','assets/uploads/');
       $this->crud->set_field_upload('image5','assets/uploads/');
       $this->crud->set_field_upload('video','assets/uploads/');

       $this->crud->callback_after_upload(array($this, 'upload_images_on_amazon_s3'));

       $output = $this->crud->render();
       $this->_site_output_data($output);
    }


}

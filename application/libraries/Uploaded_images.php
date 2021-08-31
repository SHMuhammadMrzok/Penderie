<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

//ini_set('gd.jpeg_ignore_warning', 1);?

class Uploaded_images
{
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->config->load('images');
        $this->CI->load->library('image_lib');

    }

    public function resize_image($image_name, $type)
    {
      /*
      types:
      1 => slider
      2 => brands
      */

      /* if($type == 1) //slider
       {
          $realbath   = $this->CI->config->item('slider_realpath');
          $width      = $this->CI->config->item('slider_width');
          $height     = $this->CI->config->item('slider_height');
          $source_image = realpath(APPPATH. '../assets/uploads/'.$image_name);
          $quality    = $this->CI->config->item('quality');
        }
        elseif($type == 2) // brands
        {
          $realbath   = $this->CI->config->item('brands_realpath');
          $width      = $this->CI->config->item('brands_width');
          $height     = $this->CI->config->item('brands_height');
          $source_image = realpath(APPPATH. '../assets/uploads/'.$image_name);
          $quality    = $this->CI->config->item('quality');
        }
        elseif($type == 3) //products
        {
          $realbath   = $this->CI->config->item('products_realpath');
          $width      = $this->CI->config->item('products_width');
          $height     = $this->CI->config->item('products_height');
          $source_image = realpath(APPPATH. '../assets/uploads/products/'.$image_name);
          $quality    = $this->CI->config->item('quality');
        }
        elseif($type == 4) //products thumb for api
        {
          $realbath   = $this->CI->config->item('thumb_realpath');
          $width      = $this->CI->config->item('thumb_width');
          $height     = $this->CI->config->item('thumb_height');
          $source_image = realpath(APPPATH. '../assets/uploads/products/'.$image_name);
          $quality    = $this->CI->config->item('thumb_quality');
        }


        $new_image_name = $width.'x'.$height.'_'.$image_name;


        $configSize1['image_library']   = 'gd2';
        $configSize1['source_image']    = $source_image;

        $configSize1['create_thumb']    = false;
        $configSize1['maintain_ratio']  = TRUE;

        $configSize1['width']           = $width;
        $configSize1['height']          = $height;
        $configSize1['quality']         = $quality;
        $configSize1['new_image']       = $realbath.$new_image_name;

        $this->CI->image_lib->initialize($configSize1);
        $this->CI->image_lib->resize();

        echo $this->CI->image_lib->display_errors();
*/


    }


   public function unlink_slider_thumb($image_name)
   {
        $realpath   = $this->CI->config->item('slider_realpath');
        $width      = $this->CI->config->item('width');
        $height     = $this->CI->config->item('height');
        $thumb_name = $width.'x'.$height.'_'.$image_name;

        $path = $realpath.$thumb_name;

        chown($path, 666);

        unlink($path);
   }

    /*public function resize_image($image_name, $products =1)
    {
        // Fix Width & Heigh (Auto calculate)
        $width = 200;

        if($products == 1)
        {
            $dir_src    = 'assets/uploads/products/'.$image_name;
            $dir_dist   = 'assets/uploads/products/thumb/'.$image_name;

            $images = realpath(APPPATH. '../assets/uploads/products/thumb/'.$image_name);
        }
        else
        {
            $dir_src    = 'assets/uploads/'.$image_name;
            $dir_dist   = 'assets/uploads/thumb/'.$image_name;

            $images     = realpath(APPPATH. '../assets/uploads/thumb/'.$image_name);
        }


        if(copy($dir_src, $dir_dist))
        {
            $size   = GetimageSize($dir_dist);
            $height = round($width*$size[1]/$size[0]);
            $mime   = $size['mime'];

            switch ($mime)
            {
                case 'image/jpeg' : $images_orig = @imagecreatefromjpeg($dir_dist); break;
                case 'image/png' : $images_orig  = @imagecreatefrompng($dir_dist); break;
                case 'image/gif' : $images_orig  = @imagecreatefromgif($dir_dist); break;
                // Wrong type - exiting
                default: return false;
            }

            $photoX = ImagesX($images_orig);
            $photoY = ImagesY($images_orig);
            $images_fin = ImageCreateTrueColor($width, $height);
            ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

            switch ($mime)
            {
                case 'image/jpeg' : ImageJPEG($images_fin, $dir_dist); break;
                case 'image/png' :  ImagePNG($images_fin, $dir_dist); break;
                case 'image/gif' :  ImageGIF($images_fin, $dir_dist); break;
                // Wrong type - exiting
                default: return false;
            }

            //ImageJPEG($images_fin, $images);

            ImageDestroy($images_orig);
            ImageDestroy($images_fin);
        }

    }*/
}

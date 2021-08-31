<!doctype html>
<html lang="<?php echo ($this->session->userdata('direction')=='ltr')? 'en':'ar';?>" dir="<?php echo ($this->session->userdata('direction')=='ltr')? 'ltr':'rtl';?>">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?php echo base_url();?>assets/uploads/<?php echo $this->config->item('image2');?>" />
    <title><?php echo lang('site_title');?></title>

    <?php
    if($this->session->userdata('direction')=='ltr'){   $direction = '';}
    else{  $direction = '-rtl';}
    ?>


    <?php /*
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap/css/bootstrap<?php echo $direction;?>.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-switch/css/bootstrap-switch<?php echo $direction;?>.min.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css"/>

    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css"/>

    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>

    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css"/>
    */?>
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN PAGE STYLES -->
    <link href="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>

    <!-- END PAGE STYLES -->
    <!-- BEGIN THEME STYLES -->
    <!-- DOC: To use 'rounded corners' style just load 'components-rounded.css' stylesheet instead of 'components.css' in the below style tag -->
    <link href="<?php echo base_url();?>assets/template/admin/global/css/components<?php echo $direction;?>.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/global/css/plugins<?php echo $direction;?>.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/layout/css/layout<?php echo $direction;?>.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url();?>assets/template/admin/layout/css/themes/default<?php echo $direction;?>.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="<?php echo base_url();?>assets/template/admin/layout/css/custom<?php echo $direction;?>.css" rel="stylesheet" type="text/css"/>

    <link href="<?php echo base_url();?>assets/template/admin/layout/css/custom<?php echo $direction;?>.css" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css">
    <link href="<?php echo base_url();?>assets/template/admin/tags/jquery.tagit.css" rel="stylesheet" type="text/css">
    <!-- END THEME STYLES -->


    <script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>


    <!--START upload single image like in GROCERY CRUD-->

    <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/chosen/chosen.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/file-uploader.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/jquery.fileupload-ui.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/fancybox/jquery.fancybox.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/grocery_crud/css/jquery_plugins/file_upload/fileuploader.css" />


    <script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/jquery.chosen.min.js"></script>
    <script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/config/jquery.chosen.config.js"></script>
    <script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/ui/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/tmpl.min.js"></script>
    <script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/jquery.fancybox-1.3.4.js"></script>
    <script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/jquery.fileupload.js"></script>
    <script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/config/jquery.fileupload.config.js"></script>
    <script src="<?php echo base_url();?>assets/grocery_crud/js/jquery_plugins/config/jquery.fancybox.config.js"></script>
    <!--END upload single image like in GROCERY CRUD-->

    <script>
    var tb_pathToImage = "<?php echo base_url();?>assets/template/admin/img/loadingAnimation.gif";
    </script>
    <?php
    if(isset($css_files))
    {
      foreach($css_files as $css_file)
      {
        echo '<link href="'. base_url() . 'assets/template/admin/' . $css_file . '" rel="stylesheet" type="text/css"/>';
      }
    }
    ?>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo base_url();?>assets/template/merchant/css/sell.css">




</head>
<body>
<!--SVG Icons-->
<div style="display:none">
    <svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 500">
        <defs>
            <symbol id="play" viewBox="0 0 20 20">
                <g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="#f79d36" id="Icons-AV" transform="translate(-126.000000, -85.000000)"><g id="play-circle-fill" transform="translate(126.000000, 85.000000)"><path d="M10,0 C4.5,0 0,4.5 0,10 C0,15.5 4.5,20 10,20 C15.5,20 20,15.5 20,10 C20,4.5 15.5,0 10,0 L10,0 Z M8,14.5 L8,5.5 L14,10 L8,14.5 L8,14.5 Z" id="Shape"/></g></g></g>
            </symbol>
            <symbol id="arrow" viewBox="0 0 612.002 612.002">
                <g>
                    <g>
                        <path d="M592.639,358.376L423.706,189.439c-64.904-64.892-170.509-64.892-235.421,0.005L19.363,358.379
			c-25.817,25.817-25.817,67.674,0,93.489c25.817,25.817,67.679,25.819,93.491-0.002l168.92-168.927
			c13.354-13.357,35.092-13.361,48.444-0.005l168.93,168.932c12.91,12.907,29.825,19.365,46.747,19.365
			c16.915,0,33.835-6.455,46.747-19.365C618.456,426.051,618.456,384.193,592.639,358.376z"/>
                    </g>
                </g>
            </symbol>
            <symbol id="gift" >
                <g transform="translate(0 -1028.4)"><path d="m1 1037.4v3 2 7c0 1.1 0.8954 2 2 2h9 9c1.105 0 2-0.9 2-2v-7-2-3h-22z" fill="#f39c12"/><path d="m1 1036.4v3 2 7c0 1.1 0.8954 2 2 2h9 9c1.105 0 2-0.9 2-2v-7-2-3h-22z" fill="#f1c40f"/><path d="m2 1034.4c-1.1046 0-2 0.9-2 2v2h24v-2c0-1.1-0.895-2-2-2h-2-7-2-7-2z" fill="#f1c40f"/><rect fill="#e74c3c" height="18" width="6" x="9" y="1033.4"/><path d="m7.8848 1029.4c-0.9986-0.1-1.9733 0.4-2.5079 1.2-0.7776 1.2-0.3129 2.7 1.034 3.4 0.4331 0.3 0.897 0.4 1.3639 0.4h0.231 3.3002 1.386 3.299 0.231c0.467 0 0.942-0.1 1.375-0.4 1.347-0.7 1.801-2.2 1.023-3.4-0.777-1.2-2.492-1.6-3.839-0.9-0.433 0.2-0.773 0.5-1.011 0.9h-0.022c-0.032 0.1-0.06 0.1-0.088 0.2l-1.661 2.5-1.661-2.5c-0.028-0.1-0.056-0.1-0.088-0.2h-0.022c-0.2386-0.4-0.5681-0.7-1.0013-0.9-0.4209-0.2-0.888-0.3-1.3419-0.3zm-0.165 1.2c0.2705 0 0.5504 0.1 0.803 0.2 0.0847 0 0.1609 0.1 0.2309 0.1 0.0493 0.1 0.0902 0.1 0.132 0.2h0.033c0.0225 0 0.046 0.1 0.066 0.1h0.033 0.011c0.016 0.1 0.041 0.1 0.055 0.1l1.1663 1.8h-2.3322-0.132-0.055-0.11-0.055c-0.064 0-0.1241 0-0.187-0.1-0.0894 0-0.1792 0-0.264-0.1-0.1683 0-0.3187-0.2-0.429-0.3-0.3307-0.4-0.3796-0.9-0.088-1.4 0.243-0.3 0.6711-0.6 1.122-0.6zm8.5682 0c0.451 0 0.879 0.3 1.122 0.6 0.292 0.5 0.232 1-0.099 1.4-0.11 0.1-0.249 0.3-0.418 0.3-0.084 0.1-0.174 0.1-0.264 0.1-0.062 0.1-0.134 0.1-0.198 0.1h-0.055-0.11-0.055-0.132-2.331l1.165-1.8c0.014 0 0.039 0 0.055-0.1h0.011c0.003 0-0.002 0 0 0h0.033c0.02 0 0.044-0.1 0.066-0.1h0.033c0.042-0.1 0.094-0.1 0.143-0.2 0.071 0 0.147-0.1 0.231-0.1 0.253-0.1 0.533-0.2 0.803-0.2z" fill="#c0392b"/><rect fill="#f39c12" height="1" width="22" x="1" y="1038.4"/><rect fill="#c0392b" height="1" width="6" x="9" y="1038.4"/><rect fill="#c0392b" height="1" width="6" x="9" y="1050.4"/></g>
            </symbol>
            <symbol id="comment"viewBox="0 0 24 24" >
                <g id="info"/><g id="icons"><path d="M20,1H4C1.8,1,0,2.8,0,5v10c0,2.2,1.8,4,4,4v3c0,0.9,1.1,1.3,1.7,0.7L9.4,19H20c2.2,0,4-1.8,4-4V5   C24,2.8,22.2,1,20,1z M14,13H8c-0.6,0-1-0.4-1-1c0-0.6,0.4-1,1-1h6c0.6,0,1,0.4,1,1C15,12.6,14.6,13,14,13z M16,9H8   C7.4,9,7,8.6,7,8c0-0.6,0.4-1,1-1h8c0.6,0,1,0.4,1,1C17,8.6,16.6,9,16,9z" id="message"/></g>            </symbol>
            <!---->
            <symbol id="document" viewBox="0 0 24 24">
                <path d="M21.635,6.366c-0.467-0.772-1.043-1.528-1.748-2.229c-0.713-0.708-1.482-1.288-2.269-1.754L19,1C19,1,21,1,22,2S23,5,23,5  L21.635,6.366z M10,18H6v-4l0.48-0.48c0.813,0.385,1.621,0.926,2.348,1.652c0.728,0.729,1.268,1.535,1.652,2.348L10,18z M20.48,7.52  l-8.846,8.845c-0.467-0.771-1.043-1.529-1.748-2.229c-0.712-0.709-1.482-1.288-2.269-1.754L16.48,3.52  c0.813,0.383,1.621,0.924,2.348,1.651C19.557,5.899,20.097,6.707,20.48,7.52z M4,4v16h16v-7l3-3.038V21c0,1.105-0.896,2-2,2H3  c-1.104,0-2-0.895-2-2V3c0-1.104,0.896-2,2-2h11.01l-3.001,3H4z"/>
            </symbol>
            <symbol id="person" viewBox="0 0 32 32">
                <g><path d="M22.417,14.836c-1.209,2.763-3.846,5.074-6.403,5.074c-3.122,0-5.39-2.284-6.599-5.046   c-7.031,3.642-6.145,12.859-6.145,12.859c0,1.262,0.994,1.445,2.162,1.445h10.581h10.565c1.17,0,2.167-0.184,2.167-1.445   C28.746,27.723,29.447,18.479,22.417,14.836z"/><path d="M16.013,18.412c3.521,0,6.32-5.04,6.32-9.204c0-4.165-2.854-7.541-6.375-7.541   c-3.521,0-6.376,3.376-6.376,7.541C9.582,13.373,12.491,18.412,16.013,18.412z" /></g>
            </symbol>
            <symbol id="circle" viewBox="0 0 459 459">
                <g id="memory">
                    <path d="M306,153H153v153h153V153z M255,255h-51v-51h51V255z M459,204v-51h-51v-51c0-28.05-22.95-51-51-51h-51V0h-51v51h-51V0h-51    v51h-51c-28.05,0-51,22.95-51,51v51H0v51h51v51H0v51h51v51c0,28.05,22.95,51,51,51h51v51h51v-51h51v51h51v-51h51    c28.05,0,51-22.95,51-51v-51h51v-51h-51v-51H459z M357,357H102V102h255V357z"/>
                </g>
            </symbol>
            <!---->
            <symbol id="shopping" viewBox="0 0 510 510">
                <g>
                    <g id="shopping-cart">
                        <path d="M153,408c-28.05,0-51,22.95-51,51s22.95,51,51,51s51-22.95,51-51S181.05,408,153,408z M0,0v51h51l91.8,193.8L107.1,306
			c-2.55,7.65-5.1,17.85-5.1,25.5c0,28.05,22.95,51,51,51h306v-51H163.2c-2.55,0-5.1-2.55-5.1-5.1v-2.551l22.95-43.35h188.7
			c20.4,0,35.7-10.2,43.35-25.5L504.9,89.25c5.1-5.1,5.1-7.65,5.1-12.75c0-15.3-10.2-25.5-25.5-25.5H107.1L84.15,0H0z M408,408
			c-28.05,0-51,22.95-51,51s22.95,51,51,51s51-22.95,51-51S436.05,408,408,408z"/>
                    </g>
                </g>
            </symbol>
            <symbol id="setting" viewBox="0 0 64 64">
                <g><g id="Icon-Gear" transform="translate(226.000000, 426.000000)"><path class="st0" d="M-194-382.7c-6.2,0-11.3-5.1-11.3-11.3s5.1-11.3,11.3-11.3s11.3,5.1,11.3,11.3     S-187.8-382.7-194-382.7L-194-382.7z M-194-402.9c-4.9,0-8.9,4-8.9,8.9s4,8.9,8.9,8.9s8.9-4,8.9-8.9S-189.1-402.9-194-402.9     L-194-402.9z" id="Fill-45"/><path class="st0" d="M-190.6-370.1h-6.8l-1.9-5.8c-1.3-0.4-2.5-0.9-3.7-1.5l-5.5,2.8l-4.8-4.8l2.8-5.5     c-0.6-1.2-1.2-2.4-1.5-3.7l-5.8-1.9v-6.8l5.8-1.9c0.4-1.3,0.9-2.5,1.5-3.7l-2.8-5.5l4.8-4.8l5.5,2.8c1.2-0.6,2.4-1.2,3.7-1.5     l1.9-5.8h6.8l1.9,5.8c1.3,0.4,2.5,0.9,3.7,1.5l5.5-2.8l4.8,4.8l-2.8,5.5c0.6,1.2,1.2,2.4,1.5,3.7l5.8,1.9v6.8l-5.8,1.9     c-0.4,1.3-0.9,2.5-1.5,3.7l2.8,5.5l-4.8,4.8l-5.5-2.8c-1.2,0.6-2.4,1.2-3.7,1.5L-190.6-370.1L-190.6-370.1z M-195.7-372.4h3.4     l1.8-5.4l0.6-0.2c1.5-0.4,2.9-1,4.3-1.8l0.6-0.3l5.1,2.6l2.4-2.4l-2.6-5.1l0.3-0.6c0.8-1.3,1.4-2.8,1.8-4.3l0.2-0.6l5.4-1.8v-3.4     l-5.4-1.8l-0.2-0.6c-0.4-1.5-1-3-1.8-4.3l-0.3-0.6l2.6-5.1l-2.4-2.4l-5.1,2.6l-0.6-0.3c-1.3-0.8-2.8-1.4-4.3-1.8l-0.6-0.2     l-1.8-5.4h-3.4l-1.8,5.4l-0.6,0.2c-1.5,0.4-2.9,1-4.3,1.8l-0.6,0.3l-5.1-2.6l-2.4,2.4l2.6,5.1l-0.3,0.6c-0.8,1.3-1.4,2.8-1.8,4.3     l-0.2,0.6l-5.4,1.8v3.4l5.4,1.8l0.2,0.6c0.4,1.5,1,3,1.8,4.3l0.3,0.6l-2.6,5.1l2.4,2.4l5.1-2.6l0.6,0.3c1.3,0.8,2.8,1.4,4.3,1.8     l0.6,0.2L-195.7-372.4L-195.7-372.4z" id="Fill-46"/></g></g>
            </symbol>
            <symbol id="chart" viewBox="0 0 32 32">
                <g transform="translate(672 96)"><path d="M-640-66v2h-32v-32h4v2h-2v4h2v2h-2v4h2v2h-2v4h2v2h-2v4h2v2h-2v4H-640z M-663-70c1.656,0,3-1.344,3-3   c0-0.647-0.21-1.243-0.56-1.733l3.824-3.823c0.49,0.348,1.087,0.559,1.734,0.559c0.816,0,1.555-0.328,2.096-0.858l3.301,2.399   c-0.244,0.435-0.396,0.926-0.396,1.459c0,1.656,1.344,3,3,3s3-1.344,3-3c0-1.139-0.643-2.119-1.578-2.627l3.018-12.063l1.934,0.483   L-642-93.998l-3.467,3.584l1.936,0.482l-2.996,11.979C-646.68-77.976-646.838-78-647-78c-0.762,0-1.449,0.293-1.979,0.763   l-3.348-2.434c0.201-0.403,0.324-0.85,0.324-1.329c0-1.655-1.344-3-3-3c-1.657,0-3,1.345-3,3c0,0.647,0.21,1.242,0.559,1.734   l-3.824,3.823c-0.49-0.349-1.086-0.56-1.733-0.56c-1.657,0-3,1.344-3,3S-664.657-70.001-663-70L-663-70z"/></g>
            </symbol>
            <symbol id="write" viewBox="0 0 48 48">
                <path clip-rule="evenodd" d="M44.929,14.391c-0.046,0.099-0.102,0.194-0.183,0.276L16.84,42.572  c-0.109,0.188-0.26,0.352-0.475,0.434l-13.852,3.88c-0.029,0.014-0.062,0.016-0.094,0.026l-0.047,0.014  c-0.008,0.003-0.017,0.001-0.024,0.004c-0.094,0.025-0.187,0.046-0.286,0.045c-0.098,0.003-0.189-0.015-0.282-0.041  c-0.021-0.006-0.04-0.002-0.061-0.009c-0.008-0.003-0.013-0.01-0.021-0.013c-0.088-0.033-0.164-0.083-0.24-0.141  c-0.039-0.028-0.08-0.053-0.113-0.086s-0.058-0.074-0.086-0.113c-0.058-0.075-0.107-0.152-0.141-0.24  c-0.004-0.008-0.01-0.013-0.013-0.021c-0.007-0.02-0.003-0.04-0.009-0.061c-0.025-0.092-0.043-0.184-0.041-0.281  c0-0.1,0.02-0.193,0.045-0.287c0.004-0.008,0.001-0.016,0.004-0.023l0.014-0.049c0.011-0.03,0.013-0.063,0.026-0.093l3.88-13.852  c0.082-0.216,0.246-0.364,0.434-0.475l27.479-27.48c0.04-0.045,0.087-0.083,0.128-0.127l0.299-0.299  c0.015-0.015,0.034-0.02,0.05-0.034C34.858,1.87,36.796,1,38.953,1C43.397,1,47,4.603,47,9.047  C47,11.108,46.205,12.969,44.929,14.391z M41.15,15.5l-3.619-3.619L13.891,35.522c0.004,0.008,0.014,0.011,0.018,0.019l2.373,4.827  L41.15,15.5z M3.559,44.473l2.785-0.779l-2.006-2.005L3.559,44.473z M4.943,39.53l3.558,3.559l6.12-1.715  c0,0-2.586-5.372-2.59-5.374l-5.374-2.59L4.943,39.53z M12.49,34.124c0.008,0.004,0.011,0.013,0.019,0.018L36.15,10.5l-3.619-3.619  L7.663,31.749L12.49,34.124z M38.922,3c-1.782,0-3.372,0.776-4.489,1.994l-0.007-0.007L33.912,5.5l8.619,8.619l0.527-0.528  l-0.006-0.006c1.209-1.116,1.979-2.701,1.979-4.476C45.031,5.735,42.296,3,38.922,3z" fill-rule="evenodd"/>
            </symbol>
            <!---->
            <symbol id="facebook" viewBox="0 0 512 512">
                <g id="g5991"><rect height="512" id="rect2987" rx="64" ry="64" style="fill-opacity:1;fill-rule:nonzero;stroke:none" width="512" x="0" y="0"/><path d="M 286.96783,455.99972 V 273.53753 h 61.244 l 9.1699,-71.10266 h -70.41246 v -45.39493 c 0,-20.58828 5.72066,-34.61942 35.23496,-34.61942 l 37.6554,-0.0112 V 58.807915 c -6.5097,-0.87381 -28.8571,-2.80794 -54.8675,-2.80794 -54.28803,0 -91.44995,33.14585 -91.44995,93.998125 v 52.43708 h -61.40181 v 71.10266 h 61.40039 v 182.46219 h 73.42707 z" id="f_1_" style="fill:#ffffff"/></g>
            </symbol>
            <symbol id="facebookFooter" viewBox="0 0 512 512">
                <g id="g5991"><rect height="512" id="rect2987" rx="64" ry="64" style="fill-opacity:1;fill-rule:nonzero;stroke:none" width="512" x="0" y="0"/><path d="M 286.96783,455.99972 V 273.53753 h 61.244 l 9.1699,-71.10266 h -70.41246 v -45.39493 c 0,-20.58828 5.72066,-34.61942 35.23496,-34.61942 l 37.6554,-0.0112 V 58.807915 c -6.5097,-0.87381 -28.8571,-2.80794 -54.8675,-2.80794 -54.28803,0 -91.44995,33.14585 -91.44995,93.998125 v 52.43708 h -61.40181 v 71.10266 h 61.40039 v 182.46219 h 73.42707 z" id="f_1_" style="fill:#282828"/></g>
            </symbol>
            <symbol id="twitter" viewBox="0 0 612 612">
                <g>
                    <path d="M612,116.258c-22.525,9.981-46.694,16.75-72.088,19.772c25.929-15.527,45.777-40.155,55.184-69.411
			c-24.322,14.379-51.169,24.82-79.775,30.48c-22.907-24.437-55.49-39.658-91.63-39.658c-69.334,0-125.551,56.217-125.551,125.513
			c0,9.828,1.109,19.427,3.251,28.606C197.065,206.32,104.556,156.337,42.641,80.386c-10.823,18.51-16.98,40.078-16.98,63.101
			c0,43.559,22.181,81.993,55.835,104.479c-20.575-0.688-39.926-6.348-56.867-15.756v1.568c0,60.806,43.291,111.554,100.693,123.104
			c-10.517,2.83-21.607,4.398-33.08,4.398c-8.107,0-15.947-0.803-23.634-2.333c15.985,49.907,62.336,86.199,117.253,87.194
			c-42.947,33.654-97.099,53.655-155.916,53.655c-10.134,0-20.116-0.612-29.944-1.721c55.567,35.681,121.536,56.485,192.438,56.485
			c230.948,0,357.188-191.291,357.188-357.188l-0.421-16.253C573.872,163.526,595.211,141.422,612,116.258z" />
                </g>
            </symbol>
            <symbol id="instagram" viewBox="0 0 512 512">
                <g>
                    <g>
                        <path d="M373.659,0H138.341C62.06,0,0,62.06,0,138.341v235.318C0,449.94,62.06,512,138.341,512h235.318
			C449.94,512,512,449.94,512,373.659V138.341C512,62.06,449.94,0,373.659,0z M470.636,373.659
			c0,53.473-43.503,96.977-96.977,96.977H138.341c-53.473,0-96.977-43.503-96.977-96.977V138.341
			c0-53.473,43.503-96.977,96.977-96.977h235.318c53.473,0,96.977,43.503,96.977,96.977V373.659z" />
                    </g>
                </g>
                <g>
                    <g>
                        <path d="M370.586,238.141c-3.64-24.547-14.839-46.795-32.386-64.342c-17.547-17.546-39.795-28.746-64.341-32.385
			c-11.176-1.657-22.507-1.657-33.682,0c-30.336,4.499-57.103,20.541-75.372,45.172c-18.269,24.631-25.854,54.903-21.355,85.237
			c4.499,30.335,20.541,57.102,45.172,75.372c19.996,14.831,43.706,22.619,68.153,22.619c5.667,0,11.375-0.418,17.083-1.265
			c30.336-4.499,57.103-20.541,75.372-45.172C367.5,298.747,375.085,268.476,370.586,238.141z M267.791,327.632
			c-19.405,2.882-38.77-1.973-54.527-13.66c-15.757-11.687-26.019-28.811-28.896-48.216c-2.878-19.405,1.973-38.77,13.66-54.527
			c11.688-15.757,28.811-26.019,48.217-28.897c3.574-0.53,7.173-0.795,10.772-0.795s7.199,0.265,10.773,0.796
			c32.231,4.779,57.098,29.645,61.878,61.877C335.608,284.268,307.851,321.692,267.791,327.632z" />
                    </g>
                </g>
                <g>
                    <g>
                        <path d="M400.049,111.951c-3.852-3.851-9.183-6.058-14.625-6.058c-5.442,0-10.773,2.206-14.625,6.058
			c-3.851,3.852-6.058,9.174-6.058,14.625c0,5.451,2.207,10.773,6.058,14.625c3.852,3.851,9.183,6.058,14.625,6.058
			c5.442,0,10.773-2.206,14.625-6.058c3.851-3.852,6.058-9.183,6.058-14.625C406.107,121.133,403.9,115.802,400.049,111.951z" />
                    </g>
                </g>
            </symbol>

        </defs>
    </svg>
</div>

<div class="large-container back-color">
    <!--HeaderStart-->
    <header>
        <div class="container-fluid">
            <div class="row align-items-center">

                <div class="col flex-grow-0">
                    <div class="logo">
                        <a href="<?php echo base_url();?>">
                            <img src="<?php echo base_url();?>assets/uploads/<?php echo $this->config->item('logo');?>">
                        </a>
                    </div>
                </div>
                <div class="col flex-grow-1"></div>
                <div class="col flex-grow-0">
                    <div class="nav-action">
                   
                
                    <div class="lang">
                            <ul class="">
                                <?php foreach($structure_languages as $lang){?>
                                <li>
                                    <a href="<?php echo base_url();?>admin/change_lang/<?php echo $lang->language ;?>">
                                    <img alt="" src="<?php echo base_url();?>assets/template/admin/global/img/flags/<?php echo $lang->flag;?>"/> <?php echo $lang->name;?> </a>
                                </li>
                                <?php }?>
                            </ul>
                        
                        </div>
                   
                  
                    <div class="user-area">
                        <ul class="dropdown-menu dropdown-menu-default ">
                                    <li>
                                        <a href="<?php echo base_url();?>users/admin_users/edit/<?php echo $this->data['user_id'];?>">
                                        <i class="icon-user"></i><?php echo lang('my_personal');?></a>
                                    </li>

                                    <li>
                                        <a href="<?php echo base_url();?>admin/logout">
                                        <i class="icon-key"></i><?php echo lang('logout');?></a>
                                    </li>
                                </ul>
                    </div>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <!--dashboard-->


<?php
                        $active_module=$this->admin_bootstrap->get_module();
                        $active_controller=$this->admin_bootstrap->get_controller();
                        $active_method=$this->admin_bootstrap->get_method();
                ?>
                
    <!--dashboard-->
    <main class="dashboard">
        <div class="container-fluid">
            <div class="row dashboard-content">
                <?php if(!isset($no_menu)){?>
                    <div class="col-2 dashboard-right">
                        <ul>
                            <!--#step1-->
                            <li class="active">
                                <a href="<?php echo base_url();?>sell/">
                                    <p><?php echo lang('dashboard');?></p>
                                </a>
                            </li>
                            <?php

                            $active_module=$this->admin_bootstrap->get_module();
                            $active_controller=$this->admin_bootstrap->get_controller();
                            $active_method=$this->admin_bootstrap->get_method();

                            $menu_permissions = $this->admin_bootstrap->get_menu_permissions();
                            $modules     = $menu_permissions[0];
                            $controllers = $menu_permissions[1];
                            foreach($modules as $module_id=>$module_array){
                            ?>

                                <!--Block-->
                                <li class="menu-click <?php if ($active_module == $module_array['module']){?>activate <?php } ?>" >
                                    <div class="sub-menu-item">
                                        <i class="<?php echo $module_array['module_icon_class']; ?>"></i>
                                        <p><?php echo $module_array['module_name']; ?></p>
                                        <svg><use xlink:href="#arrow"></use></svg>
                                    </div>
                                </li>

                                <!--#step2 To #step6-->
                                <div class="sub-menu" <?php if ($active_module == $module_array['module']){?> style="display: block;" <?php } ?>>
                                    <ul>

                                        <?php
                                        
                                       foreach($controllers as $key=>$controller_row)
                                       {
                                            if($controller_row->module_id == $module_id)
                                            {
                                                $controller_name='';
                                                $method='';

                                                if(in_array('index', $controller_row->methods))
                                                {
                                                    $method="";
                                                }
                                                elseif(in_array('add', $controller_row->methods))
                                                {
                                                    $method="add";
                                                }

                                                $controller_name = $controller_row->controller_name;

                                                ?>
                                                <?php {?>

                                                <li <?php if ($active_controller == $controller_row->controller){?> class="" <?php } ?>>
                                                    <a href="<?php echo base_url();?><?php echo $controller_row->module_path; ?>/<?php echo $controller_row->controller; ?>/<?php echo $method; ?>">
                                                        <i class="<?php echo ($controller_row->icon_class=='')? 'fa  fa-file-o' : $controller_row->icon_class; ?>"></i>
                                                        <span><?php echo $controller_name ; ?></span>
                                                    </a>
                                                </li>
                                                <?php //if($controller_row->controller == 'admin_products'){?>
                                                <li <?php if ($active_controller == $controller_row->controller){?> class="" <?php } ?> >

                                                        <a href="<?php echo base_url();?><?php echo $controller_row->module_path; ?>/<?php echo $controller_row->controller; ?>/<?php echo 'seller_all_products'; ?>">
                                                            <i class="<?php echo ($controller_row->icon_class=='')? 'fa  fa-file-o' : $controller_row->icon_class; ?>"></i>
                                                            <span><?php echo lang('all_prducts') ; ?></span>
                                                        </a>
                                                </li>
                                                <?php //} ?>

                                                <?php /* if($controller_row->controller == 'admin_products'){?>
                                                <li <?php if ($active_controller == $controller_row->controller){?> class="" <?php } ?>>

                                                        <a href="<?php echo base_url();?><?php echo $controller_row->module_path; ?>/<?php echo $controller_row->controller; ?>/<?php echo 'index/1'; ?>">
                                                            <i class="<?php echo ($controller_row->icon_class=='')? 'fa  fa-file-o' : $controller_row->icon_class; ?>"></i>
                                                            <span><?php echo lang('all_prducts') ; ?></span>
                                                        </a>
                                                </li>
                                                <?php } */ ?>

                                                <?php }
                                            }
                                        }?>

                                    </ul>
                                </div>
                            <?php }?>
                            <li><a href="<?php echo base_url();?>sell/edit_my_data"><?php echo lang('my_personal');?></a></li>
                            <li><a href="<?php echo base_url();?>sell/edit_my_store"><?php echo lang('edit_my_store');?></a></li>
                            <li><a href="<?php echo base_url();?>sell/logout"><?php echo lang('logout');?></a></li>
                        </ul>
                    </div>
                <?php }?>



                <div class="col-10 dashboard-left portlet">
                    <ul>
                        <li>
                            <?php if(!isset($no_menu)){?>
                                <?php $this->load->view('Sell/breadcrumb.php'); ?>
                            <?php }?>
                            <?php echo $content;?>

                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>



    <!--footerStart-->
    <footer>
        <div class="container-fluid">
            <div class="row">
                <div class="col social">
                  <?php if($this->config->item('facebook') != ''){?>
                    <a href="<?php echo $this->config->item('facebook');?>" target="_blank"><svg><use xlink:href="#facebook"></use></svg></a>
                  <?php }if($this->config->item('twitter') != ''){?>
                    <a href="<?php echo $this->config->item('twitter');?>" target="_blank"><svg><use xlink:href="#twitter"></use></svg></a>
                  <?php }if($this->config->item('instagram') != ''){?>
                    <a href="<?php echo $this->config->item('instagram');?>" target="_blank"><svg><use xlink:href="#instagram"></use></svg></a>
                  <?php }?>
                </div>
                <div class="col copyright">
                    <span><?php echo date('Y');?> &copy; <?php echo lang('site_title');?> </span>
                </div>
            </div>
        </div>
    </footer>
</div>





<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- BEGIN PAGE LEVEL PLUGINS -->


<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/select2/select2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/pages/scripts/components-pickers.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/pages/scripts/components-form-tools.js" type="text/javascript"></script>



<?php
if(isset($js_files))
{
  foreach($js_files as $js_file)
  {
    echo '<script src="'.base_url().'assets/template/admin/'.$js_file.'" type="text/javascript"></script>';

  }
}
?>

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>assets/template/admin/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>





<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script>
var assetsPath = '<?php echo base_url();?>';

    var globalImgPath = 'global/img/';

    var globalPluginsPath = 'global/plugins/';

    var globalCssPath = 'global/css/';
</script>

<script src="<?php echo base_url();?>assets/template/admin/global/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/layout/scripts/demo.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/pages/scripts/index.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/template/admin/pages/scripts/tasks.js" type="text/javascript"></script>


<!--Drag and Drop Sortable Table Row-->
<script src="<?php echo base_url();?>assets/template/admin/global/plugins/jquery.rowsorter.js"></script>
<!--jquery date-->

<?php /*<script src="<?php echo base_url();?>assets/template/merchant/js/jquery-3.3.1.min.js"></script>*/?>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="<?php echo base_url();?>assets/template/merchant/js/Script.js"></script>

<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   QuickSidebar.init(); // init quick sidebar
   Demo.init(); // init demo features
   Index.init();
   Tasks.initDashboardWidget();
   $('.select2').select2();
   ComponentsPickers.init();

   <?php
       if(isset($js_code))
       {
          echo $js_code;
       }
   ?>

});
</script>

<script>
    function showToast($msg,$title,$type)
    {
        var msg = $msg;
        var title = $title;
        var shortCutFunction = $type;

        toastr.options = {
              "closeButton": true,
              "debug": false,
              "positionClass": "toast-top-center",
              "onclick": null,
              "showDuration": "100000",
              "hideDuration": "100000",
              "timeOut": "5000000",
              "extendedTimeOut": "100000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"

           }

            var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
            $toastlast = $toast;
            if ($toast.find("#okBtn").length) {
                $toast.delegate("#okBtn", "click", function () {
                    alert("you clicked me. i was toast #" + toastIndex + ". goodbye!");
                    $toast.remove();
                });
            }
            if ($toast.find("#surpriseBtn").length) {
                $toast.delegate("#surpriseBtn", "click", function () {
                    alert("Surprise! you clicked me. i was toast #" + toastIndex + ". You could perform an action here.");
                });
            }

            $("#clearlasttoast").click(function () {
                toastr.clear($toastlast);
            });
    }
</script>

<script>
    <?php if($this->session->flashdata('qty_error')){ ?>
        showToast('<?php echo lang('qty_error');?>','<?php echo $this->session->flashdata('qty_error');?>','error');
    <?php }?>
</script>



</body>
</html>

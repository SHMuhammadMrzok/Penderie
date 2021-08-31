<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<title>
		<?php echo isset($page_title) ? $page_title : $this->config->item('site_name');?>
	</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php if(isset($meta_keywords)){ ?>
	<meta name="keywords" content="<?php echo isset($meta_keywords) ? $meta_keywords : '';?>"/>
	<?php } else{ ?>
	<meta name="keywords" content="<?php  echo $this->config->item('keywords');?>"/>
	<?php }
        if(isset($meta_description)){ ?>
	<meta name="description" content="<?php echo isset($meta_description) ? $meta_description : '';?>"/>
	<?php } else{ ?>
	<meta name="description" content="<?php echo $this->config->item('description');?>"/>
	<?php } ?>

	<link rel="shortcut icon" href="<?php echo base_url();?>assets/uploads/<?php echo $this->config->item('fav_ico');?>"/>

  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/template/new_site/css/style.css">

	<?php
      if($_SESSION['direction']=='rtl'){?>
        <!-- Set Style For RTL Direction -->
	      <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/template/new_site/css/style-rtl.css" />
	<?php } ?>

  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/template/admin/global/plugins/bootstrap-toastr/toastr.min.css"/>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/template/site/js/jquery-2.1.3.min.js"></script>

  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/new_design/new_design.css"/>
</head>

<body>

  <div style="display:none">
    <svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 500">
      <defs>
        <symbol id="close" viewBox="0 0 512 512">


          <g>
            <g>
              <path d="M505.943,6.058c-8.077-8.077-21.172-8.077-29.249,0L6.058,476.693c-8.077,8.077-8.077,21.172,0,29.249
                                        C10.096,509.982,15.39,512,20.683,512c5.293,0,10.586-2.019,14.625-6.059L505.943,35.306
                                        C514.019,27.23,514.019,14.135,505.943,6.058z"></path>
            </g>
          </g>
          <g>
            <g>
              <path
                d="M505.942,476.694L35.306,6.059c-8.076-8.077-21.172-8.077-29.248,0c-8.077,8.076-8.077,21.171,0,29.248l470.636,470.636
                                        c4.038,4.039,9.332,6.058,14.625,6.058c5.293,0,10.587-2.019,14.624-6.057C514.018,497.866,514.018,484.771,505.942,476.694z">
              </path>
            </g>
          </g>
        </symbol>

        <symbol id="google" viewBox="0 0 512 512">
          <path d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256
                    c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456
                    C103.821,274.792,107.225,292.797,113.47,309.408z"></path>
          <path
            d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451
                    c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535
                    c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z">
          </path>
          <path d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512
                    c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771
                    c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z"></path>
          <path d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012
                    c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0
                    C318.115,0,375.068,22.126,419.404,58.936z"></path>

        </symbol>


        <symbol id="edit" viewBox="0 0 129 129">
          <g>
            <path
              d="m119.2,114.3h-109.4c-2.3,0-4.1,1.9-4.1,4.1s1.9,4.1 4.1,4.1h109.5c2.3,0 4.1-1.9 4.1-4.1s-1.9-4.1-4.2-4.1z">
            </path>
            <path
              d="m5.7,78l-.1,19.5c0,1.1 0.4,2.2 1.2,3 0.8,0.8 1.8,1.2 2.9,1.2l19.4-.1c1.1,0 2.1-0.4 2.9-1.2l67-67c1.6-1.6 1.6-4.2 0-5.9l-19.2-19.4c-1.6-1.6-4.2-1.6-5.9-1.77636e-15l-13.4,13.5-53.6,53.5c-0.7,0.8-1.2,1.8-1.2,2.9zm71.2-61.1l13.5,13.5-7.6,7.6-13.5-13.5 7.6-7.6zm-62.9,62.9l49.4-49.4 13.5,13.5-49.4,49.3-13.6,.1 .1-13.5z">
            </path>
          </g>
        </symbol>

        <symbol id="remove" viewBox="0 0 129 129">
          <g>
            <path
              d="m64.5,122.4c31.9,0 57.9-26 57.9-57.9s-26-57.9-57.9-57.9-57.9,26-57.9,57.9 26,57.9 57.9,57.9zm0-107.7c27.4-3.55271e-15 49.8,22.3 49.8,49.8s-22.3,49.8-49.8,49.8-49.8-22.4-49.8-49.8 22.4-49.8 49.8-49.8z">
            </path>
            <path d="M37.8,68h53.3c2.3,0,4.1-1.8,4.1-4.1s-1.8-4.1-4.1-4.1H37.8c-2.3,0-4.1,1.8-4.1,4.1S35.6,68,37.8,68z">
            </path>
          </g>
        </symbol>


        <symbol id="check" viewBox="0 0 510 510">
          <path d="M150.45,206.55l-35.7,35.7L229.5,357l255-255l-35.7-35.7L229.5,285.6L150.45,206.55z M459,255c0,112.2-91.8,204-204,204
                            S51,367.2,51,255S142.8,51,255,51c20.4,0,38.25,2.55,56.1,7.65l40.801-40.8C321.3,7.65,288.15,0,255,0C114.75,0,0,114.75,0,255
                            s114.75,255,255,255s255-114.75,255-255H459z"></path>
        </symbol>
        <symbol id="arrow-left" viewBox="0 0 129 129">
          <path
            d="m88.6,121.3c0.8,0.8 1.8,1.2 2.9,1.2s2.1-0.4 2.9-1.2c1.6-1.6 1.6-4.2 0-5.8l-51-51 51-51c1.6-1.6 1.6-4.2 0-5.8s-4.2-1.6-5.8,0l-54,53.9c-1.6,1.6-1.6,4.2 0,5.8l54,53.9z">
          </path>

        </symbol>
        <symbol id="facebook" viewBox="0 0 470.513 470.513">
          <g>
            <path
              d="M271.521,154.17v-40.541c0-6.086,0.28-10.8,0.849-14.13c0.567-3.335,1.857-6.615,3.859-9.853
                    c1.999-3.236,5.236-5.47,9.706-6.708c4.476-1.24,10.424-1.858,17.85-1.858h40.539V0h-64.809c-37.5,0-64.433,8.897-80.803,26.691
                    c-16.368,17.798-24.551,44.014-24.551,78.658v48.82h-48.542v81.086h48.539v235.256h97.362V235.256h64.805l8.566-81.086H271.521z">
            </path>
          </g>
        </symbol>
        <symbol id="linkedin" viewBox="0 0 430.117 430.117">
          <g>
            <path id="LinkedIn" d="M430.117,261.543V420.56h-92.188V272.193c0-37.271-13.334-62.707-46.703-62.707
                c-25.473,0-40.632,17.142-47.301,33.724c-2.432,5.928-3.058,14.179-3.058,22.477V420.56h-92.219c0,0,1.242-251.285,0-277.32h92.21
                v39.309c-0.187,0.294-0.43,0.611-0.606,0.896h0.606v-0.896c12.251-18.869,34.13-45.824,83.102-45.824
                C384.633,136.724,430.117,176.361,430.117,261.543z M52.183,9.558C20.635,9.558,0,30.251,0,57.463
                c0,26.619,20.038,47.94,50.959,47.94h0.616c32.159,0,52.159-21.317,52.159-47.94C103.128,30.251,83.734,9.558,52.183,9.558z
                 M5.477,420.56h92.184v-277.32H5.477V420.56z"></path>
          </g>
        </symbol>
        <symbol id="twitter" viewBox="0 0 612 612">
          <g>
            <path
              d="M612,116.258c-22.525,9.981-46.694,16.75-72.088,19.772c25.929-15.527,45.777-40.155,55.184-69.411
                    c-24.322,14.379-51.169,24.82-79.775,30.48c-22.907-24.437-55.49-39.658-91.63-39.658c-69.334,0-125.551,56.217-125.551,125.513
                    c0,9.828,1.109,19.427,3.251,28.606C197.065,206.32,104.556,156.337,42.641,80.386c-10.823,18.51-16.98,40.078-16.98,63.101
                    c0,43.559,22.181,81.993,55.835,104.479c-20.575-0.688-39.926-6.348-56.867-15.756v1.568c0,60.806,43.291,111.554,100.693,123.104
                    c-10.517,2.83-21.607,4.398-33.08,4.398c-8.107,0-15.947-0.803-23.634-2.333c15.985,49.907,62.336,86.199,117.253,87.194
                    c-42.947,33.654-97.099,53.655-155.916,53.655c-10.134,0-20.116-0.612-29.944-1.721c55.567,35.681,121.536,56.485,192.438,56.485
                    c230.948,0,357.188-191.291,357.188-357.188l-0.421-16.253C573.872,163.526,595.211,141.422,612,116.258z">
            </path>
          </g>
        </symbol>
        <symbol id="instagram" viewBox="0 0 512 512">
          <g>
            <g>
              <path d="M373.659,0H138.341C62.06,0,0,62.06,0,138.341v235.318C0,449.94,62.06,512,138.341,512h235.318
                    C449.94,512,512,449.94,512,373.659V138.341C512,62.06,449.94,0,373.659,0z M470.636,373.659
                    c0,53.473-43.503,96.977-96.977,96.977H138.341c-53.473,0-96.977-43.503-96.977-96.977V138.341
                    c0-53.473,43.503-96.977,96.977-96.977h235.318c53.473,0,96.977,43.503,96.977,96.977V373.659z">
              </path>
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
                    c32.231,4.779,57.098,29.645,61.878,61.877C335.608,284.268,307.851,321.692,267.791,327.632z">
              </path>
            </g>
          </g>
          <g>
            <g>
              <path
                d="M400.049,111.951c-3.852-3.851-9.183-6.058-14.625-6.058c-5.442,0-10.773,2.206-14.625,6.058
                    c-3.851,3.852-6.058,9.174-6.058,14.625c0,5.451,2.207,10.773,6.058,14.625c3.852,3.851,9.183,6.058,14.625,6.058
                    c5.442,0,10.773-2.206,14.625-6.058c3.851-3.852,6.058-9.183,6.058-14.625C406.107,121.133,403.9,115.802,400.049,111.951z">
              </path>
            </g>
          </g>
        </symbol>
        <symbol id="youtube" viewBox="0 0 310 310">
          <g id="XMLID_822_">
            <path id="XMLID_823_" d="M297.917,64.645c-11.19-13.302-31.85-18.728-71.306-18.728H83.386c-40.359,0-61.369,5.776-72.517,19.938
                C0,79.663,0,100.008,0,128.166v53.669c0,54.551,12.896,82.248,83.386,82.248h143.226c34.216,0,53.176-4.788,65.442-16.527
                C304.633,235.518,310,215.863,310,181.835v-53.669C310,98.471,309.159,78.006,297.917,64.645z M199.021,162.41l-65.038,33.991
                c-1.454,0.76-3.044,1.137-4.632,1.137c-1.798,0-3.592-0.484-5.181-1.446c-2.992-1.813-4.819-5.056-4.819-8.554v-67.764
                c0-3.492,1.822-6.732,4.808-8.546c2.987-1.814,6.702-1.938,9.801-0.328l65.038,33.772c3.309,1.718,5.387,5.134,5.392,8.861
                C204.394,157.263,202.325,160.684,199.021,162.41z"></path>
          </g>
        </symbol>
        <symbol id="pinterest" viewBox="0 0 97.672 97.672">
          <g>
            <path d="M51.125,0C24.469,0,11.029,19.11,11.029,35.047c0,9.649,3.653,18.232,11.487,21.432c1.286,0.525,2.438,0.019,2.812-1.403
                c0.258-0.985,0.871-3.468,1.144-4.503c0.376-1.407,0.229-1.9-0.807-3.126c-2.259-2.665-3.703-6.115-3.703-11.002
                c0-14.178,10.608-26.87,27.624-26.87c15.064,0,23.342,9.206,23.342,21.5c0,16.176-7.159,29.828-17.786,29.828
                c-5.87,0-10.262-4.854-8.854-10.807c1.686-7.107,4.951-14.778,4.951-19.907c0-4.592-2.463-8.423-7.565-8.423
                c-6,0-10.819,6.207-10.819,14.521c0,5.296,1.789,8.878,1.789,8.878s-6.141,26.015-7.215,30.571
                c-2.145,9.072-0.322,20.195-0.168,21.318c0.089,0.666,0.944,0.824,1.332,0.322c0.555-0.723,7.7-9.544,10.129-18.359
                c0.687-2.496,3.944-15.42,3.944-15.42c1.95,3.717,7.647,6.992,13.706,6.992c18.034,0,30.271-16.441,30.271-38.45
                C86.644,15.498,72.547,0,51.125,0z"></path>
          </g>
        </symbol>
        <symbol id="googleplus" viewBox="0 0 96.828 96.827">
          <g>
            <g>
              <path
                d="M62.617,0H39.525c-10.29,0-17.413,2.256-23.824,7.552c-5.042,4.35-8.051,10.672-8.051,16.912
                    c0,9.614,7.33,19.831,20.913,19.831c1.306,0,2.752-0.134,4.028-0.253l-0.188,0.457c-0.546,1.308-1.063,2.542-1.063,4.468
                    c0,3.75,1.809,6.063,3.558,8.298l0.22,0.283l-0.391,0.027c-5.609,0.384-16.049,1.1-23.675,5.787
                    c-9.007,5.355-9.707,13.145-9.707,15.404c0,8.988,8.376,18.06,27.09,18.06c21.76,0,33.146-12.005,33.146-23.863
                    c0.002-8.771-5.141-13.101-10.6-17.698l-4.605-3.582c-1.423-1.179-3.195-2.646-3.195-5.364c0-2.672,1.772-4.436,3.336-5.992
                    l0.163-0.165c4.973-3.917,10.609-8.358,10.609-17.964c0-9.658-6.035-14.649-8.937-17.048h7.663c0.094,0,0.188-0.026,0.266-0.077
                    l6.601-4.15c0.188-0.119,0.276-0.348,0.214-0.562C63.037,0.147,62.839,0,62.617,0z M34.614,91.535
                    c-13.264,0-22.176-6.195-22.176-15.416c0-6.021,3.645-10.396,10.824-12.997c5.749-1.935,13.17-2.031,13.244-2.031
                    c1.257,0,1.889,0,2.893,0.126c9.281,6.605,13.743,10.073,13.743,16.678C53.141,86.309,46.041,91.535,34.614,91.535z
                     M34.489,40.756c-11.132,0-15.752-14.633-15.752-22.468c0-3.984,0.906-7.042,2.77-9.351c2.023-2.531,5.487-4.166,8.825-4.166
                    c10.221,0,15.873,13.738,15.873,23.233c0,1.498,0,6.055-3.148,9.22C40.94,39.337,37.497,40.756,34.489,40.756z">
              </path>
              <path
                d="M94.982,45.223H82.814V33.098c0-0.276-0.225-0.5-0.5-0.5H77.08c-0.276,0-0.5,0.224-0.5,0.5v12.125H64.473
                    c-0.276,0-0.5,0.224-0.5,0.5v5.304c0,0.275,0.224,0.5,0.5,0.5H76.58V63.73c0,0.275,0.224,0.5,0.5,0.5h5.234
                    c0.275,0,0.5-0.225,0.5-0.5V51.525h12.168c0.276,0,0.5-0.223,0.5-0.5v-5.302C95.482,45.446,95.259,45.223,94.982,45.223z">
              </path>
            </g>
          </g>
        </symbol>

        <symbol id="fav" viewBox="0 0 471.701 471.701">
          <path d="M433.601,67.001c-24.7-24.7-57.4-38.2-92.3-38.2s-67.7,13.6-92.4,38.3l-12.9,12.9l-13.1-13.1
                        c-24.7-24.7-57.6-38.4-92.5-38.4c-34.8,0-67.6,13.6-92.2,38.2c-24.7,24.7-38.3,57.5-38.2,92.4c0,34.9,13.7,67.6,38.4,92.3
                        l187.8,187.8c2.6,2.6,6.1,4,9.5,4c3.4,0,6.9-1.3,9.5-3.9l188.2-187.5c24.7-24.7,38.3-57.5,38.3-92.4
                        C471.801,124.501,458.301,91.701,433.601,67.001z M414.401,232.701l-178.7,178l-178.3-178.3c-19.6-19.6-30.4-45.6-30.4-73.3
                        s10.7-53.7,30.3-73.2c19.5-19.5,45.5-30.3,73.1-30.3c27.7,0,53.8,10.8,73.4,30.4l22.6,22.6c5.3,5.3,13.8,5.3,19.1,0l22.4-22.4
                        c19.6-19.6,45.7-30.4,73.3-30.4c27.6,0,53.6,10.8,73.2,30.3c19.6,19.6,30.3,45.6,30.3,73.3
                        C444.801,187.101,434.001,213.101,414.401,232.701z"></path>
        </symbol>

        <symbol id="shopping-cart" viewBox="0 0 446.853 446.853">
          <path d="M444.274,93.36c-2.558-3.666-6.674-5.932-11.145-6.123L155.942,75.289c-7.953-0.348-14.599,5.792-14.939,13.708
            c-0.338,7.913,5.792,14.599,13.707,14.939l258.421,11.14L362.32,273.61H136.205L95.354,51.179
            c-0.898-4.875-4.245-8.942-8.861-10.753L19.586,14.141c-7.374-2.887-15.695,0.735-18.591,8.1c-2.891,7.369,0.73,15.695,8.1,18.591
            l59.491,23.371l41.572,226.335c1.253,6.804,7.183,11.746,14.104,11.746h6.896l-15.747,43.74c-1.318,3.664-0.775,7.733,1.468,10.916
            c2.24,3.184,5.883,5.078,9.772,5.078h11.045c-6.844,7.617-11.045,17.646-11.045,28.675c0,23.718,19.299,43.012,43.012,43.012
            s43.012-19.294,43.012-43.012c0-11.028-4.201-21.058-11.044-28.675h93.777c-6.847,7.617-11.047,17.646-11.047,28.675
            c0,23.718,19.294,43.012,43.012,43.012c23.719,0,43.012-19.294,43.012-43.012c0-11.028-4.2-21.058-11.042-28.675h13.432
            c6.6,0,11.948-5.349,11.948-11.947c0-6.6-5.349-11.948-11.948-11.948H143.651l12.902-35.843h216.221
            c6.235,0,11.752-4.028,13.651-9.96l59.739-186.387C447.536,101.679,446.832,97.028,444.274,93.36z M169.664,409.814
            c-10.543,0-19.117-8.573-19.117-19.116s8.574-19.117,19.117-19.117s19.116,8.574,19.116,19.117S180.207,409.814,169.664,409.814z
             M327.373,409.814c-10.543,0-19.116-8.573-19.116-19.116s8.573-19.117,19.116-19.117s19.116,8.574,19.116,19.117
            S337.916,409.814,327.373,409.814z" />
        </symbol>


        <symbol id="fav-active" viewBox="0 0 510 510">
          <path
            d="M255,489.6l-35.7-35.7C86.7,336.6,0,257.55,0,160.65C0,81.6,61.2,20.4,140.25,20.4c43.35,0,86.7,20.4,114.75,53.55
                        C283.05,40.8,326.4,20.4,369.75,20.4C448.8,20.4,510,81.6,510,160.65c0,96.9-86.7,175.95-219.3,293.25L255,489.6z">
          </path>

        </symbol>


        <symbol id="search" viewBox="0 0 56.966 56.966">
          <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23
              s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92
              c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17
              s-17-7.626-17-17S14.61,6,23.984,6z" />
        </symbol>


        <symbol id="star" viewBox="0 0 19.481 19.481">
          <g>
            <path
              d="m10.201,.758l2.478,5.865 6.344,.545c0.44,0.038 0.619,0.587 0.285,0.876l-4.812,4.169 1.442,6.202c0.1,0.431-0.367,0.77-0.745,0.541l-5.452-3.288-5.452,3.288c-0.379,0.228-0.845-0.111-0.745-0.541l1.442-6.202-4.813-4.17c-0.334-0.289-0.156-0.838 0.285-0.876l6.344-.545 2.478-5.864c0.172-0.408 0.749-0.408 0.921,0z">
            </path>
          </g>
        </symbol>

        <symbol id="arrow-down" viewBox="0 0 129 129">
          <g>
            <path
              d="m121.3,34.6c-1.6-1.6-4.2-1.6-5.8,0l-51,51.1-51.1-51.1c-1.6-1.6-4.2-1.6-5.8,0-1.6,1.6-1.6,4.2 0,5.8l53.9,53.9c0.8,0.8 1.8,1.2 2.9,1.2 1,0 2.1-0.4 2.9-1.2l53.9-53.9c1.7-1.6 1.7-4.2 0.1-5.8z" />
          </g>
        </symbol>
        <symbol id="language" viewBox="0 0 511.999 511.999">
          <g>
            <path d="M436.921,75.079C389.413,27.571,326.51,1.066,259.464,0.18C258.296,0.074,257.137,0,255.999,0s-2.297,0.074-3.465,0.18
                                C185.488,1.065,122.585,27.57,75.077,75.078C26.752,123.405,0.138,187.657,0.138,255.999s26.614,132.595,74.94,180.921
                                c47.508,47.508,110.41,74.013,177.457,74.898c1.168,0.107,2.327,0.18,3.464,0.18c1.138,0,2.297-0.074,3.465-0.18
                                c67.047-0.885,129.95-27.39,177.457-74.898c48.325-48.325,74.939-112.577,74.939-180.921
                                C511.861,187.657,485.247,123.405,436.921,75.079z M96.586,96.587c27.181-27.181,60.086-46.552,95.992-57.018
                                c-8.093,9.317-15.96,20.033-23.282,31.908c-9.339,15.146-17.425,31.562-24.196,48.919H75.865
                                C82.165,112.063,89.071,104.102,96.586,96.587z M56.486,150.813h78.373c-8.15,28.522-12.97,58.908-14.161,89.978H31.071
                                C33.176,208.987,41.865,178.465,56.486,150.813z M56.487,361.186c-14.623-27.652-23.312-58.174-25.417-89.978h89.627
                                c1.191,31.071,6.011,61.457,14.161,89.978H56.487z M96.587,415.412c-7.517-7.515-14.423-15.475-20.722-23.809h69.236
                                c6.771,17.357,14.856,33.773,24.196,48.919c7.322,11.875,15.189,22.591,23.282,31.908
                                C156.674,461.964,123.769,442.593,96.587,415.412z M240.79,475.322c-12.671-8.29-29.685-24.946-45.605-50.764
                                c-6.385-10.354-12.124-21.382-17.197-32.954h62.801V475.322z M240.79,361.186h-74.195c-8.888-28.182-14.163-58.651-15.459-89.978
                                h89.654V361.186z M240.79,240.791h-89.654c1.295-31.327,6.57-61.797,15.459-89.978h74.195V240.791z M240.79,120.395h-62.801
                                c5.073-11.572,10.812-22.6,17.197-32.954c15.919-25.818,32.934-42.475,45.605-50.764V120.395z M455.512,150.813
                                c14.623,27.653,23.311,58.174,25.416,89.978H391.3c-1.191-31.071-6.011-61.457-14.161-89.978H455.512z M415.413,96.587
                                c7.515,7.515,14.421,15.476,20.721,23.809h-69.235c-6.771-17.357-14.856-33.773-24.196-48.919
                                c-7.322-11.875-15.188-22.591-23.282-31.908C355.326,50.035,388.231,69.406,415.413,96.587z M271.208,36.677
                                c12.671,8.29,29.685,24.946,45.605,50.764c6.385,10.354,12.124,21.382,17.197,32.954h-62.801V36.677z M271.208,150.813h74.195
                                c8.889,28.182,14.164,58.653,15.459,89.978h-89.654V150.813z M360.861,271.208c-1.295,31.327-6.57,61.797-15.459,89.978h-74.195
                                v-89.978H360.861z M271.208,475.322v-83.718h62.801c-5.073,11.572-10.812,22.6-17.197,32.954
                                C300.893,450.377,283.879,467.032,271.208,475.322z M415.413,415.413c-27.182,27.181-60.086,46.551-95.992,57.018
                                c8.093-9.317,15.96-20.033,23.282-31.908c9.339-15.146,17.425-31.562,24.196-48.919h69.235
                                C429.835,399.937,422.928,407.898,415.413,415.413z M455.512,361.186h-78.373c8.15-28.521,12.971-58.907,14.161-89.978h89.627
                                C478.822,303.012,470.133,333.534,455.512,361.186z"></path>
          </g>
        </symbol>

        <symbol id="calendar" viewBox="0 0 512 512">
          <g>
            <g>
              <path
                d="M452,40h-24V0h-40v40H124V0H84v40H60C26.916,40,0,66.916,0,100v352c0,33.084,26.916,60,60,60h392
                                    c33.084,0,60-26.916,60-60V100C512,66.916,485.084,40,452,40z M472,452c0,11.028-8.972,20-20,20H60c-11.028,0-20-8.972-20-20V188
                                    h432V452z M472,148H40v-48c0-11.028,8.972-20,20-20h24v40h40V80h264v40h40V80h24c11.028,0,20,8.972,20,20V148z">
              </path>
            </g>
          </g>
          <g>
            <g>
              <rect x="76" y="230" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="156" y="230" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="236" y="230" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="316" y="230" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="396" y="230" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="76" y="310" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="156" y="310" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="236" y="310" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="316" y="310" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="76" y="390" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="156" y="390" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="236" y="390" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="316" y="390" width="40" height="40"></rect>
            </g>
          </g>
          <g>
            <g>
              <rect x="396" y="310" width="40" height="40"></rect>
            </g>
          </g>
        </symbol>






      </defs>
    </svg>
  </div>
  <?php /*
  <div class="promo-topline">
    <div class="container">
      <div class="promo-topline-item"><b>GET 10% OFF YOUR FIRST ORDER WITH CODE <span></span>&nbsp;<span
            class="hidden-xs">&nbsp;|&nbsp;&nbsp; FREE SHIPPING
            OVER R.S 250</span></b></div>
    </div><a href="#" class="promo-topline-close js-promo-topline-close">
      <svg>
        <use xlink:href="#close"></use>
      </svg>
    </a>
  </div>
  */?>

<section class="fixed-header" id="myHeader">
    <header class="header-2">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-2">
            <div class="logo">
              <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/uploads/<?php echo $this->config->item('logo');?>" alt="" /></a>
            </div>
          </div>
          <div class="col-md-5">
            <div class="search">
              <form method="POST" action="<?php echo base_url(); ?>products/products/search">
                <input type="text" name="product_name" placeholder="<?php echo isset($product_name)? $product_name : lang ('search_word'); ?>" class="form-control" />
                <button>
                  <svg>
                    <use xlink:href="#search"></use>
                  </svg>
                </button>
              </form>
            </div>
          </div>
          <div class="col-md-5">
            <div class="action-web">
              <ul>
                <li>


                  <div class="login-links">
                    <?php if(! $is_logged_in){ ?>
                        <div class="dropdown">

                            <button class="dropbtn"><?php echo lang('register');?></button>
                            <div class="dropdown-content">
                              <a href="<?php echo base_url();?>User_login"><?php echo lang('login');?></a>
        					  <a href="<?php echo base_url();?>Register"><?php echo lang('new_user');?></a>
  					        <a href="<?php echo base_url();?>sell"><?php echo lang('continue_as_seller');?></a>
                            </div>
                        </div>
                        <?php }else{?>
                            <div class="dropdown">
                                    <button class="dropbtn"><?php echo lang('welcome')."  ".$user->first_name.' '. $user->last_name;?></button>
                                <div class="dropdown-content">
                                    <a href="<?php echo base_url();?>Edit_Profile"><?php echo lang('my_personal');?></a>
    								<a href="<?php echo base_url();?>Balance_Recharge"><?php echo lang('recharge_pocket');?></a>
    								<a href="<?php echo base_url();?>Payment_Log"><?php echo lang('balance_details');?></a>
    	   						    <a href="<?php echo base_url();?>Orders_Log"><?php echo lang('orders_log');?></a>
    			 		            <a href="<?php echo base_url();?>Wishlist"><?php echo lang('wishlist')?></a>
    				 			    <a href="<?php echo base_url();?>Compare_Products"><?php echo lang('compare_products')?></a>
    							    <a href="<?php echo base_url();?>User_logout"><?php echo lang('logout');?></a>
    							</div>
    						</div>
                        <?php } ?>
		          </div>

                </li>
                <li>
            <form method="post" action="<?php echo base_url();?>front_end_global/change_lang_country2" id="country_form" enctype="multipart/form-data">
                  <div class="country">
                    <select id="country" name="country_id">
                        <option selected disabled>
						  <?php echo lang('country');?>
                        </option>
                        <?php foreach($countries as $country) {?>
						  <option value="<?php echo $country->id?>" <?php echo $country->id == $country_id? 'selected':''; ?> >
						  <?php echo $country->name.' / '. $country->currency; ?>
						  </option>
						<?php }?>
                    </select>
                    <svg>
                      <use xlink:href="#arrow-down"></use>
                    </svg>
                  </div>
                </li>


                <li>
                    <div class="country">
                        <select id="lang" name="lang_id">
					    						<option selected disabled>
					    							<?php echo lang('language');?>
					    						</option>
					    						<?php foreach($languages as $lang){?>
					    						<option value="<?php echo $lang->id?>" <?php echo $lang->language == $active_lang? 'selected':''; ?> id="
					    							<?php echo $lang->flag?>">
					    							<?php echo $lang->name?>
					    						</option>
					    						<?php }?>
				    					</select>
				    			</div>
                </li>
            </form>
              </ul>
            </div>

          </div>

        </div>
      </div>
    </header>

    <section class="menu-section">
      <div class="container-fluid">
        <div class="row">
          <nav id="navigation1" class="navigation">

            <div class="nav-header">

              <div class="nav-toggle"></div>
            </div>
            <div class="nav-menus-wrapper">
					<ul class="nav-menu">
						<li class="active">
						<a href="<?php echo base_url();?>">
							<?php echo lang('home');?>
						</a>
					</li>

          <?php foreach($categories_array[0] as $key=>$cat){
                if($key < $cats_vertical_limit){?>
                    <li class=""><a href="<?php echo base_url().$main_category_route.$cat->route.'/0';?>"><?php echo $cat->name;?></a>
                        <div class="megamenu-panel">
		        							<div class="megamenu-tabs">
                                <ul class="megamenu-tabs-nav">
																	<?php if(isset($categories_array[ $cat->id ]) && count($categories_array[ $cat->id ]) != 0){
                                   foreach ( $categories_array[ $cat->id ] as $index => $category ) {
											                 if ( $index < $menu_horizontal_limit ) {?>
										                    	<li class="<?php echo $index==1? 'active':'';?> "><a href="<?php echo base_url().$sub_category_route.$category->route.'/0';?>"><?php echo $category->name;?></a></li>
										                    <?php }
										                }
                                  }?>
																	</ul>

                                	<?php if(isset($categories_array[ $cat->id ]) && count($categories_array[ $cat->id ]) != 0){
                                     foreach ( $categories_array[ $cat->id ] as $index => $category ) {?>

																		<?php if ( $index < $menu_horizontal_limit ) {?>
                                    	<div class="megamenu-tabs-pane <?php echo $index==0?'active':'';?>">
						        										<div class="megamenu-panel-row phones">

																					<div class="col-md-4">
																			    	<h3 class="title-menu"><?php echo lang('best_brands');?></h3>
																			   		<div class="row">

																							<?php foreach($category->brands as $brand){?>
																								<div class="col-md-4 padding-2">
																									<div class="brands">
																						   				<a href="#"><img src="<?php echo base_url();?>assets/uploads/thumb/75x50/<?php echo '75x50_'.$brand->image;?>" alt="<?php echo $brand->name;?>" title="<?php echo $brand->name;?>" style="height: 75px;"/></a>
																						  		    </div>
																								</div>
																						<?php }?>
																				    </div>
																		   	 </div>

						        											<?php if(count($category->ads) != 0){
																						foreach($category->ads as $k=>$adv){
						                                   if($k < $menu_horizontal_limit){?>
						                                   	<div class="col-md-4">
																									<div class="container-mega-menu-area">
																											<img style="width: 100%" src="<?php echo base_url();?>assets/uploads/<?php echo $adv->image;?>" alt="image">
																											<a href="<?php echo base_url()."advertisements/advertisement/track_link/".$adv->id;?>"><?php echo $adv->title;?></a>

																									</div>
						        														</div>
						                                   <?php }
						                                 }
																					 }?>

						        										</div>

        															</div>
                                <?php }
                                }
                              }?>

                            </div>
                        </div>
                    </li>
                <?php }
              }
              ?>


					<li>
						<a class="mega-menu-links" href="<?php echo base_url();?>All_stores">
							<?php echo lang('all_stores');?> <!--<i class="fa fa-caret-down fa-indicator"></i>--> </a>

						<ul class="drop-down">
							<?php foreach($menu_stores as $store){?>
							<li>
								<a href="<?php echo base_url().'Store_details/'.$store->store_id;?>">
									<?php echo $store->store_name;?>
								</a>
							</li>
							<?php }?>
							<div class="divider"></div>
							<li>
								<a href="<?php echo base_url().'All_stores/';?>">
									<?php echo lang('all_stores');?>
								</a>
							</li>

						</ul>
					</li>
                    <li>
											<a href="<?php echo base_url().'products/products/all_offers/1/';?>">
												<?php echo lang('all_offers');?>
											</a>

										</li>
									</ul>

									<ul class="nav-menu float-right">
										<li class="">
											<div class="shopping-cart-area">
												<svg>
													<use xlink:href="#shopping-cart"></use>
												</svg>

												<h4><?php echo lang('shopping_cart');?></h4>
												<p>( <?php echo $cart_items_count;?> )</p>
												<a href="<?php echo base_url();?>Shopping_Cart" class="link-shopping-cart"></a>
											</div>
										</li>
									</ul>

				</div>

          </nav>
        </div>
      </div>
    </section>









  </section>


  <?php
    echo $content;
  ?>



  <footer id="footer" class="footer color-bg">
    <div class="footer-bottom">
      <div class="container-fluid new-container-padding">
        <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="address-block">

              <!-- /.module-heading -->

              <div class="module-body">
                <ul class="toggle-footer" style="">
                  <li class="media">
                    <div class="pull-left"> <span class="icon fa-stack fa-lg"> <i
                          class="fa fa-map-marker fa-stack-1x fa-inverse"></i> </span> </div>
                    <div class="media-body">
                      <span>
                        <?php echo $site_address;?>
                      </span>
                    </div>
                  </li>
                  <li class="media">
                    <div class="pull-left"> <span class="icon fa-stack fa-lg"> <i
                          class="fa fa-mobile fa-stack-1x fa-inverse"></i> </span> </div>
                    <div class="media-body">
                      <span>
                        <?php echo $site_phones;?>
					  </span>
                    </div>
                  </li>
                  <li class="media">
                    <div class="pull-left"> <span class="icon fa-stack fa-lg"> <i
                          class="fa fa-envelope fa-stack-1x fa-inverse"></i> </span> </div>
                    <div class="media-body">
                        <a href="mailto:<?php echo $site_emails;?>"><?php echo $site_emails;?></a>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <!-- /.module-body -->
          </div>


          <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="module-heading">
              <h4 class="module-title"><?php echo lang('site_map');?></h4>
            </div>
            <!-- /.module-heading -->

            <div class="module-body">
            <ul class='list-unstyled'>
                <li><a href="<?php echo base_url();?>"><i aria-hidden="true"></i><?php echo lang('home');?></a>
				</li>
            </ul>

			<?php if( $is_logged_in ){ ?>
			<ul>
				<li><a href="<?php echo base_url();?>Edit_Profile"><i aria-hidden="true"></i><?php echo lang('my_personal');?></a>
				</li>
				<li><a href="<?php echo base_url();?>Orders_Log"><i aria-hidden="true"></i><?php echo lang('orders_log');?></a>
				</li>
        <?php if($this->data['user_orders_count'] != 0){?>
          <li><a href="<?php echo base_url();?>tickets/tickets/index"><i aria-hidden="true"></i><?php echo lang('support_tickets');?></a>
  				</li>
      <?php }?>

				<li><a href="<?php echo base_url();?>User_logout"><i aria-hidden="true"></i><?php echo lang('logout');?></a>
				</li>
			</ul>
			<?php }?>
            </div>
            <!-- /.module-body -->
          </div>


          <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="module-heading">
            </div>
            <!-- /.module-heading -->

            <div class="module-body">
              <ul class='list-unstyled'>
				<li><a href="<?php echo base_url();?>Contact_US"><i aria-hidden="true"></i><?php echo lang('contact_us');?></a>
				</li>
				<li><a href="<?php echo base_url();?>Page_Details/2"><i aria-hidden="true"></i><?php echo lang('about')?></a>
				</li>
				<li><a href="<?php echo base_url();?>faq/faq"><i aria-hidden="true"></i><?php echo lang('faqs')?></a>
				</li>
				<li><a href="<?php echo base_url();?>products/products/user_wishlist"><i aria-hidden="true"></i><?php echo lang('wishlist')?></a>
				</li>
				<li><a href="<?php echo base_url();?>Compare_Products"><i aria-hidden="true"></i><?php echo lang('compare_products')?></a>
				</li>
              </ul>
            </div>
            <!-- /.module-body -->
          </div>
          <!-- /.col -->
        </div>
      </div>
    </div>
    <div class="copyright-bar">
      <div class="container">
        <div class="row">
          <div class="col-xs-12 col-sm-4 no-padding social">
            <ul class="link">
              <?php if($this->config->item('facebook') != ''){?>
                <li class="fb pull-left"><a target="_blank" rel="nofollow" href="<?php echo $this->config->item('facebook');?>" title="Facebook"></a></li>
              <?php }?>

              <?php if($this->config->item('twitter') != ''){?>
                <li class="tw pull-left"><a target="_blank" rel="nofollow" href="<?php echo $this->config->item('twitter');?>" title="Twitter"></a></li>
              <?php }?>
              <!--<li class="rss pull-left"><a target="_blank" rel="nofollow" href="#" title="RSS"></a></li>
              <li class="pintrest pull-left"><a target="_blank" rel="nofollow" href="#" title="PInterest"></a></li>-->
              <?php if($this->config->item('linkedin') != ''){?>
                <li class="linkedin pull-left"><a target="_blank" rel="nofollow" href="<?php echo $this->config->item('linkedin');?>" title="Linkedin"></a></li>
              <?php }?>

              <?php if($this->config->item('youtube') != ''){?>
                <li class="youtube pull-left"><a target="_blank" rel="nofollow" href="<?php echo $this->config->item('youtube');?>" title="Youtube"></a></li>
              <?php }?>

              <?php if($this->config->item('instagram') != ''){?>
                <li class="instagram pull-left"><a target="_blank" rel="nofollow" href="<?php echo $this->config->item('instagram');?>" title="instagram"></a></li>
              <?php }?>
            </ul>
          </div>



          <div class="col-xs-12 col-sm-2 no-padding copyright">
						<div class="copy-right">
							<span>
								<?php echo lang('copy_rights');?>
								<a style="color: #f6dc5b;" href="#">
									<?php echo lang('store_name');?>
								</a>
								<?php echo ' &copy; '.date('Y'); ?> </span>
						</div>
						<!--copy-right-->
					</div>
          <div class="col-xs-11 col-sm-6 no-padding">
            <div class="clearfix payment-methods">
              <ul>
                <li><img src="<?php echo base_url();?>assets/template/site/img/mastercard.png" alt=""></li>
                <li><img src="<?php echo base_url();?>assets/template/site/img/paypal.png" alt=""></li>
                <li><img src="<?php echo base_url();?>assets/template/site/img/visa.png" alt=""></li>
                <li><img src="<?php echo base_url();?>assets/template/site/img/american-express.png" alt=""></li>
                <li><a style="width: 32px; height: 20px;" href="<?php echo $this->config->item('android_app_link');?>"><img width="30px" title="<?php echo lang('android_app_link');?>" src="<?php echo base_url();?>assets/template/site/img/play_store.png" alt=""></a></li>
                <li><a style="width: 32px; height: 20px;" href="<?php echo $this->config->item('ios_app_link');?>"><img width="30px" title="<?php echo lang('ios_app_link');?>" src="<?php echo base_url();?>assets/template/site/img/app_store.jpg" alt=""></a></li>
              </ul>
            </div>
          </div>

        </div>

      </div>
    </div>
  </footer>
  <script src="<?php echo base_url(); ?>assets/template/new_site/js/scripts.js"></script>

  <script src="<?php echo base_url(); ?>assets/template/admin/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
  <script>
    $( "#lang" ).change( function () {
			this.form.submit();
		} );
		////////////////////////////////////////////////////////////
		$( "#country" ).change( function () {
			bootbox.confirm( '<?php echo lang('change_country_confirm_msg ');?>',
				function ( result ) {
					if ( $.trim( result ) == 'true' ) {
						$( "#country_form" ).submit();
					} else {
						var country_id = <?php echo $_SESSION['country_id'];?>;
						$( '#country' ).val( country_id );
					}
				} );
		} );

        //Toast Notifications
		////////////////////////////////////////////////////////////
		function showToast( $msg, $title, $type ) {
			var msg = $msg;
			var title = $title;
			var shortCutFunction = $type;

			toastr.options = {
				"closeButton": true,
				"debug": false,
				"positionClass": "toast-bottom-full-width",
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

			var $toast = toastr[ shortCutFunction ]( msg, title ); // Wire up an event handler to a button in the toast, if it exists
			$toastlast = $toast;
			if ( $toast.find( "#okBtn" ).length ) {
				$toast.delegate( "#okBtn", "click", function () {
					alert( "you clicked me. i was toast #" + toastIndex + ". goodbye!" );
					$toast.remove();
				} );
			}
			if ( $toast.find( "#surpriseBtn" ).length ) {
				$toast.delegate( "#surpriseBtn", "click", function () {
					alert( "Surprise! you clicked me. i was toast #" + toastIndex + ". You could perform an action here." );
				} );
			}

			$( "#clearlasttoast" ).click( function () {
				toastr.clear( $toastlast );
			} );
		}

        ////////////////////////////////////////////////////////
		//when click add to wishlist
	<?php /*	$( '.wishlist_product' ).click( function ( event ) {
			event.preventDefault();

			var product_id = $( this ).data( 'product_id' );
			var postData = {
				product_id: product_id
			};
			$.post( '<?php echo base_url()."products/products/add_to_wishlist/";?>', postData, function ( data ) {

				/*
				result :
				    product_required
				    login
				    already_exist
				    success
				*/

/*				if ( data == 'product_required' ) {
					showToast( '<?php echo lang('no_product_details');?>', '<?php echo lang('error');?>', 'error' );
				} else if ( data == 'login' ) {
					window.location = "<?php echo base_url().'User_login';?>";
				} else if ( data == 'already_exist' ) {
					showToast( '<?php echo lang('product_exist_in_wishlist');?>', '<?php echo lang('sorry');?>', 'warning' );
				} else if ( data == 'success' ) {
					showToast( '<?php echo lang('added_to_wishlist_successfully');?>', '<?php echo lang('success');?>', 'success' );
				}
			} );
		} );

		////////////////////////////////////////////////////////
		//when click remove from wishlist
		$( '.remove_wishlist' ).click( function ( event ) {
			event.preventDefault();

			var product_id = $( this ).data( 'product_id' );
			var postData = {
				product_id: product_id
			};
			$.post( '<?php echo base_url()."products/products/remove_from_wishlist/";?>', postData, function ( data ) {


				if ( data[ 0 ] == 'product_required' ) {
					showToast( '<?php echo lang('
						no_product_details ');?>', '<?php echo lang('error');?>', 'error' );
				} else if ( data[ 0 ] == 'login' ) {
					window.location = "<?php echo base_url().'User_login';?>";
				} else if ( data[ 0 ] == 'product_not_exist' ) {
					showToast( '<?php echo lang('product_not_exist_in_wishlist');?>', '<?php echo lang('sorry');?>', 'warning' );
				} else if ( data[ 0 ] == 'success' ) {
					showToast( '<?php echo lang('product_removed_successfully_from_wishlist');?>', '<?php echo lang('success');?>', 'success' );
					$( '.product_' + data[ 1 ] ).remove();
				}

			}, 'json' );
		} );
    */?>


    ////////////////////////////////////////////////////////
		//when click add to cart
		$( '.cart' ).click( function ( event ) {
			event.preventDefault();

			var product_id = $( this ).data( 'product_id' );
			var postData = {
				product_id: product_id
			};
			$.post( '<?php echo base_url()."shopping_cart/cart/add_to_cart/";?>', postData, function ( data ) {

				if ( data[ 0 ] == 'no_stock' ) {
					showToast( '<?php echo lang('no_stock_for_this_product');?>', '<?php echo lang('sorry');?>', 'error' );
				} else if ( data[ 0 ] == 'max_per_discount' ) {
					showToast( '<?php echo lang('max_qty_per_user_discount_reached');?>', '<?php echo lang('warning');?>', 'warning' );
				} else if ( data[ 0 ] == 'max_products_per_order' ) {
					showToast( '<?php echo lang('max_products_per_order_reached');?>', '<?php echo lang('sorry');?>', 'error' );
				} else if ( data[ 0 ] == 'product_exist' ) {
					showToast( '<?php echo lang('product_exist_in_your_shopping_cart');?>', '<?php echo lang('sorry');?>', 'error' );
				} else if ( data[ 0 ] == 'optional_fields_required' ) {
					window.location = "<?php echo base_url().$product_route.'/';?>" + data[ 1 ];
				} else {
					showToast( data[ 0 ], 'success', 'success' );
				}
			}, 'json' );
		} );

		////////////////////////////////////////////////////////
		//when click buy now
		$( '.buy_now, .buy_product' ).click( function ( event ) {
			event.preventDefault();

			var product_id = $( this ).data( 'product_id' );

			var postData = {
				product_id: product_id
			};

			$.post( '<?php echo base_url()."shopping_cart/cart/add_to_cart/";?>', postData, function ( data ) {

				if ( data[ 0 ] == 'no_stock' ) {
					showToast( '<?php echo lang('no_stock_for_this_product');?>', '<?php lang('sorry ');?>', 'error' );
				} else if ( data[ 0 ] == 'max_per_discount' ) {
					showToast( '<?php echo lang('max_qty_per_user_discount_reached');?>', '<?php echo lang('warning ');?>', 'warning' );
				} else if ( data[ 0 ] == 'max_products_per_order' ) {
					showToast( '<?php echo lang('max_products_per_order_reached');?>', '<?php echo lang('sorry ');?>', 'error' );
				} else if ( data[ 0 ] == 'product_exist' ) {
					showToast( '<?php echo lang('product_exist_in_your_shopping_cart');?>', '<?php echo lang('sorry ');?>', 'error' );
				} else if ( data[ 0 ] == 'optional_fields_required' ) {
					window.location = "<?php echo base_url().$product_route.'/';?>" + data[ 1 ];
				} else {
					window.location = "<?php echo base_url()."shopping_cart/cart/view_cart";?>";
				}
			}, 'json' );
		} );

		////////////////////////////////////////////////////////
		// insert user optional fields
		// Add To Cart
		$( "body" ).on( "click", ".add_optional_fields", function ( event ) {
			event.preventDefault();
			//var postData = $.param( $( '#optional_fields_form' ).find( ':input option:selected' ).not( $( this ) ) );

			//$( "#myselect option:selected" ).val();
			var postData = $('#optional_fields_form').serialize();

			$.post( '<?php echo base_url()."shopping_cart/cart/submit_product_optional_fields";?>', postData, function ( result ) {
				if ( result[ 0 ] == 1 ) {
					$( "#optional_fields_form" ).trigger( 'reset' );
					showToast( result[ 1 ], 'success', 'success' );
				} else {
					showToast( result[ 1 ], 'error', 'error' );
				}
			}, 'json' );
		} );
		// Buy Now
		$( "body" ).on( "click", ".buy_optional_fields", function ( event ) {
			event.preventDefault();
			//var postData = $.param( $( '#optional_fields_form' ).find( ':input' ).not( $( this ) ) );
            var postData = $('#optional_fields_form').serialize();
			$.post( '<?php echo base_url()."shopping_cart/cart/submit_product_optional_fields";?>', postData, function ( result ) {
				if ( result[ 0 ] == 1 ) {
					window.location = "<?php echo base_url()."shopping_cart/cart/view_cart";?>";
				} else {
					showToast( result[ 1 ], 'error', 'error' );
				}
			}, 'json' );
		} );
		/////////////////////////////////////////////////////////////



		//Toast Notifications
		////////////////////////////////////////////////////////////
		function showToast( $msg, $title, $type ) {
			var msg = $msg;
			var title = $title;
			var shortCutFunction = $type;

			toastr.options = {
				"closeButton": true,
				"debug": false,
				"positionClass": "toast-bottom-full-width",
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

			var $toast = toastr[ shortCutFunction ]( msg, title ); // Wire up an event handler to a button in the toast, if it exists
			$toastlast = $toast;
			if ( $toast.find( "#okBtn" ).length ) {
				$toast.delegate( "#okBtn", "click", function () {
					alert( "you clicked me. i was toast #" + toastIndex + ". goodbye!" );
					$toast.remove();
				} );
			}
			if ( $toast.find( "#surpriseBtn" ).length ) {
				$toast.delegate( "#surpriseBtn", "click", function () {
					alert( "Surprise! you clicked me. i was toast #" + toastIndex + ". You could perform an action here." );
				} );
			}

			$( "#clearlasttoast" ).click( function () {
				toastr.clear( $toastlast );
			} );
		}

		////////////////////////////////////////////////////////
		//when click compare products
		$( "body" ).on( "click", ".compare_products", function ( event ) {
			event.preventDefault();

			var product_id = $( this ).data( 'product_id' );
			var postData = {
				product_id: product_id
			};

			$.post( '<?php echo base_url();?>products/products/add_compare_product', postData, function ( result ) {

				//check if more than one product to show compare table
				if ( result == 1 ) {
					showToast( '<?php echo lang('product_added_successfully_to_comparison');?>', '<?php echo lang('success');?>', 'success' );
				} else {
					//show table
					window.location = "<?php echo base_url();?>Compare_Products";
				}
			} );

		} );

		/////////////////////////////////////////////////////////////
		//When click remove compare product

		$( "body" ).on( "click", ".remove_compare_product", function ( event ) {
			event.preventDefault();

			var product_id = $( this ).data( 'product_id' );
			var postData = {
				product_id: product_id
			};

			$.post( '<?php echo base_url();?>products/products/remove_compare_product', postData, function ( result ) {
				if ( result == 1 ) {
					$( ".compare_product_" + product_id ).remove();
					showToast( '<?php echo lang('product_removed_from_compare');?>', '<?php echo lang('success');?>', 'success' );
				}
			} );
		} );


		////////////////////////////////////////////////////////
		//when click add to wishlist
		$( '.wishlist_product' ).click( function ( event ) {
			event.preventDefault();

			var product_id = $( this ).data( 'product_id' );
			var postData = {
				product_id: product_id
			};
			$.post( '<?php echo base_url()."products/products/add_to_wishlist/";?>', postData, function ( data ) {

				/*
				result :
				    product_required
				    login
				    already_exist
				    success
				*/

				if ( data == 'product_required' ) {
					showToast( '<?php echo lang('no_product_details');?>', '<?php echo lang('error');?>', 'error' );
				} else if ( data == 'login' ) {
					window.location = "<?php echo base_url().'User_login';?>";
				} else if ( data == 'already_exist' ) {
					showToast( '<?php echo lang('product_exist_in_wishlist');?>', '<?php echo lang('sorry');?>', 'warning' );
				} else if ( data == 'success' ) {
					showToast( '<?php echo lang('added_to_wishlist_successfully');?>', '<?php echo lang('success');?>', 'success' );
				}
			} );
		} );

		////////////////////////////////////////////////////////
		//when click remove from wishlist
		$( '.remove_wishlist' ).click( function ( event ) {
			event.preventDefault();

			var product_id = $( this ).data( 'product_id' );
			var postData = {
				product_id: product_id
			};
			$.post( '<?php echo base_url()."products/products/remove_from_wishlist/";?>', postData, function ( data ) {


				if ( data[ 0 ] == 'product_required' ) {
					showToast( '<?php echo lang('
						no_product_details ');?>', '<?php echo lang('error');?>', 'error' );
				} else if ( data[ 0 ] == 'login' ) {
					window.location = "<?php echo base_url().'User_login';?>";
				} else if ( data[ 0 ] == 'product_not_exist' ) {
					showToast( '<?php echo lang('product_not_exist_in_wishlist');?>', '<?php echo lang('sorry');?>', 'warning' );
				} else if ( data[ 0 ] == 'success' ) {
					showToast( '<?php echo lang('product_removed_successfully_from_wishlist');?>', '<?php echo lang('success');?>', 'success' );
					$( '.product_' + data[ 1 ] ).remove();
				}

			}, 'json' );
		} );
  </script>
  <!--Start of Tawk.to Script-->
	<script type="text/javascript">
		var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
		(function(){
		var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
		s1.async=true;
		s1.src='https://embed.tawk.to/5be043334cfbc9247c1ec1ab/default';
		s1.charset='UTF-8';
		s1.setAttribute('crossorigin','*');
		s0.parentNode.insertBefore(s1,s0);
		})();
	</script>
	<!--End of Tawk.to Script-->
</body>
</html>

<!doctype html>
<html dir="<?php echo $_SESSION['direction'];?>" style="direction: <?php echo $_SESSION['direction'];?>;">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo lang('store_name');?></title>

     

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo base_url();?>assets/template/merchant/css/sell.css">
    
    <link rel="shortcut icon" href="<?php echo base_url();?>assets/uploads/<?php echo $this->config->item('image2');?>" />

    <style>
    .error_msg{
      border-color: red!important;
    }
    .add-lcation{
      position: relative;
      width: 100%;
      border: 1px solid #ccc;
    }
    .add-lcation .map-location{
      width: 100%;
      min-height: 350px;

    }
    .add-lcation .input-search-area {
    background: rgba(220,245,238,0.5);
    padding: 15px;
    position: absolute;
    left: 0;
    top: 0;
    display: flex;
    width: 100%;
    box-shadow: 0 0 1px 2px rgba(0,0,0,0.1215686275);
}

.add-lcation .input-search-area .getcurrentLocation {
    width: 240px;
    text-align: center;
    align-items: center;
    justify-content: center;
    display: inline-flex;
    background: #fff;
    color: #6C1F37;
    border: 1px solid #6C1F37;
}

    </style>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

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
            <symbol id="correct" viewBox="0 0 612 792">
                <style type="text/css">
                    .st0{fill:#41AD49;}
                </style><g><path class="st0" d="M562,396c0-141.4-114.6-256-256-256S50,254.6,50,396s114.6,256,256,256S562,537.4,562,396L562,396z    M501.7,296.3l-241,241l0,0l-17.2,17.2L110.3,421.3l58.8-58.8l74.5,74.5l199.4-199.4L501.7,296.3L501.7,296.3z"/></g>
            </symbol>
            <symbol id="wrong">
                <g>
                    <title>Layer 1</title>
                    <path fill="#7f0000" id="svg_2" d="m12,2c-5.5,0 -10,4.5 -10,10c0,5.5 4.5,10 10,10s10,-4.5 10,-10c0,-5.5 -4.5,-10 -10,-10zm4.9,13.5l-1.4,1.4l-3.5,-3.5l-3.5,3.5l-1.4,-1.4l3.5,-3.5l-3.5,-3.5l1.4,-1.4l3.5,3.5l3.5,-3.5l1.4,1.4l-3.5,3.5l3.5,3.5z"/>
                </g>
            </symbol>

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
            <symbol id="googleplus" viewBox="0 0 96.828 96.827">
                <g>
                    <g>
                        <path d="M62.617,0H39.525c-10.29,0-17.413,2.256-23.824,7.552c-5.042,4.35-8.051,10.672-8.051,16.912
			c0,9.614,7.33,19.831,20.913,19.831c1.306,0,2.752-0.134,4.028-0.253l-0.188,0.457c-0.546,1.308-1.063,2.542-1.063,4.468
			c0,3.75,1.809,6.063,3.558,8.298l0.22,0.283l-0.391,0.027c-5.609,0.384-16.049,1.1-23.675,5.787
			c-9.007,5.355-9.707,13.145-9.707,15.404c0,8.988,8.376,18.06,27.09,18.06c21.76,0,33.146-12.005,33.146-23.863
			c0.002-8.771-5.141-13.101-10.6-17.698l-4.605-3.582c-1.423-1.179-3.195-2.646-3.195-5.364c0-2.672,1.772-4.436,3.336-5.992
			l0.163-0.165c4.973-3.917,10.609-8.358,10.609-17.964c0-9.658-6.035-14.649-8.937-17.048h7.663c0.094,0,0.188-0.026,0.266-0.077
			l6.601-4.15c0.188-0.119,0.276-0.348,0.214-0.562C63.037,0.147,62.839,0,62.617,0z M34.614,91.535
			c-13.264,0-22.176-6.195-22.176-15.416c0-6.021,3.645-10.396,10.824-12.997c5.749-1.935,13.17-2.031,13.244-2.031
			c1.257,0,1.889,0,2.893,0.126c9.281,6.605,13.743,10.073,13.743,16.678C53.141,86.309,46.041,91.535,34.614,91.535z
			 M34.489,40.756c-11.132,0-15.752-14.633-15.752-22.468c0-3.984,0.906-7.042,2.77-9.351c2.023-2.531,5.487-4.166,8.825-4.166
			c10.221,0,15.873,13.738,15.873,23.233c0,1.498,0,6.055-3.148,9.22C40.94,39.337,37.497,40.756,34.489,40.756z"/>
                        <path d="M94.982,45.223H82.814V33.098c0-0.276-0.225-0.5-0.5-0.5H77.08c-0.276,0-0.5,0.224-0.5,0.5v12.125H64.473
			c-0.276,0-0.5,0.224-0.5,0.5v5.304c0,0.275,0.224,0.5,0.5,0.5H76.58V63.73c0,0.275,0.224,0.5,0.5,0.5h5.234
			c0.275,0,0.5-0.225,0.5-0.5V51.525h12.168c0.276,0,0.5-0.223,0.5-0.5v-5.302C95.482,45.446,95.259,45.223,94.982,45.223z"/>
                    </g>
                </g>
            </symbol>
        </defs>
    </svg>
</div>

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

<div class="large-container back-color">
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
                                    <a href="<?php echo base_url();?>sell/change_lang/<?php echo $lang->language ;?>">
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

    <?php
        $value = '';
        $display_style = '';
        $display_image_div = '';
        $field_name1  = 'image';
        $unique_id1   = mt_rand();
        $unique_name1 = 's'.substr(md5($field_name1),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);

        $field_name2  = 'image2';
        $unique_id2   = mt_rand();
        $unique_name2 = 's'.substr(md5($field_name2),0,8);//'s5ae0c1c8';//'p'.substr(md5($unique_id), 0, 10);
    ?>
    <section class="guide">
       <div class="guide-steps">
           <div class="guide-steps">
               <div class="container-fluid">
                   <h2><?php echo lang('steps_to_get_store');?></h2>
                   <div class="welcome-border">
                       <hr>
                       <hr>
                       <hr>
                       <hr>
                       <hr>
                   </div>
                   <div class="guide-steps">
                       <p><?php echo $info_text->page_text;?></p>
                   </div>
               </div>
        </div>
       </div>
       <div class="steps-bar">
           <div class="container-fluid">
               <div class="steps">
                    <div class="step-num active">
                       <div class="step-item">
                          
                           <span>1</span>
                       </div>
                       <p><?php echo lang('terms_conditions');?></p>
                   </div>

                   <div class="step-num">
                       <div class="step-item">
                           
                           <span>2</span>
                       </div>
                       <p><?php echo lang('data');?></p>
                   </div>

                   <div class="step-num ">
                       <div class="step-item">
                          
                           <span>3</span>
                       </div>
                       <p class="center"><?php echo lang('store_details');?></p>
                   </div>

                   <div class="step-num ">
                      <div class="step-item">
                         
                          <span>4</span>
                        
                      </div>
                       <p class="far"><?php echo lang('finish');?></p>
                   </div>

               </div>
           </div>
       </div>
    </section>
    <!--Main-->
    <main class="admin-change">
        <ul>
            <form action="<?php echo base_url();?>users/Register_store"  method="post" enctype="multipart/form-data" id="reg_form">


            <li class="activeA">
                <!--contact-info-->
                <section class="contact-info">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8 col-12 col-sm-12 mx-auto">
                                <h3><?php echo lang('terms_conditions');?></h3>
                                <h4 class="validation_msg_1" style="display: none; color:red"><?php echo lang('aprove_terms_and_condition');?></h4>
                                <div class="form-group flex-column">
                                 
                                        <div class="special" for="pho"><?php echo lang('terms_conditions');?></div>
                                        <div class="terms-area">
                                            <?php echo $terms_text;?>
                                        </div>
                                    
                                </div>

                                <div class="form-group">
                                    <div class="form-item">
                                        <div class="checkbox-input" id="i-agree">
                                            <!--<input type="checkbox" required="true" checked="checked" name="i_agree" required="true"  >-->
                                            <?php
                                            $check_box_data = array(
                                                            'name'          => 'i_agree',
                                                            'value'         => '1',
                                                            'checked'       => TRUE,
                                                            'required'      => 'required'
                                                    );
                                            echo form_checkbox($check_box_data);?>
                                            <?php echo form_error('i_agree');?>
                                        </div>
                                        <label class="special" for="i-agree"><?php echo lang('i_agree');?><span>*</span></label>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--admin-buttons-->
                <section class="admin-buttons">
                    <div class="container-fluid button-style">
                        <button class="next1"><a ><?php echo lang('continue');?></a></button>
                    </div>
                </section>
            </li>
            <!--first tab end-->

            <li >
                <!--contact-info-->
                <section class="contact-info">
                    <div class="container-fluid">
                        <div class="col-md-8 mx-auto">
                            <h3><?php echo lang('data');?></h3>
                            <h4 class="validation_msg_2" style="display: none; color:red;margin: 0 10px 10px 10px;"><?php echo lang('fill_required_fields');?></h4>
                            <div class="row">

                                <div class=" col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="insta"><?php echo lang('first_name');?><span>*</span></label>
                                            <?php
                                                $f_name_data = array(
                                                                    'name'     => 'first_name',
                                                                    'class'    => 'form-control',
                                                                    'id'       => 'first_name',
                                                                    'required' => 'required',
                                                                    'value'    => set_value('first_name')
                                                                );
                                                echo form_input($f_name_data);
                                                echo form_error('first_name');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="insta"><?php echo lang('last_name');?><span>*</span></label>
                                            <?php
                                                $last_name_data = array(
                                                                    'name'  => 'last_name',
                                                                    'id'    => 'last_name',
                                                                    'class' => 'form-control',
                                                                    'required' => 'required',
                                                                    'value' => set_value('last_name')
                                                                );
                                                echo form_input($last_name_data);
                                                echo form_error('last_name');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="face"><?php echo lang('email');?><span>*</span></label>
                                            <?php
                                                $email_data = array(
                                                                    'name' => 'email',
                                                                    'type' => 'email',
                                                                    'id'   => 'email',
                                                                    'class' => 'form-control',
                                                                    'required'      => 'required',
                                                                    'value' => set_value('email')
                                                                );
                                                echo form_input($email_data);
                                                echo form_error('email');

                                            ?>
                                            <span class="email_error error_msg"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6"></div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="face"><?php echo lang('country');?><span>*</span></label>
                                            <?php
                                            echo form_dropdown('country_id', $user_countries, set_value('country_id'), 'class="form-control select2" id="country_id" style="height: 45px;"');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="face"><?php echo lang('phone');?><span>*</span></label>
                                            <input type="text" value="" class="form-control country_code" readonly="" style="width: 25%;">
                                            <?php
                                                $phone_data = array(
                                                                    'type' => 'number',
                                                                    'name' => 'phone',
                                                                    'id'   => 'phone',
                                                                    'class' => 'form-control',
                                                                    'value' => set_value('phone'),
                                                                    'required'      => 'required',
                                                                    'placeholder' => lang('phone_ex')

                                                                );
                                                echo form_input($phone_data);
                                                echo form_error('phone');
                                            ?>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="insta"><?php echo lang('password');?><span>*</span></label>
                                            <?php
                                                $password_data = array(
                                                                    'name'  => 'password',
                                                                    'id'    => 'password',
                                                                    'class' => 'form-control',
                                                                    'required'      => 'required',
                                                                    'type'  => 'password'
                                                                );
                                                echo form_input($password_data);
                                                echo form_error('password');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="insta"><?php echo lang('confirm_password');?><span>*</span></label>

                                            <?php
                                            $conf_password_att = array(
                                                                        'name'  => 'conf_password',
                                                                        'id'    => 'conf_password',
                                                                        'type'  => 'password',
                                                                        'required'      => 'required',
                                                                        'class' =>'form-control'
                                                                        );
                                            echo form_input($conf_password_att);
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="insta"><?php echo lang('store_image');?><span></span></label>

                                            <div class="col-md-4">
                                                <!-- image upload-->

                                                <div class="form-div">
                                                    <div class="form-field-box odd" id="<?php echo $field_name1;?>_field_box">
                                                        <div class="form-input-box" id="<?php echo $field_name1;?>_input_box">

                                                            <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id1; ?>" style="<?php echo $display_style;?>">
                                                                <span><?php echo lang('upload')?></span>
                                                                <input type="file" name="<?php echo $unique_name1; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/site_uploads/upload_file/<?php echo $field_name1;?>" id="<?php echo $unique_id1; ?>">
                                                                <input class="hidden-upload-input" type="hidden" name="<?php echo $field_name1;?>" value="<?php if(isset($general_data->image)){echo $general_data->image;}?>" rel="<?php echo $unique_name1; ?>">
                                                            </span>

                                                            <div id="uploader_<?php echo $unique_id1; ?>" rel="<?php echo $unique_id1; ?>" class="grocery-crud-uploader" style=""></div>

                                                            <?php echo $display_image_div; ?>

                                                            <div id="success_<?php echo $unique_id1; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                                                <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id1; ?>" class="open-file" target="_blank"></a>
                                                                <a href="javascript:void(0)" id="delete_<?php echo $unique_id1; ?>" class="delete-anchor">delete</a>
                                                            </div>

                                                            <div style="clear:both"></div>

                                                            <div id="loading-<?php echo $unique_id1; ?>" style="display:none">
                                                                <span id="upload-state-message-<?php echo $unique_id1; ?>"></span>
                                                                <span class="qq-upload-spinner"></span>
                                                                <span id="progress-<?php echo $unique_id1; ?>"></span>
                                                            </div>

                                                            <div style="display:none">
                                                                <a href="<?php echo base_url();?>uploads/upload_image/site_uploads/upload_file/<?php echo $field_name1;?>" id="url_<?php echo $unique_id1; ?>"></a>
                                                            </div>

                                                            <div style="display:none">
                                                                <a href="<?php echo base_url();?>uploads/upload_image/site_uploads/delete_file/<?php echo $field_name1;?>" id="delete_url_<?php echo $unique_id1; ?>" rel=""></a>
                                                            </div>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="insta"><?php echo lang('id_image');?><span></span></label>

                                            <div class="col-md-4">
                                                <!-- image upload-->

                                                <div class="form-div">
                                                    <div class="form-field-box odd" id="<?php echo $field_name2;?>_field_box">
                                                        <div class="form-input-box" id="<?php echo $field_name2;?>_input_box">

                                                            <span class="fileinput-button qq-upload-button" id="upload-button-<?php echo $unique_id2; ?>" style="<?php echo $display_style;?>">
                                                                <span><?php echo lang('upload')?></span>
                                                                <input type="file" name="<?php echo $unique_name2; ?>" class="gc-file-upload" rel="<?php echo base_url();?>uploads/upload_image/site_uploads/upload_file/<?php echo $field_name2;?>" id="<?php echo $unique_id2; ?>">
                                                                <input class="hidden-upload-input" type="hidden" name="<?php echo $field_name2;?>" value="<?php if(isset($general_data->image)){echo $general_data->image;}?>" rel="<?php echo $unique_name2; ?>">
                                                            </span>

                                                            <div id="uploader_<?php echo $unique_id2; ?>" rel="<?php echo $unique_id2; ?>" class="grocery-crud-uploader" style=""></div>

                                                            <?php echo $display_image_div; ?>

                                                            <div id="success_<?php echo $unique_id2; ?>" class="upload-success-url" style="display:none; padding-top:7px;">
                                                                <a href="<?php echo base_url();?>assets/uploads/" id="file_<?php echo $unique_id2; ?>" class="open-file" target="_blank"></a>
                                                                <a href="javascript:void(0)" id="delete_<?php echo $unique_id2; ?>" class="delete-anchor">delete</a>
                                                            </div>

                                                            <div style="clear:both"></div>

                                                            <div id="loading-<?php echo $unique_id2; ?>" style="display:none">
                                                                <span id="upload-state-message-<?php echo $unique_id2; ?>"></span>
                                                                <span class="qq-upload-spinner"></span>
                                                                <span id="progress-<?php echo $unique_id2; ?>"></span>
                                                            </div>

                                                            <div style="display:none">
                                                                <a href="<?php echo base_url();?>uploads/upload_image/site_uploads/upload_file/<?php echo $field_name2;?>" id="url_<?php echo $unique_id2; ?>"></a>
                                                            </div>

                                                            <div style="display:none">
                                                                <a href="<?php echo base_url();?>uploads/upload_image/site_uploads/delete_file/<?php echo $field_name2;?>" id="delete_url_<?php echo $unique_id2; ?>" rel=""></a>
                                                            </div>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>



                                <?php /*<div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-item">
                                            <label for="face"><?php echo lang('store_route');?><span>*</span></label>
                                            <?php
                                                $route_data = array(
                                                                    'name'  => 'route',
                                                                    'id'    => 'route',
                                                                    'class' => 'form-control',
                                                                    'required'      => 'required',
                                                                    'value' => set_value('route'),
                                                                    'placeholder' => lang('store_route_note')
                                                                );
                                                echo form_input($route_data);
                                                echo form_error('route');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                */?>



                                <div class="col-12 col-sm-12">

                                    <div class="add-lcation">

                                    <div id="map" class="map-location"></div>
                                    <div class="input-search-area">
                                        <input type="text" id="autocomplete" class="form-control">
                                        <button class="getcurrentLocation">
                                            <span><?php echo lang('get_current_location');?><br>(<?php echo lang('marker_can_be_moved');?>)</span>
                                        </button>
                                    </div>
                                    </div>
                                    <input type="hidden" id="lat_input" name="store_lat">
                                    <input type="hidden" id="lng_input" name="store_lng">


                                </div>

                            </div>
                        </div>
                    </div>
                </section>
                <!--admin-buttons-->
                <section class="admin-buttons">
                    <div class="container-fluid button-style">
                        <button class="back"><a ><?php echo lang('previous');?></a></button>
                        <button class="next2"><a  onclick=""><?php echo lang('continue');?></a></button>
                    </div>
                </section>
            </li>
            <!--Main data tab end-->

            <li>
                <!--contact-info-->
                <section class="contact-info">
                    <div class="container-fluid">
                       
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <h4 class="validation_msg_3"><?php echo lang('fill_required_fields');?></h4>
                                <?php foreach(array_reverse($data_languages) as $lang){?>
                                <input type="hidden" name="lang_id[]" value="<?php echo $lang->id;?>" />
                                    <div class="col-12 col-sm-6 <?php if($lang->direction == 'ltr'){?>en-version<?}?>">
                                        <h3><?php echo $lang->name;?></h3>
                                        <div class="form-group">
                                            <div class="form-item">
                                                <label for="name"><?php echo lang('name_of_store');?><span>*</span></label>
                                                <?php
                                                    $name_atts = array(
                                                                            'name'          => 'name['.$lang->id.']',
                                                                            'placeholder'   => $lang->name,
                                                                            'id'            => 'input-name'.$lang->id,
                                                                            'class'         => 'form-control',
                                                                            'required'      => 'required',
                                                                            'value'         => set_value('name['.$lang->id.']')
                                                                        );
                                                    echo form_input($name_atts);
                                                    echo form_error('name['.$lang->id.']');
                                                ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-item">
                                                <label for="desc"><?php echo lang('store_details');?><span>*</span></label>
                                                <?php
                                                $des_atts = array(
                                                                    'name'          => 'description['.$lang->id.']',
                                                                    'rows'          => 10,
                                                                    'placeholder'   => $lang->name,
                                                                    'id'            => 'input-description'.$lang->id ,
                                                                    'class'         => 'form-control',
                                                                    'required'      => 'required',
                                                                    'value'         => set_value('description['.$lang->id.']')
                                                                );
                                                echo form_textarea($des_atts);
                                                echo form_error('description['.$lang->id.']');
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </section>
                <!--admin-buttons-->
                <section class="admin-buttons">
                    <div class="container-fluid button-style">
                      <button class="back"><a href="#"><?php echo lang('previous');?></a></button>
                      <button class="next3"><a href="#"><?php echo lang('continue');?></a></button>
                    </div>
                </section>
            </li>

            <li>
              <!--choosePackage-->
              <section class="shop-reg">
                  <div class="container-fluid">
                      <div class="reg-content text-center">
                          <h3><?php echo lang('all_fields_are_added');?></h3>
                          <p><?php echo lang('finish_all_steps');?></p>
                      </div>
                  </div>
              </section>
              <!--admin-buttons-->
              <section class="admin-buttons">
                  <div class="container-fluid button-style">
                      <button class="back"><a href="#"><?php echo lang('previous');?></a></button>
                      <button class="finish"><?php echo lang('finish');?></button>
                  </div>
              </section>
            </li>
        </ul>
      </form>
    </main>
    <!--footerStart-->
    <footer>
        <div class="container-fluid">
            <div class="row">
                <div class="col social">
                    <?php if($this->config->item('facebook') != ''){?>
                      <a href="<?php echo $this->config->item('facebook');?>" target="_blank"><svg><use xlink:href="#facebookFooter"></use></svg></a>
                    <?php }if($this->config->item('twitter') != ''){?>
                      <a href="<?php echo $this->config->item('twitter');?>" target="_blank"><svg><use xlink:href="#twitter"></use></svg></a>
                    <?php }if($this->config->item('instagram') != ''){?>
                      <a href="<?php echo $this->config->item('instagram');?>" target="_blank"><svg><use xlink:href="#instagram"></use></svg></a>
                    <?php }?>
                  <?php if($this->config->item('youtube') != ''){?>
                    <a href="<?php echo $this->config->item('youtube');?>" target="_blank"><svg id="youtube" viewBox="0 0 310 310">
          <g id="XMLID_822_">
            <path id="XMLID_823_" d="M297.917,64.645c-11.19-13.302-31.85-18.728-71.306-18.728H83.386c-40.359,0-61.369,5.776-72.517,19.938
                                            C0,79.663,0,100.008,0,128.166v53.669c0,54.551,12.896,82.248,83.386,82.248h143.226c34.216,0,53.176-4.788,65.442-16.527
                                            C304.633,235.518,310,215.863,310,181.835v-53.669C310,98.471,309.159,78.006,297.917,64.645z M199.021,162.41l-65.038,33.991
                                            c-1.454,0.76-3.044,1.137-4.632,1.137c-1.798,0-3.592-0.484-5.181-1.446c-2.992-1.813-4.819-5.056-4.819-8.554v-67.764
                                            c0-3.492,1.822-6.732,4.808-8.546c2.987-1.814,6.702-1.938,9.801-0.328l65.038,33.772c3.309,1.718,5.387,5.134,5.392,8.861
                                            C204.394,157.263,202.325,160.684,199.021,162.41z"></path>
          </g>
        </svg></a>
                  <?php }?>

                </div>
                <div class="col copyright">
                    <span><?php echo lang('copy_rights').' '.lang('store_name');?><span class="copy-icon"> © </span><?php echo date('Y');?> </span>
                </div>
            </div>
        </div>
    </footer>
    <?php /*<footer>
        <div class="container-fluid">
            <div class="row">
                <div class="col social">
                    <a href="<?php echo $this->config->item('facebook');?>" target="_blank"><svg><use xlink:href="#facebook"></use></svg></a>
                    <a href="<?php echo $this->config->item('twitter');?>" target="_blank"><svg><use xlink:href="#twitter"></use></svg></a>
                    <a href="<?php echo $this->config->item('instagram');?>" target="_blank"><svg><use xlink:href="#instagram"></use></svg></a>
                </div>
                <div class="col copyright">
                    <span><?php echo date('Y');?> &copy; <?php echo lang('site_title');?> </span>
                </div>
            </div>
        </div>
    </footer>*/ ?>
</div>

<script src="<?php echo base_url();?>assets/template/merchant/js/Script.js"></script>

<script>
$(".finish").click(function(){
    $( "#reg_form" ).submit();
});
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->config->item('googleapi_key');?>&libraries=places&callback=initMap" async defer></script>

<script>
  var map, autocomplete, places, marker, geocoder;

  function initMap() {
    geocoder = new google.maps.Geocoder();
    var lat_var = 24.7253981;
    var lng_var = 46.2620271;

    map = new google.maps.Map(document.getElementById('map'), {
      zoom: 8,
      center: {
          lat: lat_var,
          lng: lng_var },
      mapTypeControl: false,
      panControl: false,
      zoomControl: false,
      streetViewControl: false
    });

    marker = new google.maps.Marker({ position: {
      lat: lat_var,
      lng: lng_var },
      map: map, draggable: true });


    autocomplete = new google.maps.places.Autocomplete(
          /** @type {!HTMLInputElement} */(
        document.getElementById('autocomplete')), {
      types: ['(cities)']
    });
    places = new google.maps.places.PlacesService(map);

    autocomplete.addListener('place_changed', onPlaceChanged);

    google.maps.event.addListener(marker, 'dragend', function () {
      // updateMarkerStatus('Drag ended');
      geocodePosition(marker.getPosition());
      map.panTo(marker.getPosition());

      console.log(marker.getPosition().lat())
      console.log(marker.getPosition().lng())
      $("#lat_input").val(marker.getPosition().lat());
      $("#lng_input").val(marker.getPosition().lng());
    });
  }

  function onPlaceChanged() {
    var place = autocomplete.getPlace();
    if (place.geometry) {
      map.panTo(place.geometry.location);
      map.setZoom(15);
      marker.setPosition(place.geometry.location)
      search();
    } else {
      document.getElementById('autocomplete').placeholder = 'Enter a city';
    }
  }
  function search() {
    var search = {
      bounds: map.getBounds(),
      types: ['lodging']
    };
  }
  function geocodePosition(pos) {
    geocoder.geocode({
      latLng: pos
    }, function (responses) {
      if (responses && responses.length > 0) {
        updateMarkerAddress(responses[0].formatted_address);
      } else {
        updateMarkerAddress('Cannot determine address at this location.');
      }
    });
  }
  function updateMarkerAddress(str) {
    //document.getElementById('autocomplete').innerHTML = str;
    $("#autocomplete").val(str);
  }

  function getLocation(e) {
    e.preventDefault();
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(renderMapInCurrentPosition);
    } else {
      alert("browser doesn't support Geo Location")
    }
  }

  function renderMapInCurrentPosition(position) {
    userLocation = {lat:position.coords.latitude,lng:position.coords.longitude}

    $("#lat_input").val(position.coords.latitude);
    $("#lng_input").val(position.coords.longitude);

    map.panTo(userLocation);
    map.setZoom(15);
    marker.setPosition(userLocation)
  }
  document.querySelector(".getcurrentLocation").addEventListener("click",getLocation);
</script>

  <script>
    //first step validation
    $( "body" ).on( "click", ".next1", function(event){
      event.preventDefault();
      if($('input[name=i_agree]:checked').length <= 0){

        $('.validation_msg_1').show();
      }
      else {
        $('.validation_msg_1').hide();

        $(".steps .step-num.active").next().addClass("active");
        var a=$(".admin-change ul li.activeA").next();
        $(".admin-change ul li").hide().removeClass("activeA");
        $(a).show().addClass("activeA");
      }
    });

    //second step validation
    $( "body" ).on( "click", ".next2", function(event){
      event.preventDefault();
      var validation_needed = 0;

      if($('#first_name').val() == '' )
      {
        validation_needed = 1;
        $('#first_name').addClass("error_msg");
      }
      else {
        //validation_needed = 0;
        $('#first_name').removeClass("error_msg");
      }

      if($('#last_name').val() == '' )
      {
        validation_needed = 1;
        $('#last_name').addClass("error_msg");
      }
      else {
        //validation_needed = 0;
        $('#last_name').removeClass("error_msg");
      }

      if($('#phone').val() == '' )
      {
        validation_needed = 1;
        $('#phone').addClass("error_msg");
      }
      else {
        //validation_needed = 0;
        $('#phone').removeClass("error_msg");
      }

      if($('#email').val() == '' || !validateEmail($('#email').val()) )
      {
        validation_needed = 1;
        $('#email').addClass("error_msg");
      }
      else {
        $('#email').removeClass("error_msg");

        //email unique check
        var postData = {email: $('#email').val()};
        $.post('<?php echo base_url()."users/register_store/check_email_unique";?>', postData, function(result){
          if(result != '')
          {
            validation_needed = 1;
            $('#email').addClass("error_msg");
            $('.email_error').html(result);
            //alert(result);
          }
        });
      }




      if($('#password').val() == '' )
      {
        validation_needed = 1;
        $('#password').addClass("error_msg");
      }
      else {
        //validation_needed = 0;
        $('#password').removeClass("error_msg");
      }

      if($('#conf_password').val() == '' || $('#conf_password').val()!= $('#password').val())
      {
        validation_needed = 1;
        $('#conf_password').addClass("error_msg");
      }
      else {
        //validation_needed = 0;
        $('#conf_password').removeClass("error_msg");
      }

      if($('#route').val() == '' )
      {
        validation_needed = 1;
        $('#route').addClass("error_msg");
      }
      else {
        //validation_needed = 0;
        $('#route').removeClass("error_msg");
      }

      if($('#lng_input').val() == '' || $('#lat_input').val() == '')
      {
        validation_needed = 1;
        $('#autocomplete').addClass("error_msg");
      }
      else {
        //validation_needed = 0;
        $('#autocomplete').removeClass("error_msg");
      }

      if(validation_needed == 0)
      {
        $('.validation_msg_2').hide();

        $(".steps .step-num.active").next().addClass("active");
        var a=$(".admin-change ul li.activeA").next();
        $(".admin-change ul li").hide().removeClass("activeA");
        $(a).show().addClass("activeA");
      }
      else {
          $('.validation_msg_2').show();
      }

    });

    function validateEmail($email) {
      var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      //alert(emailReg.test( $email ));
      return emailReg.test( $email );
    }

    //first stip validation
    $( "body" ).on( "click", ".next3", function(event){
      event.preventDefault();
      var validation_needed = 0;

      <?php foreach($data_languages as $lang){?>

        if($('#input-name<?php echo $lang->id;?>').val() == '' )
        {
          validation_needed = 1;
          $('#input-name<?php echo $lang->id;?>').addClass("error_msg");
        }
        else {
          $('#input-name<?php echo $lang->id;?>').removeClass("error_msg");
        }

        if($('#input-description<?php echo $lang->id;?>').val() == '' )
        {
          validation_needed = 1;
          $('#input-description<?php echo $lang->id;?>').addClass("error_msg");
        }
        else {
          $('#input-description<?php echo $lang->id;?>').removeClass("error_msg");
        }
      <?php }?>

       if(validation_needed == 0)
       {
        $('.validation_msg_3').hide();

        $(".steps .step-num.active").next().addClass("active");
        var a=$(".admin-change ul li.activeA").next();
        $(".admin-change ul li").hide().removeClass("activeA");
        $(a).show().addClass("activeA");
      }
      else {
        $('.validation_msg_3').show();
      }
    });
    </script>

    <script>

    //on page load
    $( document ).ready (function(){
     if($( "#country_id option:selected" ).val() != 0)
     {
       postData = {country_id : $( "#country_id option:selected" ).val()}
       $.post('<?php echo base_url().'users/register/get_country_call_code';?>', postData, function(result){
           $('.country_code').val(result);
           $('.country_code').show();

      }, 'json');
     }
    });
    // on change country_id input
    $('body').on("change", '#country_id', function(){
     postData = {country_id : $( "#country_id option:selected" ).val()}

     $.post('<?php echo base_url().'users/register/get_country_call_code';?>', postData, function(result){
         $('.country_code').val(result);
         $('.country_code').show();

    }, 'json');
    });
    </script>

    <script type="text/javascript">
    	var upload_info_<?php echo $unique_id1; ?> = {
    		accepted_file_types: /(\.|\/)(gif|jpeg|jpg|png|tiff|doc|docx|txt|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx|mp3|m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2)$/i,
    		accepted_file_types_ui : ".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2",
    		max_file_size: 20971520,
    		max_file_size_ui: "20MB"
    	};

        var upload_info_<?php echo $unique_id2; ?> = {
    		accepted_file_types: /(\.|\/)(gif|jpeg|jpg|png|tiff|doc|docx|txt|odt|xls|xlsx|pdf|ppt|pptx|pps|ppsx|mp3|m4a|ogg|wav|mp4|m4v|mov|wmv|flv|avi|mpg|ogv|3gp|3g2)$/i,
    		accepted_file_types_ui : ".gif,.jpeg,.jpg,.png,.tiff,.doc,.docx,.txt,.odt,.xls,.xlsx,.pdf,.ppt,.pptx,.pps,.ppsx,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.flv,.avi,.mpg,.ogv,.3gp,.3g2",
    		max_file_size: 20971520,
    		max_file_size_ui: "20MB"
    	};


    	var string_upload_file 	= "Upload a file";
    	var string_delete_file 	= "Deleting file";
    	var string_progress 			= "Progress: ";
    	var error_on_uploading 			= "An error has occurred on uploading.";
    	var message_prompt_delete_file 	= "Are you sure that you want to delete this file?";

    	var error_max_number_of_files 	= "You can only upload one file each time.";
    	var error_accept_file_types 	= "You are not allow to upload this kind of extension.";
    	var error_max_file_size 		= "The uploaded file exceeds the 20MB directive that was specified.";
    	var error_min_file_size 		= "You cannot upload an empty file.";

    	var base_url = "<?php echo base_url();?>";
    	var upload_a_file_string = "Upload a file";

    </script>


  </body>
</html>

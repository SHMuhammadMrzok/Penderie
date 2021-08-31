<!DOCTYPE HTML>
<html>
	<head>
		<title>Control Panel</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style type="text/css" media="all">
        	#ProgressReportContainer {
        		padding: 0px;
        		margin: 0px;
        		width: auto;
        	}
        
        	#ProgressReportProgress {
        		margin: 0px;
        		text-align: center;
        	}
        
        	#ProgressReportProgressBar {
        		padding: 0px;
        		height: 25px;
        		margin: auto;
        		width: 300px;
        		border: 1px solid #CCCCCC;
        		background: url(images/progressbar.gif) no-repeat -300px 0px;
        		text-align: center;
        		font-weight: bold;
        	}
        
        	#ProgressReportStatus {
        		text-align: center;
        	}
        
        	#ProgressReportWindow_Close {
        		float: right;
        		cursor: pointer;
        		display: none;
        	}
            
            .body
            {
            	font-size: 12px;
            	font-family: Tahoma, Verdana, Helvetica, sans-serif;
            }
            
            .popupBody {
            	background-color:#F3F2E9;
            	color:black;
            	line-height:1.5;
            	margin:20px;
            	padding:0pt;
            }
        
            
            .Heading1
            {
            	color: #000000;
            	font-size: 18px;
            	font-weight: bold;
            	font-family: Tahoma, Verdana, Helvetica, sans-serif;
            	padding-bottom: 10px;
            }
            
            .Heading1 a {
            	font-size:18px;
            	color:#005FA3;
            }
            .Intro
            {
            	font-size: 12px;
            	font-family: Tahoma, Verdana, Helvetica, sans-serif;
            	padding: 0px 0px 3px 2px;
            }
            
            .Intro div
            {
            	margin: 10px 0px 11px 0px;
            }
            
            .Intro .Button
            {
            	font-size: 11px;
            	width: 150px;
            	font-family: Tahoma, Verdana, Helvetica, sans-serif;
            }
            
            .popupContainer {
            	background-color:#FFFFFF;
            	border:1px solid #CAC7BD;
            	padding:20px;
            }
            .close_btn{
                color: #FFFFFF;
                background-color: #4B8DF8;
                margin-top: 0px;
                margin-left: 0px;
                margin-right: 5px;
                outline: none !important;
                background-image: none !important;
                filter: none;
                -webkit-box-shadow: none;
                -moz-box-shadow: none;
                box-shadow: none;
                text-shadow: none;
                padding: 4px 10px 5px 10px;
                font-size: 13px;
                line-height: 1.5;
                border-width: 0;
                display: inline-block;
                margin-bottom: 0;
                font-weight: 400;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                -ms-touch-action: manipulation;
                touch-action: manipulation;
                cursor: pointer;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                background-image: none;
                border: 1px solid transparent;
                border-radius: 0 !important;
                box-sizing: border-box;
                font-family: "Open Sans", sans-serif;
            }
            .close_btn:focus, .close_btn:hover {
                /*color: #333;*/
                text-decoration: none;
            }
            a:hover {
                background-color: #4B8DF8;
            }
            
        </style>
		<script defer>
			// Hack for IE
			if(navigator.userAgent.indexOf('MSIE') > -1) {
				document.getElementById('popContainer').style.width = '100%';
			}
		</script>
        
        <script src="http://localhost/ecommerce/assets/template/admin/global/plugins/jquery.min.js" type="text/javascript"></script>


</head>

<body class="popupBody">
	<div class="popupContainer" id="popContainer">

<div id="ProgressReportContainer">
	<div id="ProgressReportWindow_Close">
		<a href="#" id="ProgressReportWindow_CloseButton">[ x Close ]</a>
	</div>
	<div id="ProgressReportTitle" class="Heading1">Rebuild Encryption</div>
	<br />
	<div id="ProgressReportMessage" class="body pageinfo"><p>Please wait while we attempt to re-encrypt data.</p></div>
	<br />
	<div id="ProgressReportReport" class="body"></div>
	<br />
	<div id="ProgressReportProgress">
		<div id="ProgressReportProgressBar">&nbsp;</div>
		<div id="ProgressReportProgressNumber">&nbsp;</div>
	</div>
	<div id="ProgressReportStatus" class="Intro"></div>
    <button class="close_btn" style="display: none;" onclick="self.parent.tb_remove();">close</button>
</div>
<!-- iframe which does all of the work -->
<iframe id="fmeWorker" width="1" height="1" frameborder="0" border="0"></iframe>

<script>
    <?php if(!$completed){?>
    
    	setTimeout(function() {
    		var e = document.getElementById('fmeWorker');
            window.location = '<?php echo base_url();?>settings/rebuild_encryption/rebuild_encryption_modal';
    	}, 100);
         
    <?php }else{?>
        $(".close_btn").show();
    <?php }?>
    
    UpdateStatus('<?php echo $process;?>' , '<?php echo $percent;?>');

	function UpdateStatus(status, percentage)
	{
		var f = document.getElementById('ProgressReportProgressBar');
		f.style.background = 'url(<?php echo base_url();?>assets/template/admin/img/progressbar.gif) no-repeat -' + (300 - (percentage * 3)) + 'px 0px';
		f.innerHTML = parseInt(percentage) + "%";
		document.getElementById('ProgressReportStatus').innerHTML = status;
	}

	function UpdateStatusReport(report)
	{
		document.getElementById('ProgressReportReport').innerHTML = report;
	}

	$('#ProgressReportWindow_CloseButton').click(function(event) {
		parent.tb_remove();
	});
</script>
<!-- END PAGE FOOTER -->
</div>
</body>
</html>
<!-- END PAGE FOOTER -->
</div>
</body>
</html>
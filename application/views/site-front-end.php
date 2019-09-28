<?php $template_path = BASE_PATH.'application/views/TemplateFrontEnd/'; $session = $_SESSION; ?>
<!DOCTYPE html>
<html>
<head>
<title>YouGrabber - Premium YouTube Downloader - PHP Script</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!--css-->
<link rel="stylesheet" href="<?php echo $template_path; ?>css/select2.min.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo $template_path; ?>css/style.css" type="text/css" media="all" />
<!--/css-->
<!--fonts-->
<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<!--fonts-->
<!--js-->
<script src="<?php echo $template_path; ?>js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $template_path; ?>js/select2.min.js" type="text/javascript"></script>
<script>
<?php if(isset($use_mp3)): ?>
var use_mp3 = <?=$use_mp3;?>;
<?php endif; ?>
var base_path = "<?php echo BASE_PATH;?>";
</script>

<?php if(isset($use_external_js) && $use_external_js == 1): ?>
<script src="http://oraksoft.com/demos/YouGrabber-demo/application/views/TemplateFrontEnd/js/script.js"></script>
<?php else: ?>
<script src="<?php echo $template_path; ?>js/script.js"></script>
<?php endif; ?>
<!--js-->
</head>
<body>
	<div class="container1">
		<div class="content-header">
			<a href="<?php echo BASE_PATH; ?>"><img src='<?php echo $template_path; ?>images/logo.png'/></a>
		</div>
	</div>
	<div class="clear"> </div>
	
	<div class="notify" class="main clearfix column">
			<form>
				<div id="step1" class="step">
					<input id="video_url" type="text" class="textbox" placeholder="Enter Valid Video URL ..."/>
					<input id="download_btn" class="md-trigger md-setperspective" data-modal="top-scroll" type="submit" value="Download">
				</div>
				
				
				
				<div id="step2" class="step">
					<a id='title' target="_blank"></a> <!-- max len: 60 -->

					<div class='clear'>&nbsp;</div>
					<img src='' alt='thumbnail' id='thumb' class='thumbnail'/>
					<a class='color'>Duration</a> &mdash; <span id='duration'></span>
					<br/><br/>
					Author &mdash; <a class='color' id='author'></a>
					<br/><br/>
					<a class='color'>Views</a> &mdash; <span id='view_count'></span>
					<br/><br/>
					Is video public &mdash; <a class='color' id='is_listed'></a>
					
					<div class="clear">&nbsp;</div>
					
					<div class='center-align'>
						<select class='' id='formats'></select>
					
						<div class="clear">&nbsp;</div>
						<div class="clear">&nbsp;</div>
						<div id='after-selection'>
							<a class='submit' id='dwn_anchor'>Download your file</a>
							<div class="clear">&nbsp;</div>
							<div class="clear">&nbsp;</div>
							<a class='submit inverse_color' id='start_over'>Start over</a>
						</div>
					</div>
				</div>
				
				<div id='loadbar'><img src='<?php echo $template_path; ?>images/load.gif'/></div>
			</form>
		</div>
		
		<div class="social-icons">
			<h3>Social Networks</h3>
				<ul>
					<?php if($facebook != ''): ?><li><a href="<?php echo $facebook; ?>" target="_blank"><img src="<?php echo $template_path; ?>images/facebook.png" title="facebook"></a></li><?php endif; ?>
					<?php if($twitter != ''): ?><li><a href="<?php echo $twitter; ?>" target="_blank"><img src="<?php echo $template_path; ?>images/twitter.png" title="Twiiter"></a></li><?php endif; ?>
				</ul>
		</div>
		
		<div class="copy-right">
			<p>A PRODUCT OF <a href="https://codecanyon.net/user/oraksoft/portfolio?ref=oraksoft" target="_blank">ORAKSOFT</a> © 2017. </p>
		</div>
</body>
</html>
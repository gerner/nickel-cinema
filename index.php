<?php 
require_once("includes.php");
require_once("lib/session.php");

$m = new Movies(98116);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<link rel="stylesheet" href="css/aal.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="css/canonicalizable.css" type="text/css" media="screen" charset="utf-8" /> 
</head>
<body>
<div id="wrapper">

<?php 
$m->load();
echo '<pre>';
print_r($m->movies);
echo '</pre>';
?>

</div>

<?php if(GG_ANALYTICS_ID) {?>
<script type="text/javascript"> 
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script> 
<script type="text/javascript"> 
	try {
		var pageTracker = _gat._getTracker("<?php echo GG_ANALYTICS_ID;?>");
		// Cookied already: 
		pageTracker._trackPageview();
	} catch(err) {}
</script>
<?php }?>
</body>
</html> 
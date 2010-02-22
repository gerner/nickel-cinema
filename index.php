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

function filterLowRatings($movie)
{ 
	return $movie->rating > 2.5;
}
$filteredMovies = array_filter($m->movies, "filterLowRatings");

function compareRatings($a, $b)
{
	if ($a->rating == $b->rating) {
        return 0;
    }
    return ($a->rating < $b->rating) ? 1 : -1;
}
usort($filteredMovies, "compareRatings");
?>

<ul>
<?php foreach($filteredMovies as $movie) {?>
	<li><strong><?php echo $movie->name;?></strong> <?php echo "(".$movie->rating.")";?><br />
	<ul>
	<?php foreach($movie->theaters as $theater => $showtimes) {?>
		<li><?php echo $theater.": ".implode(" ", $showtimes);?></li>
	<?php }?>
	</ul>
	</li>
<?php } ?>
</ul>

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
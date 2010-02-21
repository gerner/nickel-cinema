<?php 

function cache_open_cache()
{
	global $_CACHEMANAGER_CACHE;
	if(isset($_CACHEMANAGER_CACHE))
		return $_CACHEMANAGER_CACHE;
	if(!file_exists(CACHEMANAGER_FILE))
		return array();
	
	$cacheContents = file_get_contents(CACHEMANAGER_FILE);
	$a = json_decode($cacheContents, true);
	$_CACHEMANAGER_CACHE = $a;
	return $a;
}

function cache_flush_cache()
{
	global $_CACHEMANAGER_CACHE;
	file_put_contents(CACHEMANAGER_FILE, json_encode($_CACHEMANAGER_CACHE), LOCK_EX);
}

function cache_get($key)
{
	global $_CACHEMANAGER_CACHE;
	cache_open_cache();
	return $_CACHEMANAGER_CACHE[$key];
}

function cache_set($key, $value)
{
	global $_CACHEMANAGER_CACHE;
	cache_open_cache();
	$_CACHEMANAGER_CACHE[$key] = $value;
	cache_flush_cache();
}

?>
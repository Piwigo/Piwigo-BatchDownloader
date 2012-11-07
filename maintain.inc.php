<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

defined('BATCH_DOWNLOAD_ID') or define('BATCH_DOWNLOAD_ID', basename(dirname(__FILE__)));
include_once(PHPWG_PLUGINS_PATH . BATCH_DOWNLOAD_ID . '/include/install.inc.php');

function plugin_install() 
{
  batch_download_install();
  
  define('batch_download_installed', true);
}

function plugin_activate()
{
  if (!defined('batch_download_installed'))
  {
    batch_download_install();
  }
}

function plugin_uninstall() 
{
  global $prefixeTable, $conf;
  
  pwg_query('DELETE FROM `' . CONFIG_TABLE . '` WHERE param = "batch_download" LIMIT 1;');
  pwg_query('DROP TABLE IF EXISTS `' . $prefixeTable . 'download_sets`;');
  pwg_query('DROP TABLE IF EXISTS `' . $prefixeTable . 'download_sets_images`;');
  
  rrmdir($conf['data_location'].'download_archives/');
}


if (!function_exists('rrmdir'))
{
  function rrmdir($dir)
  {
    if (!is_dir($dir))
    {
      return false;
    }
    $dir = rtrim($dir, '/');
    $objects = scandir($dir);
    $return = true;
    
    foreach ($objects as $object)
    {
      if ($object !== '.' && $object !== '..')
      {
        $path = $dir.'/'.$object;
        if (filetype($path) == 'dir') 
        {
          $return = $return && rrmdir($path); 
        }
        else 
        {
          $return = $return && @unlink($path);
        }
      }
    }
    
    return $return && @rmdir($dir);
  }
}

?>
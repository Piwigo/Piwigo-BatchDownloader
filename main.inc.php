<?php 
/*
Plugin Name: Batch Downloader
Version: auto
Description: Allows users to download pictures sets in ZIP. Compatible with User Selection.
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

global $conf, $prefixeTable;

define('BATCH_DOWNLOAD_PATH',    PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');
define('BATCH_DOWNLOAD_TSETS',   $prefixeTable . 'download_sets');
define('BATCH_DOWNLOAD_TIMAGES', $prefixeTable . 'download_sets_images');
define('BATCH_DOWNLOAD_LOCAL',   PHPWG_ROOT_PATH . $conf['data_location'] . 'download_archives/');
define('BATCH_DOWNLOAD_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . basename(dirname(__FILE__)));
define('BATCH_DOWNLOAD_PUBLIC',  make_index_url(array('section' => 'download')) . '/');

if (class_exists('ZipArchive'))
{
  add_event_handler('init', 'batch_download_init');

  add_event_handler('loc_end_section_init', 'batch_download_section_init');
  add_event_handler('loc_end_index', 'batch_download_page');
  
  add_event_handler('loc_end_index', 'batch_download_clean');

  add_event_handler('loc_begin_index', 'batch_download_index_button');

  add_event_handler('blockmanager_register_blocks', 'batch_download_add_menublock');
  add_event_handler('blockmanager_apply', 'batch_download_applymenu');
  
  require(BATCH_DOWNLOAD_PATH . 'include/functions.inc.php');
  require(BATCH_DOWNLOAD_PATH . 'include/BatchDownloader.class.php');
  require(BATCH_DOWNLOAD_PATH . 'include/events.inc.php');
}

add_event_handler('get_admin_plugin_menu_links', 'batch_download_admin_menu');

/* admin plugins menu */
function batch_download_admin_menu($menu) 
{
  array_push($menu, array(
    'NAME' => 'Batch Downloader',
    'URL' => BATCH_DOWNLOAD_ADMIN,
  ));
  return $menu;
}

?>
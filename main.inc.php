<?php 
/*
Plugin Name: Batch Downloader
Version: auto
Description: Allows users to download pictures sets in ZIP. Compatible with User Collections.
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=616
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

global $conf, $prefixeTable;

define('BATCH_DOWNLOAD_PATH',    PHPWG_PLUGINS_PATH . 'BatchDownloader/');
define('BATCH_DOWNLOAD_TSETS',   $prefixeTable . 'download_sets');
define('BATCH_DOWNLOAD_TIMAGES', $prefixeTable . 'download_sets_images');
define('BATCH_DOWNLOAD_LOCAL',   PHPWG_ROOT_PATH . $conf['data_location'] . 'download_archives/');
define('BATCH_DOWNLOAD_ADMIN',   get_root_url() . 'admin.php?page=plugin-BatchDownloader');
define('BATCH_DOWNLOAD_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 'download')) . '/');
define('BATCH_DOWNLOAD_VERSION', '1.0.3');


add_event_handler('init', 'batch_download_init');

if (class_exists('ZipArchive'))
{
  add_event_handler('loc_end_section_init', 'batch_download_section_init');
  add_event_handler('loc_end_index', 'batch_download_page');
  
  add_event_handler('loc_end_index', 'batch_download_clean');

  add_event_handler('loc_end_index', 'batch_download_index_button', EVENT_HANDLER_PRIORITY_NEUTRAL+10);

  add_event_handler('blockmanager_register_blocks', 'batch_download_add_menublock');
  add_event_handler('blockmanager_apply', 'batch_download_applymenu');
  
  include_once(BATCH_DOWNLOAD_PATH . 'include/BatchDownloader.class.php');
  include_once(BATCH_DOWNLOAD_PATH . 'include/functions.inc.php');
  include_once(BATCH_DOWNLOAD_PATH . 'include/events.inc.php');
}

if (defined('IN_ADMIN'))
{
  add_event_handler('get_admin_plugin_menu_links', 'batch_download_admin_menu');
}



/**
 * unserialize conf and load language
 */
function batch_download_init()
{
  global $conf, $pwg_loaded_plugins;
  
  if (
    $pwg_loaded_plugins['BatchDownloader']['version'] == 'auto' or
    version_compare($pwg_loaded_plugins['BatchDownloader']['version'], BATCH_DOWNLOAD_VERSION, '<')
  )
  {
    include_once(BATCH_DOWNLOAD_PATH . 'include/install.inc.php');
    batch_download_install();
    
    if ($pwg_loaded_plugins['BatchDownloader']['version'] != 'auto')
    {
      $query = '
UPDATE '. PLUGINS_TABLE .'
SET version = "'. BATCH_DOWNLOAD_VERSION .'"
WHERE id = "BatchDownloader"';
      pwg_query($query);
      
      $pwg_loaded_plugins['BatchDownloader']['version'] = BATCH_DOWNLOAD_VERSION;
      
      if (defined('IN_ADMIN'))
      {
        $_SESSION['page_infos'][] = 'BatchDownloader updated to version '. BATCH_DOWNLOAD_VERSION;
      }
    }
  }
  
  $conf['batch_download'] = unserialize($conf['batch_download']);
  load_language('plugin.lang', BATCH_DOWNLOAD_PATH);
}

/**
 * admin plugins menu
 */
function batch_download_admin_menu($menu) 
{
  array_push($menu, array(
    'NAME' => 'Batch Downloader',
    'URL' => BATCH_DOWNLOAD_ADMIN,
  ));
  return $menu;
}

?>
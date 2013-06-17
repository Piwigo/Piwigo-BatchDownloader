<?php 
/*
Plugin Name: Batch Downloader
Version: auto
Description: Allows users to download pictures sets in ZIP. Compatible with User Collections.
Plugin URI: auto
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

/*
 * advanced config:
 * $conf['batch_download_max_elements']   max value of the elements slider (default 1000)
 * $conf['batch_download_max_size']       max value of the size slider (default 500)
 * $conf['batch_download_force_pclzip']   if true, force the usage of PclZip instead of ZipArchive
 * $conf['batch_download_direct']         if true, the download script will redirect to the zip instead of deliver it through PHP
 * $conf['batch_download_additional_ext'] array containing downloadable filetypes (case sensitive), default is $conf['picture_ext']
 */

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

global $conf, $prefixeTable;

defined('BATCH_DOWNLOAD_ID') or define('BATCH_DOWNLOAD_ID', basename(dirname(__FILE__)));
define('BATCH_DOWNLOAD_PATH',    PHPWG_PLUGINS_PATH . BATCH_DOWNLOAD_ID . '/');
define('BATCH_DOWNLOAD_TSETS',   $prefixeTable . 'download_sets');
define('BATCH_DOWNLOAD_TIMAGES', $prefixeTable . 'download_sets_images');
define('BATCH_DOWNLOAD_LOCAL',   PHPWG_ROOT_PATH . $conf['data_location'] . 'download_archives/');
define('BATCH_DOWNLOAD_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . BATCH_DOWNLOAD_ID);
define('BATCH_DOWNLOAD_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 'download')) . '/');
define('BATCH_DOWNLOAD_VERSION', 'auto');


add_event_handler('init', 'batch_download_init');

if (defined('IN_ADMIN'))
{
  add_event_handler('get_admin_plugin_menu_links', 'batch_download_admin_menu');
}
else
{
  add_event_handler('loc_end_section_init', 'batch_download_section_init');
  add_event_handler('loc_end_index', 'batch_download_page');

  add_event_handler('loc_end_index', 'batch_download_clean');

  add_event_handler('loc_end_index', 'batch_download_index_button', EVENT_HANDLER_PRIORITY_NEUTRAL+10);

  add_event_handler('blockmanager_register_blocks', 'batch_download_add_menublock');
  add_event_handler('blockmanager_apply', 'batch_download_applymenu');
}


include_once(BATCH_DOWNLOAD_PATH . 'include/BatchDownloader.class.php');
include_once(BATCH_DOWNLOAD_PATH . 'include/functions.inc.php');
include_once(BATCH_DOWNLOAD_PATH . 'include/events.inc.php');



/**
 * update plugin & unserialize conf & load language
 */
function batch_download_init()
{
  global $conf, $pwg_loaded_plugins;
  
  if (
    BATCH_DOWNLOAD_VERSION == 'auto' or
    $pwg_loaded_plugins[BATCH_DOWNLOAD_ID]['version'] == 'auto' or
    version_compare($pwg_loaded_plugins[BATCH_DOWNLOAD_ID]['version'], BATCH_DOWNLOAD_VERSION, '<')
  )
  {
    include_once(BATCH_DOWNLOAD_PATH . 'include/install.inc.php');
    batch_download_install();
    
    if ( $pwg_loaded_plugins[BATCH_DOWNLOAD_ID]['version'] != 'auto' and BATCH_DOWNLOAD_VERSION != 'auto' )
    {
      $query = '
UPDATE '. PLUGINS_TABLE .'
SET version = "'. BATCH_DOWNLOAD_VERSION .'"
WHERE id = "'. BATCH_DOWNLOAD_ID .'"';
      pwg_query($query);
      
      $pwg_loaded_plugins[BATCH_DOWNLOAD_ID]['version'] = BATCH_DOWNLOAD_VERSION;
      
      if (defined('IN_ADMIN'))
      {
        $_SESSION['page_infos'][] = 'BatchDownloader updated to version '. BATCH_DOWNLOAD_VERSION;
      }
    }
  }
  
  $conf['batch_download'] = unserialize($conf['batch_download']);
  $conf['batch_download']['allowed_ext'] = $conf['picture_ext'];
  if (!empty($conf['batch_download_additional_ext']))
  {
    $conf['batch_download']['allowed_ext'] = array_merge($conf['batch_download']['allowed_ext'], $conf['batch_download_additional_ext']);
  }
  
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
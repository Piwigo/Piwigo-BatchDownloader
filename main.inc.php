<?php
/*
Plugin Name: Batch Downloader
Version: auto
Description: Allows users to download pictures sets in ZIP. Compatible with User Collections.
Plugin URI: auto
Author: Mistic
Author URI: http://www.strangeplanet.fr
Has Settings: true
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (basename(dirname(__FILE__)) != 'BatchDownloader')
{
  add_event_handler('init', 'batch_download_error');
  function batch_download_error()
  {
    global $page;
    $page['errors'][] = 'Batch Downloader folder name is incorrect, uninstall the plugin and rename it to "BatchDownloader"';
  }
  return;
}

global $conf, $prefixeTable;

define('BATCH_DOWNLOAD_PATH',    PHPWG_PLUGINS_PATH . 'BatchDownloader/');
define('BATCH_DOWNLOAD_TSETS',   $prefixeTable . 'download_sets');
define('BATCH_DOWNLOAD_TIMAGES', $prefixeTable . 'download_sets_images');
define('IMAGE_SIZES_TABLE',      $prefixeTable . 'image_sizes');
define('BATCH_DOWNLOAD_LOCAL',   PHPWG_ROOT_PATH . $conf['data_location'] . 'download_archives/');
define('BATCH_DOWNLOAD_ADMIN',   get_root_url() . 'admin.php?page=plugin-BatchDownloader');
define('BATCH_DOWNLOAD_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 'download')) . '/');
define('BATCH_DOWNLOAD_TREQUESTS', $prefixeTable . 'download_requests');

add_event_handler('init', 'batch_download_init');

if (!defined('IN_ADMIN'))
{
  add_event_handler('init', 'batch_downloader_remove_image');

  add_event_handler('loc_end_section_init', 'batch_download_section_init');
  add_event_handler('loc_end_index', 'batch_download_page');

  add_event_handler('loc_end_index', 'batch_download_clean');

  add_event_handler('loc_end_index', 'batch_download_index_button', EVENT_HANDLER_PRIORITY_NEUTRAL+10);
}

add_event_handler('blockmanager_register_blocks', 'batch_download_add_menublock');
add_event_handler('blockmanager_apply', 'batch_download_applymenu');

include_once(BATCH_DOWNLOAD_PATH . 'include/BatchDownloader.class.php');
include_once(BATCH_DOWNLOAD_PATH . 'include/functions.inc.php');
include_once(BATCH_DOWNLOAD_PATH . 'include/events.inc.php');

add_event_handler('ws_add_methods', 'batch_download_ws_add_methods');
include_once(BATCH_DOWNLOAD_PATH . 'include/ws_functions.inc.php');

/**
 * update plugin & unserialize conf & load language
 */
function batch_download_init()
{
  global $conf;

  if (is_string($conf['batch_download']))
  {
    // Piwigo 11 has added an automatic espace of the word "groups" (new MySQL reserved keyword).
    // Unserialize doesn't like the escaped `groups` at all, so we need to remove it
    $conf['batch_download'] = str_replace('`groups`', 'groups', $conf['batch_download']);
  }

  $conf['batch_download'] = safe_unserialize($conf['batch_download']);
  $conf['batch_download']['file_pattern'] = isset($conf['batch_download_file_pattern']) ? $conf['batch_download_file_pattern'] : '%id%_%filename%_%dimensions%';
  $conf['batch_download']['allowed_ext'] = $conf['picture_ext'];
  if (!empty($conf['batch_download_additional_ext']))
  {
    $conf['batch_download']['allowed_ext'] = array_merge($conf['batch_download']['allowed_ext'], $conf['batch_download_additional_ext']);
  }
  $conf['batch_download']['use_representative_for_ext'] = isset($conf['batch_download_use_representative_for_ext'])
    ? $conf['batch_download_use_representative_for_ext']
    : array('tif', 'TIF', 'tiff', 'TIFF')
    ;

  load_language('plugin.lang', BATCH_DOWNLOAD_PATH);
}

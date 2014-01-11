<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

class BatchDownloader_maintain extends PluginMaintain
{
  private $installed = false;

  private $default_conf = array(
    'groups'          => array(),
    'level'           => 0,
    'what'            => array('categories','specials','collections'),
    'photo_size'      => 'original',
    'multisize'       => true,
    'archive_prefix'  => 'piwigo',
    'archive_timeout' => 48, /* hours */
    'max_elements'    => 500,
    'max_size'        => 100, /* MB */
    'last_clean'      => time(),
    'one_archive'     => false,
    'force_pclzip'    => false,
    'direct'          => false,
    );

  function install($plugin_version, &$errors=array())
  {
    global $conf, $prefixeTable;

    // configuration
    if (empty($conf['batch_download']))
    {
      $conf['batch_download'] = serialize($this->default_config);
      $conf['batch_download_comment'] = null;

      conf_update_param('batch_download', $conf['batch_download']);
      conf_update_param('batch_download_comment', $conf['batch_download_comment']);
    }
    else
    {
      $new_conf = is_string($conf['batch_download']) ? unserialize($conf['batch_download']) : $conf['batch_download'];

      if (!isset($new_conf['what']))
      {
        $new_conf['what'] = array('categories','specials','collections');
      }
      if (!isset($new_conf['one_archive']))
      {
        $new_conf['one_archive'] = false;
        $new_conf['force_pclzip'] = isset($conf['batch_download_force_pclzip']) && $conf['batch_download_force_pclzip'];
        $new_conf['direct'] = isset($conf['batch_download_direct']) && $conf['batch_download_direct'];
      }
      if (!isset($new_conf['multisize']))
      {
        $new_conf['multisize'] = true;
      }

      $conf['batch_download'] = serialize($new_conf);
      conf_update_param('batch_download', $conf['batch_download']);
    }

    // archives directory
    if (!file_exists(PHPWG_ROOT_PATH . $conf['data_location'] . 'download_archives/'))
    {
      mkgetdir(PHPWG_ROOT_PATH . $conf['data_location'] . 'download_archives/', MKGETDIR_DEFAULT&~MKGETDIR_DIE_ON_ERROR);
    }

    // create tables
    $query = '
CREATE TABLE IF NOT EXISTS `' . $prefixeTable . 'download_sets` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) NOT NULL,
  `date_creation` datetime NOT NULL,
  `type` varchar(16) NOT NULL,
  `type_id` varchar(64) NOT NULL,
  `size` varchar(16) NOT NULL DEFAULT "original",
  `nb_zip` smallint(3) NOT NULL DEFAULT 0,
  `last_zip` smallint(3) NOT NULL DEFAULT 0,
  `nb_images` mediumint(8) NOT NULL DEFAULT 0,
  `total_size` int(10) NOT NULL DEFAULT 0,
  `status` enum("new","ready","download","done") NOT NULL DEFAULT "new",
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
;';
    pwg_query($query);

    $query = '
CREATE TABLE IF NOT EXISTS `' . $prefixeTable . 'download_sets_images` (
  `set_id` mediumint(8) NOT NULL,
  `image_id` mediumint(8) NOT NULL,
  `zip` smallint(5) NOT NULL DEFAULT 0,
  UNIQUE KEY `UNIQUE` (`set_id`,`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;';
    pwg_query($query);

    $query = '
CREATE TABLE IF NOT EXISTS `' . $prefixeTable . 'image_sizes` (
  `image_id` mediumint(8) NOT NULL,
  `type` varchar(16) NOT NULL,
  `width` smallint(9) NOT NULL,
  `height` smallint(9) NOT NULL,
  `filesize` mediumint(9) NOT NULL,
  `filemtime` int(16) NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;';
    pwg_query($query);

    // add a "size" column to download_sets
    $result = pwg_query('SHOW COLUMNS FROM `' . $prefixeTable . 'download_sets` LIKE "size";');
    if (!pwg_db_num_rows($result))
    {
      pwg_query('ALTER TABLE `' . $prefixeTable . 'download_sets` ADD `size` varchar(16) NOT NULL DEFAULT "original";');
    }

    // add "ready" status
    pwg_query('ALTER TABLE `' . $prefixeTable . 'download_sets` CHANGE `status` `status` enum("new","ready","download","done") NOT NULL DEFAULT "new";');
    
    $this->installed = true;
  }

  function activate($plugin_version, &$errors=array())
  {
    if (!$this->installed)
    {
      $this->install($plugin_version, $errors);
    }
  }

  function deactivate()
  {
  }

  function uninstall()
  {
    global $prefixeTable, $conf;

    conf_delete_param('batch_download');

    pwg_query('DROP TABLE IF EXISTS `' . $prefixeTable . 'download_sets`;');
    pwg_query('DROP TABLE IF EXISTS `' . $prefixeTable . 'download_sets_images`;');

    self::rrmdir(PHPWG_ROOT_PATH . $conf['data_location'] . 'download_archives/');
  }

  static function rrmdir($dir)
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
          $return = $return && self::rrmdir($path);
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

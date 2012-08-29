<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

function batch_download_install() 
{
  global $conf, $prefixeTable;
  
  // configuration
  if (empty($conf['batch_download']))
  {
    $batch_download_default_config = serialize(array(
      'groups'          => array(),
      'level'           => 0,
      'photo_size'      => 'original',
      'archive_prefix'  => 'piwigo',
      'archive_timeout' => 48, /* hours */
      'max_elements'    => 500,
      'max_size'        => 100, /* MB */
      'last_clean'      => time(),
      ));
    
    conf_update_param('batch_download', $batch_download_default_config);
    conf_update_param('batch_download_comment', null);
    
    $conf['batch_download'] = $batch_download_default_config;
    $conf['batch_download_comment'] = null;
  }

  // archives directory
  if (!file_exists($conf['data_location'] . 'download_archives/'))
  {
    mkdir($conf['data_location'] . 'download_archives/', 0755);
  }

  // create tables
  $query = '
CREATE TABLE IF NOT EXISTS `' . $prefixeTable . 'download_sets` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) NOT NULL,
  `date_creation` datetime NOT NULL,
  `type` varchar(16) CHARACTER SET utf8 NOT NULL,
  `type_id` varchar(64) CHARACTER SET utf8 NOT NULL,
  `nb_zip` smallint(3) NOT NULL DEFAULT 0,
  `last_zip` smallint(3) NOT NULL DEFAULT 0,
  `nb_images` mediumint(8) NOT NULL DEFAULT 0,
  `total_size` int(10) NOT NULL DEFAULT 0,
  `status` enum("new","download","done") CHARACTER SET utf8 NOT NULL DEFAULT "new",
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
;';
  pwg_query($query);
  
  $query = '
CREATE TABLE IF NOT EXISTS `' . $prefixeTable . 'download_sets_images` (
  `set_id` mediumint(8) NOT NULL,
  `image_id` mediumint(8) NOT NULL,
  `zip` smallint(5) NOT NULL DEFAULT 0,
  UNIQUE KEY `UNIQUE` (`set_id`,`image_id`)
) DEFAULT CHARSET=utf8
;';
  pwg_query($query);
}

?>
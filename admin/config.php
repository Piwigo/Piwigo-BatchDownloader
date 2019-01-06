<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

if (isset($_POST['save_config']))
{
  if (!defined('USER_COLLEC_PATH')) $_POST['what']['collections'] = 'on';

  $conf['batch_download'] = array(
    'groups'          => isset($_POST['groups']) ? $_POST['groups'] : array(),
    'level'           => $_POST['level'],
    'what'            => isset($_POST['what']) ? array_keys($_POST['what']) : array(),
    'photo_size'      => $_POST['photo_size'],
    'multisize'       => $_POST['multisize'] == 'true',
    'archive_prefix'  => trim($_POST['archive_prefix']),
    'archive_timeout' => intval($_POST['archive_timeout']),
    'max_elements'    => intval($_POST['max_elements']),
    'max_size'        => intval($_POST['max_size']),
    'one_archive'     => isset($_POST['one_archive']),
    'force_pclzip'    => isset($_POST['force_pclzip']),
    'direct'          => isset($_POST['direct']),
    'last_clean'      => $conf['batch_download']['last_clean'],
    );

  conf_update_param('batch_download', $conf['batch_download']);
  conf_update_param('batch_download_comment', trim($_POST['archive_comment']), true);

  $page['infos'][] = l10n('Information data registered in database');
}


// groups
$query = '
SELECT id, name
  FROM '.GROUPS_TABLE.'
  ORDER BY name ASC
;';
$group_options = simple_hash_from_query($query, 'id', 'name');

// levels
$level_options = get_privacy_level_options();

// sizes
$type_map = ImageStdParams::get_defined_type_map();
$sizes_keys = array_keys($type_map);
$sizes_names = array_map(function ($s) {return l10n($s);}, $sizes_keys);

$sizes_options = array_combine($sizes_keys, $sizes_names);
$sizes_options['original'] = l10n('Original');

// max values
$conf['batch_download']['max_elements_value'] = isset($conf['batch_download_max_elements']) ? $conf['batch_download_max_elements'] : 1000;
$conf['batch_download']['max_size_value'] =     isset($conf['batch_download_max_size']) ?     $conf['batch_download_max_size'] :     500;


$template->assign(array(
  'group_options' => $group_options,
  'level_options' => $level_options,
  'sizes_options' => $sizes_options,
  'USER_COLLEC_LOADED' => defined('USER_COLLEC_PATH'),
  'DOWNLOAD_PERM_LOADED' => defined('DLPERMS_PATH'),
  'batch_download' => $conf['batch_download'],
  'batch_download_comment' => stripslashes($conf['batch_download_comment']),
  'use_ziparchive' => class_exists('ZipArchive') && !$conf['batch_download']['force_pclzip'],
  'PHP_VERSION' => PHP_VERSION,
  'ADVANCED_CONF' => load_language('advanced.html', BATCH_DOWNLOAD_PATH, array('return'=>true))
  ));


$template->set_filename('batch_download', realpath(BATCH_DOWNLOAD_PATH . 'admin/template/config.tpl'));

<?php
if (!defined('BATCH_DOWNLOAD_PATH')) die('Hacking attempt!');

if (isset($_POST['save_config']))
{
  $conf['batch_download'] = array(
    'groups'          => isset($_POST['groups']) ? $_POST['groups'] : array(),
    'level'           => $_POST['level'],
    'photo_size'      => $_POST['photo_size'],
    'archive_prefix'  => trim($_POST['archive_prefix']),
    'archive_timeout' => intval($_POST['archive_timeout']),
    'max_elements'    => intval($_POST['max_elements']),
    'max_size'        => intval($_POST['max_size']),
    'last_clean'      => $conf['batch_download']['last_clean'],
    );
  $conf['batch_download_comment'] = trim($_POST['archive_comment']);
  
  conf_update_param('batch_download', serialize($conf['batch_download']));
  conf_update_param('batch_download_comment', $conf['batch_download_comment']);
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
$enabled = ImageStdParams::get_defined_type_map();
$disabled = @unserialize(@$conf['disabled_derivatives']);
if ($disabled === false) $disabled = array();

$sizes_keys = array_diff(array_keys($enabled), array_keys($disabled));
$sizes_names = array_map(create_function('$s', 'return l10n($s);'), $sizes_keys);

$sizes_options = array_combine($sizes_keys, $sizes_names);
$sizes_options['original'] = l10n('Original');

// max values
$conf['batch_download']['max_elements_value'] = isset($conf['batch_download_max_elements']) ? $conf['batch_download_max_elements'] : 1000;
$conf['batch_download']['max_size_value'] = isset($conf['batch_download_max_size']) ? $conf['batch_download_max_size'] : 500;

$template->assign(array(
  'group_options' => $group_options,
  'level_options' => $level_options,
  'sizes_options' => $sizes_options,
  'batch_download' => $conf['batch_download'],
  'batch_download_comment' => stripslashes($conf['batch_download_comment']),
  ));


$template->set_filename('batch_download', dirname(__FILE__) . '/template/config.tpl');

?>
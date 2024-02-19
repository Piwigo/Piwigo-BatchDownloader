<?php
if (!defined('BATCH_DOWNLOAD_PATH')) die('Hacking attempt!');

global $template, $page, $conf;


// tabsheet
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : 'sets';

$tabsheet = new tabsheet();
$tabsheet->add('sets', l10n('History'), BATCH_DOWNLOAD_ADMIN . '-sets');
if ($conf['batch_download']['request_permission'])
{ 
  $query = '
SELECT 
    COUNT(*) as requests
  FROM '.BATCH_DOWNLOAD_TREQUESTS.'
    WHERE request_status = "pending"
;';
  
  $result = query2array($query);
  
  $tabsheet->add('requests', l10n('Requests') .'<span class="badge-number">'.$result[0]['requests'].'</span>' , BATCH_DOWNLOAD_ADMIN . '-requests');
}

$tabsheet->add('config', l10n('Configuration'), BATCH_DOWNLOAD_ADMIN . '-config');
$tabsheet->select($page['tab']);
$tabsheet->assign();

if (!class_exists('ZipArchive'))
{
  $page['warnings'][] = l10n('Unable to find ZipArchive PHP extension, Batch Downloader will use PclZip instead, but with degraded performance.');
}

// include page
include(BATCH_DOWNLOAD_PATH . 'admin/' . $page['tab'] . '.php');

// template
$template->assign(array(
  'BATCH_DOWNLOAD_PATH' => BATCH_DOWNLOAD_PATH,
  'BATCH_DOWNLOAD_ADMIN' => BATCH_DOWNLOAD_ADMIN,
  'ADMIN_PAGE_TITLE' => 'Batch Downloader',
  ));

$template->assign_var_from_handle('ADMIN_CONTENT', 'batch_download');

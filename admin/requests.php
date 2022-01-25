<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

$template->set_filename('batch_download', realpath(BATCH_DOWNLOAD_PATH . 'admin/template/requests.tpl'));

list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));
$template->assign(
  'PAGE_INFOS_FOR_UPDATE',
  json_encode( 
    array(
      'status_change_date' => $dbnow,
    )
  )
);
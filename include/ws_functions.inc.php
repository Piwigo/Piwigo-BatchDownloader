<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

function ws_add_methods($arr)
{
  $service = &$arr[0];

  $service->addMethod(
    'pwg.batch_downloader_csv',
    'ws_batch_downloader_csv',
    null, // no parameters
    'Download the history of batch downloads',
    null,
    array(
      'admin_only' => true, // you can restrict access to admins only
      )
    );
}

function ws_batch_downloader_csv($params, &$service)
{
  global $conf;

  $output_lines = array();

  $query = '
  SELECT
      s.id,
      u.'.$conf['user_fields']['username'].' AS username
    FROM '.BATCH_DOWNLOAD_TSETS.' AS s
      INNER JOIN '.USERS_TABLE.' AS u
      ON s.user_id = u.'.$conf['user_fields']['id'].'
    ORDER BY date_creation DESC, status DESC
  ;';
  $sets = simple_hash_from_query($query, 'id', 'username');
  $printed_lines = count($sets);

  $res = array();
  array_push($res, ['Image_count', 'Archive_count', 'Status', 'Archive_count', 'Total_size', 'Date', 'Size', 'Username']);
  
  foreach ($sets as $set_id => $username)
  {
    $set = new BatchDownloader($set_id);
  
    $res[] = array_merge(
      $set->getCsvInfos(),
      array(
        'USERNAME' => $username,
      )
      );
  
    unset($set);
  }

  header('Content-type: application/csv');
  header('Content-Disposition: attachment; filename='.date('YmdGis').'piwigo_batch_downloader_log.csv');
  header("Content-Transfer-Encoding: UTF-8");

  $f = fopen('php://output', 'w');  
      foreach ($res as $line) { 
          fputcsv($f, $line, ";"); 
      }
  fclose($f);
}


<?php
if (!defined('BATCH_DOWNLOAD_PATH')) die('Hacking attempt!');

// actions
if (isset($_GET['delete']))
{
  $BatchDownloader = new BatchDownloader($_GET['delete']);
  $BatchDownloader->delete();
  unset($BatchDownloader);
}
if (isset($_GET['cancel']))
{
  $BatchDownloader = new BatchDownloader($_GET['cancel']);  
  $BatchDownloader->updateParam('total_size', $BatchDownloader->getEstimatedTotalSize());
  $BatchDownloader->updateParam('status', 'done');
  $BatchDownloader->deleteLastArchive();
  $BatchDownloader->clearImages();
  unset($BatchDownloader);
}
if (isset($_POST['delete_done']))
{
  $query = '
DELETE s, i
  FROM '.BATCH_DOWNLOAD_TSETS.' AS s
    LEFT JOIN '.BATCH_DOWNLOAD_TIMAGES.' AS i
    ON i.set_id = s.id
  WHERE
    status = "done" AND
    date_creation < DATE_SUB(NOW(), INTERVAL 1 HOUR)
;';
  pwg_query($query);
}


// filter
$where_clauses = array('1=1');
$order_by = 'date_creation DESC, status DESC';

if (isset($_POST['filter']))
{
  if (!empty($_POST['username']))
  {
    array_push($where_clauses, 'username LIKE "%'.$_POST['username'].'%"');
  }
  
  if ($_POST['type'] != -1)
  {
    array_push($where_clauses, 'type = "'.$_POST['type'].'"');
  }
  
  if ($_POST['status'] != -1)
  {
    array_push($where_clauses, 'status = "'.$_POST['status'].'"');
  }
  
  $order_by = $_POST['order_by'].' '.$_POST['direction'];
}


// get sets
$query = '
SELECT 
    s.id,
    u.'.$conf['user_fields']['username'].' AS username
  FROM '.BATCH_DOWNLOAD_TSETS.' AS s
    INNER JOIN '.USERS_TABLE.' AS u
    ON s.user_id = u.'.$conf['user_fields']['id'].'
  WHERE
    '.implode("\n    AND ", $where_clauses).'
  ORDER BY '.$order_by.'
;';
$sets = simple_hash_from_query($query, 'id', 'username');

foreach ($sets as $set_id => $username)
{
  $set = new BatchDownloader($set_id);
  
  $template->append('sets', array_merge(
    $set->getSetInfo(),
    array(
      'USERNAME' => $username,
      'U_DELETE' => BATCH_DOWNLOAD_ADMIN . '-sets&amp;delete='.$set->getParam('id'),
      'U_CANCEL' => BATCH_DOWNLOAD_ADMIN . '-sets&amp;cancel='.$set->getParam('id'),
    )
    ));
  
  unset($set);
}


// filter options
$page['status_items'] = array(
  -1 => '------------',
  'new' => l10n('new'),
  'download' => l10n('download'),
  'done' => l10n('done'),
  );

$page['type_items'] = array(
  -1 => '------------',
  'calendar' => l10n('Calendar'),
  'category' => l10n('Album'),
  'flat' => l10n('Whole gallery'),
  'tags' => l10n('Tags'),
  'search' => l10n('Search'),
  'favorites' => l10n('Favorites'),
  'most_visited' => l10n('Most visited'),
  'best_rated' => l10n('Best rated'),
  'list' => l10n('Random'),
  'recent_pics' => l10n('Recent photos'),
  'collection' => l10n('User collection'),
  );

$page['order_by_items'] = array(
  'date_creation' => l10n('Creation date'),
  'total_size' => l10n('Total size'),
  'nb_images' => l10n('Number of images'),
  'nb_archives' => l10n('Number of archives'),
  );

$page['direction_items'] = array(
  'DESC' => l10n('descending'),
  'ASC' => l10n('ascending'),
  );

$template->assign(array(
  'status_options' => $page['status_items'],
  'status_selected' => isset($_POST['status']) ? $_POST['status'] : '',
  'type_options' => $page['type_items'],
  'type_selected' => isset($_POST['type']) ? $_POST['type'] : '',
  'order_options' => $page['order_by_items'],
  'order_selected' => isset($_POST['order_by']) ? $_POST['order_by'] : '',
  'direction_options' => $page['direction_items'],
  'direction_selected' => isset($_POST['direction']) ? $_POST['direction'] : '',

  'F_USERNAME' => @htmlentities($_POST['username'], ENT_COMPAT, 'UTF-8'),
  'F_FILTER_ACTION' => BATCH_DOWNLOAD_ADMIN . '-sets',
  ));


$template->set_filename('batch_download', dirname(__FILE__) . '/template/sets.tpl');

?>
<?php
if (!defined('BATCH_DOWNLOAD_PATH')) die('Hacking attempt!');

// actions
if (isset($_GET['delete']))
{
  $BatchDownloader = new BatchDownloader($_GET['delete']);
  $BatchDownloader->deleteLastArchive();
  $BatchDownloader->clearImages();
  pwg_query('DELETE FROM '.BATCH_DOWNLOAD_TSETS.' WHERE id = '.$_GET['delete'].';');
}
if (isset($_GET['cancel']))
{
  $BatchDownloader = new BatchDownloader($_GET['cancel']);
  $BatchDownloader->deleteLastArchive();
  $BatchDownloader->clearImages();
  $BatchDownloader->updateParam('status', 'done');
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
      'STATUS' => $set->getParam('status'),
      'LAST_ZIP' => $set->getParam('last_zip'),
      'U_DELETE' => BATCH_DOWNLOAD_ADMIN . '-sets&amp;delete='.$set->getParam('set_id'),
      'U_CANCEL' => BATCH_DOWNLOAD_ADMIN . '-sets&amp;cancel='.$set->getParam('set_id'),
    )
    ));
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
  // 'selection' => l10n('Selection'),
  );

$page['order_by_items'] = array(
  'date_creation' => l10n('Creation date'),
  'total_size' => l10n('Total size'),
  'nb_images' => l10n('Nb images'),
  'nb_archives' => l10n('Nb archives'),
  );

$page['direction_items'] = array(
  'DESC' => l10n('descending'),
  'ASC' => l10n('ascending'),
  );

$template->assign('status_options', $page['status_items']);
$template->assign('status_selected',
    isset($_POST['status']) ? $_POST['status'] : '');

$template->assign('type_options', $page['type_items']);
$template->assign('type_selected',
    isset($_POST['type']) ? $_POST['type'] : '');

$template->assign('order_options', $page['order_by_items']);
$template->assign('order_selected',
    isset($_POST['order_by']) ? $_POST['order_by'] : '');

$template->assign('direction_options', $page['direction_items']);
$template->assign('direction_selected',
    isset($_POST['direction']) ? $_POST['direction'] : '');


$template->assign(array(
  'F_USERNAME' => @htmlentities($_POST['username'], ENT_COMPAT, 'UTF-8'),
  'F_FILTER_ACTION' => BATCH_DOWNLOAD_ADMIN . '-sets',
  ));


$template->set_filename('batch_download', dirname(__FILE__) . '/template/sets.tpl');

?>
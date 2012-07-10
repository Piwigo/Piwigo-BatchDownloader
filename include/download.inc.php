<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

# this file is called on public page #

global $page, $template, $conf, $user;

switch ($page['sub_section'])
{
  /* download page */
  case 'init_zip':
  {
    $template->set_filename('index', dirname(__FILE__) . '/../template/init_zip.tpl');
    
    $BatchDownloader = new BatchDownloader($_GET['set_id']);
    
    if ( isset($_GET['cancel']) )
    {
      $BatchDownloader->deleteLastArchive();
      $BatchDownloader->clearImages();
      pwg_query('DELETE FROM '.BATCH_DOWNLOAD_TSETS.' WHERE id = '.$_GET['set_id'].';');
      $_SESSION['page_infos'][] = l10n('Download set deleted');
      redirect('index.php');
    }
    
    if ( isset($_GET['zip']) and $BatchDownloader->getParam('status') != 'done' and $_GET['zip'] > $BatchDownloader->getParam('last_zip') )
    {
      $BatchDownloader->deleteLastArchive();
      $next_file = $BatchDownloader->createNextArchive();
    }

    $set = $BatchDownloader->getSetInfo();
    
    if (isset($next_file))
    {
      $set['U_DOWNLOAD'] = BATCH_DOWNLOAD_PATH . 'download.php?set_id='.$_GET['set_id'].'&amp;zip='.$_GET['zip'];
      array_push($page['infos'], sprintf(l10n('The archive is downloading, if the download doesn\'t start automatically please <a href="%s">click here</a>'), $set['U_DOWNLOAD']));
    }
    
    if ($BatchDownloader->getParam('nb_images') > $conf['batch_download']['max_elements'])
    {
      $template->assign('elements_error', sprintf(
        l10n('You choose to download %d pictures, but the system is limited to %d. You can edit the set, or the last %d pictures will not be downloaded.'),
        $BatchDownloader->getParam('nb_images'),
        $conf['batch_download']['max_elements'],
        $BatchDownloader->getParam('nb_images') - $conf['batch_download']['max_elements']
        ));
    }
    
    if ($BatchDownloader->getParam('status') == 'new')
    {
      $set['U_CANCEL'] = BATCH_DOWNLOAD_PUBLIC . 'init_zip&amp;set_id='.$_GET['set_id'].'&amp;cancel';
    }
    
    $template->assign(array(
      'set' => $set,
      'archive_timeout' => $conf['batch_download']['archive_timeout'],
      ));
    
    break;
  }
  
  /* edition page */
  case 'view':
  {
    $self_url = BATCH_DOWNLOAD_PUBLIC . 'view&amp;set_id='.$_GET['set_id'];
    
    $template->set_filename('index', dirname(__FILE__).'/../template/view.tpl');
    $template->assign(array(
      'BATCH_DOWNLOAD_PATH' => BATCH_DOWNLOAD_PATH,
      'U_VIEW' => $self_url,
      'U_INIT_ZIP' => BATCH_DOWNLOAD_PUBLIC . 'init_zip&amp;set_id='.$_GET['set_id'],
      ));
    
    $BatchDownloader = new BatchDownloader($_GET['set_id']);
    
    if ($BatchDownloader->getParam('status') != 'new')
    {
      array_push($page['errors'], l10n('You can not edit this set'));
      break;
    }
    
    if ( isset($_GET['remove']) and preg_match('#^[0-9]+$#', $_GET['remove']) )
    {
      $BatchDownloader->removeImages(array($_GET['remove']));
    }
    
    $template->assign('set', $BatchDownloader->getSetInfo());
    
    $template->set_prefilter('index_thumbnails', 'batch_download_thumbnails_list_prefilter');
    
    $page['start'] = isset($_GET['start']) ? $_GET['start'] : 0;
    $page['items'] = array_keys($BatchDownloader->getImages());
    
    if (count($page['items']) > $page['nb_image_page'])
    {
      $page['navigation_bar'] = create_navigation_bar(
        $self_url,
        count($page['items']),
        $page['start'],
        $page['nb_image_page'],
        false
        );
      $template->assign('navbar', $page['navigation_bar']);
    }
    
    include(PHPWG_ROOT_PATH . 'include/category_default.inc.php');
    
    break;
  }
}

$template->assign('BATCH_DOWNLOAD_PATH', BATCH_DOWNLOAD_PATH);


function batch_download_thumbnails_list_prefilter($content, &$smarty)
{
  $search = '<span class="thumbName">';
  
  $add = '<a href="{$U_VIEW}&amp;remove={$thumbnail.id}" rel="nofollow">
<img src="{$BATCH_DOWNLOAD_PATH}template/image_delete.png" title="{\'Remove from download set\'|@translate}">
</a>&nbsp;';

  return str_replace($search, $search.$add, $content);
}

?>
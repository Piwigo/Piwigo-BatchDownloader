<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

# this file contains all functions directly called by the triggers #

/* unserialize conf and load language */
function batch_download_init()
{
  global $conf;
  
  $conf['batch_download'] = unserialize($conf['batch_download']);
  load_language('plugin.lang', BATCH_DOWNLOAD_PATH);
}


/* define page section from url */
function batch_download_section_init()
{
  global $tokens, $page, $conf;
  
  if ($tokens[0] == 'download')
  {
    if (check_download_access() === false) access_denied();
    
    $page['section'] = 'download';
    $page['title'] = l10n('Batch Downloader').$conf['level_separator'].' ';
    
    switch (@$tokens[1])
    {
      case 'init_zip':
        $page['sub_section'] = 'init_zip';
        $page['title'].= l10n('Generate ZIP');
        break;
      case 'view':
        $page['sub_section'] = 'view';
        $page['title'].= l10n('Edit the set');
        break;
      default:
        redirect('index.php');
    }
  }
}

/* download section */
function batch_download_page() 
{
  global $page;

  if (isset($page['section']) and $page['section'] == 'download')
  {
    include(BATCH_DOWNLOAD_PATH . '/include/download.inc.php');
  }
}


/* add buttons on thumbnails list */
function batch_download_index_button()
{
  global $page, $template, $user, $conf;
  
  if ( !count($page['items']) or !isset($page['section']) ) return;
  
  if (check_download_access() === false) return;
  
  // download the set
  if ( isset($_GET['action']) and $_GET['action']=='advdown_set' )
  {
    $set = get_set_info_from_page();
    
    if ($set !== false)
    {
      $BatchDownloader = new BatchDownloader('new', $page['items'], $set['type'], $set['id']);
      $BatchDownloader->getEstimatedArchiveNumber();
      
      // if we plan only one zip with less elements than 'max_elements', the download starts immediately
      if (
        $BatchDownloader->getParam('nb_images') <= $conf['batch_download']['max_elements']
        and $BatchDownloader->getParam('nb_zip') == 1
      )
      {
        $BatchDownloader->createNextArchive(true); // make sure we have only one zip, even if 'max_size' is exceeded
        
        $u_download = BATCH_DOWNLOAD_PATH . 'download.php?set_id='.$BatchDownloader->getParam('set_id').'&amp;zip=1';
        
        $null = null;
        $template->block_footer_script(null, 'setTimeout("document.location.href = \''.$u_download.'\';", 1000);', $null, $null);
        
        array_push($page['infos'], sprintf(l10n('The archive is downloading, if the download doesn\'t start automatically please <a href="%s">click here</a>'), $u_download));
      }
      // oterwise we go to summary page
      else
      {
        redirect(BATCH_DOWNLOAD_PUBLIC . 'init_zip&amp;set_id='.$BatchDownloader->getParam('set_id'));
      }
    }
  }
  
  if ($page['section'] == 'collections')
  {
    $url = $_SERVER['REQUEST_URI'];
  }
  else
  {
    $url = duplicate_index_url(array(), array('action'));
  }
  
  // toolbar button
  $button = '<li><a href="'. $url .'&amp;action=advdown_set" title="'.l10n('Download all pictures of this selection').'" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon" style="background:url(\'' . BATCH_DOWNLOAD_PATH . 'template/zip.png\') center center no-repeat;">&nbsp;</span><span class="pwg-button-text">'.l10n('Batch Downloader').'</span>
		</a></li>';
  $template->concat('PLUGIN_INDEX_ACTIONS', $button);
  $template->concat('COLLECTION_ACTIONS', $button);
}


/* menu block */
function batch_download_add_menublock($menu_ref_arr)
{
  global $user;
  
  $menu = &$menu_ref_arr[0];
  if ($menu->get_id() != 'menubar') return;
  
  if (check_download_access() === false) return;
  
  $query = '
SELECT id
  FROM '.BATCH_DOWNLOAD_TSETS.'
  WHERE
    user_id = '.$user['id'].'
    AND status != "done"
  LIMIT 1
;';
  $result = pwg_query($query);
  if (!pwg_db_num_rows($result)) return;
  
  $menu->register_block(new RegisteredBlock('mbBatchDownloader', l10n('Batch Downloader'), 'BatchDownloader'));
}

function batch_download_applymenu($menu_ref_arr)
{
  global $template, $conf, $user;
  
  $menu = &$menu_ref_arr[0];
  $block = $menu->get_block('mbBatchDownloader');
  
  if ($block != null)
  {
    $query = '
SELECT id 
  FROM '.BATCH_DOWNLOAD_TSETS.'
  WHERE
    user_id = '.$user['id'].'
    AND status != "done"
;';
    $sets = array_from_query($query, 'id');
    
    $data = array();
    foreach ($sets as $set_id)
    {
      $BatchDownloader = new BatchDownloader($set_id);
      $set = $BatchDownloader->getSetInfo();
      
      array_push($data, array(
        'URL' => BATCH_DOWNLOAD_PUBLIC . 'init_zip&amp;set_id='.$BatchDownloader->getParam('set_id'),
        'TITLE' => strip_tags($set['COMMENT']),
        'NAME' => $set['sNAME'],
        'COUNT' => $set['NB_IMAGES'],
        ));
    }
    
    $template->set_template_dir(BATCH_DOWNLOAD_PATH . 'template/');
    $block->set_title(l10n('Downloads'));
    $block->template = 'menublock_batch_down.tpl';
    $block->data = $data;
  }
}


/* archives and databse cleanup */
function batch_download_clean()
{
  global $conf;
  
  // we only search for old downloads every hour, nevermind which user is connected
  if ($conf['batch_download']['last_clean'] > time() - 3600) return;
  
  $conf['batch_download']['last_clean'] = time();
  conf_update_param('batch_download', serialize($conf['batch_download']));
  
  // set old sets as done and clean images table
  $query = '
DELETE i
  FROM '.BATCH_DOWNLOAD_TIMAGES.' AS i
    INNER JOIN '.BATCH_DOWNLOAD_TSETS.' AS s
    ON i.set_id = s.id
  WHERE
    status != "done" AND
    date_creation < DATE_SUB(NOW(), INTERVAL '.$conf['batch_download']['archive_timeout'].' HOUR)
;';
  pwg_query($query);
  
  $query = '
UPDATE '.BATCH_DOWNLOAD_TSETS.'
  SET status = "done"
  WHERE 
    status != "done" AND
    date_creation < DATE_SUB(NOW(), INTERVAL '.$conf['batch_download']['archive_timeout'].' HOUR)
;';
  pwg_query($query);
  
  // remove old archives
  $zips = glob(BATCH_DOWNLOAD_LOCAL . 'u-*/*.zip');
  foreach ($zips as $zip)
  {
    if (filemtime($zip) < time()-$conf['batch_download']['archive_timeout']*3600)
    {
      unlink($zip);
    }
  }
}

?>
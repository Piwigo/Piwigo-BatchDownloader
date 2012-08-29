<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

class BatchDownloader
{
  private $conf;
  private $data;
  private $images;
  
  /**
   * __construct
   * @param: mixed set id (##|'new')
   * @param: array images
   * @param: string set type ('album'|'tag'|'selection')
   * @param: int set type id (for retrieving album infos for instance)
   */
  function __construct($set_id, $images=array(), $type=null, $type_id=null)
  {
    global $user, $conf;
    
    $this->conf = $conf['batch_download'];
    $this->conf['archive_acomment'] = $conf['batch_download_comment'];
    $this->data = array(
      'id' => 0,
      'user_id' => $user['id'],
      'date_creation' => '0000-00-00 00:00:00',
      'type' => null,
      'type_id' => null,
      'nb_zip' => 0,
      'last_zip' => 0,
      'nb_images' => 0,
      'total_size' => 0,
      'status' => 'new',
      );
    $this->images = array();
    
    // load specific set
    if (preg_match('#^[0-9]+$#', $set_id))
    {
      $query = '
SELECT
    id,
    user_id,
    date_creation,
    type,
    type_id,
    nb_zip,
    last_zip,
    nb_images,
    total_size,
    status
  FROM '.BATCH_DOWNLOAD_TSETS.'
  WHERE
    id = '.$set_id.'
    '.(!is_admin() ? 'AND user_id = '.$this->data['user_id'] : null).'
;';
      $result = pwg_query($query);
      
      if (pwg_db_num_rows($result))
      {
        $this->data = array_merge(
          $this->data,
          pwg_db_fetch_assoc($result)
          );
        
        // make sur all pictures of the set exist
        $query = '
DELETE FROM '.BATCH_DOWNLOAD_TIMAGES.'
  WHERE image_id NOT IN (
    SELECT id FROM '.IMAGES_TABLE.'
    )
;';
        pwg_query($query);
      
        $query = '
SELECT
    image_id,
    zip
  FROM '.BATCH_DOWNLOAD_TIMAGES.'
  WHERE set_id = '.$this->data['id'].'
;';
        $this->images = simple_hash_from_query($query, 'image_id', 'zip');
        
        if ( $this->data['status'] != 'done' and count($this->images) != $this->data['nb_images'] )
        {
          $this->updateParam('nb_images', count($this->images));
        }
      }
      else
      {
        throw new Exception(l10n('Invalid dowload set'));
      }
    }
    // create a new set
    else if ($set_id == 'new')
    {
      $this->data['type'] = $type;
      $this->data['type_id'] = $type_id;
      
      $query = '
INSERT INTO '.BATCH_DOWNLOAD_TSETS.'(
    user_id,
    date_creation,
    type,
    type_id,
    nb_zip,
    last_zip,
    nb_images,
    total_size,
    status
  ) 
  VALUES(
    '.$this->data['user_id'].',
    NOW(),
    "'.$this->data['type'].'",
    "'.$this->data['type_id'].'",
    0,
    0,
    0,
    0,
    "new"
  )
;';
      pwg_query($query);
      $this->data['id'] = pwg_db_insert_id();
      
      $date = pwg_query('SELECT FROM_UNIXTIME(NOW());');
      list($this->data['date_creation']) = pwg_db_fetch_row($date);
      
      if (!empty($images))
      {
        $this->addImages($images);
      }
    }
    else
    {
      trigger_error('BatchDownloader::__construct, invalid input parameter', E_USER_ERROR);
    }
  }
  
  /**
   * updateParam
   * @param: string param name
   * @param: mixed param value
   */
  function updateParam($name, $value)
  {
    $this->data[$name] = $value;
    pwg_query('UPDATE '.BATCH_DOWNLOAD_TSETS.' SET '.$name.' = "'.$value.'" WHERE id = '.$this->data['id'].';');
  }
  
  /**
   * getParam
   * @param: string param name
   * @return: mixed param value
   */
  function getParam($name)
  {
    return $this->data[$name];
  }
  
  /**
   * getImages
   * @return: array
   */
  function getImages()
  {
    return $this->images;
  }
  
  /**
   * isInSet
   * @param: int image id
   * @return: bool
   */
  function isInSet($image_id)
  {
    return array_key_exists($image_id, $this->images);
  }
  
  /**
   * removeImages
   * @param: array image ids
   */
  function removeImages($image_ids)
  {
    if (empty($image_ids) or !is_array($image_ids)) return;
    
    foreach ($image_ids as $image_id)
    {
      unset($this->images[ $image_id ]);
    }
    
    $query = '
DELETE FROM '.BATCH_DOWNLOAD_TIMAGES.'
  WHERE 
    set_id = '.$this->data['id'].'
    AND image_id IN('.implode(',', $image_ids).')
;';
    pwg_query($query);
    
    $this->updateParam('nb_images', count($this->images));
  }
  
  /**
   * addImages
   * @param: array image ids
   */
  function addImages($image_ids)
  {
    if (empty($image_ids) or !is_array($image_ids)) return;
    
    $image_ids = array_unique($image_ids);
    $inserts = array();
    
    foreach ($image_ids as $image_id)
    {
      if ($this->isInSet($image_id)) continue;
      
      $this->images[ $image_id ] = 0;
      array_push($inserts, array('set_id'=>$this->data['id'], 'image_id'=>$image_id, 'zip'=>0));
    }
    
    mass_inserts(
      BATCH_DOWNLOAD_TIMAGES,
      array('set_id', 'image_id', 'zip'),
      $inserts
      );
      
    $this->updateParam('nb_images', count($this->images));
  }
  
  /**
   * clearImages
   */
  function clearImages()
  {
    $this->images = array();
    
    $query = '
DELETE FROM '.BATCH_DOWNLOAD_TIMAGES.'
  WHERE set_id = '.$this->data['id'].'
;';
    pwg_query($query);
  }
  
  /**
   * deleteLastArchive
   */
  function deleteLastArchive()
  {
    $zip_path = $this->getArchivePath();
    if (file_exists($zip_path))
    {
      unlink($zip_path);
    }
  }
  
  /**
   * createNextArchive
   * @param: bool force all elements in one archive
   * @return: string zip path or false
   */
  function createNextArchive($force_one_archive=false)
  {
    // set already downloaded (we should never be there !)
    if ( $this->data['status'] == 'done' or $this->data['nb_images'] == 0 )
    {
      trigger_error('BatchDownloader::createNextArchive, the set is empty', E_USER_ERROR);
    }
    
    // first zip
    if ($this->data['last_zip'] == 0)
    {
      $this->updateParam('status', 'download');
      
      // limit number of elements
      if ($this->data['nb_images'] > $this->conf['max_elements'])
      {
        $images_ids = array_slice(array_keys($this->images), 0, $this->conf['max_elements']);
        $this->clearImages();
        $this->addImages($images_ids);
      }
      
      $this->getEstimatedArchiveNumber();
      
      $this->updateParam('date_creation', date('Y-m-d H:i:s'));
    }
    
    // get next images of the set
    $images_to_add = array();
    foreach ($this->images as $image_id => $zip_id)
    {
      if ($zip_id != 0) continue; // here are already added images
      array_push($images_to_add, $image_id);
    }
    
    if (count($images_to_add))
    {
      $query = '
SELECT
    id,
    name,
    file,
    path,
    filesize
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $images_to_add).')
;';
      $images_to_add = hash_from_query($query, 'id');
      
      // open zip
      $this->updateParam('last_zip', $this->data['last_zip']+1);
      $zip_path = $this->getArchivePath();
      
      $zip = new ZipArchive;
      if ($zip->open($zip_path, ZipArchive::CREATE) !== true)
      {
        trigger_error('BatchDownloader::createNextArchive, unable to open ZIP archive', E_USER_ERROR);
      }
      
      // add images until size limit is reach, or all images are added
      $images_added = array();
      $total_size = 0;
      foreach ($images_to_add as $row)
      {        
        $zip->addFile(PHPWG_ROOT_PATH . $row['path'], $row['id'].'_'.get_filename_wo_extension($row['file']).'.'.get_extension($row['path']));
        
        array_push($images_added, $row['id']);
        $this->images[ $row['id'] ] = $this->data['last_zip'];
        
        $total_size+= $row['filesize'];
        if ($total_size >= $this->conf['max_size']*1024 and !$force_one_archive) break;
      }
      
      $this->updateParam('total_size', $this->data['total_size'] + $total_size);
      
      // archive comment
      global $conf;
      $comment = 'Generated on '.date('r').' with PHP ZipArchive '.PHP_VERSION.' by Piwigo Batch Downloader.';
      $comment.= "\n".$conf['gallery_title'].' - '.get_absolute_root_url();
      if (!empty($this->conf['archive_comment']))
      {
        $comment.= "\n\n".wordwrap(remove_accents($this->conf['archive_comment']), 60);
      }
      $zip->setArchiveComment($comment);
      
      $zip->close();
      
      // update database
      $query = '
UPDATE '.BATCH_DOWNLOAD_TIMAGES.'
  SET zip = '.$this->data['last_zip'].'
  WHERE
    set_id = '.$this->data['id'].'
    AND image_id IN('.implode(',', $images_added).')
;';
      pwg_query($query);
      
      // all images added ?
      if (count($images_to_add) == count($images_added))
      {
        $this->updateParam('status', 'done');
      }
      
      // over estimed
      if ( $this->data['status'] == 'done' and $this->data['last_zip'] < $this->data['nb_zip'] )
      {
        $this->updateParam('nb_zip', $this->data['last_zip']);
      }
      // under estimed
      else if ( $this->data['last_zip'] == $this->data['nb_zip'] and $this->data['status'] != 'done' )
      {
        $this->updateParam('nb_zip', $this->data['last_zip']+1);
      }
      
      return $zip_path;
    }
    else
    {
      return false;
    }
  }
  
  /**
   * getEstimatedTotalSize
   * @return: int
   */
  function getEstimatedTotalSize()
  {
    if ($this->data['status'] == 'done') return $this->data['total_size'];
    if ($this->data['nb_images'] == 0) return 0;
    
    $image_ids = array_slice(array_keys($this->images), 0, $this->conf['max_elements']);
    
    $query = '
SELECT SUM(filesize) AS total
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $image_ids).')
;';
    list($total) = pwg_db_fetch_row(pwg_query($query));
    return $total;
  }
  
  /**
   * getEstimatedArchiveNumber
   * @return: int
   */
  function getEstimatedArchiveNumber()
  {
    $nb_zip = ceil( $this->getEstimatedTotalSize() / ($this->conf['max_size']*1024) );
    $this->updateParam('nb_zip', $nb_zip);
    return $nb_zip;
  }
  
  /**
   * getDownloadList
   * @return: string html
   */
  function getDownloadList($url='')
  {
    if ($this->data['nb_images'] == 0)
    {
      return '<b>'.l10n('No archive').'</b>';
    }
    
    $root_url = get_root_url();
    
    $out = '';
    for ($i=1; $i<=$this->data['nb_zip']; $i++)
    {
      $out.= '<li id="zip-'.$i.'">';
      
      if ($this->data['status'] == 'done' or $i < $this->data['last_zip']+1)
      {
        $out.= '<img src="'.$root_url.BATCH_DOWNLOAD_PATH.'template/drive.png"> Archive #'.$i.' (already downloaded)';
      }
      else if ($i == $this->data['last_zip']+1)
      {
          $out.= '<a href="'.add_url_params($url, array('set_id'=>$this->data['id'],'zip'=>$i)).'" rel="nofollow" style="font-weight:bold;"' 
            .($i!=1 ? 'onClick="return confirm(\'Starting download Archive #'.$i.' will destroy Archive #'.($i-1).', be sure you finish the download. Continue ?\');"' : null).
            '><img src="'.$root_url.BATCH_DOWNLOAD_PATH.'template/drive_go.png"> Archive #'.$i.' (ready)</a>';
      }
      else
      {
        $out.= '<img src="'.$root_url.BATCH_DOWNLOAD_PATH.'template/drive.png"> Archive #'.$i.' (pending)';
      }
      
      $out.= '</li>';
    }
    
    return $out;
  }
  
  /**
   * getArchivePath
   * @param: int archive number
   * @return: string
   */
  function getArchivePath($i=null)
  {
    if (!file_exists(BATCH_DOWNLOAD_LOCAL . 'u-' .$this->data['user_id']. '/'))
    {
      mkdir(BATCH_DOWNLOAD_LOCAL . 'u-' .$this->data['user_id']. '/', 0755, true);
    }
    
    if ($i === null) $i = $this->data['last_zip'];
    
    include_once(PHPWG_ROOT_PATH . 'admin/include/functions.php');
    
    return BATCH_DOWNLOAD_LOCAL .'u-'. $this->data['user_id'] .'/'.
          (!empty($this->conf['archive_prefix']) ? $this->conf['archive_prefix'] .'_' : null).
          get_username($this->data['user_id']) .'_'. 
          $this->data['type'] .'-'. $this->data['type_id'] .'_'.
          $this->data['user_id'] . $this->data['id'] .
          ($this->data['nb_zip']!=1 ? '_part'. $i : null).
          '.zip';
  }
  
  /**
   * getSetInfo
   * @return: array
   */
  function getSetInfo()
  {
    $set = array(
      'NB_IMAGES' => $this->data['nb_images'],
      'NB_ARCHIVES' => $this->data['nb_zip'],
      'TOTAL_SIZE' => ceil($this->getEstimatedTotalSize()/1024),
      'LINKS' => $this->getDownloadList(BATCH_DOWNLOAD_PUBLIC . 'init_zip'),
      'DATE_CREATION' => format_date($this->data['date_creation'], true),
      );
    
    switch ($this->data['type'])
    {
      // calendar
      case 'calendar':
      {
        global $conf, $page;
        $old_page = $page;
        
        $fields = array(
          'created' => l10n('Creation date'),
          'posted' => l10n('Post date'),
          );
        
        $chronology = explode('-', $this->data['type_id']);
        $page['chronology_field'] = $chronology[0];
        $page['chronology_style'] = $chronology[1];
        $page['chronology_view'] = $chronology[2];
        $page['chronology_date'] = array_splice($chronology, 3);
        
        if (!class_exists('Calendar'))
        {
          include_once(PHPWG_ROOT_PATH.'include/calendar_'. $page['chronology_style'] .'.class.php');
        }
        $calendar = new Calendar();
        $calendar->initialize('');
        $display_name = strip_tags($calendar->get_display_name());
        
        $set['NAME'] = l10n('Calendar').': '.$fields[$page['chronology_field']].$display_name;
        $set['sNAME'] = l10n('Calendar').': '.ltrim($display_name, $conf['level_separator']);
        
        $page = $old_page;
        break;
      }
      
      // category
      case 'category':
      {
        $category = get_cat_info($this->data['type_id']);
        if ($category == null)
        {
          $set['NAME'] = l10n('Album').': #'.$this->data['type_id'].' (deleted)';
        }
        else
        {
          $set['NAME'] = l10n('Album').': '.get_cat_display_name($category['upper_names']);
          $set['sNAME'] = l10n('Album').': '.trigger_event('render_category_name', $category['name']);
          $set['COMMENT'] = trigger_event('render_category_description', $category['comment']);
        }
        break;
      }
      
      // flat
      case 'flat':
      {
        $set['NAME'] = l10n('Whole gallery');
        break;
      }
      
      // tags
      case 'tags':
      {
        $tags = find_tags(explode(',', $this->data['type_id']));
        $set['NAME'] = l10n('Tags').': ';
        
        $first = true;
        foreach ($tags as $tag)
        {
          if ($first) $first = false;
          else $set['NAME'].= ', ';
          $set['NAME'].=
            '<a href="' . make_index_url(array('tags'=>array($tag))) . '">'
            .trigger_event('render_tag_name', $tag['name'])
            .'</a>';
        }
        break;
      }
      
      // search
      case 'search':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'search', 'search'=>$this->data['type_id'])).'">'.l10n('Search').'</a>';
        break;
      }
      
      // favorites
      case 'favorites':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'favorites')).'">'.l10n('Your favorites').'</a>';
        break;
      }
      
      // most_visited
      case 'most_visited':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'most_visited')).'">'.l10n('Most visited').'</a>';
        break;
      }
      
      // best_rated
      case 'best_rated':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'best_rated')).'">'.l10n('Best rated').'</a>';
        break;
      }
      
      // list
      case 'list':
      {
        $set['NAME'] = l10n('Random');
        break;
      }
      
      // recent_pics
      case 'recent_pics':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'recent_pics')).'">'.l10n('Recent photos').'</a>';
        break;
      }
      
      // collection
      case 'collection':
      {
        try
        {
          if (!class_exists('UserCollection')) throw new Exception();
          $UserCollection = new UserCollection($this->data['type_id']);
          $infos = $UserCollection->getCollectionInfo();
          $set['NAME'] = l10n('Collection').': <a href="'.$infos['U_PUBLIC'].'">'.$UserCollection->getParam('name').'</a>';
        }
        catch (Exception $e)
        {
          $set['NAME'] = l10n('Collection').': #'.$this->data['type_id'].' (deleted)';
        }
        break;
      }
    }
    
    if (!isset($set['sNAME'])) $set['sNAME'] = strip_tags($set['NAME']);
    if (!isset($set['COMMENT'])) $set['COMMENT'] = null;
    
    return $set;
  }
}

?>
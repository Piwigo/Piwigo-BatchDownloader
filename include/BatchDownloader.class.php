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
    $this->data = array(
      'user_id' => $user['id'],
      'set_id' => 0,
      'type' => null,
      'type_id' => null,
      'nb_zip' => 0,
      'last_zip' => 0,
      'nb_images' => 0,
      'status' => 'new',
      );
    $this->images = array();
    
    // load specific set
    if (preg_match('#^[0-9]+$#', $set_id))
    {
      $query = '
SELECT
    type,
    type_id,
    nb_zip,
    last_zip,
    nb_images,
    status
  FROM '.BATCH_DOWNLOAD_TSETS.'
  WHERE
    user_id = '.$this->data['user_id'].'
    AND id = '.$set_id.'
;';
      $result = pwg_query($query);
      
      if (pwg_db_num_rows($result))
      {
        $this->data['set_id'] = $set_id;
        list(
          $this->data['type'], 
          $this->data['type_id'], 
          $this->data['nb_zip'], 
          $this->data['last_zip'], 
          $this->data['nb_images'], 
          $this->data['status']
          ) = pwg_db_fetch_row($result);
        
        // make sur all pictures of the set exists
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
  WHERE set_id = '.$this->data['set_id'].'
;';
        $this->images = simple_hash_from_query($query, 'image_id', 'zip');
        
        if (count($this->images) != $this->data['nb_images'])
        {
          $this->updateParam('nb_images', count($this->images));
        }
      }
      else
      {
        trigger_error('BatchDownloader::__construct, invalid set id', E_USER_ERROR);
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
    "new"
  )
;';
      pwg_query($query);
      $this->data['set_id'] = pwg_db_insert_id();
      
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
    pwg_query('UPDATE '.BATCH_DOWNLOAD_TSETS.' SET '.$name.' = "'.$value.'" WHERE id = '.$this->data['set_id'].';');
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
    set_id = '.$this->data['set_id'].'
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
      array_push($inserts, array('set_id'=>$this->data['set_id'], 'image_id'=>$image_id, 'zip'=>0));
    }
    
    mass_inserts(
      BATCH_DOWNLOAD_TIMAGES,
      array('set_id', 'image_id', 'zip'),
      $inserts
      );
      
    $this->updateParam('nb_images', count($this->images));
  }
  
  /**
   * clear
   */
  function clear($reset=true)
  {
    $this->images = array();
    
    $query = '
DELETE FROM '.BATCH_DOWNLOAD_TIMAGES.'
  WHERE set_id = '.$this->data['set_id'].'
;';
    pwg_query($query);
    
    if ($reset)
    {
      $this->updateParam('nb_zip', 0);
      $this->updateParam('last_zip', 0);
      $this->updateParam('nb_images', 0);
    }
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
   */
  function createNextArchive()
  {
    // set already downloaded
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
        $this->clear(false);
        $this->addImages($images_ids);
      }
      
      $this->getEstimatedArchiveNumber();
      
      pwg_query('UPDATE '.BATCH_DOWNLOAD_TSETS.' SET date_creation = NOW() WHERE id = '.$this->data['set_id'].';');
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
        $zip->addFile(PHPWG_ROOT_PATH . $row['path'], $row['id'].'_'.$row['name'].'.'.get_extension($row['file']));
        
        array_push($images_added, $row['id']);
        $this->images[ $row['id'] ] = $this->data['last_zip'];
        
        $total_size+= $row['filesize'];
        if ($total_size >= $this->conf['max_size']*1024) break;
      }
      
      // archive comment
      global $conf;
      $comment = 'Generated on '.date('r').' with PHP ZipArchive '.PHP_VERSION.' by Piwigo Advanced Downloader.';
      $comment.= "\n".$conf['gallery_title'].' - '.get_absolute_root_url();
      if (!empty($this->conf['archive_comment']))
      {
        $comment.= "\n\n".$this->conf['archive_comment'];
      }
      $zip->setArchiveComment($comment);
      
      $zip->close();
      
      // update database
      $query = '
UPDATE '.BATCH_DOWNLOAD_TIMAGES.'
  SET zip = '.$this->data['last_zip'].'
  WHERE
    set_id = '.$this->data['set_id'].'
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
    $nb_archives = $this->getEstimatedArchiveNumber();
    
    $out = '<ul id="download_list">';
    if ($this->data['status'] == 'done')
    {
      $out.= '<li id="zip-1">Already downloaded</li>';
    }
    else
    {
      for ($i=1; $i<=$this->data['nb_zip']; $i++)
      {
        $out.= '<li id="zip-'.$i.'">';
        
        if ($i < $this->data['last_zip']+1)
        {
          $out.= 'Archive #'.$i.' (already downloaded)';
        }
        else if ($i == $this->data['last_zip']+1)
        {
            $out.= '<a href="'.add_url_params($url, array('set_id'=>$this->data['set_id'],'zip'=>$i)).'" rel="nofollow"' 
              .($i!=1 ? 'onClick="return confirm(\'Starting download Archive #'.$i.' will destroy Archive #'.($i-1).', be sure you finish the download. Continue ?\');"' : null).
              '>Archive #'.$i.' (ready)</a>';
        }
        else
        {
          $out.= 'Archive #'.$i.' (pending)';
        }
        
        $out.= '</li>';
      }
    }
    $out.= '</ul>';
    
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
          $this->data['user_id'] . $this->data['set_id'] .'_'.
          'part'. $i .'.zip';
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
      );
        
    if ($this->data['status'] == 'new')
    {
      $set['U_EDIT_SET'] = BATCH_DOWNLOAD_PUBLIC . 'view&amp;set_id='.$this->data['set_id'];
    }
    
    switch ($this->data['type'])
    {
      case 'calendar':
      {
        $set['NAME'] = l10n('Calendar');
        $set['COMMENT'] = $this->data['type_id'];
        break;
      }
      
      case 'category':
      {
        $category = get_cat_info($this->data['type_id']);
        $set['NAME'] = get_cat_display_name($category['upper_names']);
        $set['sNAME'] = $category['name'];
        $set['COMMENT'] = trigger_action('render_category_description', $category['comment']);
        break;
      }
      
      case 'flat':
      {
        $set['NAME'] = l10n('Whole gallery');
        break;
      }
      
      case 'tags':
      {
        $tags = find_tags(explode(',', $this->data['type_id']));
        $set['NAME'] = l10n('Tags');
        
        $set['COMMENT'] = ''; $first = true;
        foreach ($tags as $tag)
        {
          if ($first) $first = false;
          else $set['COMMENT'].= ', ';
          $set['COMMENT'].=
            '<a href="' . make_index_url(array('tags'=>array($tag))) . '">'
            .trigger_event('render_tag_name', $tag['name'])
            .'</a>';
        }
        break;
      }
      
      case 'search':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'search', 'search'=>$this->data['type_id'])).'">'.l10n('Search').'</a>';
        break;
      }
      
      case 'favorites':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'favorites')).'">'.l10n('Your favorites').'</a>';
        break;
      }
      
      case 'most_visited':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'most_visited')).'">'.l10n('Most visited').'</a>';
        break;
      }
      
      case 'best_rated':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'best_rated')).'">'.l10n('Best rated').'</a>';
        break;
      }
      
      case 'list':
      {
        $set['NAME'] = l10n('Random');
        break;
      }
      
      case 'recent_pics':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'recent_pics')).'">'.l10n('Recent photos').'</a>';
        break;
      }
      
      // case 'selection':
      // {
        // $set['NAME'] = '';
        // $set['COMMENT'] = '';
        // break;
      // }
    }
    
    if (!isset($set['sNAME'])) $set['sNAME'] = $set['NAME'];
    if (!isset($set['COMMENT'])) $set['COMMENT'] = null;
    
    return $set;
  }
}

?>
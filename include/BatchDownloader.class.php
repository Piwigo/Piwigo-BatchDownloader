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
   * @param: string size to download
   */
  function __construct($set_id, $images=array(), $type=null, $type_id=null, $size='original')
  {
    global $user, $conf;
    
    $this->conf = $conf['batch_download'];
    $this->data = array(
      'id' => 0,
      'user_id' => $user['id'],
      'date_creation' => '0000-00-00 00:00:00',
      'type' => null,
      'type_id' => null,
      'size' => 'original',
      'nb_zip' => 0,
      'last_zip' => 0,
      'nb_images' => 0,
      'total_size' => 0,
      'estimated_total_size' => 0,
      'status' => 'new',
      );
    $this->images = array();
    
    // load specific set
    if (preg_match('#^[0-9]+$#', $set_id))
    {
      $query = '
SELECT *
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
      if ($size != 'original')
      {
        $type_map = array_keys(ImageStdParams::get_defined_type_map());
        if (!in_array($size, $type_map))
        {
          throw new Exception(sprintf(l10n('Invalid size %s'), $size));
        }
      }
  
      $this->data['type'] = $type;
      $this->data['type_id'] = $type_id;
      $this->data['size'] = $size;
      
      $query = '
INSERT INTO '.BATCH_DOWNLOAD_TSETS.'(
    user_id,
    date_creation,
    type,
    type_id,
    size,
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
    "'.$this->data['size'].'",
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
    global $conf;
    
    if (empty($image_ids) or !is_array($image_ids)) return;
    
    $query = '
SELECT id, file
  FROM '.IMAGES_TABLE.'
  WHERE id IN('.implode(',', array_unique($image_ids)).')
;';
    $images = simple_hash_from_query($query, 'id', 'file');
    
    $inserts = array();
    
    foreach ($images as $image_id => $file)
    {
      if ($this->isInSet($image_id)) continue;
      if (!in_array(get_extension($file), $conf['batch_download']['allowed_ext'])) continue;
      
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
   * get missing derivatives files
   * @return: array of i.php urls
   */
  function getMissingDerivatives($update=false)
  {
    if ($this->data['size'] == 'original')
    {
      return array();
    }
    
    global $conf;
    
    $root_url = get_root_url();
    $uid = '&b='.time();
    
    $params = ImageStdParams::get_by_type($this->data['size']);
    $last_mod_time = $params->last_mod_time;
    
    $image_ids = array_keys($this->images);
    $to_update = $urls = $inserts = array();
    
    $conf['question_mark_in_urls'] = $conf['php_extension_in_urls'] = true;
    $conf['derivative_url_style'] = 2; //script
    
    // images which we need to update stats
    if ($update)
    {
      $query = '
SELECT image_id, filemtime FROM '.IMAGE_SIZES_TABLE.'
  WHERE image_id IN('.implode(',', $image_ids).')
    AND type = "'.$this->data['size'].'"
;';
      $registered = array_from_query($query, 'image_id', 'filemtime');
      
      $to_update = array_filter($registered, create_function('$t', 'return $t<'.$last_mod_time.';'));
      $to_update = array_merge($to_update, array_diff($image_ids, $registered));
    }
    
    $query = '
SELECT id, path, width, height, rotation
  FROM '.IMAGES_TABLE.'
  WHERE id IN('.implode(',', $image_ids).')
  ORDER BY id DESC
;';

    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      $src_image = new SrcImage($row); // don't give representive_ext
      
      // no-image files
      if ($src_image->is_mimetype())
      {
        if ($update && in_array($row['id'], $to_update))
        {          
          $inserts[ $row['id'] ] = array(
            'image_id' => $row['id'],
            'type' => $this->data['size'],
            'width' => 0,
            'height' => 0,
            'filesize' => filesize(PHPWG_ROOT_PATH.$row['path'])/1024,
            'filemtime' => filemtime(PHPWG_ROOT_PATH.$row['path']),
            );
        }
      }
      // images files
      else
      {
        $derivative = new DerivativeImage($this->data['size'], $src_image);
        // if ($this->data['size'] != $derivative->get_type()) continue;
        
        $filemtime = @filemtime($derivative->get_path());
        $src_mtime = @filemtime(PHPWG_ROOT_PATH.$row['path']);
        if ($src_mtime===false) continue;
        
        if ($filemtime===false || $filemtime<$last_mod_time || $filemtime<$src_mtime)
        {
          $urls[] = $root_url.$derivative->get_url().$uid;
        }
        else if ($update && in_array($row['id'], $to_update))
        {
          $imagesize = getimagesize($derivative->get_path());
          
          $inserts[ $row['id'] ] = array(
            'image_id' => $row['id'],
            'type' => $this->data['size'],
            'width' => $imagesize[0],
            'height' => $imagesize[1],
            'filesize' => filesize($derivative->get_path())/1024,
            'filemtime' => $filemtime,
            );
        }
      }
    }
    
    if (!empty($inserts))
    {
      $query = '
DELETE FROM '.IMAGE_SIZES_TABLE.'
  WHERE image_id IN('.implode(',', array_keys($inserts)).')
;';
      pwg_query($query);
      
      mass_inserts(
        IMAGE_SIZES_TABLE,
        array('image_id','type','width','height','filesize','filemtime'),
        $inserts
        );
    }
    
    return $urls;
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
    
    global $conf;
    
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
    id, name, file, path,
    rotation, filesize, width, height
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $images_to_add).')
;';
      $images_to_add = hash_from_query($query, 'id');
      
      if ($this->data['size'] != 'original')
      {
        $query = '
SELECT image_id, filesize
  FROM '.IMAGE_SIZES_TABLE.'
  WHERE image_id IN ('.implode(',', array_keys($images_to_add)).')
    AND type = "'.$this->data['size'].'"
;';
        $filesizes = simple_hash_from_query($query, 'image_id', 'filesize');
      }
      
      // open zip
      $this->updateParam('last_zip', $this->data['last_zip']+1);
      $zip_path = $this->getArchivePath();
      $zip = new myZip($zip_path, isset($conf['batch_download_force_pclzip']));
      
      // add images until size limit is reach, or all images are added
      $images_added = array();
      $total_size = 0;
      foreach ($images_to_add as $row)
      {
        if (!file_exists(PHPWG_ROOT_PATH.$row['path']))
        {
          $this->removeImages(array($row['id']));
          continue;
        }
        
        if ($this->data['size'] == 'original')
        {
          $zip->addFile(PHPWG_ROOT_PATH.$row['path'], $row['id'].'_'.stripslashes(get_filename_wo_extension($row['file'])).'.'.get_extension($row['path']));
          $total_size+= $row['filesize'];
        }
        else
        {
          $src_image = new SrcImage($row); // don't give representive_ext
          
          // no-image files
          if ($src_image->is_mimetype())
          {
            $zip->addFile(PHPWG_ROOT_PATH.$row['path'], $row['id'].'_'.stripslashes(get_filename_wo_extension($row['file'])).'.'.get_extension($row['path']));
            $total_size+= $row['filesize'];
          }
          // images files
          else
          {
            $derivative = new DerivativeImage($this->data['size'], $src_image);
            $path = $derivative->get_path();
        
            $zip->addFile($path, $row['id'].'_'.stripslashes(get_filename_wo_extension(basename($path))).'.'.get_extension($path));
            $total_size+= $filesizes[ $row['id'] ];
          }
        }
        
        array_push($images_added, $row['id']);
        $this->images[ $row['id'] ] = $this->data['last_zip'];
        
        if ($total_size >= $this->conf['max_size']*1024 and !$force_one_archive) break;
      }
      
      $this->updateParam('total_size', $this->data['total_size'] + $total_size);
      
      // archive comment
      $comment = 'Generated on '.date('r').' with PHP '.PHP_VERSION.' by Piwigo Batch Downloader.';
      $comment.= "\n".$conf['gallery_title'].' - '.get_absolute_root_url();
      if (!empty($conf['batch_download_comment']))
      {
        $comment.= "\n\n".wordwrap(remove_accents($conf['batch_download_comment']), 60);
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
      if ($this->data['status'] == 'done')
      {
        $this->updateParam('nb_zip', $this->data['last_zip']);
      }
      // under estimed
      else if ($this->data['last_zip'] == $this->data['nb_zip'])
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
  function getEstimatedTotalSize($force=false)
  {
    if ($this->data['status'] == 'done') return $this->data['total_size'];
    if ($this->data['nb_images'] == 0) return 0;
    if ( !empty($this->data['estimated_total_size']) and !$force ) return $this->data['estimated_total_size'];
    
    $image_ids = array_slice(array_keys($this->images), 0, $this->conf['max_elements']);
    
    if ($this->data['size'] == 'original')
    {
      $query = '
SELECT SUM(filesize) AS total
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $image_ids).')
;';
    }
    else
    {
      $query = '
SELECT SUM(filesize) AS total
  FROM '.IMAGE_SIZES_TABLE.'
  WHERE image_id IN ('.implode(',', $image_ids).')
;';
    }
    
    list($total) = pwg_db_fetch_row(pwg_query($query));
    $this->data['estimated_total_size'] = $total;
    return $total;
  }
  
  /**
   * getEstimatedArchiveNumber
   * @return: int
   */
  function getEstimatedArchiveNumber()
  {
    if ($this->data['status'] == 'done') return $this->data['nb_zip'];
    
    return ceil( $this->getEstimatedTotalSize() / ($this->conf['max_size']*1024) );
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
    for ($i=1; $i<=$this->getEstimatedArchiveNumber(); $i++)
    {
      $out.= '<li id="zip-'.$i.'">';
      
      if ($this->data['status'] == 'done' or $i < $this->data['last_zip']+1)
      {
        $out.= '<img src="'.$root_url.BATCH_DOWNLOAD_PATH.'template/images/drive_error.png"> Archive #'.$i.' (already downloaded)';
      }
      else if ($i == $this->data['last_zip']+1)
      {
          $out.= '<a href="'.add_url_params($url, array('set_id'=>$this->data['id'],'zip'=>$i)).'" rel="nofollow" style="font-weight:bold;"' 
            .($i!=1 ? ' onClick="return confirm(\''.addslashes(sprintf(l10n('Starting download Archive #%d will destroy Archive #%d, be sure you finish the download. Continue ?'), $i, $i-1)).'\');"' : null).
            '><img src="'.$root_url.BATCH_DOWNLOAD_PATH.'template/images/drive_go.png"> Archive #'.$i.' (ready)</a>';
      }
      else
      {
        $out.= '<img src="'.$root_url.BATCH_DOWNLOAD_PATH.'template/images/drive.png"> Archive #'.$i.' (pending)';
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
      mkgetdir(BATCH_DOWNLOAD_LOCAL . 'u-' .$this->data['user_id']. '/');
    }
    
    if ($i === null) $i = $this->data['last_zip'];
    $set = $this->getNames();
    
    include_once(PHPWG_ROOT_PATH . 'admin/include/functions.php');
    
    $path = BATCH_DOWNLOAD_LOCAL . 'u-'. $this->data['user_id'] . '/';
    $path.= !empty($this->conf['archive_prefix']) ? $this->conf['archive_prefix'] . '_' : null;
    $path.= get_username($this->data['user_id']) . '_';
    $path.= $set['BASENAME'] . '_';
    $path.= $this->data['user_id'] . $this->data['id'];
    $path.= '_part' . $i;
    $path.= '.zip';
    
    return $path;
  }
  
  /**
   * getNames
   * @return: array
   */
  function getNames()
  {    
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
        $set['BASENAME'] = 'calendar-'.$page['chronology_field'].'-'.implode('-',$page['chronology_date']);
        
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
          $set['BASENAME'] = 'album'.$this->data['type_id'];
        }
        else
        {
          $set['NAME'] = l10n('Album').': '.get_cat_display_name($category['upper_names']);
          $set['sNAME'] = l10n('Album').': '.trigger_event('render_category_name', $category['name']);
          $set['COMMENT'] = trigger_event('render_category_description', $category['comment']);
          
          if (!empty($category['permalink']))
          {
            $set['BASENAME'] = 'album-'.$category['permalink'];
          }
          else if ( ($name = str2url($category['name'])) != null )
          {
            $set['BASENAME'] = 'album-'.$name;
          }
          else
          {
            $set['BASENAME'] = 'album'.$this->data['type_id'];
          }
        }
        break;
      }
      
      // flat
      case 'flat':
      {
        $set['NAME'] = l10n('Whole gallery');
        $set['BASENAME'] = 'all-gallery';
        break;
      }
      
      // tags
      case 'tags':
      {
        $tags = find_tags(explode(',', $this->data['type_id']));
        $set['NAME'] = l10n('Tags').': ';
        $set['BASENAME'] = 'tags';
        
        $first = true;
        foreach ($tags as $tag)
        {
          if ($first) $first = false;
          else $set['NAME'].= ', ';
          $set['NAME'].=
            '<a href="' . make_index_url(array('tags'=>array($tag))) . '">'
            .trigger_event('render_tag_name', $tag['name'])
            .'</a>';
          $set['BASENAME'].= '-'.$tag['url_name'];
        }
        break;
      }
      
      // search
      case 'search':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'search', 'search'=>$this->data['type_id'])).'">'.l10n('Search').'</a>';
        $set['BASENAME'] = 'search'.$this->data['type_id'];
        break;
      }
      
      // favorites
      case 'favorites':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'favorites')).'">'.l10n('Your favorites').'</a>';
        $set['BASENAME'] = 'favorites';
        break;
      }
      
      // most_visited
      case 'most_visited':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'most_visited')).'">'.l10n('Most visited').'</a>';
        $set['BASENAME'] = 'most-visited';
        break;
      }
      
      // best_rated
      case 'best_rated':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'best_rated')).'">'.l10n('Best rated').'</a>';
        $set['BASENAME'] = 'best-rated';
        break;
      }
      
      // list
      case 'list':
      {
        $set['NAME'] = l10n('Random');
        $set['BASENAME'] = 'random';
        break;
      }
      
      // recent_pics
      case 'recent_pics':
      {
        $set['NAME'] = '<a href="'.make_index_url(array('section'=>'recent_pics')).'">'.l10n('Recent photos').'</a>';
        $set['BASENAME'] = 'recent-pics';
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
          
          if ( ($name = str2url($UserCollection->getParam('name'))) != null)
          {
            $set['BASENAME'] = 'collection-'.$name;
          }
          else
          {
            $set['BASENAME'] = 'collection'.$this->data['type_id'];
          }
        }
        catch (Exception $e)
        {
          $set['NAME'] = l10n('Collection').': #'.$this->data['type_id'].' (deleted)';
          $set['BASENAME'] = 'collection'.$this->data['type_id'];
        }
        break;
      }
    }
    
    if (!isset($set['sNAME']))    $set['sNAME'] = strip_tags($set['NAME']);
    if (!isset($set['COMMENT']))  $set['COMMENT'] = null;
    if (!isset($set['BASENAME'])) $set['BASENAME'] = $this->data['type'] . $this->data['type_id'];
    
    return $set;
  }
  
  /**
   * getSetInfo
   * @return: array
   */
  function getSetInfo()
  {    
    $set = array(
      'NB_IMAGES' =>     $this->data['nb_images'],
      'NB_ARCHIVES' =>   $this->data['status']=='new' ? l10n('Unknown') : $this->getEstimatedArchiveNumber(),
      'STATUS' =>        $this->data['status'],
      'LAST_ZIP' =>      $this->data['last_zip'],
      'TOTAL_SIZE' =>    $this->data['status']=='new' ? l10n('Unknown') : sprintf(l10n('%d MB'), ceil($this->getEstimatedTotalSize()/1024)),
      'DATE_CREATION' => format_date($this->data['date_creation'], true),
      'SIZE_ID' =>       $this->data['size'],
      'SIZE' =>          $this->data['size']=='original' ? l10n('Original') : l10n($this->data['size']),
      );
      
    if ($this->data['size'] != 'original')
    {
      $params = ImageStdParams::get_by_type($this->data['size']);
      $set['SIZE_INFO'] = $params->sizing->ideal_size[0].' x '.$params->sizing->ideal_size[1];
    }
    
    return array_merge($set, $this->getNames());
  }
  
  /**
   * delete
   */
  function delete()
  {
    $this->deleteLastArchive();
    $this->clearImages();
    pwg_query('DELETE FROM '.BATCH_DOWNLOAD_TSETS.' WHERE id = '.$this->data['id'].';');
  }
}


/**
 * small class implementing basic ZIP creation
 * with ZipArchive or PclZip
 */
class myZip
{
  private $lib;
  private $zip;
  
  function __construct($zip_path, $pclzip=false)
  {
    if ( class_exists('ZipArchive') and !$pclzip )
    {
      $this->lib = 'zipa';
      
      $this->zip = new ZipArchive;
      if ($this->zip->open($zip_path, ZipArchive::CREATE) !== true)
      {
        trigger_error('BatchDownloader::createNextArchive, unable to open ZIP archive (ZipArchive)', E_USER_ERROR);
      }
    }
    else
    {
      $this->lib = 'pcl';
      
      require_once(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');
      $this->zip = new PclZip($zip_path);
      
      // create a temporary file for archive creation
      touch(BATCH_DOWNLOAD_LOCAL.'temp.txt');
      
      if ($this->zip->create(BATCH_DOWNLOAD_LOCAL.'temp.txt', PCLZIP_OPT_REMOVE_ALL_PATH) == 0)
      {
        trigger_error('BatchDownloader::createNextArchive, unable to open ZIP archive (PclZip)', E_USER_ERROR);
      }
      
      unlink(BATCH_DOWNLOAD_LOCAL.'temp.txt');
      $this->zip->delete(PCLZIP_OPT_BY_NAME, 'temp.txt');
    }
  }
  
  function addFile($path, $filename)
  {
    if ($this->lib == 'zipa')
    {
      $this->zip->addFile($path, $filename);
    }
    else
    {
      $this->zip->add($path, PCLZIP_OPT_REMOVE_ALL_PATH);
    }
  }
  
  function setArchiveComment($comment)
  {
    if ($this->lib == 'zipa')
    {
      $this->zip->setArchiveComment($comment);
    }
  }
  
  function close()
  {
    if ($this->lib == 'zipa')
    {
      $this->zip->close();
    }
  }
}

?>
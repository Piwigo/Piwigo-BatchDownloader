<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

/**
 * get BatchDownloader type and type_id from page info
 * @return: array or false
 */
function get_set_info_from_page()
{
  global $page;
  
  switch ($page['section'])
  {
    case 'categories':
      if (isset($page['chronology_field']))
      {
        $batch_type = 'calendar';
        $batch_id = add_well_known_params_in_url('', 
          array_intersect_key($page, 
            array(
              'chronology_field'=>0,
              'chronology_style'=>0,
              'chronology_view'=>0,
              'chronology_date'=>0,
          )));
        $batch_id = ltrim($batch_id, '/');
      }
      else if (isset($page['category']))
      {
        $batch_type = 'category';
        $batch_id = $page['category']['id'];
      }
      else if (isset($page['flat'])) // this is for the whole gallery only, flat mode for category is above
      {
        $batch_type = 'flat';
        $batch_id = 0;
      }
      break;
    case 'tags':
      $batch_type = 'tags';
      $batch_id = implode(',', array_map(create_function('$t', 'return $t["id"];'), $page['tags']));
      break;
    case 'search':
      $batch_type = 'search';
      $batch_id = $page['search'];
      break;
    case 'collections':
      if (in_array(@$page['sub_section'], array('view','edit')))
      {
        $batch_type = 'collection';
        $batch_id = $page['col_id'];
      }
      break;
    case 'favorites':
    case 'most_visited':
    case 'best_rated':
    case 'list':
    case 'recent_pics':
      $batch_type = $page['section'];
      $batch_id = 0;
      break;
    default:
      return false;
  }
  
  return array(
    'type' => $batch_type,
    'id' => $batch_id,
    'size' => isset($_GET['down_size']) ? $_GET['down_size'] : 'original',
    );
}

/**
 * check is current user can use BatchDownloader
 * @return: boolean
 */
function check_download_access()
{
  global $user, $conf;
  
  if (is_a_guest()) return false;
  if (is_admin()) return true;
  
  if ($user['level'] < $conf['batch_download']['level']) return false;
  
  if (!empty($conf['batch_download']['groups']))
  {
    $query = '
SELECT 1 FROM '.USER_GROUP_TABLE.'
  WHERE
    user_id = '.$user['id'].'
    AND group_id IN('.implode(',', $conf['batch_download']['groups']).')
;';
    $result = pwg_query($query);
    
    if (!pwg_db_num_rows($result)) return false;
  }
  
  return true;
}

// https://bugs.php.net/bug.php?id=61636
function readlargefile($fullfile)
{
  $fp = fopen($fullfile, 'rb');

  if ($fp)
  {
    while (!feof($fp))
    {
      print(fread($fp, 2097152));
    }

    fclose($fp);
  }
}

?>
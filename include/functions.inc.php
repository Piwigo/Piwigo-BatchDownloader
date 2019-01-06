<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

/**
 * get BatchDownloader type and type_id from page info
 * @return: array or false
 */
function get_set_info_from_page()
{
  global $page, $conf;

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
        return false;
      }
      break;
    case 'tags':
      $batch_type = 'tags';
      $batch_id = implode(',', array_map(function ($t) {return $t["id"];}, $page['tags']));
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
      $batch_id = null;
      break;
    default:
      return false;
  }

  $set = array(
    'type' => $batch_type,
    'id' => $batch_id,
    'size' => !empty($_GET['down_size']) ? $_GET['down_size'] : 'original',
    'items' => $page['items'],
    );

  // check size
  if (!$conf['batch_download']['multisize'])
  {
    $set['size'] = $conf['batch_download']['photo_size'];
  }
  else
  {
    $avail_sizes = array();
    foreach (ImageStdParams::get_defined_type_map() as $params)
    {
      $avail_sizes[] = $params->type;
      if ($params->type == $conf['batch_download']['photo_size']) break;
    }
    if ($conf['batch_download']['photo_size'] == 'original')
    {
      $avail_sizes[] = 'original';
    }

    if (!in_array($set['size'], $avail_sizes))
    {
      $set['size'] = $conf['batch_download']['photo_size'];
    }
  }

  return trigger_change('batchdownload_get_set_info', $set);
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

function check_album_download_access($catid)
{
  $query = 'SELECT 1 FROM '.CATEGORIES_TABLE.' WHERE id = '.$catid.' AND downloadable = \'true\';';
  return pwg_db_num_rows(pwg_query($query)) > 0;
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

if (!function_exists('str2lower'))
{
  if (function_exists('mb_strtolower') && defined('PWG_CHARSET'))
  {
    function str2lower($term)
    {
      return mb_strtolower($term, PWG_CHARSET);
    }
    function str2upper($term)
    {
      return mb_strtoupper($term, PWG_CHARSET);
    }
  }
  else
  {
    function str2lower($term)
    {
      return strtolower($term);
    }
    function str2upper($term)
    {
      return strtoupper($term);
    }
  }
}

<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_url.inc.php');

function batch_download_ws_add_methods($arr)
{
  $service = &$arr[0];
  global $conf;

  $service->addMethod(
    'batch_download.downloadRequest.create',
    'ws_downloadRequest_create',
    array(
      'type' => array(),
      'type_id' => array(),
      'user_id' => array('type'=>WS_TYPE_ID),
      'first_name' => array(),
      'last_name' => array(),
      'organisation' => array(),
      'email' => array(),
      'telephone' => array(),
      'profession' => array(),
      'reason' => array(),
      'request_date' => array(),
      'image_size' => array('default'=>'original',
                            'info'=>'square, thumbnail, xxs, xs, s, m, l, xl, xxl, original'),
    ),
    'Create a new Download request.'
  );

  $service->addMethod(
    'batch_download.downloadRequest.update',
    'ws_downloadRequest_update',
    array(
      'id' => array('type'=>WS_TYPE_ID),
      'status_change_date' => array(),
      'request_status' => array('default'=>'pending',
                            'info'=>'pending,reject,accept'),
      'updated_by' => array('type'=>WS_TYPE_ID),
    ),
    'Create a new Download request.'
  );

  $service->addMethod(
    'batch_download.downloadRequest.getList',
    'ws_downloadRequest_getList',
    array(),
    'Get a list of all requests'
  );

  $service->addMethod(
    'batch_download.downloadRequest.getInfo',
    'ws_downloadRequest_getInfo',
    array(
      'id' => array('type'=>WS_TYPE_ID)
    ),
    'Get one request'
  );
}

/**
 * Create a new request
 */
function ws_downloadRequest_create($params, &$service)
{
  global $user;

  // check status
  if (is_a_guest())
  {
    return new PwgError(403, 'Forbidden');
  }

  //check first name
  if (empty($params['first_name']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty first name');
  }
  
  //check last name
  if (empty($params['last_name']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty last name');
  }

  //check last name
  if (empty($params['organisation']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty organisation');
  }

  //check email
  if (empty($params['email']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty email');
  }
  
  //check telephone
  if (empty($params['telephone']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty telephone');
  }

  //check profession
  if (empty($params['profession']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty profession');
  }
   
  //check reason
  if (empty($params['reason']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty reason ');
  }

  // Check if email address is valid
  if (!email_check_format($params['email']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Email isn\'t the right format');
  }

  //check image_size
  if (empty($params['image_size']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty image size');
  }

  single_insert(
    BATCH_DOWNLOAD_TREQUESTS,
    $params
  );

  $request_info_email = l10n("There is a new request to download a batch of photos.");
  $request_info_email .= "\n";
  $request_info_email .= l10n('Here are the details of the request:');
  $request_info_email .= "\n";

  foreach ($params as $detail => $info){
    switch($detail){
      case "type":
        $request_info_email .= l10n("Set")." = ".$info;
        break;
      case "type_id":
        $request_info_email .= " ".$info ."\n";
        break;
      case "user_id":
        break;
      case "first_name":
      $request_info_email .= l10n("First name")." = ".$info."\n";
        break;
      case "last_name":
      $request_info_email .= l10n("Last name")." = ".$info."\n";
        break;
      case "Email":
      $request_info_email .= l10n("Email")." = ".$info."\n";
        break;
      case "Telephone":
      $request_info_email .= l10n("Telephone Number")." = ".$info."\n";
        break;
      case "Organisation":
        $request_info_email .= l10n("Organisation")." = ".$info."\n";
        break;
      case "Profession":
        $request_info_email .= l10n("Profession")." = ".$info."\n";
        break;
      case "Reason":
        $request_info_email .= l10n("Reason")." = ".$info."\n";
        break;
      case "image_size":
        break;
      case "nb_images":
        $request_info_email .= l10n("Number of photos")." = ".l10n($info)."\n";
        break;
    }
  }

  $request_info_email .= "\n";
  $request_info_email .= l10n("See the request here");
  $request_info_email .= "\n";
  $url_admin =get_absolute_root_url().BATCH_DOWNLOAD_ADMIN.'-requests';
  $request_info_email .= $url_admin;

  $subject =l10n('Batch downloader, new download request ');
  // $subject .= '#'.$request_id ;

  pwg_mail_admins(
    array(
      'subject' => $subject,
      'content' => $request_info_email,
      'content_format' => 'text/plain',
    ),
    array(
      'filename' => 'notification_admin',
    ),
    false, // do not exclude current user
    false // only webmasters
  );

}

/**
 * Update status of a request
 */
 function ws_downloadRequest_update($params, &$service){
  global $conf, $conf_mail; 

  include_once(PHPWG_ROOT_PATH.'include/functions_user.inc.php');

  single_update(
    BATCH_DOWNLOAD_TREQUESTS,
    $params,
    array(
      'id' => $params['id'],
    )
  );

  $query = '
SELECT 
  id,
  type,
  type_id,
  user_id,
  email,
  nb_images,
  request_status,
  image_size
  FROM '.BATCH_DOWNLOAD_TREQUESTS.'
  WHERE id ='.$params['id'].'
;';

  $request = query2array($query);
  $request = $request[0];
 
  //Notify user once request staus changed
  $set_info = l10n($request['type']).' '.$request['type_id'];
  
  $subject = l10n("Your download request has been accepted");

  if ("accept" == $request['request_status'])
  {
    switch($request['image_size']){
        case 'Original':
          $request['image_size'] = 'original';
          break;
        case 'Square (120 x 120)':
          $request['image_size'] = 'square';
          break;
        case 'Thumbnail (144 x 144)':
          $request['image_size'] = 'thumbnail';
          break;
        case'XXS - tiny (240 x 240)':
          $request['image_size'] = 'xxs';
          break;
        case 'XS - extra small (432 x 324)':
          $request['image_size'] = 'xs';
          break;
        case 'S - small (576 x 432)':
          $request['image_size'] = 's';
          break;
        case 'M - medium (792 x 594)':
          $request['image_size'] = 'm';
          break;
        case 'L - large (1008 x 756)':
          $request['image_size'] = 'l';
          break;
        case 'XL - extra large (1224 x 918)':
          $request['image_size'] = 'xl';
          break;
        case'XXL - huge (1656 x 1242)':
          $request['image_size'] = 'xxl';
          break;
    }

    $query = '
    SELECT
        ui.user_id,
        ui.status,
        ui.language,
        u.'.$conf['user_fields']['email'].' AS email,
        u.'.$conf['user_fields']['username'].' AS username
      FROM '.USER_INFOS_TABLE.' AS ui
        JOIN '.USERS_TABLE.' AS u ON u.'.$conf['user_fields']['id'].' = ui.user_id
      WHERE ui.user_id = '.$request['user_id'].'
    ;';

    $requesting_user = query2array($query);
    $requesting_user = $requesting_user[0];

    $url = '';

    switch ($request['type'])
    {
      // calendar
      case 'calendar':
      {
        break;
      }
      // category
      case 'category':
      {
        $query = '
SELECT
    id,
    name,
    permalink
  FROM '.CATEGORIES_TABLE.' 
    WHERE id = '.$request['type_id'].'
;';
      
        $requested_category = pwg_db_fetch_assoc(pwg_query($query));
      
        $url .= make_index_url(
          array(
            'category' => array(
              'id' => $request['type_id'],
              'name' => $requested_category['name'],
              'permalink' => $requested_category['permalink'],
            )
          )
        );
      }
      // flat
      case 'flat':
      {
        break;
      }
      // tags
      case 'tags':
      {
        $query = '
SELECT
    id,
    url_name
  FROM '.TAGS_TABLE.' 
  WHERE id = '.$request['type_id'].'
;';
  
        $requested_tag = pwg_db_fetch_assoc(pwg_query($query));
  
        $url .= make_index_url(
          array(
            'tags' => array(
              array(
                'id' => $request['type_id'],
                'url_name' => $requested_tag['url_name'],
              )
            )
          )
        );
      }
      // search
      case 'search':
      {
        break;
      }
      // favorites
      case 'favorites':
      {
        break;
      }
      // most_visited
      case 'most_visited':
      {
        break;
      }
      // best_rated
      case 'best_rated':
      {
        break;
      }
      // list
      case 'list':
      {
        break;
      }
      // recent_pics
      case 'recent_pics':
      {
        break;
      }
      // collection
      case 'collection':
      {
        if (defined('USER_COLLEC_PUBLIC'))
        {
          $url = USER_COLLEC_PUBLIC . 'edit/'.$request['type_id'];
        }
        break;
      }
      default:
      {
        $url = get_root_url().$request['type'];
      }
    }

    //Define url parameters for download link
    $url_parameters = array(
      'action'=>'advdown_set',
      'down_size'=>$request['image_size'],
      'request_id'=>$request['id'],
    );
    
    //Add auth key for automatic connection execpt for admins
    $authkey = create_user_auth_key($requesting_user['user_id'], $requesting_user['status']);
    isset($authkey)? $url_parameters['auth'] = $authkey['auth_key'] : '';

    $url = str_replace('&amp;', '&', add_url_params($url, $url_parameters));
  
    //set accept message and add link to set
    $content =  l10n("Your download request for the set %s has been accepted.", $set_info);
    $content .= "<br><br>";
    $content .= l10n('You can now <a href="%s">download this set</a>.', $url);
    if (!empty($conf['batch_download']['general_conditions_link']))
    {
      $content .= "<br><br>";
      $content .= l10n("As a reminder, you agree to accept the general conditions of use and to respect the rights relating to intellectual property.");
      $content .= " ";
      $content .= l10n('Here is the link to <a href="%s">our general conditions of use</a>.', $conf['batch_download']['general_conditions_link']);
    }
  }
  else if ("reject" == $request['request_status'])
  {
    $subject = l10n("Your download request has been rejected");
    $subject = l10n("Your download request for the %s has been rejected." , $set_info);
    $content .= l10n("rejected").'.';
    $content .= l10n("\n");
    if(!isset($conf_mail))
    {
      $conf_mail = get_mail_configuration();
    }

    $content .= l10n("For more details or information, please %scontact the administrator%s.", "<a href='mailto:".$conf_mail['email_webmaster']."'>", "</a>" );
  }

  pwg_mail(
    $request['email'],
    array(
      'subject' => $subject,
      'content' => $content,
      'content_format' => 'text/html',
    )
  );
 }

/**
* Get a list of requests
*/
 function ws_downloadRequest_getList($params, &$service){
  $query = '
SELECT 
  r.id,
  type,
  type_id,
  user_id,
  first_name,
  last_name,
  organisation,
  email,
  telephone,
  profession,
  reason,
  nb_images,
  request_date,
  request_status, 
  status_change_date,
  image_size,
  updated_by,
  u.username as updated_by_username
  FROM '.BATCH_DOWNLOAD_TREQUESTS.' as r
  LEFT JOIN '.USERS_TABLE.' as u ON r.updated_by = u.id
  ORDER BY id DESC,
    request_date DESC
;';

  $result = pwg_query($query);

  $requests = array();

  while ($row = pwg_db_fetch_assoc($result))
  {

    $row['request_date'] = format_date($row['request_date']);
    $row['status_change_date'] = format_date($row['status_change_date']);

    switch ($row['type'])
    {
      // calendar
      case 'calendar':
      {
        $calendarName = str_replace("-", " ", $row['type_id']);
        $row['NAME'] = l10n('Calendar').': <a href="'.make_index_url(array('section' => 'categories',)).'/'.$row['type_id'].'" target="_blank">'.$calendarName.'</a>';
        $row['BASENAME'] = 'calendar-'.$row['type_id'];
        break;
      }
      // category
      case 'category':
      {
        $category = get_cat_info($row['type_id']);
        if ($category == null)
        {
          $row['NAME'] = l10n('Album').': #'.$row['type_id'].' (deleted)';
          $row['BASENAME'] = 'album'.$row['type_id'];
        }
        else
        {
          $row['NAME'] = l10n('Album').': '.get_cat_display_name($category['upper_names']);
          $row['sNAME'] = l10n('Album').': '.trigger_change('render_category_name', $category['name']);
          $row['COMMENT'] = trigger_change('render_category_description', $category['comment']);

          if (!empty($category['permalink']))
          {
            $row['BASENAME'] = 'album-'.$category['permalink'];
          }
          else if ( ($name = str2url($category['name'])) != null )
          {
            $row['BASENAME'] = 'album-'.$name;
          }
          else
          {
            $row['BASENAME'] = 'album'.$row['type_id'];
          }
        }
        break;
      }
      // flat
      case 'flat':
      {
        $row['NAME'] = l10n('Whole gallery');
        $row['BASENAME'] = 'all-gallery';
        break;
      }
      // tags
      case 'tags':
      {
        $tags = find_tags(explode(',', $row['type_id']));
        $row['NAME'] = l10n('Tags').': ';
        $row['BASENAME'] = 'tags';

        $first = true;
        foreach ($tags as $tag)
        {
          if ($first) $first = false;
          else $row['NAME'].= ', ';
          $row['NAME'].=
            '<a href="' . make_index_url(array('tags'=>array($tag))) . '" target="_blank">'
            .trigger_change('render_tag_name', $tag['name'])
            .'</a>';
          $row['BASENAME'].= '-'.$tag['url_name'];
        }
        break;
      }
      // search
      case 'search':
      {

        $row['NAME'] = '<a href="'.make_index_url(array('section'=>'search', 'search'=>$row['type_id'])).'" target="_blank">'.l10n('Search').'</a>';
        $row['BASENAME'] = 'search'.$row['type_id'];
        break;
      }
      // favorites
      case 'favorites':
      {
        $row['NAME'] = '<a href="'.make_index_url(array('section'=>'favorites')).'" target="_blank">'.l10n('Your favorites').'</a>';
        $row['BASENAME'] = 'favorites';
        break;
      }
      // most_visited
      case 'most_visited':
      {
        $row['NAME'] = '<a href="'.make_index_url(array('section'=>'most_visited')).'" target="_blank">'.l10n('Most visited').'</a>';
        $row['BASENAME'] = 'most-visited';
        break;
      }
      // best_rated
      case 'best_rated':
      {
        $row['NAME'] = '<a href="'.make_index_url(array('section'=>'best_rated')).'" target="_blank">'.l10n('Best rated').'</a>';
        $row['BASENAME'] = 'best-rated';
        break;
      }
      // list
      case 'list':
      {
        $row['NAME'] = l10n('Random');
        $row['BASENAME'] = 'random';
        break;
      }
      // recent_pics
      case 'recent_pics':
      {
        $row['NAME'] = '<a href="'.make_index_url(array('section'=>'recent_pics')).'" target="_blank">'.l10n('Recent photos').'</a>';
        $row['BASENAME'] = 'recent-pics';
        break;
      }
      // collection
      case 'collection':
      {
        try
        {
          if (!class_exists('UserCollection')) throw new Exception();
          $UserCollection = new UserCollection($row['type_id']);
          $name = str2url($UserCollection->getParam('name'));

          $collectionURL = make_index_url(array('section' => 'collections')) . '/'. 'edit/'.$row['type_id'];
          $row['NAME'] = l10n('Collection').': '.'<a href= "'.$collectionURL.'" target="_blank">'.$UserCollection->getParam('name').'</a>';

          if ( $name != null)
          {
            $row['BASENAME'] = 'collection-'.$name;
          }
          else
          {
            $row['BASENAME'] = 'collection'.$row['type_id'];
          }
        }
        catch (Exception $e)
        {
          $row['NAME'] = l10n('Collection').': #'.$row['type_id'].' (deleted)';
          $row['BASENAME'] = 'collection'.$row['type_id'];
        }
        break;
      }
    }

    array_push($requests, $row);
  }

  return $requests;
}

/**
 * Get a request
 */ 
function ws_downloadRequest_getInfo($params, &$service){
  $query = '
SELECT 
  id,
  type,
  type_id,
  user_id,
  first_name,
  last_name,
  organisation,
  email,
  telephone,
  profession,
  reason,
  nb_images,
  request_date,
  image_size
  FROM '.BATCH_DOWNLOAD_TREQUESTS.'
  WHERE id = '.$params['id'].'  
;';

  $request = pwg_db_fetch_row(pwg_query($query));
  $request[12] = format_date($request[12]);

  return $request;
}
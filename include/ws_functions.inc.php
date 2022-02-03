<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

function batch_download_ws_add_methods($arr)
{
  $service = &$arr[0];
  global $conf;

  $service->addMethod(
    'pwg.downloadRequest.create',
    'ws_downloadRequest_create',
    array(
      'type' => array(),
      'type_id' => array('type'=>WS_TYPE_INT|WS_TYPE_POSITIVE),
      'user_id' => array('type'=>WS_TYPE_ID),
      'first_name' => array(),
      'last_name' => array(),
      'organisation' => array('default' => null),
      'email' => array(),
      'telephone' => array('default' => null),
      'profession' => array('default' => null),
      'reason' => array(),
      'request_date' => array(),
      'image_size' => array('default'=>'original',
                            'info'=>'square, thumbnail, xxs, xs, s, m, l, xl, xxl, original'),
    ),
    'Create a new Download request.'
  );

  $service->addMethod(
    'pwg.downloadRequest.update',
    'ws_downloadRequest_update',
    array(
      'id' => array('type'=>WS_TYPE_ID),
      'status_change_date' => array(),
      'request_status' => array('default'=>'pending',
                            'info'=>'pending,rejected,accepted'),
    ),
    'Create a new Download request.'
  );

  $service->addMethod(
    'pwg.downloadRequest.getList',
    'ws_downloadRequest_getList',
    array(),
    'Get a list of all requests'
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
  
  //check email
  if (empty($params['email']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty email name');
  }
  
  //check reason
  if (empty($params['reason']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty reason name');
  }

  // Check if email address is valid
  if (!email_check_format($params['email']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Email isn\'t the right format');
  }

  //check iùage_size
  if (empty($params['image_size']))
  {
    return new PwgError(WS_ERR_MISSING_PARAM, 'Empty image size');
  }

  single_insert(
    BATCH_DOWNLOAD_TREQUESTS,
    $params
  );

  $request_info_email = "There is a new request to download a batch of photos.";
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

  $subject =l10n('Batch downloader, new download request');

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

  $subject = 'Batch downloader, your request has been processed';
  
  //Notify user once request staus changed
  $set_info = $request['type'].' '.$request['type_id'];
  
  $content = l10n("Your download request for the set %s has been", $set_info)." ";

  if ("accept" == $request['request_status'])
  {
    $url = get_absolute_root_url().'index.php?/'.$request['type'].'/'.$request['type_id'];
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
    $url = str_replace('&amp;', '&', add_url_params($url, array('action'=>'advdown_set', 'down_size'=>$request['image_size'])));

    //set accept message and add link to set
    $content .= l10n("accepted");
    $content .= l10n("\n");
    $content .= l10n("You can now download this set here :");
    $content .= l10n("\n");
    $content .= $url;
  }
  else if ("reject" == $request['request_status'])
  {
    $content .= l10n("rejected");
  }

  pwg_mail(
    $request['email'],
    array(
      'subject' => $subject,
      'content' => $content,
    )
  );
 }

 /**
 * Get a list of requests
 */

function ws_downloadRequest_getList($params, &$service){
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
  request_status, 
  status_change_date,
  image_size
  FROM '.BATCH_DOWNLOAD_TREQUESTS.'
  ORDER BY id DESC,
    request_date DESC
;';

  $result = pwg_query($query);

  $requests = array();

  while ($row = pwg_db_fetch_assoc($result))
  {

    $row['request_date'] = format_date($row['request_date']);
    $row['status_change_date'] = format_date($row['status_change_date']);

    array_push($requests, $row);
  }

  return $requests;
}
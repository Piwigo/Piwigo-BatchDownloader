<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

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

  //Check if email address is valid
  // if (!email_check_format($_POST['email']))
  // {
  //   return new PwgError(WS_ERR_MISSING_PARAM, 'Email isn\'t the right format');
  // }

  single_insert(
    BATCH_DOWNLOAD_TREQUESTS,
    $params
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
  size
  FROM '.BATCH_DOWNLOAD_TREQUESTS.'
  ORDER BY request_date DESC
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
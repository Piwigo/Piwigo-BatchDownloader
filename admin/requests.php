<?php
defined('BATCH_DOWNLOAD_PATH') or die('Hacking attempt!');

$template->set_filename('batch_download', realpath(BATCH_DOWNLOAD_PATH . 'admin/template/requests.tpl'));

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
$current_request ;

while ($row = pwg_db_fetch_assoc($result))
{
  if (!empty($_POST) and $row['id'] == $_POST['requestId'])
  {
    $row['request_status'] = $_POST['status'];
    $current_request = $row;
  }
    
  $row['request_date'] = format_date($row['request_date']);
  $row['status_change_date'] = format_date($row['status_change_date']);

  array_push($requests, $row);
}

if (!empty($_POST))
{
  list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));

  $update = array(
    'request_status'=> $_POST['status'],
    'status_change_date' => $dbnow,
  );

  single_update(
    BATCH_DOWNLOAD_TREQUESTS,
    $update,
    array(
      'id' => $_POST['requestId'],
    )
  );

  $subject = 'Batch downloader, your request has been processed';
  
  //Notify user once request staus changed
  $set_info = $current_request['type'].' '.$current_request['type_id'];
  
  $content = l10n("Your download request for the set %s has been", $set_info)." ";

  if ("accepted" == $_POST['status'])
  {
    $url = get_absolute_root_url().'index.php?/'.$current_request['type'].'/'.$current_request['type_id'];
    $url = add_url_params($url,array('action'=>'advdown_set', 'down_size'=>$current_request['size'])); 

    //set accept message and add link to set
    $content .= l10n("accepted");
    $content .= l10n("<br>");
    $content .= l10n("You can now download this set here : <br><br> <a>%s</a>", $url);
  }
  else if ("rejected" == $_POST['status'])
  {
    $content .= l10n("rejected");
  }

  pwg_mail(
    $current_request['email'],
    array(
      'subject' => $subject,
      'content' => $content,
      'content_format' => 'html',
    )
  );
}



$template->assign('requests', $requests);
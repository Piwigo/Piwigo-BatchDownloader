<?php
define('PHPWG_ROOT_PATH', '../../');
include(PHPWG_ROOT_PATH.'include/common.inc.php');

check_status(ACCESS_CLASSIC);

if (isset($_POST['set_id']) and isset($_POST['toggle_id']))
{
  try
  {
    $BatchDownloader = new BatchDownloader($_POST['set_id']);
    $BatchDownloader->removeImages(array($_POST['toggle_id']));
    echo "false";
  }
  catch (Exception $e)
  {
    echo 'error';
  }
}
else
{
  echo 'error';
}

?>
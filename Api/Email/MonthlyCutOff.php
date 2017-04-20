<?php
include __DIR__.'/../ApiCore.php';
include BASE_PATH.'/Model/MailCenter.php';
include_once BASE_PATH.'/Model/dbBusiness/MonthlyProcessing.php';
include_once BASE_PATH.'/Model/dbBusiness/ConfigCyclical.php';


if( empty($_SERVER['HTTP_HOST']) ){
  //被命令呼叫
  
  
}else{
  //被網頁呼叫
  
  
}

//只催該月的
$year = date('Y');
$month = date('m');

$process = new Model\Business\MonthlyProcessing();

$process_data = $process->select(array('owner_staff_id','id','status_code'),array('status_code'=>'< 5','year'=>$year,'month'=>$month));
if( count($process_data)==0 ){echo 'Complete';exit;}


$config_cyc = new Model\Business\ConfigCyclical($year,$month);


$cyc_data = $config_cyc->data;
$cutTime = strtotime($cyc_data['cut_off_date']);

if(!$cutTime || $cyc_data['monthly_launched']==0 || $cutTime>strtotime(date('Y-m-d')) ){echo 'Not Yet.';exit;}

$process_map = $process->map('owner_staff_id',true);

$mail = new Model\MailCenter;
foreach($process_map as $key => &$val){
  $mail->addAddress($key);  
}
 // $mail->addAddress(80);
// $mail->addCC('mavis.wu@rv88.tw');   

$res = $mail->sendTemplate('monthly_delay',array(
  'year'=>$year,
  'month'=>$month,
  'cut_off_date'=>str_replace('-','/',$cyc_data['cut_off_date'])
));

if($res===true){
  echo 'good';
}else{
  echo $res;
}

?>


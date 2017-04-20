<?php
include __DIR__.'/../ApiCore.php';
include BASE_PATH.'/Model/MailCenter.php';

$mail = new Model\MailCenter;

    
// $mail->addAddress('snow.jhung@rv88.tw', 'Snow');   
// $mail->addCC('mavis.wu@rv88.tw');   

// $ok = $mail->send('');
// $res = $mail->sendTemplate('monthly_start',array(
  // 'year'=>date('Y'),
  // 'month'=>date('m')
// ));

if($res===true){
  echo 'good';
}else{
  echo $res;
}

?>


<?php
namespace Model;

include_once BASE_PATH.'/Model/PHPMailer/PHPMailerAutoload.php';
include_once BASE_PATH.'/Model/dbBusiness/EmailTemplate.php';
include_once BASE_PATH.'/Model/dbBusiness/Staff.php';
include_once BASE_PATH.'/Model/dbBusiness/MonthlyProcessing.php';

use \Exception;

class MailCenter {
  
  protected $CONF;
  
  protected $Mailer;
  
  protected $Template;
  
  protected $Staff;
  
  protected $Process;
  
  private $log_time;
  
  private $tmp_staff_data;
  
  private $enabled;
  
  public function __construct(){
    
    $this->CONF = include(BASE_PATH."/Config/mail_config.php");
    
    if( isset($this->CONF['MAIL_CONFIG']['enabled']) && $this->CONF['MAIL_CONFIG']['enabled']==1 ){
      $mail = $this->buildMaillServiceConnection();
    
      $this->Template = new Business\EmailTemplate();
      $this->Staff = new Business\Staff();
      $this->Process = new Business\MonthlyProcessing();
      
      $this->log_time = microtime(true);
      $this->enabled = true;
    }else{
      $this->enabled = false;
    }
    
    // $mail->Encoding = "base64";
    
           
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addBCC('bcc@example.com');
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
  }
  
  public function send($subject, $content, $html = true){
    if($this->enabled==false){return true;}
    $mail = $this->Mailer;
    $mail->isHTML($html); 
    $mail->Subject = $subject;
    $mail->Body    = $content;
    $mail->AltBody = $content;
    if(!$mail->send()) {
      $this->writeDBLog($mail->ErrorInfo);
       return $mail->ErrorInfo;
    } else {
       return true;
    }
  }
  
  public function sendTemplate($name,$data=array()){
    if($this->enabled==false){return true;}
    $data['URL'] = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST'].WEB_ROOT : $this->CONF['SCHEDULE_CONFIG']['web_root'];
    $temp = $this->Template->map('name',true);
    if( empty($temp[$name]) ){return 'No Match Template.';}
    $loc = $temp[$name];
    $title = $this->parseData($loc['title'],$data);
    $text = $this->parseData($loc['text'],$data);
    // echo($text);exit;
    
    return $this->send($title, $text);
  }
  
  private function parseData($str,$data){
    if($this->enabled==false){return true;}
    return preg_replace_callback('/\{(.+?)\}/',function($m) use ($data){
      return isset($data[$m[1]])?$data[$m[1]]:$m[0];
    },$str);
  }
  
  public function addAddress($target, $name=''){
    if($this->enabled==false){return true;}
    if(is_int($target) || ctype_digit($target)){
      $staff = $this->Staff->map();
      if( isset($staff[$target]) ){
        $this->Mailer->addAddress($staff[$target]['email']);
      }else{
        $this->writeDBLog('Not Found Staff Id : '.$target);
      }
    }else{
      $this->Mailer->addAddress($target);
    }
    // $this->Mailer->addAddress($target, $name);
  }
  
  public function addAddressGroup($type='monthly_process',$data=array()){
    if($this->enabled==false){return true;}
    $staff_table = $this->Staff->table_name;
    $year = isset($data['year'])?$data['year']:date('Y');
    $month = isset($data['month'])?$data['month']:date('m');
    switch($type){
      case 'monthly_process':
        $result = $this->Process->sql(" select b.id, b.name , b.name_en, b.email, b.passwd, b.staff_no, b.is_admin, b.is_leader 
        from {table} as a right join $staff_table as b on a.owner_staff_id = b.id where (a.year = $year and a.month = $month) or (b.is_admin=1 and status_id < 4) 
        group by b.id ")->data;
        break;
      default:$result = array();
    }
    // LG($result);
    foreach($result as &$v){
      if($v['is_admin']==1 && $v['is_leader']==0){
        $this->addCC($v['email']);
      }else{
        $this->addAddress($v['email'],$v['name_en']);
      }
      $this->tmp_staff_data[$v['id']] = $v;
    }
    return $this;
  }
  
  public function addCC($target){
    if($this->enabled==false){return true;}
    $this->Mailer->addCC($target);
  }
  
  private function buildMaillServiceConnection(){
    
    date_default_timezone_set("Asia/Taipei");
    try{
      
      $conf = $this->CONF['MAIL_CONFIG'];
      // $username = getenv('username'); 
      // $password = getenv('password'); 
      // $pop = new \POP3(); 
      // $auth = $pop->Authorise($conf['host'], 110, 30, $conf['user'], $conf['pwd'], 1); 
      
      $this->Mailer = new \PHPMailer;
      $mail = $this->Mailer;
      $mail->isSMTP();
      $mail->SMTPDebug = 0;
      
      $mail->Host = $conf['host']; 
      $mail->SMTPAuth = false;

      $mail->Username = $conf['user'];
      $mail->Password = $conf['pwd'];

      $mail->SMTPSecure = $conf['secure'];
      
      $mail->Port = $conf['port'];
      $mail->CharSet = $conf['char'];

      $mail->setFrom( $conf['from'], $conf['fromName']);
      
      $mail->smtpConnect([
          'ssl' => [
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
          ]
      ]);
      
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
        $this->writeDBLog($e.getMessage());
    }
    return $mail;
  }
  
  
  
  protected function writeDBLog($str){
    $file = '/mail_error';
    $time_end = microtime(true);
    $spend_time = $time_end - $this->log_time;
    
    $log = (empty($log))? new \Logging() : $log;
    $log->lfile( $file );
    $log->lwrite("\n----------------------------------------------------------------------\n ".$str."\n\r - Spend Time : ( ".$spend_time." )\n"."----------------------------------------------------------------------\n");
    
    $log->lclose();
  }
  
}

?>

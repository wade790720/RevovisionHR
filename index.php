<?php

  include __DIR__."/Global.php";

  // var_dump($_SERVER);

  //var_dump($_ROT);exit;

  $controller_name = ucfirst($_ROT[0]).'Controller';
  $controller_path = RP('Controller/'.$controller_name.'.class.php');

  // var_dump($controller_path);
  // var_dump(WEB_ROOT);

  if(file_exists($controller_path)){
    include $controller_path;
    $cnt = new $controller_name($_ROT);
  }else{
    r404();
  }

?>



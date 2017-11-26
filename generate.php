<?php
require_once "class.keygen.php";
// ini_set('display_errors', 1); error_reporting(E_ALL);
if(isset($_GET['qty'])){
  $qty = $_GET['qty'];
  if($qty==1){
    header('Content-Type: application/json');
  }else{
    header('Content-Type: text/csv');
    header('Content-disposition: filename="'.date("Ymd-His").'.csv"');
  }

  Keygen::set_baseX(array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","H","J","K","L","M","N","R","T","V","W","X","Y"));
  Keygen::set_sum_loc(0); //placing checksum at beginning, using automatic grouping
  Keygen::set_code_len(5);//set code length to 5 (not including check digits)


  if($qty==1){
    echo json_encode(Keygen::generate());
  }else{
    $max = pow(sizeof(Keygen::$baseX),Keygen::$code_len);
    $qty = ($qty<=$max)?$qty:$max;
    //we cannot generate more codes than the codelength and base can hold

    $list = array();
    do{
      $single = Keygen::generate();
      if(!in_array($single, $list)){
        array_push($list, $single);
      }
    }
    while(sizeof($list)<$qty);

    foreach($list as $item){
      echo "\"".$item."\"\n";
    }
  }
}
?>

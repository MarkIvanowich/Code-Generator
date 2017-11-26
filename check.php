<?php
require_once "class.keygen.php";
// ini_set('display_errors', 1); error_reporting(E_ALL);
header('Content-Type: application/json');

Keygen::set_baseX(array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","H","J","K","L","M","N","R","T","V","W","X","Y"));
Keygen::set_sum_loc(0); //placing checksum at beginning, using automatic grouping
Keygen::set_code_len(5);//set code length to 5 (not including check digits)


if(isset($_GET['code'])){
  if(strlen($_GET['code'])<2){
    echo json_encode(false);
  }else
    echo json_encode(Keygen::validate($_GET['code']));//true (valid) or false (invalid)
}else{
  echo json_encode(false);
}

?>

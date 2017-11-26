<?php


/**
 * Marks Keygen class
 *
 * Creates a unique code based off a custom alphabet Checksums added automatically

 * Default settings a 6-character code, checksum at the end of the code.
 * Possible combinations are pow(base_number,code_length), where base_number is
 * equal to number of characters in alphabet/"baseX"
 *
 * PHP Version 7
 * @category  N/A
 * @package   N/A
 * @author    Mark Ivanowich <mark@ivanowich.ca>
 * @copyright 2017 mark.ivanowich.ca
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version   GIT: MarkIvanowich
 * @link      https://mark.ivanowich.ca
 */


class Keygen {
  //TODO: privitize and create getters for variables

  public static $baseX = array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","H","J","K","L","M","N","R","T","V","W","X","Y");
    //this is the letters and numbers our system will use as character of the code
  public static $aliases = array(
    //characters that do not exist in $baseX, but are accepted for later validation
    "G" => "0",
    "O" => "0",
    "Q" => "0",
    "S" => "5",
    "P" => "R",
    "I" => "J",
    "U" => "V",
    "Z" => "2"
  );
  public static $error_char = "!";
  public static $code_len = 6;//desired code length not including checksum digits(thats variable)
  public static $sum_location = 0; //0 for beginning & prettified, 1 for beginning and no dashes, 2 for beginning and dashes, 3 for middle, 4 for end;
  public static function set_baseX($in){
    self::$baseX = (array) $in;
  }
  public static function set_code_len($in){
    self::$code_len = (int) $in;
  }
  public static function set_sum_loc($in){
    self::$sum_location = (int) $in;
  }
  public static function set_error_char($in){
    self::$error_char = (string) $in;
  }
  public static function set_aliases($in){
    self::$aliases = (array) $in;
  }
  public static function get_bin_base($in){
    //how many bits to store our characters?
    $digits = 1;
    while(pow(2,$digits) < $in)  $digits++;
    return $digits;
  }
  public static function digits_of_number($in, $in_base){
    //how many digits to store a number in an arbitrary base?

    //digit value = x*(base_no^place)
    $digits = 1;
    while(pow($in_base,$digits) < $in)  $digits++;
    return $digits;
  }

  private static function to_baseX($in){
    //convert a decimal number to number in baseX
    if(($in >= 0)&&($in < sizeof(self::$baseX))){
      return self::$baseX[$in];
    }else{
      return self::$error_char;
    }
  }
  private static function to_deci($in){
    //convert baseX number to decimal
    $out = array_search($in,self::$baseX,true);
    //search the values of the array for string $in, identical matches only.
    if($in != self::$baseX[$out]){
    //double check that array_search returned a number, and not 'false'
    return -1;
    }
    return $out;
  }
  private static function gen_digit(){
    //gereate one random baseX digit
    $dig = mt_rand(0,sizeof(self::$baseX)-1);
    //random selection of a symbol from baseX
    return self::to_baseX($dig);
  }
  private static function count_bits($in, $type = true){
    //counts either 1s or 0s from a given decimal number - dependant upon how many bits to baseX's maximum
    $binary_base = self::get_bin_base(sizeof(self::$baseX)-1);

    $in_bin = str_pad(decbin($in),$binary_base, "0",STR_PAD_LEFT);
    //all binary representations should be $binary_base bits long -- for proper counting of 0'd bits
    $out = substr_count($in_bin, $type?"1":"0");
    //type=true counts high bits, false is low bits
    return $out;
  }
  public static function gen_checksum($key){
    //generate a check digit (function has improper nomenclature, whoopdie-do)

    $left_sum = 0;
    $right_sum = 0;
    $sum = 0;
    $left_key = substr($key,0,(strlen($key)/2));
    $right_key = substr($key,(strlen($key)/2));

    //function divides code into two halves, cointing the high bits of the left, and low bits of the right.    
    for($i=0;$i<strlen($left_key);$i++){
      $left_dig = self::to_deci($left_key[$i]);
      $left_sum += self::count_bits($left_dig, true);
    }
    for($i=0;$i<strlen($right_key);$i++){
      $right_dig = self::to_deci($right_key[$i]);
      $right_sum += self::count_bits($right_dig, false);
    }
    //uneven code lengths forces left & right sides to be in their own for loops 

    $sum = $left_sum +$right_sum;
    $out = "";
    $base = sizeof(self::$baseX);
    $binary_base = self::get_bin_base($base-1);
    while($sum!=0){
      $out=self::to_baseX($sum%$base).$out;
      $sum=(int)($sum/$base);
    }
    //larger codes need checksums that occupy more than one digit
    //if the code length is the size of which, we need to make sure lesser checksums have equal digits.
    $out = str_pad($out,Keygen::digits_of_number(self::$code_len*$binary_base,$base),"0",STR_PAD_LEFT);
    return $out;
  }
  public static function generate(){

    $key = "";
    for($i = 0;$i<(self::$code_len);$i++){
      $key .= self::gen_digit();
    }
    $checkletter = self::gen_checksum($key);

    $full_len = (self::$code_len+strlen($checkletter));
    switch(self::$sum_location){
      default:
      case 0:
        $key = $checkletter . $key;
        if($full_len>5){
          $best_groupcount = 1;
          $best_dist = $full_len;
          for($i=10;$i>=2;$i--){
            if($full_len%$i==0){//this is evenly divisible
                $mod = $full_len/$i;
                $dist = max($mod, $i) - min($mod, $i);
                if($dist <= $best_dist){
                  $best_groupcount = $i;
                  $best_dist = $dist;
                }
            }
          }
          if($best_groupcount!=1){
            $group_size = ((int)$full_len/$best_groupcount );
          }
          $in_group = ($full_len/$best_groupcount);
          for($i=$full_len-1;$i>0;$i--){
            if($i%$in_group==0){
              $key = substr_replace($key,"-",$i,0);
            }
          }
        }

        break;
      case 1://sum at the beginning no dashes
        $key = $checkletter . $key;
        break;
      case 2://sum at the beginning with dash
        $key = $checkletter .($full_len%2==0?"":"-"). $key;
        break;
      case 3://sum in the middle
        $code_even = ($full_len%2==0);
        $key_even = (strlen($checkletter)%2==0);

        $middle = $key_even?($code_even?  "-".$checkletter."-" : $checkletter):
                            ($code_even?  $checkletter."-"     : "-".$checkletter."-");

        $key =  substr_replace($key, $middle, self::$code_len/2, 0);
        //dash always after checkletter, dash before only if code will be odd-numbered
        break;
      case 4://sum at the end
        $key .= ($full_len%2==0?"":"-").$checkletter;
        break;
    }
    return $key;
  }
  public static function validate($in_code){
    if(strlen($in_code)<self::$code_len)return false;

    $in_code = preg_replace("/[^a-zA-Z0-9]/", "", $in_code); 
    $in_code = strtoupper($in_code);
    //TODO: expand regex to contain more characters than A-Z, 0-9 and uppercase

    foreach(self::$aliases as $from => $to){
      $in_code = str_replace($from, $to, $in_code);
    }
    $this_len = strlen($in_code);
    $sum_len = $this_len - self::$code_len;
    $in_key_len = $this_len - $sum_len;
    $in_key = "";
    $in_sum = "";
    switch(self::$sum_location){
      case 0 ://sum at beginning, prettified
      case 1 ://sum at beginning, no dashes
      case 2 ://sum at beginning
        $in_sum = substr($in_code,0,$sum_len);
        $in_key = substr($in_code,$sum_len);
        $out_sum = self::gen_checksum($in_key);
        return ($in_sum == $out_sum);
        break;
      case 3 ://sum in the middle
        $in_sum = substr($in_code,($in_key_len/2),$sum_len);
        $in_left_key = substr($in_code,0,($in_key_len/2));
        $in_right_key = substr($in_code,($in_key_len/2)+$sum_len);
        $in_key = $in_left_key . $in_right_key;
        $out_sum = self::gen_checksum($in_key);
        return ($in_sum == $out_sum);
        break;
      case 4:
      default://sum at the end
        $in_key = substr($in_code,0,$in_key_len);
        $in_sum = substr($in_code,$in_key_len);
        $out_sum = self::gen_checksum($in_key);
        return ($in_sum == $out_sum);
        break;
    }

  }

}
?>

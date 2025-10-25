<?php
namespace App\Utils;
final class Normalizer {
  public static function nullIfEmpty($v){
    if($v===null) return null;
    if(is_string($v)){ $t=trim($v); if($t===''|| strtolower($t)==='null') return null; if(preg_match('/^1900-01-01( |T|$)/',$t)) return null; return $t; }
    return $v?:null;
  }
  public static function ascii(string $s): string {
    $map=['Š'=>'S','š'=>'s','Đ'=>'Dj','đ'=>'dj','Ž'=>'Z','ž'=>'z','Č'=>'C','č'=>'c','Ć'=>'C','ć'=>'c'];
    return strtr($s,$map);
  }
}

<?php
if (!defined('BASEPATH')) exit('No');
class Dbg extends CI_Controller {
  public function __construct(){parent::__construct();}
  public function tok(){
    $t1 = $this->config->item('stem_digest_token');
    $t2 = $this->config->item('csr_bearer_token');
    $h = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : 'NONE';
    $rh = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : 'NONE';
    $gah = function_exists('getallheaders') ? json_encode(getallheaders()) : 'NA';
    header('Content-Type: application/json');
    echo json_encode(array('stem_digest_token'=>$t1,'csr_bearer_token'=>$t2,'HTTP_AUTHORIZATION'=>$h,'REDIRECT_HTTP_AUTHORIZATION'=>$rh,'getallheaders'=>$gah));
  }
}

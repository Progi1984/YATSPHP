<?php

include '../src/Progi1984/YATSPHP.class.php';

$paramThing = (isset($_GET['thing']) ? $_GET['thing'] : 'there');

$oTPL = new YATSPHP();
$oTPL->define('sample01.tpl');
if($oTPL){
  $oTPL->assign('thing', $paramThing);
  print_r($oTPL);
  echo $oTPL->render();
}
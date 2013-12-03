<?php

include '../YATSPHP.phar';

$paramThing = (isset($_GET['thing']) ? $_GET['thing'] : 'there');

$oTPL = new \Progi1984\YATSPHP();
$oTPL->define('sample01.tpl');
if($oTPL){
  $oTPL->assign('thing', $paramThing);
  print_r($oTPL);
  echo $oTPL->render();
}
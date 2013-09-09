<?php

include '../src/YATSPHP.class.php';

$oTPL = new YATSPHP();
$oTPL->define('sample02.tpl');
if($oTPL){
  $oTPL->assign('color', array('red', 'blue', 'green', 'orange', 'purple', 'black', 'mauve', 'peach'));
  print_r($oTPL);
  echo $oTPL->render();
}

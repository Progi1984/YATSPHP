<?php

include '../src/Progi1984/YATSPHP.php';

$oTPL = new YATSPHP();
$oTPL->define('sample07.tpl');
if($oTPL){
  $colors = array('red', 'blue', 'green', 'orange', 'purple');
  $flavors = array('peach', 'mint', 'chocolate', 'vanilla', 'coffee');
  $oTPL->assign(array('color' => $colors, 'flavor' => $flavors, 'count_color' => count($colors), 'count_flavor' => count($flavors), 'person' => 'Joe'));
  $oTPL->hide('color', true, 3);
  print_r($oTPL);
  echo $oTPL->render();
}
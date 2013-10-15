<?php

include '../src/Progi1984/YATSPHP.class.php';

$oTPL = new YATSPHP();
$oTPL->define('sample03.tpl');
if($oTPL){
  $colors = array('red', 'blue', 'green', 'orange', 'purple', 'black', 'mauve', 'peach');
  $flavors = array('peach', 'mint', 'chocolate', 'vanilla', 'coffee');
  $oTPL->assign(array('color' => $colors, 'flavor' => $flavors, 'count_color' => count($colors), 'count_flavor' => count($flavors)));
  print_r($oTPL);
  echo $oTPL->render();
}

<?php

include '../src/YATSPHP.class.php';

$tmpl = yats_define("sample7.tmpl", '', getcwd());

$thing = $GLOBALS[HTTP_GET_VARS][thing];
if(!$thing) {
  $thing = "there";
}

$colors = array("red", "blue", "green", "orange", "purple");
$flavors = array("peach", "mint", "chocolate", "vanilla", "coffee");

if($tmpl) {
  yats_assign($tmpl, array(color => $colors,
    flavor => $flavors,
    count_color => count($colors),
    count_flavor => count($flavors),
    person => "Joe"
  ) );

  // hide the third loop of colors section only.
  yats_hide($tmpl, 'color', true, 3);

  echo yats_getbuf($tmpl);
}
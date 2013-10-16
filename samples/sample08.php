<?php

include '../src/Progi1984/YATSPHP.php';

$tmpl = yats_define("sample8.tmpl", '', getcwd());

$thing = $GLOBALS[HTTP_GET_VARS][thing];
if(!$thing) {
  $thing = "there";
}

// Note: same as sample 7 except we use appending assignments
// for a more natural 1 : 1 mapping using php arrays.

$items = array('peach' => 'red',
  'mint' => 'blue',
  'chocolate' => 'green' ,
  'vanilla' => 'orange',
  'coffee' => 'purple');

if($tmpl) {

  // assign the looping items
  foreach( $items as $flavor => $color ) {
    yats_assign($tmpl, array(color => $color,
      flavor => $flavor
      // we could have N number of assignments here.
    ) );
  }

  // assign the scalar items
  yats_assign($tmpl, array(count_color => count($items),
    count_flavor => count($items),
    person => "Joe"));

  // hide the third loop of colors section only.
  yats_hide($tmpl, 'color', true, 3);

  echo yats_getbuf($tmpl);
}
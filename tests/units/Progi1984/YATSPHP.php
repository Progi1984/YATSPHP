<?php

namespace tests\units\Progi1984;

require_once __DIR__ . '/../../src/Progi1984/YATSPHP.php';

use \mageekguy\atoum;
use Progi1984;

class YATSPHP extends atoum\test
{
    public function testDefine()
    {
      $oYATS = new YATSPHP();
      $this->variable($oYATS->define('filename_not_exists.tpl'))->isNull();
    }

}

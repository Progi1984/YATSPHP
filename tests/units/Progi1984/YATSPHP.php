<?php

namespace tests\units\Progi1984;

require_once __DIR__ . '/../../../src/Progi1984/YATSPHP.php';

use \mageekguy\atoum;
use Progi1984;

class YATSPHP extends atoum\test
{
    public function testDefine()
    {
        $this
            ->if($oYATS = new YATSPHP())
            ->then
                ->variable($oYATS->define('filename_not_exists.tpl'))->isNull();
    }
    
    public function testAssign()
    {
        $arrayValue = array('key' => 'value', 'key1' => 'value1');
        
        $this
            ->if($oYATS = new YATSPHP())
            ->then
                ->if($oYATS->assign('key1'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key1' => null))
                ->if($oYATS->assign('key2', 'value2'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key2' => 'value2'))
                ->if($oYATS->assign($arrayValue)
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo($arrayValue))
                ->if($oYATS->assign($arrayValue, 'data'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo($arrayValue)
        ;
    }
}

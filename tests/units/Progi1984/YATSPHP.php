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
            ->if($oYATS = new Progi1984\YATSPHP())
            ->and($oYATS->assign('key1'))
                ->then
                    ->phpArray($oYATS->getvars())->isIdenticalTo(array('key1' => null))
        ;
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->and($oYATS->assign('key2', 'value2'))
                ->then
                    ->phpArray($oYATS->getvars())->isIdenticalTo(array('key2' => 'value2'))
        ;
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->and($oYATS->assign($arrayValue)
                ->then
                    ->phpArray($oYATS->getvars())->isIdenticalTo($arrayValue))
        ;
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->and($oYATS->assign($arrayValue, 'data'))
                ->then
                    ->phpArray($oYATS->getvars())->isIdenticalTo($arrayValue)
        ;
    }
}

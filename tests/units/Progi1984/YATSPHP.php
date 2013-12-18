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
        $this
            ->given($oYATS = new YATSPHP())
            ->then
                ->if($oYATS->assign('key'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key' => null))
                ->if($oYATS->assign('key', 'value'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key' => 'value'))
                ->if($oYATS->assign(array('key' => 'value', 'key1' => 'value1'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key' => 'value', 'key1' => 'value1')))
                ->if($oYATS->assign(array('key' => 'value', 'key1' => 'value1'), 'data'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key' => 'value', 'key1' => 'value1'))
        ;
    }
}

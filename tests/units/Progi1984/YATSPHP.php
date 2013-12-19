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
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
                ->assert
                ->if($oYATS->assign('key1'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key1' => null))
                ->assert
                ->if($oYATS->assign('key2', 'value2'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key1' => null, 'key2' => 'value2'))
                ->assert
                ->if($oYATS->assign('key2', 'value_new'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key1' => null, 'key2' => 'value_new'))
                ->assert
                ->if($oYATS->assign(array('key3' => 'value3')))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key1' => null, 'key2' => 'value_new', 'key3' => 'value3'))
                ->assert
                ->if($oYATS->assign(array('key4' => 'value4'), 'data'))
                ->then
                    ->array($oYATS->getvars())->isIdenticalTo(array('key1' => null, 'key2' => 'value_new', 'key3' => 'value3', 'key4' => 'value4'));
    }
}

?>

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
        $oYATS = new Progi1984\YATSPHP();
        $this
            ->if($oYATS->assign('key1'))
            ->then
                ->array($oYATS->getvars())->isIdenticalTo(array('key1' => null));
        
        $oYATS = new Progi1984\YATSPHP();
        $this
            ->if($oYATS->assign('key2', 'value2'))
            ->then
                ->array($oYATS->getvars())->isIdenticalTo(array('key2' => 'value2'))
                ->if($oYATS->assign('key2', 'value_new'))
                    ->array($oYATS->getvars())->isIdenticalTo(array('key2' => 'value_new'));

        $oYATS = new Progi1984\YATSPHP();
        $this
            ->if($oYATS->assign(array('key3' => 'value3')))
            ->then
                ->array($oYATS->getvars())->isIdenticalTo(array('key3' => 'value3'));

        $oYATS = new Progi1984\YATSPHP();
        $this
            ->if($oYATS->assign(array('key4' => 'value4'), 'fakedata'))
            ->then
                ->array($oYATS->getvars())->isIdenticalTo(array('key4' => 'value4'));
    }
}

?>

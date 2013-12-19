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
    
	public function testAssignWithNoData()
	{
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
			->and($oYATS->assign('key1'))
            ->then
                ->array($oYATS->getvars())->isEqualTo(array('key1' => null));
	}
	public function testAssignWithStringData()
	{
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
			->and($oYATS->assign('key2', 'value2'))
            ->then
                ->array($oYATS->getvars())->isEqualTo(array('key2' => 'value2'));
	}
	public function testAssignWithStringDataAndNewData()
	{
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
			->and($oYATS->assign('key2', 'value2'))
            ->then
                ->array($oYATS->getvars())->isEqualTo(array('key2' => 'value2'))
                ->if($oYATS->assign('key2', 'value_new'))
                    ->array($oYATS->getvars())->isEqualTo(array('key2' => 'value_new'));
	}
	public function testAssignWithArrayData()
	{
		$arrayValue = array('key3' => 'value3');
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
			->and($oYATS->assign($arrayValue))
            ->then
                ->array($oYATS->getvars())->isEqualTo($arrayValue);
	}
	
    public function testAssignWithArrayDataAndValue()
    {
		$arrayValue = array('key4' => 'value4');
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
			->and($oYATS->assign($arrayValue, 'fakedata'))
            ->then
                ->array($oYATS->getvars())->isEqualTo($arrayValue);
    }
}

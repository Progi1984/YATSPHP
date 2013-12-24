<?php

namespace tests\units\Progi1984;

require_once __DIR__ . '/../../../src/Progi1984/YATSPHP.php';

use \mageekguy\atoum;
use Progi1984;

class YATSPHP extends atoum\test
{
    public function testDefineFileNotExists()
    {
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
                ->variable($oYATS->define('filename_not_exists.tpl'))->isNull();
    }
    
    public function testDefineFileExists()
    {
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
                ->variable($oYATS->define('renderVariableDefined.tpl', join(DIRECTORY_SEPARATOR, array(__DIR__, 'tpl'))))->isEqualTo($oYATS);
    }
    
    public function testGetVars()
    {
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
            	->array($oYATS->getVariables());
    }
    
    public function testAssignWithNoData()
    {
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->and($oYATS->assign('key1'))
            ->then
                ->array($oYATS->getVariables())->isEqualTo(array('key1' => null));
    }
    
    public function testAssignWithStringData()
    {
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->and($oYATS->assign('key2', 'value2'))
            ->then
                ->array($oYATS->getVariables())->isEqualTo(array('key2' => 'value2'));
    }
    
    public function testAssignWithStringDataAndNewData()
    {
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->and($oYATS->assign('key2', 'value2'))
            ->then
                ->array($oYATS->getVariables())->isEqualTo(array('key2' => 'value2'))
                ->if($oYATS->assign('key2', 'value_new'))
                    ->array($oYATS->getVariables())->isEqualTo(array('key2' => 'value_new'));
    }
    
    public function testAssignWithArrayData()
    {
        $arrayValue = array('key3' => 'value3');
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->and($oYATS->assign($arrayValue))
            ->then
                ->array($oYATS->getVariables())->isEqualTo($arrayValue);
    }
	
    public function testAssignWithArrayDataAndValue()
    {
    	$arrayValue = array('key4' => 'value4');
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
    	    ->and($oYATS->assign($arrayValue, 'fakedata'))
            ->then
                ->array($oYATS->getVariables())->isEqualTo($arrayValue);
    }
    
    public function testGetSections()
    {
    	$this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
            	->array($oYATS->getSections());
    }
    
    public function testHideWithString()
    {
    	$this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
            	->boolean($oYATS->hide('string'))->isFalse()
            	->array($oYATS->getSections())->isEqualTo(array());
    }
    
    public function testHideWithArray()
    {
    	$arraySection = array('S_Section1' => true, 'S_Section' => false);
    	$this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
            	->boolean($oYATS->hide($arraySection))->isTrue()
            	->array($oYATS->getSections())->isEqualTo($arraySection);
    }
    
    public function testHideWithKeyValueNotBoolean()
    {
    	$this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
            	->boolean($oYATS->hide('S_Section1', 'string'))->isFalse()
            	->array($oYATS->getSections())->isEqualTo(array());
    }
    
    public function testHideWithKeyValueBoolean()
    {
    	$this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
            	->boolean($oYATS->hide('S_Section1', true))->isTrue()
            	->array($oYATS->getSections())->isEqualTo(array('S_Section1' => true));
    }
    
    public function testHideWithKeyValueBooleanAndNewValue()
    {
    	$this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
            	->boolean($oYATS->hide('S_Section1', true))->isTrue()
            	->boolean($oYATS->hide('S_Section1', false))->isTrue()
            	->array($oYATS->getSections())->isEqualTo(array('S_Section1' => false));
    }
    
    public function testHideWithRowKeyValue()
    {
    	$this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
            	->boolean($oYATS->hide('S_Section1', true, 5))->isTrue()
            	->array($oYATS->getSections())->isEqualTo(array('S_Section1' => array(5, true)));
    }
    
    public function testHideWithRowKeyValueAndNewValue()
    {
    	$this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->then
            	->boolean($oYATS->hide('S_Section1', true, 5))->isTrue()
            	->boolean($oYATS->hide('S_Section1', false))->isTrue()
            	->array($oYATS->getSections())->isEqualTo(array('S_Section1' => false));
    }
    
    public function testRenderFileNotExists()
    {
        $this
            ->if($oYATS = new Progi1984\YATSPHP())
            ->and($oYATS->define('filename_not_exists.tpl'))
            ->then
                ->variable($oYATS->render())->isNull();
    }
    
    public function testRenderVariableNotDefined()
    {
    	$this
    		->if($oYATS = new Progi1984\YATSPHP())
    		->and($oYATS->define('renderVariableUndefined.tpl', join(DIRECTORY_SEPARATOR, array(__DIR__, 'tpl'))))
    		->then
    			->string($oYATS->render())->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array(__DIR__, 'html', 'renderVariableUndefined.html')));
    }
    
    public function testRenderVariableDefined()
    {
    	$this
    		->if($oYATS = new Progi1984\YATSPHP())
    		->and($oYATS->define('renderVariableDefined.tpl', join(DIRECTORY_SEPARATOR, array(__DIR__, 'tpl'))))
    		->and($oYATS->assign('variable' , 'Content'))
    		->then
    			->string($oYATS->render())->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array(__DIR__, 'html', 'renderVariableDefined.html')));
    }
    
    public function testRenderVariableNotDefinedWithAlt()
    {
    	$this
    		->if($oYATS = new Progi1984\YATSPHP())
    		->and($oYATS->define('renderVariableUndefinedWithAlt.tpl', join(DIRECTORY_SEPARATOR, array(__DIR__, 'tpl'))))
    		->then
    			->string($oYATS->render())->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array(__DIR__, 'html', 'renderVariableUndefinedWithAlt.html')));
    }
    
    public function testRenderVariableDefinedWithAlt()
    {
    	$this
    		->if($oYATS = new Progi1984\YATSPHP())
    		->and($oYATS->define('renderVariableDefinedWithAlt.tpl', join(DIRECTORY_SEPARATOR, array(__DIR__, 'tpl'))))
    		->and($oYATS->assign('variable' , 'Content'))
    		->then
    			->string($oYATS->render())->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array(__DIR__, 'html', 'renderVariableDefinedWithAlt.html')));
    }
    
    public function testRenderTextNoTranslated()
    {
    	$this
    		->if($oYATS = new Progi1984\YATSPHP())
    		->and($oYATS->define('renderTextNoTranslated.tpl', join(DIRECTORY_SEPARATOR, array(__DIR__, 'tpl'))))
    		->then
    			->string($oYATS->render())->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array(__DIR__, 'html', 'renderTextNoTranslated.html')));
    }
    
    public function testRenderTextTranslated()
    {
    	$this
    		->if($oYATS = new Progi1984\YATSPHP())
    		->and($oYATS->define('renderTextTranslated.tpl', join(DIRECTORY_SEPARATOR, array(__DIR__, 'tpl'))))
    		->then
    			->string($oYATS->render())->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array(__DIR__, 'html', 'renderTextTranslated.html')));
    }
    
    public function testRenderTextNoTranslatedVariable()
    {
    	$this
    		->if($oYATS = new Progi1984\YATSPHP())
    		->and($oYATS->define('renderTextNoTranslatedVariable.tpl', join(DIRECTORY_SEPARATOR, array(__DIR__, 'tpl'))))
    		->and($oYATS->assign('variable', 'Progi1984'))
    		->then
    			->string($oYATS->render())->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array(__DIR__, 'html', 'renderTextNoTranslatedVariable.html')));
    }
    
    public function testRenderTextTranslatedVariable()
    {
    	$this
    		->if($oYATS = new Progi1984\YATSPHP())
    		->and($oYATS->define('renderTextTranslatedVariable.tpl', join(DIRECTORY_SEPARATOR, array(__DIR__, 'tpl'))))
    		->and($oYATS->assign('variable', 'Progi1984'))
    		->then
    			->string($oYATS->render())->isEqualToContentsOfFile(join(DIRECTORY_SEPARATOR, array(__DIR__, 'html', 'renderTextTranslatedVariable.html')));
    }
}

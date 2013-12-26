<?php

namespace Progi1984;

class YATSPHP {
  private $_docroot;
  private $_template;
  private $_searchpath;
  private $_vars = array();
  private $_hiddenSection = array();
  private $_levelImbrication = 0;

  private $_renderSectionAutohide = 'no';

  public function define($psFilename, $psDocRoot = null, $psSearchPath = null){
    if(is_null($psDocRoot) && substr($psFilename, 0, 1) != DIRECTORY_SEPARATOR){
      $psDocRoot = getcwd().DIRECTORY_SEPARATOR;
    }
    if(!is_null($psDocRoot) && substr($psDocRoot, 0, -1) != DIRECTORY_SEPARATOR){
      $psDocRoot .= DIRECTORY_SEPARATOR;
    }
    if(!is_null($psSearchPath)){
      $this->_searchpath = $psSearchPath;
      if(substr($this->_searchpath, -1) != DIRECTORY_SEPARATOR){
        $this->_searchpath .= DIRECTORY_SEPARATOR;
      }
    }
    if(file_exists($psDocRoot.$psFilename)){
      $this->_docroot = $psDocRoot;
      $this->_template = $psFilename;
      return $this;
    } else {
      if(!is_null($this->_searchpath)){
        if(file_exists($this->_searchpath.$psFilename)){
          $this->_template = $this->_searchpath.$psFilename;
          return $this;
        }
      }
    }
    return null;
  }

  public function assign($key, $value = null){
    if(is_array($key) == true){
      $this->_vars = array_merge($this->_vars, $key);
    } else {
      $this->_vars[$key] = $value;
    }
    return $this;
  }
  
  public function getVariables(){
    return $this->_vars;
  }
  public function getSections(){
    return $this->_hiddenSection;
  }

  public function hide($psSection, $pbState = null, $piNumRow = null){
    if(is_null($pbState) && is_null($piNumRow)){
      if(!is_array($psSection)){
        return false;
      }
      $this->_hiddenSection = array_merge($this->_hiddenSection, $psSection);
    } else {
      if(is_null($piNumRow)){
        $this->_hiddenSection[$psSection] = $pbState;
      } else {
        if(!isset($this->_hiddenSection[$psSection])){
          $this->_hiddenSection[$psSection] = array();
        }
        $this->_hiddenSection[$psSection][$piNumRow] = $pbState;
      }
    }
    return true;
  }

  private function renderVariable($psContent, $psTagContent, $psKey, $psAttributeAlt){
    if(isset($this->_vars[$psKey])){
      if(is_array($this->_vars[$psKey])){
        $psContentToRepeat = $psContent;
        $psContent = '';
        foreach($this->_vars[$psKey] as $value){
          $psContent .= str_replace($psTagContent, $value, $psContentToRepeat);
        }
      } else {
        $psContent = str_replace($psTagContent, $this->_vars[$psKey], $psContent);
      }
    } else {
      if(strpos($psAttributeAlt, 'alt=') === false){
        $psContent = str_replace($psTagContent, '', $psContent);
      } else {
        preg_match('#alt="([a-z]{0,100})"#', $psAttributeAlt, $arrResultAlt);
        $psContent = str_replace($psTagContent, $arrResultAlt[1], $psContent);
      }
    }
    return $psContent;
  }

  private function renderSection($psSection){
    $this->_levelImbrication++;
    // Section : Extract Sub
    $psSection = $this->extractSections($psSection);
    // Section : Render translating text
    $psSection = $this->extractL10N($psSection, false);
    // Section : Rendu
    $psSection = $this->extractVariables($psSection);
    // Section : Render translating text
    $psSection = $this->extractL10N($psSection, true);
    // Section : Hide Sub Sections
    $psSection = $this->extractSectionsChildren($psSection);
    $this->_levelImbrication--;
    return $psSection;
  }

  private function extractInclude($psContent){
    preg_match_all('#{{include file="([a-zA-Z0-9_/\.]*)"}}#', $psContent, $arrResult);
    if(!empty($arrResult[0])){
      #echo '<pre>'.print_r($arrResult, true).'</pre>';
      foreach ($arrResult[1] as $key => $sFileInclude){
        $oYATS = new YATSPHP();
        $sDocRoot = null;
        if(is_null($this->_docroot)){
          $sDocRoot = dirname($this->_template).DIRECTORY_SEPARATOR;
        } else {
          if(substr($sFileInclude, 0, 2) == './'){
            $sDocRoot = dirname($this->_template).DIRECTORY_SEPARATOR;
            $sFileInclude = substr($sFileInclude, 2);
          } else {
            $sDocRoot = $this->_docroot;
          }
        }
        $oYATS->define($sFileInclude, $sDocRoot, $this->_searchpath);
        $oYATS->assign($this->_vars);
        $sInclude = $oYATS->render();
        $psContent = str_replace($arrResult[0][$key], $sInclude, $psContent);
      }
    }
    return $psContent;
  }

  private function extractSections($psContentToExtract){
    preg_match_all('#{{section:([a-zA-Z0-9_]{0,50})\s{0,50}([a-z"=\s]*)}}#', $psContentToExtract, $arrResult);
    if(!empty($arrResult[0])){
      #echo 'Niveau '.$this->_levelImbrication.'<br />';
      #echo '<pre>'.print_r($arrResult, true).'</pre>';

      $arrResSectionData = $arrResult[0];
      $arrResSectionName = $arrResult[1];
      $arrResSectionParam = $arrResult[2];
      $arrResSectionAutoHide = array();
      $arrResSectionParentLoop = array();
      $arrResSectionHidden = array();
      // Divide params
      foreach($arrResSectionParam as $key => $value){
        $arrResSectionAutoHide[$key] = 'no';
        $arrResSectionParentLoop[$key] = 'no';
        $arrResSectionHidden[$key] = 'no';
        if(!empty($value)){
          $value = explode(' ', $value);
          foreach($value as $itmValue){
            if(substr($itmValue,0,10) == 'autohide="'){
              $arrResSectionAutoHide[$key] = substr($itmValue, 10 , strlen($itmValue) - 11);
            } elseif(substr($itmValue,0,12) == 'parentloop="'){
              $arrResSectionParentLoop[$key] = substr($itmValue, 12 , strlen($itmValue) - 13);
            } elseif(substr($itmValue,0,8) == 'hidden="'){
              $arrResSectionHidden[$key] = substr($itmValue, 8 , strlen($itmValue) - 9);
            }
          }
        }
      }

      foreach($arrResSectionData as $keySection => $valSection){
        #echo 'SECTION ('.$this->_levelImbrication.'):'.$valSection.'<br />';
        #var_dump(isset($this->_hiddenSection[$arrResSectionName[$keySection]]) ? $this->_hiddenSection[$arrResSectionName[$keySection]] : null);
        #echo '<br />';
        // Section
        $piPositionStart = strpos($psContentToExtract, $valSection);
        if($piPositionStart !== false){
          $psSection = substr($psContentToExtract, $piPositionStart);
          $psSection = substr($psSection, 0, strpos($psSection, '{{/section:'.$arrResSectionName[$keySection].'}}') + strlen('{{/section:'.$arrResSectionName[$keySection].'}}'));

          // Section Hidden
          if(// If the parameter hidden = yes && no hide asked
            ($arrResSectionHidden[$keySection] == 'yes' &&  !isset($this->_hiddenSection[$arrResSectionName[$keySection]]))
            // If hide is asked
            || (isset($this->_hiddenSection[$arrResSectionName[$keySection]]) && is_bool($this->_hiddenSection[$arrResSectionName[$keySection]]) && $this->_hiddenSection[$arrResSectionName[$keySection]] == true)){
            $psSectionContent = '';
          } else {
            // Section : Contenu
            $psSectionContent = substr($psSection, strlen($valSection), strlen($psSection) - strlen($valSection) - strlen('{{/section:'.$arrResSectionName[$keySection].'}}'));
            // Section : Render
            $this->_renderSectionAutohide = $arrResSectionAutoHide[$keySection];
            if($arrResSectionParentLoop[$keySection] == 'no'){
              $psSectionContent = $this->renderSection($psSectionContent);
            } else {
              if(isset($this->_hiddenSection[$arrResSectionName[$keySection]]) && is_array($this->_hiddenSection[$arrResSectionName[$keySection]])){
                $psSectionContent = '{{sectionChild:'.$arrResSectionName[$keySection].'}}'.$psSectionContent.'{{/sectionChild:'.$arrResSectionName[$keySection].'}}';
              }
            }
          }

          $psContentToExtract = str_replace($psSection, $psSectionContent, $psContentToExtract);
        }
      }
    }
    return $psContentToExtract;
  }

  private function extractVariables($psContentToExtract){
    preg_match_all('#{{(?!text)([a-z0-9"=_]{0,50})\s{0,50}([a-z"=\s]*)}}#msi', $psContentToExtract, $arrResult);

    #echo '<pre>'.print_r($arrResult, true).'</pre>';
    $arrResVarData = $arrResult[0];
    $arrResVarName = $arrResult[1];
    $arrResVarParam = $arrResult[2];
    $arrResVarAlt = array();
    $arrResVarRepeatScalar = array();
    // Divide params
    foreach($arrResVarParam as $keyParam => $valParam){
      $arrResVarAlt[$keyParam] = '';
      $arrResVarRepeatScalar[$keyParam] = 'no';
      if(!empty($valParam)){
        $valParam = explode(' ', $valParam);
        foreach($valParam as $itmValParam){
          if(substr($itmValParam,0,5) == 'alt="'){
            $arrResVarAlt[$keyParam] = substr($itmValParam, 5 , strlen($itmValParam) - 6);
          } elseif(substr($itmValParam,0,14) == 'repeatscalar="'){
            $arrResVarRepeatScalar[$keyParam] = substr($itmValParam, 14 , strlen($itmValParam) - 15);
          }
        }
      }
    }

    // If array in variables
    $bHasArray = false;
    $iMinimalSize = 0;
    foreach($arrResVarData as $key => $item){
      if(isset($this->_vars[$arrResVarName[$key]]) && is_array($this->_vars[$arrResVarName[$key]])){
        $bHasArray = true;
        $iSizeArray = count($this->_vars[$arrResVarName[$key]]);
        $iMinimalSize = ($iMinimalSize == 0 ? $iSizeArray : ($iSizeArray < $iMinimalSize ? $iSizeArray : $iMinimalSize));
      }
    }

    if($bHasArray == false){
      // Render variable simple
      $iNumNoVar = 0;
      foreach($arrResVarData as $key => $item){
        #echo $item.'<br>';
        if(!isset($this->_vars[$arrResVarName[$key]]) && strpos($arrResult[2][$key], 'alt=') === false){
          $iNumNoVar++;
        }
        $psContentToExtract = $this->renderVariable($psContentToExtract, $item, $arrResVarName[$key], $arrResult[2][$key]);
      }

      #echo $iNumNoVar.'-'.count($arrResVarData).'<br />';
      #echo $psContentToExtract.'<br />';
      if($iNumNoVar > 0 && $iNumNoVar == count($arrResVarData) && $this->_renderSectionAutohide == 'yes'){
        $psContentToExtract = '';
      }
    } else {
      // Render variable with multiple arrays
      $psContent = '';
      $iInc = 0;
      while($iInc < $iMinimalSize) {
        $psContentToRepeat = $psContentToExtract;
        // For Each variable found
        foreach($arrResVarData as $key => $item){
          if(isset($this->_vars[$arrResVarName[$key]])){
            if(is_array($this->_vars[$arrResVarName[$key]])){
              $psContentToRepeat = str_replace($item, $this->_vars[$arrResVarName[$key]][$iInc], $psContentToRepeat);
            } else {
              if($arrResVarRepeatScalar[$key] == 'yes'){
                $psContentToRepeat = str_replace($item, $this->_vars[$arrResVarName[$key]], $psContentToRepeat);
              } else {
                if($this->_renderSectionAutohide == 'yes'){
                  $psContent = '';
                  break 2;
                } else {
                  if($iInc == 0){
                    $psContentToRepeat = str_replace($item, $this->_vars[$arrResVarName[$key]], $psContentToRepeat);
                  } else {
                    $psContentToRepeat = str_replace($item, '', $psContentToRepeat);
                  }
                }
              }
            }
          } else {
            if($this->_renderSectionAutohide == 'yes'){
              $psContent = '';
              break 2;
            } else {
              $psContentToRepeat = str_replace($item, '', $psContentToRepeat);
            }
          }
        }
        $psContent .= $psContentToRepeat;
        $iInc++;
      }
      $psContentToExtract = $psContent;
    }

    return $psContentToExtract;
  }

  /**
   *
   * @param string $psContentToExtract
   * @param boolean $bWithParse
   * @return string
   * @todo Manage in the str_replace the translation of the string
   */
  private function extractL10N($psContentToExtract, $bWithParse){
    if($bWithParse == true){
      preg_match_all('#{{text parse="yes"}}(.*?){{/text}}#ms', $psContentToExtract, $arrResult);
    } else {
      preg_match_all('#{{text}}(.*?){{/text}}#ms', $psContentToExtract, $arrResult);
    }
    if(!empty($arrResult[0])){
      #echo '<pre>'.print_r($arrResult, true).'</pre>';
      foreach ($arrResult[1] as $key => $l10n_text){
        $psContentToExtract = str_replace($arrResult[0][$key], $arrResult[1][$key], $psContentToExtract);
      }
    }
    return $psContentToExtract;
  }

  private function extractSectionsChildren($psContentToExtract){
    preg_match_all('#{{sectionChild:([a-zA-Z0-9_]{0,50})}}#', $psContentToExtract, $arrResult);
    if(!empty($arrResult[0])){
      #echo '<pre>'.print_r($arrResult, true).'</pre>';
      foreach ($arrResult[1] as $section){
        if(isset($this->_hiddenSection[$section]) && is_array($this->_hiddenSection[$section])){
          $iLenSection = strlen('{{sectionChild:'.$section.'}}');
          $iPos = strpos($psContentToExtract, '{{sectionChild:'.$section.'}}');
          $iNumSub = 1;
          while($iPos !== false){
            if($iPos !== false){
              $iPosEnd = strpos($psContentToExtract, '{{/sectionChild:'.$section.'}}', $iPos);
              $psContentCleaned = substr($psContentToExtract, 0, $iPos - 1);
              if(!(isset($this->_hiddenSection[$section][$iNumSub]) && $this->_hiddenSection[$section][$iNumSub] == true)){
                $psContentCleaned .= substr($psContentToExtract, $iPos + $iLenSection, $iPosEnd - ($iPos + $iLenSection));
              }
              $psContentCleaned .= substr($psContentToExtract, $iPosEnd + $iLenSection + 1);
              $psContentToExtract = $psContentCleaned;
              $iPos = strpos($psContentToExtract, '{{sectionChild:'.$section.'}}');
              $iNumSub++;
            }
          }
        }
      }
    }
    return $psContentToExtract;
  }

  public function render(){
    if(file_exists($this->_docroot.$this->_template)){
      $psContent = file_get_contents($this->_docroot.$this->_template);
      if($psContent){
        #echo '<pre>'.print_r($this->_vars, true).'</pre>';
        $psContent = $this->renderSection($psContent);

        $psContent = $this->extractInclude($psContent);
        return $psContent;
      }
    }

    return null;
  }
}

<?php

class YATSPHP {
  private $_docroot;
  private $_template;
  private $_vars = array();

  private $_renderSectionAutohide = 'no';
  private $_renderSectionParentLoop = 'no';

  public function define($psFilename, $psDocRoot = null){
    if($psDocRoot){
      $psDocRoot = getcwd().'/';
    }
    if(file_exists($psDocRoot.$psFilename)){
      $this->_docroot = $psDocRoot;
      $this->_template = $psFilename;
      return $this;
    }
    return null;
  }

  public function assign($key, $value = null){
    if(is_null($value) && is_array($key) == true){
      $this->_vars = array_merge($this->_vars, $key);
    } else {
      $this->_vars[$key] = $value;
    }
    return $this;
  }

  public function hide($psSection, $pbState, $piNumRow = null){

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
    // Section : Extract Sub
    $psSection = $this->extractSections($psSection);
    // Section : Rendu
    $psSection = $this->extractVariables($psSection);

    return $psSection;
  }

  private function extractSections($psContentToExtract){
    preg_match_all('#{{section:([a-z]{0,50})\s{0,50}([a-z"=\s]*)}}#', $psContentToExtract, $arrResult);
    if(!empty($arrResult[0])){
      #echo '<pre>'.print_r($arrResult, true).'</pre>';

      $arrResSectionData = $arrResult[0];
      $arrResSectionName = $arrResult[1];
      $arrResSectionParam = $arrResult[2];
      $arrResSectionAutoHide = array();
      $arrResSectionParentLoop = array();
      // Divide params
      foreach($arrResSectionParam as $key => $value){
        $arrResSectionAutoHide[$key] = 'no';
        $arrResSectionParentLoop[$key] = 'no';
        if(!empty($value)){
          $value = explode(' ', $value);
          foreach($value as $itmValue){
            if(substr($itmValue,0,10) == 'autohide="'){
              $arrResSectionAutoHide[$key] = substr($itmValue, 10 , strlen($itmValue) - 11);
            } elseif(substr($itmValue,0,12) == 'parentloop="'){
              $arrResSectionParentLoop[$key] = substr($itmValue, 12 , strlen($itmValue) - 13);
            }
          }
        }
      }

      foreach($arrResSectionData as $keySection => $valSection){
        #echo 'SECTION :'.$valSection.'<br />';
        // Section
        $piPositionStart = strpos($psContentToExtract, $valSection);
        if($piPositionStart !== false){
          $psSection = substr($psContentToExtract, $piPositionStart);
          $psSection = substr($psSection, 0, strpos($psSection, '{{/section:'.$arrResSectionName[$keySection].'}}') + strlen('{{/section:'.$arrResSectionName[$keySection].'}}'));
          // Section : Contenu
          $psSectionContent = substr($psSection, strlen($valSection), strlen($psSection) - strlen($valSection) - strlen('{{/section:'.$arrResSectionName[$keySection].'}}'));
          // Section : Render
          $this->_renderSectionAutohide = $arrResSectionAutoHide[$keySection];
          $this->_renderSectionParentLoop = $arrResSectionParentLoop[$keySection];
          $psSectionContent = $this->renderSection($psSectionContent);

          $psContentToExtract = str_replace($psSection, $psSectionContent, $psContentToExtract);
        }
      }
    }
    return $psContentToExtract;
  }

  private function extractVariables($psContentToExtract){
    preg_match_all('#{{([a-z"=_]{0,50})\s{0,50}([a-z"=\s]*)}}#msi', $psContentToExtract, $arrResult);

    # echo '<pre>'.print_r($arrResult, true).'</pre>';

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
      foreach($arrResVarData as $key => $item){
        $psContentToExtract = $this->renderVariable($psContentToExtract, $item, $arrResVarName[$key], $arrResult[2][$key]);
      }
    } else {
      // Render variable with multiple arrays
      $psContent = '';
      $iInc = 0;
      while($iInc < $iMinimalSize) {
        $psContentToRepeat = $psContentToExtract;
        // For Each variable found
        foreach($arrResVarData as $key => $item){
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
        }
        $psContent .= $psContentToRepeat;
        $iInc++;
      }
      $psContentToExtract = $psContent;
    }

    return $psContentToExtract;
  }

  public function render(){
    $psContent = file_get_contents($this->_docroot.$this->_template);
    if($psContent){
      $psContent = $this->renderSection($psContent);
      return $psContent;
    }
    return null;
  }
}
<?php

class YATSPHP {
  private $_docroot;
  private $_template;
  private $_vars = array();

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
    preg_match_all('#{{section:([a-z]{0,50})}}#', $psContentToExtract, $arrResultSections);
    if(!empty($arrResultSections[0])){
      #echo '<pre>'.print_r($arrResultSections, true).'</pre>';
      foreach($arrResultSections[0] as $keySection => $valSection){
        // Section
        $piPositionStart = strpos($psContentToExtract, $valSection);
        $psSection = substr($psContentToExtract, $piPositionStart);
        $psSection = substr($psSection, 0, strpos($psSection, '{{/section:'.$arrResultSections[1][$keySection].'}}') + strlen('{{/section:'.$arrResultSections[1][$keySection].'}}'));
        // Section : Contenu
        $psSectionContent = substr($psSection, strlen($valSection), strlen($psSection) - strlen($valSection) - strlen('{{/section:'.$arrResultSections[1][$keySection].'}}'));
        // Section : Render
        $psSectionContent = $this->renderSection($psSectionContent);

        $psContentToExtract = str_replace($psSection, $psSectionContent, $psContentToExtract);
      }
    }
    return $psContentToExtract;
  }

  private function extractVariables($psContentToExtract){
    preg_match_all('#{{([a-z"=]{0,50})\s{0,50}([a-z"=]{0,50})}}#msi', $psContentToExtract, $arrResult);

    foreach($arrResult[0] as $key => $item){
      $psContentToExtract = $this->renderVariable($psContentToExtract, $item, $arrResult[1][$key], $arrResult[2][$key]);
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
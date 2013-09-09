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

  public function render(){
    $psContent = file_get_contents($this->_docroot.$this->_template);
    if($psContent){
      preg_match_all('#{{([a-z"=]{0,50})\s{0,50}([a-z"=]{0,50})}}#msi', $psContent, $arrResult);

      foreach($arrResult[0] as $key => $item){
        if(isset($this->_vars[$arrResult[1][$key]])){
          $psContent = str_replace($item, $this->_vars[$arrResult[1][$key]], $psContent);
        } else {
          if(strpos($arrResult[2][$key], 'alt=') === false){
            $psContent = str_replace($item, '', $psContent);
          } else {
            preg_match('#alt="([a-z]{0,100})"#', $arrResult[2][$key], $arrResultAlt);
            $psContent = str_replace($item, $arrResultAlt[1], $psContent);
          }
        }
      }

      echo '<pre>'.print_r($arrResult, true).'</pre>';
      return $psContent;
    }
    return null;
  }
}
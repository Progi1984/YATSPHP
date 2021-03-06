<?php

namespace Progi1984;

/**
 * Class YATSPHP
 * @package Progi1984
 */
class YATSPHP
{
    private $docroot;
    private $template;
    private $searchpath;
    private $vars = array();
    private $hiddenSection = array();
    private $levelImbrication = 0;
    private $renderSectionAutohide = 'no';

    /**
     * @param $psFilename
     * @param null $psDocRoot
     * @param null $psSearchPath
     * @return $this|null
     */
    public function define($psFilename, $psDocRoot = null, $psSearchPath = null)
    {
        if (is_null($psDocRoot) && substr($psFilename, 0, 1) != DIRECTORY_SEPARATOR) {
            $psDocRoot = getcwd().DIRECTORY_SEPARATOR;
        }
        if (!is_null($psDocRoot) && substr($psDocRoot, 0, -1) != DIRECTORY_SEPARATOR) {
            $psDocRoot .= DIRECTORY_SEPARATOR;
        }
        if (!is_null($psSearchPath)) {
            $this->searchpath = $psSearchPath;
            if (substr($this->searchpath, -1) != DIRECTORY_SEPARATOR) {
                $this->searchpath .= DIRECTORY_SEPARATOR;
            }
        }
        if (file_exists($psDocRoot.$psFilename)) {
            $this->docroot = $psDocRoot;
            $this->template = $psFilename;
            return $this;
        } else {
            if (!is_null($this->searchpath)) {
                if (file_exists($this->searchpath.$psFilename)) {
                    $this->template = $this->searchpath.$psFilename;
                    return $this;
                }
            }
        }
        return null;
    }

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function assign($key, $value = null)
    {
        if (is_array($key) == true) {
            $this->vars = array_merge($this->vars, $key);
        } else {
            $this->vars[$key] = $value;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->vars;
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->hiddenSection;
    }

    /**
     * 
     * @param boolean $pbState
     */
    public function hide($psSection, $pbState = null, $piNumRow = null)
    {
        if (is_null($pbState) && is_null($piNumRow)) {
            if (!is_array($psSection)) {
                return false;
            }
            $this->hiddenSection = array_merge($this->hiddenSection, $psSection);
        } else {
            if (!is_bool($pbState)) {
                return false;
            }
            if (is_null($piNumRow)) {
                $this->hiddenSection[$psSection] = $pbState;
            } else {
                if (!isset($this->hiddenSection[$psSection])) {
                    $this->hiddenSection[$psSection] = array();
                }
                $this->hiddenSection[$psSection][$piNumRow] = $pbState;
            }
        }
        return true;
    }

    private function renderVariable($psContent, $psTagContent, $psKey, $psAttributeAlt)
    {
        if (isset($this->vars[$psKey])) {
            if (is_array($this->vars[$psKey])) {
                $psContentToRepeat = $psContent;
                $psContent = '';
                foreach ($this->vars[$psKey] as $value) {
                    $psContent .= str_replace($psTagContent, $value, $psContentToRepeat);
                }
            } else {
                $psContent = str_replace($psTagContent, $this->vars[$psKey], $psContent);
            }
        } else {
            if (strpos($psAttributeAlt, 'alt=') !== false) {
                preg_match('#alt="([a-z]{0,100})"#', $psAttributeAlt, $arrResultAlt);
                $psContent = str_replace($psTagContent, $arrResultAlt[1], $psContent);
            }
        }
        return $psContent;
    }

    private function renderSection($psSection)
    {
        $this->levelImbrication++;
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
        $this->levelImbrication--;
        return $psSection;
    }

    private function extractInclude($psContent)
    {
        preg_match_all('#{{include file="([a-zA-Z0-9_/\.]*)"}}#', $psContent, $arrResult);
        if (!empty($arrResult[0])) {
            #echo '<pre>'.print_r($arrResult, true).'</pre>';
            foreach ($arrResult[1] as $key => $sFileInclude) {
                $oYATS = new YATSPHP();
                $sDocRoot = null;
                if (is_null($this->docroot)) {
                    $sDocRoot = dirname($this->template).DIRECTORY_SEPARATOR;
                } else {
                    if (substr($sFileInclude, 0, 2) == './') {
                        $sDocRoot = dirname($this->template).DIRECTORY_SEPARATOR;
                        $sFileInclude = substr($sFileInclude, 2);
                    } else {
                        $sDocRoot = $this->docroot;
                    }
                }
                $oYATS->define($sFileInclude, $sDocRoot, $this->searchpath);
                $oYATS->assign($this->vars);
                $sInclude = $oYATS->render();
                $psContent = str_replace($arrResult[0][$key], $sInclude, $psContent);
            }
        }
        return $psContent;
    }

    private function extractSections($psContentToExtract)
    {
        preg_match_all('#{{section:([a-zA-Z0-9_]{0,50})\s{0,50}([a-z"=\s]*)}}#', $psContentToExtract, $arrResult);
        if (!empty($arrResult[0])) {
            #echo 'Niveau '.$this->levelImbrication.'<br />';
            #echo '<pre>'.print_r($arrResult, true).'</pre>';
            
            $arrResSectionData = $arrResult[0];
            $arrResSectionName = $arrResult[1];
            $arrResSectionParam = $arrResult[2];
            $arrResSectionAH = array();
            $arrResSectionPL = array();
            $arrResSectionHidden = array();
            // Divide params
            foreach ($arrResSectionParam as $key => $value) {
                $arrResSectionAH[$key] = 'no';
                $arrResSectionPL[$key] = 'no';
                $arrResSectionHidden[$key] = 'no';
                if (!empty($value)) {
                    $value = explode(' ', $value);
                    foreach ($value as $itmValue) {
                        if (substr($itmValue, 0, 10) == 'autohide="') {
                            $arrResSectionAH[$key] = substr($itmValue, 10, strlen($itmValue) - 11);
                        } elseif (substr($itmValue, 0, 12) == 'parentloop="') {
                            $arrResSectionPL[$key] = substr($itmValue, 12, strlen($itmValue) - 13);
                        } elseif (substr($itmValue, 0, 8) == 'hidden="') {
                            $arrResSectionHidden[$key] = substr($itmValue, 8, strlen($itmValue) - 9);
                        }
                    }
                }
            }
            
            foreach ($arrResSectionData as $keySection => $valSection) {
                #echo 'SECTION ('.$this->levelImbrication.'):'.$valSection.'<br />';
                #echo '<br />';
                // Section
                $piPositionStart = strpos($psContentToExtract, $valSection);
                if ($piPositionStart !== false) {
                    $psSection = substr($psContentToExtract, $piPositionStart);
                    $psSectionName = $arrResSectionName[$keySection];
                    $psTagSectionEnd = '{{/section:'.$psSectionName.'}}';
                    $psSection = substr($psSection, 0, strpos($psSection, $psTagSectionEnd) + strlen($psTagSectionEnd));

                    // Section Hidden
                    if (// If the parameter hidden = yes && no hide asked
                        ($arrResSectionHidden[$keySection] == 'yes'
                            && !isset($this->hiddenSection[$psSectionName]))
                        // If hide is asked
                        || (isset($this->hiddenSection[$psSectionName])
                            && is_bool($this->hiddenSection[$psSectionName])
                            && $this->hiddenSection[$psSectionName] == true)
                    ) {
                        $psSectionContent = '';
                    } else {
                        // Section : Contenu
                        $iLength = strlen($psSection) - strlen($valSection) - strlen($psTagSectionEnd);
                        $psSectionContent = substr($psSection, strlen($valSection), $iLength);
                        // Section : Render
                        $this->renderSectionAutohide = $arrResSectionAH[$keySection];
                        if ($arrResSectionPL[$keySection] == 'no') {
                            $psSectionContent = $this->renderSection($psSectionContent);
                        } else {
                            if (isset($this->hiddenSection[$psSectionName])
                                && is_array($this->hiddenSection[$psSectionName])) {
                                $psSectionContent = '{{sectionChild:'.$psSectionName.'}}';
                                $psSectionContent .= $psSectionContent;
                                $psSectionContent .= '{{/sectionChild:'.$psSectionName.'}}';
                            }
                        }
                    }
                    
                    $psContentToExtract = str_replace($psSection, $psSectionContent, $psContentToExtract);
                }
            }
        }
        return $psContentToExtract;
    }

    private function extractVariables($psContentToExtract)
    {
        preg_match_all('#{{(?!text)([a-z0-9"=_]{0,50})\s{0,50}([a-z"=\s]*)}}#msi', $psContentToExtract, $arrResult);
        
        #echo '<pre>'.print_r($arrResult, true).'</pre>';
        $arrResVarData = $arrResult[0];
        $arrResVarName = $arrResult[1];
        $arrResVarParam = $arrResult[2];
        $arrResVarAlt = array();
        $arrResVarRS = array();
        // Divide params
        foreach ($arrResVarParam as $keyParam => $valParam) {
            $arrResVarAlt[$keyParam] = '';
            $arrResVarRS[$keyParam] = 'no';
            if (!empty($valParam)) {
                $valParam = explode(' ', $valParam);
                foreach ($valParam as $itmValParam) {
                    if (substr($itmValParam, 0, 5) == 'alt="') {
                        $arrResVarAlt[$keyParam] = substr($itmValParam, 5, strlen($itmValParam) - 6);
                    } elseif (substr($itmValParam, 0, 14) == 'repeatscalar="') {
                        $arrResVarRS[$keyParam] = substr($itmValParam, 14, strlen($itmValParam) - 15);
                    }
                }
            }
        }
        
        // If array in variables
        $bHasArray = false;
        $iMinSize = 0;
        foreach ($arrResVarData as $key => $item) {
            if (isset($this->vars[$arrResVarName[$key]]) && is_array($this->vars[$arrResVarName[$key]])) {
                $bHasArray = true;
                $iSizeArray = count($this->vars[$arrResVarName[$key]]);
                $iMinimalSize = ($iMinSize == 0 ? $iSizeArray : ($iSizeArray < $iMinSize ? $iSizeArray : $iMinSize));
            }
        }
        
        if ($bHasArray == false) {
            // Render variable simple
            $iNumNoVar = 0;
            foreach ($arrResVarData as $key => $item) {
                if (!isset($this->vars[$arrResVarName[$key]]) && strpos($arrResult[2][$key], 'alt=') === false) {
                    $iNumNoVar++;
                }
                $nameVar = $arrResVarName[$key];
                $altVar = $arrResult[2][$key];
                $psContentToExtract = $this->renderVariable($psContentToExtract, $item, $nameVar, $altVar);
            }
            
            #echo $iNumNoVar.'-'.count($arrResVarData).'<br />';
            #echo $psContentToExtract.'<br />';
            if ($iNumNoVar > 0 && $iNumNoVar == count($arrResVarData) && $this->renderSectionAutohide == 'yes') {
                $psContentToExtract = '';
            }
        } else {
            // Render variable with multiple arrays
            $psContent = '';
            $iInc = 0;
            while ($iInc < $iMinimalSize) {
                $psContentToRepeat = $psContentToExtract;
                // For Each variable found
                foreach ($arrResVarData as $key => $item) {
                    $valVar = $this->vars[$arrResVarName[$key]];
                    if (isset($valVar)) {
                        if (is_array($valVar)) {
                            $psContentToRepeat = str_replace($item, $valVar[$iInc], $psContentToRepeat);
                        } else {
                            if ($arrResVarRS[$key] == 'yes') {
                                $psContentToRepeat = str_replace($item, $valVar, $psContentToRepeat);
                            } else {
                                if ($this->renderSectionAutohide == 'yes') {
                                    $psContent = '';
                                    break 2;
                                } else {
                                    if ($iInc == 0) {
                                        $psContentToRepeat = str_replace($item, $valVar, $psContentToRepeat);
                                    } else {
                                        $psContentToRepeat = str_replace($item, '', $psContentToRepeat);
                                    }
                                }
                            }
                        }
                    } else {
                        if ($this->renderSectionAutohide == 'yes') {
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
    private function extractL10N($psContentToExtract, $bWithParse)
    {
        if ($bWithParse == true) {
            preg_match_all('#{{text parse="yes"}}(.*?){{/text}}#ms', $psContentToExtract, $arrResult);
        } else {
            preg_match_all('#{{text}}(.*?){{/text}}#ms', $psContentToExtract, $arrResult);
        }
        if (!empty($arrResult[0])) {
            #echo '<pre>'.print_r($arrResult, true).'</pre>';
            foreach ($arrResult[1] as $key => $l10n_text) {
                $psContentToExtract = str_replace($arrResult[0][$key], $l10n_text, $psContentToExtract);
            }
        }
        return $psContentToExtract;
    }

    private function extractSectionsChildren($psContentToExtract)
    {
        preg_match_all('#{{sectionChild:([a-zA-Z0-9_]{0,50})}}#', $psContentToExtract, $arrResult);
        if (!empty($arrResult[0])) {
            #echo '<pre>'.print_r($arrResult, true).'</pre>';
            foreach ($arrResult[1] as $section) {
                if (isset($this->hiddenSection[$section]) && is_array($this->hiddenSection[$section])) {
                    $iLenSection = strlen('{{sectionChild:'.$section.'}}');
                    $iPos = strpos($psContentToExtract, '{{sectionChild:'.$section.'}}');
                    $itmSection = $this->hiddenSection[$section];
                    $iNumSub = 1;
                    while ($iPos !== false) {
                        if ($iPos !== false) {
                            $iPosEnd = strpos($psContentToExtract, '{{/sectionChild:'.$section.'}}', $iPos);
                            $psContentCleaned = substr($psContentToExtract, 0, $iPos - 1);
                            if (!(isset($itmSection[$iNumSub]) && $itmSection[$iNumSub] == true)) {
                                $iStart = $iPos + $iLenSection;
                                $psContentCleaned .= substr($psContentToExtract, $iStart, $iPosEnd - $iStart);
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

    public function render($locale = null, $gettext_domain = null, $gettext_dir = null)
    {
        if (!is_null($locale) && !is_null($gettext_domain) && !is_null($gettext_dir)) {
            putenv('LC_ALL='.$locale);
            setlocale(LC_ALL, $locale);
            bindtextdomain($gettext_domain, $gettext_dir);
            textdomain($gettext_domain);
        }
        
        if (file_exists($this->docroot.$this->template)) {
            $psContent = file_get_contents($this->docroot.$this->template);
            if ($psContent) {
                #echo '<pre>'.print_r($this->vars, true).'</pre>';
                $psContent = $this->renderSection($psContent);
                
                $psContent = $this->extractInclude($psContent);
                return $psContent;
            }
        }
        
        return null;
    }
}

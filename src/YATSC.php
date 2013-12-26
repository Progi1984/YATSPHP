<?php
if (!extension_loaded('yats')) {
    /**
     * @param string $filename
     * @param string docroot
     * @param string searchpath
     * @return YATSPHP
     */
    function yats_define($filename, $docroot = null, $searchpath = null)
    {
      $oYATS = new YATSPHP();
      $oYATS->define($filename, $docroot, $searchpath);
      return $oYATS;
    }

    /**
     *
     * @param YATSPHP $template
     * @param string|array $var
     * @param  $value
     * @return boolean
     */
    function yats_assign($template, $var, $value = null)
    {
      return $template->assign($var, $value);
    }

    /**
     * @param YATSPHP $template
     * @param string $locale
     * @param string $gettext_domain
     * @param string $gettext_dir
     * @return string
     */
    function yats_getbuf($template, $locale = null, $gettext_domain = null, $gettext_dir = null)
    {
      return $template->render();
    }

    /**
     * @param YATSPHP $template
     * @param string $section
     * @param boolean $hide
     * @param integer $row
     * @return boolean
     */
    function yats_hide($template, $section, $hide, $row = null)
    {
      return $template->hide($section, $hide, $row);
    }

    /**
     * @param YATSPHP $template
     * @return array
     */
    function yats_getvars($template)
    {
      //return $template
    }
}

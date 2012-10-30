<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple optimized function that detect the mime type of given file.
 *
 * @version   0.1.2
 * @since     0.1.2
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC-BY-NC-SA 3.0
 */
namespace Hestia\Tools;

final class CompileLessCssJs
{
    const LessCssJsAll = 0;
    const LessCssAll = 1;
    const CssJsAll = 2;
    const LessAll = 3;
    const CssAll = 4;
    const JsAll = 5;

    public function __construct($query = null, $path = null, $ignored = null)
    {
        if($query == LessCssJsAll || $query == LessCssAll || $query == LessAll)
        {
            if(!defined('APPLICATION_PATH_TEMPLATE_STYLES') && $path == null)
                throw new \InvalidArgumentException('Path empty and constant path undefined.');

            $dirname = ($path == null)?APPLICATION_PATH_TEMPLATE_STYLES:$path;
            $files = scanDirRecurse($dirname);

            foreach($files as $file)
            {
                if($file != '.' && $file != '..' && !is_dir($dirname.$file) && ($ignored == null || !in_array($file, $ignored)))
                {
                    preg_match("|(.*)\.([a-z0-9]{2,4})$|i", $file, $filePart);
                    if(strtolower($filePart[2]) == 'less') $this->CompileLess($file, $filePart[1] . '.css');
                }
            }
        }
        if($query == LessCssJsAll || $query == LessCssAll || $query == CssAll)
        {
            if(!defined('APPLICATION_PATH_TEMPLATE_STYLES') && $path == null)
                throw new \InvalidArgumentException('Path null and constant path undefined.');

            $dirname = ($path == null)?APPLICATION_PATH_TEMPLATE_STYLES:$path;
            $files = scanDirRecurse($dirname);

            $cssFiles = array();

            foreach($files as $file)
            {
                if($file != '.' && $file != '..' && !is_dir($dirname.$file) && ($ignored == null || !in_array(basename($file), $ignored)))
                {
                    preg_match("|(.*)\.([a-z0-9]{2,4})$|i", $file, $filePart);
                    if(strtolower($filePart[2]) == 'css') $cssFiles[] = $file;
                }
            }

            $this->CompileCss($dirname, $cssFiles);
        }
        if($query == LessCssJsAll || $query == CssJsAll || $query == JsAll)
        {
            if(!defined('DEFAULT_JAVASCRIPT_FILENAME') && $path == null)
                throw new \InvalidArgumentException('Path null and constant path undefined.');

            $dirname = ($path == null)?APPLICATION_PATH_TEMPLATE_JAVASCRIPTS:$path;

            $files = scanDirRecurse($dirname);

            $jsFiles = array();

            foreach($files as $file)
            {
                if($file != '.' && $file != '..' && !is_dir($dirname.$file) && ($ignored == null || !in_array(basename($file), $ignored)))
                {
                    preg_match("|(.*)\.([a-z0-9]{2,4})$|i", $file, $filePart);
                    if(strtolower($filePart[2]) == 'js') $jsFiles[] = $file;
                }
            }

            $this->CompileJs($dirname, $jsFiles);
        }
    }

    public function CompileCss($dirname, $input, $output = null)
    {
        if($output == null && defined('DEFAULT_CSS_FILENAME')) $output = $dirname . DEFAULT_CSS_FILENAME;
        elseif($output == null) $output = $dirname . 'Style.css';

        $correctInput = array();

        for($i = 0; $i < count($input); $i++)
        {
            if($input[$i] != $output) $correctInput[] = $input[$i];
        }

        set_include_path(TOOLS_PATH . 'Minify/');
        require_once 'Minify.php';

        $options = array
        (
            'files' => $correctInput,
            'quiet' => true,
            'encodeOutput' => false,
        );

        $minifiedResponse = \Minify::serve('Files', $options);

        $minifiedCssCode = $minifiedResponse['content'];


        try{
            $minifiedCssFile = fopen($output, 'w');
            fwrite($minifiedCssFile, $minifiedCssCode);
        }
        catch(exeption $e){
            echo 'Problem with writing the minified css file : ',  $e->getMessage(), "\n";

        }

        set_include_path(__FILE__);
    }

    public function CompileJs($dirname, $input, $output = null)
    {
        if($output == null && defined('DEFAULT_JAVASCRIPT_FILENAME')) $output = $dirname . DEFAULT_JAVASCRIPT_FILENAME;
        elseif($output == null) $output = $dirname . 'JavaScript.css';

        $correctInput = array();

        for($i = 0; $i < count($input); $i++)
        {
            if($input[$i] != $output) $correctInput[] = $input[$i];
        }


        set_include_path(TOOLS_PATH . 'Minify/');
        require_once 'Minify.php';

        $options = array
        (
            'files' => $correctInput,
            'quiet' => true,
            'encodeOutput' => false,
        );


        $minifiedResponse = \Minify::serve('Files', $options);

        $minifiedJsCode = $minifiedResponse['content'];

        try{
            $minifiedCssFile = fopen($output, 'w');
            fwrite($minifiedCssFile, $minifiedJsCode);
        }
        catch(exeption $e){
            echo 'Problem with writing the minified js file : ',  $e->getMessage(), "\n";

        }

        set_include_path(__FILE__);

    }

    public function CompileLess($input, $output = null)
    {
        require_once TOOLS_PATH . 'LessToCss.php';

        if($output == null)
        {
            preg_match("|(.*)\.([a-z0-9]{2,4})$|i", $input, $filePart);
            $output = $filePart[1] . '.css';
        }

        try
        {
\            lessc::ccompile($input, $output);
        }
        catch (exception $ex)
        {
            exit('lessc fatal error:<br />'.$ex->getMessage());
        }
    }
}
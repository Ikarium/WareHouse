<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Template Engine
 *
 * Implement the layout, and generate the argument like each module view, and others,
 * manage the vues variable.
 * 
 * @version   0.0.5
 * @since     0.0.3
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC BY-NC-SA 3.0  
*/

namespace Hestia\Core;

final class Page extends ApplicationComponent implements \Hestia\Tools\ISingleton
{
    ///Path to the layout
    private $layout;
    
    ///List of content of each area.
    private $areasContentPath = array();
    
    ///List of variable needed for the vue
    private $vueVars = array();

    /// Singletron function
    public static function singletron($app)
    {
        static $Page = null;
        if ( $Page == null ) $Page = new Page($app);
        else throw new \InvalidArgumentException('Triying to implement twice the singltron class: ' . __CLASS__);
        return $Page;
    }
    
    /**
     * Simple constructor
     *
     * Set the default template if function templatesManager is non-defined
     * and set the layout to his default value.
     */
    protected final function __construct($app)
    {
        parent::__construct($app);
        
        if (function_exists ('templatesManager'))
        {
            configureTemplate(templatesManager(), $this->app->Router()->domain());
        }
        else//Default template
        {
            configureTemplate(DEFAULT_TEMPLATE, $this->app->Router()->domain());
        }
        
        
        $this->layout = APPLICATION_PATH_TEMPLATE . DEFAULT_LAYOUT;//Default layout
    }
    
    /**
     * Getter methods.
     *
     * @{
     */
    final public function layout()
    {
        return $this->layout;
    }
    
    final public function areasContentPath()
    {
        return $this->areasContentPath;
    }
    
    final public function vueVars()
    {
        return $this->vueVars;
    }
    /**@}*/
    
    /**
     * Set a new layout, with a vaidity test.
     */
    public final function setLayout($layout)
    {
        //Simple validity test.
        if (!file_exists(APPLICATION_PATH_TEMPLATE . $layout))
        {
            throw new \InvalidArgumentException('Layout: "' . APPLICATION_PATH_TEMPLATE . $layout . '" not found.<br/><br/>');
        }

        $this->layout = APPLICATION_PATH_TEMPLATE . $layout;
    }
    
    /**
     * Set a view variable.
     */
    public final function addVar($var, $value)
    {
        //Simple validity test.
        if (!is_string($var) || is_numeric($var) || empty($var))
        {
            throw new \InvalidArgumentException('Invalide variable in function addVar (Page.class).<br/><br/>');
        }

        $this->vueVars[$var] = $value;
    }

    /**
     * Add a view to the page.
     */
    public final function setContentPath($area, $areaContentPath)
    {
        //File existance test.
        if (!file_exists($areaContentPath))
        {
            throw new \RuntimeException('The view: ' . $areaContentPath . ' for the area : ' . $area . ' doesn\'t existe.<br/><br/>');
        }
        $this->areasContentPath[$area] = $areaContentPath;
        
    }

    /**
     * Generate the final page.
     */
    public final function getGeneratedPage()
    {
            $vueVars = $this->vueVars();
            
           //Beacause we have mulpiple controller (one for each Area), we have multiple view.
           foreach($this->areasContentPath as $area => $areaContentPath)
           {
                //We put the view content in a cache.
                ob_start();
                    require_once $areaContentPath;
                $content[$area] = ob_get_clean();//Then we add it in our contents, ho will be implemented in the layout.
           }
           
        //After that, we implement the layout ho will use the content array to implement each view in specified Area.
        ob_start();
        
            require_once $this->layout;
        return ob_get_clean();
    }
}
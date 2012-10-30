<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Abstraction of each Module Controller, used in the application.
 *
 * Each module have a name and area as main atribute.
 * 
 * @version   0.0.5
 * @since     0.0.3
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC BY-NC-SA 3.0  
*/

namespace Hestia\Core;

abstract class BackController extends ApplicationComponent
{
    protected $name;
    
    protected $midController;/// The parent controller
    
    protected $area;/// The associated area.
    
    protected $vuePath;/// Path to the associated vue
    

    /**
     * Set the mid controllers the name and invoke the user constructor.
     *
     * @param Application   $app
     * @param MidController $midController
     */
    final public function __construct(Application $app, MidController $midController)
    {
        parent::__construct($app);
        
        $this->midController = $midController;
        
        $this->name = get_class($this);
        
        $this->construct();
    }
    
    //User constructor, to give possibility to implement code in the parent contructor (BackController->_construct() )
    protected function construct() {}
    
    /**
     * Getter methods.
     *
     * @{
     */
    final public function name()
    {
        return $this->name;
    }
    
    final public function midController()
    {
        return $this->midController;
    }
    
    final public function area()
    {
        return $this->area;
    }
    
    final public function vuePath()
    {
        return $this->vuePath;
    }
    /**@}*/
    
    /**
     * Shortcuts function.
     *
     * @{
     */
    
    //Add Vue Var
    final protected function avv($var, $value)
    {
        $this->app->Page()->addVar($var, $value);
    }

    final protected function router()
    {
        return $this->app->router();
    }
    
    //Script end here
    final protected function PageNotFound()
    {
        $this->app->Response()->pageNotFound();
    }
    
    /**@}*/
    
    //Default execution, just set the view, if empty and if default vues are enable, set this one.
    public function run()
    {
        if(empty($this->vuePath) && SETTING_DEFAULT_VUE) $this->vuePath = 
            APPLICATION_PATH_TEMPLATE . $this->name() . EXTENTION_TEMPLATES;
        $this->app->Page()->setContentPath($this->area, $this->vuePath);
    }
    
}
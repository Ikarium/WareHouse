<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * The Mid Controller
 *
 * Used to link each module of some branch
 * 
 * @version   0.0.5
 * @since     0.0.5
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC-BY-NC-SA 3.0 
*/

namespace Hestia\Core;

abstract class MidController extends ApplicationComponent
{
    private $name;
    private $subDomain; /// The pased sub domain, if exist, like Domain.SunDomain.Uri.xxx
    private $modules; /// Fined in run function
    
    ///Set the name, the sub domain and laucj the user constructor
    final public function __construct($app)
    {
        parent::__construct($app);
        
        $this->name = get_class($this);
        $this->subDomain = $this->app->Router()->subDomain();
        
        $this->construct();
    }
    
    //User constructor, to give possibility to implement code in the parent contructor (MidController->_construct() )
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
    
    final public function subDomain()
    {
        return $this->subDomain;
    }
    /**@}*/
    
    /**
     * The domain controller runing function, run the necessary module.
     *
     * Manage possibility of change remaining module by one of them,
     * and persistant module (if enable), default module are managed by Request.class
     * 
     */
    function run()
    {
        $requestedModule = $this->app->Router()->module();//Set the requested module 
        
        $this->modules = appSetDefaultModules();//Load the default modules
        //Test if the requested module exist
        if (file_exists(APPLICATION_PATH_MODULE . $requestedModule . EXTENTION_CONTROLLERS))
        {
            require_once APPLICATION_PATH_MODULE . $requestedModule . EXTENTION_CONTROLLERS;
            
            $requestedModulController = new $requestedModule($this->app, $this);
                $this->modules[$requestedModulController->area()] = $requestedModule;
            $requestedModulController->run();//Here he can change the remaining module that will be executed
        }
        else//If not, send a 404 error
        {
            $this->app->Response()->pageNotFound();//Script stop here !
        }
        
        //Exec the remaining modules
        foreach ($this->modules as $module)//Execute all other module, one for each area.
        {
            $moduleController = APPLICATION_PATH_DOMAIN . $module . '/' . $module . EXTENTION_CONTROLLERS;
            
            //First part of the condition needed just for save some time
            if ($module != $requestedModule && file_exists($moduleController))
            {
                require_once $moduleController;
                
                $additionalsModuleController = new $module($this->app, $this);
                
                $additionalsModuleController->run();
            }
        }
    }
    
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
    
    //Script end here
    final protected function notFound()
    {
        $this->app->Response()->pageNotFound();
    }
    
    /**@}*/
}
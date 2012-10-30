<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Find the needed controllers.
 *
 * Sample of fullest possibsle url is:
 * http://www.branch.subBranch.Uri.xxx/module/action/parameter1/parameter2...
 * nothing is required.
 * 
 * @version   0.0.5
 * @since     0.0.2
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC BY-NC-SA 3.0  
*/

namespace Hestia\Core;

final class Router extends ApplicationComponent implements \Hestia\Tools\ISingleton
{
    private $domain;
        private $initialDomain; ///In case of domain redirection, we store here his initial value
    private $subDomain;
    private $module;
        private $initialModule; ///In case of module redirection, we store here his initial value
    private $action;
    private $parameters;
    
    /// Singleton function
    public static function singletron($app)
    {
        static $Router = null;
        if ( $Router == null ) $Router = new Router($app);
        else exit('Triying to implement twice the singltron class: ' . __CLASS__);
        return $Router;
    }
    
    protected function __construct($app)
    {
        parent::__construct($app);
    
        $this->URLAnalyser();
        
    }
    
    /**
     * Getter methods.
     *
     * @{
     */
    public function domain()
    {
        return $this->domain;
    }
    
    public function initialDomain()
    {
        return $this->initialDomain;
    }
    
    public function subDomain()
    {
        return $this->subDomain;
    }
    
    public function module()
    {
        return $this->module;
    }
    
    public function initialModule()
    {
        return $this->initialModule;
    }
    
    public function action()
    {
        return $this->action;
    }
    
    public function parameters()
    {
        return $this->parameters;
    }
    /**@}*/
    
    /*
     * Extract information from the url
     * Optionally, set the persistant domain and/or the persistant module
     * in case if the requested value are false, and a persistant domain/module is defined.
    */
    private function URLAnalyser()
    {
        /*Url basic analyser*/
        $url = $this->app->Request()->requestURL();
        $urlDatas = preg_filter ('#(http\://)?(www\.)?((.[^/\. ]*)\.)?((.[^/\. ]*)\.)?' . APPLICATION_URI . '(/(.[^/ ]*))?(/(.[^/ ]*)?)?(/(.[^ ]*)?)?#i', '$4 $6 $8 $10 $12', $url);
        $urlDatas = explode(" ", $urlDatas);
    
        /*Domain & sub domain analyser*/
        //If there are a subBranch, like A.B.site.com, we choose B as the main branch, and A as the sub-branch
        if($urlDatas[1] != '')
        {
            $this->domain = $urlDatas[1];
            $this->subDomain = $urlDatas[0];
        }
        elseif($urlDatas[0] != '')
        {
            $this->domain = $urlDatas[0];
            $this->subDomain = '';
    
        }
        else
        {
            $this->domain = DEFAULT_DOMAIN;
            $this->subDomain = '';
    
        }

        //If the PERSISTANT_DOMAIN aren't empty and the domain innexist
        $application_path_domain = APPLICATION_PATH_DOMAINS . $this->domain . '/';//From conf file, coreDefineDomain
        if (PERSISTANT_DOMAIN != '' && PERSISTANT_DOMAIN != null
                && !file_exists($application_path_domain . $this->domain . EXTENTION_CONTROLLERS))
        {
            $this->initialDomain = $this->domain;
            $this->domain = PERSISTANT_DOMAIN;
        }
    
        configureDomain($this->domain);//Define the domaine configuration
    
    
        /*Module analyser*/
        $this->module =  ($urlDatas[2] != '')?$urlDatas[2]:DEFAULT_MODULE;//If empty, set the default module
    
        $application_path_module = APPLICATION_PATH_DOMAIN . $urlDatas[2] . '/';//From conf file, coreDefineModule
        $persistant_module = PERSISTANT_MODULE;
        //if no module are asked, we plug the default one.
        if ($urlDatas[2] == '')
        {
            $this->module = DEFAULT_MODULE;
        }
        //if the module exist and are valide(is a primary one) we plug it
        elseif (file_exists($application_path_domain . $urlDatas[2] . EXTENTION_CONTROLLERS)//If the domain exist
                && in_array($this->module(), appSetPrimaryModules()))//And the module is a primary one
        {
            $this->module = $urlDatas[2];
        }
        //If not, and if a persistent module are defined, we plug it.
        elseif (!empty($persistant_module))//If the PERSISTANT_DOMAIN aren't empty
        {
            $this->initialModule = $this->module;
            $this->module = $persistant_module;
        }
    
        configureModule($this->module);//Define the module configuration
    
    
        /*Action & params analyser*/
        $this->action = $urlDatas[3];
        $this->parameters = $urlDatas[4];
    }
}
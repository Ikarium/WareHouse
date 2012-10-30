<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Manage the application.
 *
 * Specially the http connection, database connection, user management,
 * router launching, template engine and application execution.
 *
 * @version   0.0.5
 * @since     0.0.2
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC BY-NC-SA 3.0
*/

namespace Hestia\Core;

abstract class Application
{
    protected $name; ///Application name

    protected $Request; ///HTTP Request Reader
    protected $Router; /// Router, URL Analyser
    protected $Response; /// HTTP Response Crafting
    protected $DataBase; /// Database Factory
    protected $User; /// User Management
    protected $Page; /// Template Engine

    /**
     * Initialise the main object.
     *
     * As they are a ApplicationComponent (inheritance),
     * we need to pass the applicartion as a parameter to they parent constructor. See ApplicationComponent.class
     *
     * @param string $name Application name.
     */
    protected function __construct($name)
    {
        require_once 'Core.conf';

        //Start the session
        Session_start();

        //Set the name
        $this->name = $name;
            //Define application general configuration
            configureApplication($this->name);//Require to be included after the name resolution.

        //Initialise members
        $this->Request = Request::singletron($this);
        $this->Router = Router::singletron($this);
        $this->Response = Response::singletron($this);
        $this->DataBase = DataBase::singletron($this);
        $this->User = User::singletron($this);
        $this->Page = Page::singletron($this);
    }



    /**
     * Getter methods.
     *
     * @{
    */
    public final function name()
    {
        return $this->name;
    }

    public final function Request()
    {

        return $this->Request;
    }

    public final function Router()
    {

        return $this->Router;
    }

    public final function Response()
    {
        return $this->Response;
    }

    public final function DataBase()
    {
        return $this->DataBase;
    }

    public final function User()
    {
        return $this->User;
    }

    public final function Page()
    {
        return $this->Page;
    }
    /**@}*/


    /**
     * Launch the application:
     *
     * Default construction, test if the domain controller exist,
     * then run it, if not: page not found.
     */
    public function run()
    {
        $domain = $this->Router()->domain();

        if (file_exists(APPLICATION_PATH_DOMAIN . $domain . EXTENTION_CONTROLLERS))
        {
            require_once APPLICATION_PATH_DOMAIN . $domain . EXTENTION_CONTROLLERS;

            $midController = new $domain($this);
            $midController->run();
        }
        else
        {
            $this->Response()->pageNotFound();//Script stop here !
        }

        //Send the generated page.
        $this->Response->send();
    }
}
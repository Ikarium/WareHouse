<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Manage the HTTP response.
 *
 * Create a cockie, redirection, header and co.
 * 
 * @version   0.0.2
 * @since     0.0.2
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC BY-NC-SA 3.0  
*/

namespace Hestia\Core;

final class Response extends ApplicationComponent implements \Hestia\Tools\ISingleton
{

    /// Singletron function
    public static function singletron($app)
    {
        static $Response = null;
        if ( $Response == null ) $Response = new Response($app);
        else throw('Triying to implement twice the singltron class: ' . __CLASS__);
        return $Response;
    }
    
    final protected function __construct($app)
    {
        parent::__construct($app);
    }
    
    
    /**
     * Add a information to a header.
     */
    public final function addHeader($header)
    {
        header($header);
    }

    
    /**
     * Redirect the user o another page.
     */
    public final function redirect($location)
    {
        header('Location: ' . $location);
        exit;
    }

    
    /**
     * The 404 errors redirections.
     * 
     * External show mean if they whole page will be used to show the error
     * or if the error will just be showed in the main area of the seted layout.
     */
    public final function pageNotFound(bool $externalShow = null)
    {   
        if($externalShow) 
        {
            $this->app->page()->setLayout('404' . EXTENTION_LAYOUTS);
            
            $this->addHeader('HTTP/1.0 404 Not Found');
            
            $this->send();
            
        }   
        else 
        {  
            $path = APPLICATION_PATH_TEMPLATE . '404.html';
            
            $this->app->page()->setContentPath(MAIN_AREA, $path);
            
            $this->addHeader('HTTP/1.0 404 Not Found');
            
            $this->send();
        }
    }
    
    /**
     * Store a cookie.
     *
     * The last parameter is set to true, additional security.    
     */
    public final function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }
    
    
    /**
     * Sendt the generated page then exit
     */
    public final function send()
    {
        exit($this->app->page()->getGeneratedPage());
    }
}
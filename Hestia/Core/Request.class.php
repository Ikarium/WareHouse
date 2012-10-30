<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Manage the request information.
 *
 * Specially: POST, GET and Cookies
 * 
 * @version   0.0.5
 * @since     0.0.2
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC BY-NC-SA 3.0  
*/

namespace Hestia\Core;

final class Request extends ApplicationComponent implements \Hestia\Tools\ISingleton
{
    private $requestURI;

    /// Singletron function
    public static function singletron($app)
    {
        static $Request = null;
        if ( $Request == null ) $Request = new Request($app);
        else exit('Triying to implement twice the singltron class: ' . __CLASS__);
        return $Request;
    }
    
    protected function __construct($app)
    {
        parent::__construct($app);
    
        $this->requestURI = $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Getter methods.
     *
     * @{
     */
    public function requestURI()
    {
        return $this->requestURI;
    }
    /**@}*/
    
    /**
     * Check a cookie existance.
     */
    public function cookieExists($key)
    {
        return isset($_COOKIE[$key]);
    }
    
    /**
     * Return requested coockie.
     */
    public function cookieData($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }
    
    /**
     * Check a GET variable existance.
     */
    public function getExists($key)
    {
        return isset($_GET[$key]);
    }
    
    /**
     * Return a GET variable.
     */
    public function getData($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }
    
    /**
     * Check a POST variable existance
     */
    public function postExists($key)
    {
        return isset($_POST[$key]);
    }

    /**
     * Return a POST variable.
     */
    public function postData($key)
    {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    /**
     * Return the reuest's URL
     */
    public function requestURL()
    {
        return $_SERVER['SERVER_NAME'] . $this->requestURI();
    }
    
}
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Basic user informations manager.
 *
 * Working with the Globar array $_SESSION
 * 
 * @version   0.0.3
 * @since     0.0.3
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC BY-NC-SA 3.0  
*/

namespace Hestia\Core;

class User extends ApplicationComponent implements \Hestia\Tools\ISingleton
{
    
    /// Singletron function
    public static function singletron($app)
    {
        static $User = null;
        if ( $User == null ) $User = new User($app);
        else throw('Triying to implement twice the singltron class: ' . __CLASS__);
        return $User;
    }
    
    protected function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * Pseudo setter, work whit the session global variable of course.
     */
    public function setAttribute($attr, $value)
    {
        
        if (!is_string($attr) || empty($attr))
        {
            throw new InvalidArgumentException('The attribute name msut be a string.');
        }
        
        $_SESSION[$attr] = $value;
    }
    
    /**
     * Pseudo getter, work whit the session global variable of course.
     */
    public function getAttribute($attr)
    {
        return isset($_SESSION[$attr]) ? $_SESSION[$attr] : null;
    }

    /**
     * Simple return true or false.
     */
    public function isAuthenticated()
    {
        return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
    }

    /**
     * Set the autentification true or false.
     */
    public function setAuthenticated($authenticated = true)
    {
        //Simple verification
        if (!is_bool($authenticated))
        {
            throw new InvalidArgumentException('The value specified to setAuthenticated() must be a boolean.');
        }

        $_SESSION['auth'] = $authenticated;
    }
}
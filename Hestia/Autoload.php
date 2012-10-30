<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Automatiquely load the library class, and include the tools functions.
 *
 * Using the spl_autoload
 * 
 * @version   0.0.5
 * @since     0.0.2
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC BY-NC-SA 3.0  
*/

namespace Hestia
{
    //The autoload class...
    function autoload($class)
    {
        $classes = array (
                'Hestia\Core\Application' => CORE_PATH . 'Application.class.php', 
                'Hestia\Core\ApplicationComponent' => CORE_PATH . 'ApplicationComponent.class.php', 
                'Hestia\Core\Request' => CORE_PATH . 'Request.class.php', 
                'Hestia\Core\Response' => CORE_PATH . 'Response.class.php', 
                'Hestia\Core\DataBase' => CORE_PATH . 'DataBase.class.php', 
                'Hestia\Core\Router' => CORE_PATH . 'Router.class.php',
                'Hestia\Core\MidController' => CORE_PATH . 'MidController.class.php',
                'Hestia\Core\BackController' => CORE_PATH . 'BackController.class.php',
                'Hestia\Core\Page' => CORE_PATH . 'Page.class.php',
                'Hestia\Core\User' => CORE_PATH . 'User.class.php'
        );
        
        if(isset($classes[$class])) require_once $classes[$class];
    }
    
    //Requesting the tools functions:

    require_once TOOLS_PATH . 'mimeType.php';
    require_once TOOLS_PATH . 'Singleton.if';
    require_once TOOLS_PATH . 'scanDirRecurse.php';
}

namespace
{
    //Put it in the load stack.
    spl_autoload_register('\Hestia\autoload');
}

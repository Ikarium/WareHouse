<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * An abstract simple class to all framework core component.
 *
 * 
 * @version   0.0.2
 * @since     0.0.2
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC BY-NC-SA 3.0  
*/

namespace Hestia\Core;

abstract class ApplicationComponent
{
    protected $app;
            
    protected function __construct(Application $app)
    {
        $this->app = $app;
    }
}
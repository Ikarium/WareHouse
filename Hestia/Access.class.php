<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Sort the request and launch the corespondant script.
 *
 * The possible request is: 
 *     Designe files (.css, .js, picture etc...)
 *         Give possibility to simple adressing file like "/Designe/Images/somethink.jpg"
 *     Public files (pictures, .js, .doc etc ...)
 *     Ajax request, then launch the requested function.
 *     Normal task then launch the application core part.
 * 
 * @version   0.1.0
 * @since     0.1.0
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC-BY-NC-SA 3.0 
*/

namespace Hestia;

final class Access
{
    /**
     * Configure the Framework, include the autoload
     */
    public final function __construct()
    {
        require_once 'Hestia.conf';
            configureFramework();
        require_once FRAMEWORK_PATH . 'Autoload.php';
    }
    
    /**
     * hey are 3 reserved uri, the *.Site.xxx/Designe/*
     *                             *.Site.xxx/Public/*
     *                             *.Site.xxx/Ajax/*
     * If none of them requested, start the standart page generation.
     */
    public final function run()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        if($requestUri == '' || $requestUri == '/')
        {
            $this->launchCore();
        }
        elseif(!substr_compare($requestUri, REQUEST_DESIGN, 1, strlen(REQUEST_DESIGN), true))
        {
            $this->getDesigneFile();
        }
        elseif(!substr_compare($requestUri, REQUEST_PUBLIC, 1, strlen(REQUEST_PUBLIC), true))
        {
            $this->getPublicFile();
        }
        elseif(!substr_compare($requestUri, REQUEST_AJAX, 1, strlen(REQUEST_AJAX), true))
        {
            $this->getAjaxFunction();
        }
        else
        {
            $this->launchCore();
        }

    }
    
    /**
     * Launch, if exist, the requested application.
     * If not and if the persistant app defined, included it.
     * If not, 404.
     *
     */
    private final function launchCore()
    {
        $EXTENTION_CONTROLLERS = '.class.ctrl';//!!!Duplicated data !

        $serverName = $_SERVER['SERVER_NAME'];
        $ROOT_ADRESSE_ARRAY = unserialize(ROOT_ADRESSE_ARRAY);

        if (array_key_exists($serverName , $ROOT_ADRESSE_ARRAY))
        {
            $serverName = $ROOT_ADRESSE_ARRAY[$serverName];
            $requestedApplication = APPLICATIONS_PATH . $serverName . '/' . $serverName . $EXTENTION_CONTROLLERS;
            Require_once $requestedApplication;
        }
        else
        {
            //Filter the adresse, to keep only the main domain, "sub.domain" => "domain";
            $serverName = preg_filter('#(.*\.){0,2}(.*)#i', '$2', $serverName);

            $PERSISTANT_APPLICATION = PERSISTANT_APPLICATION;
            $requestedApplication = APPLICATIONS_PATH . $serverName . '/' . $serverName . $EXTENTION_CONTROLLERS;

            echo $serverName;
            if (file_exists($requestedApplication))
            {
                Require_once $requestedApplication;
            }
            elseif(!empty($PERSISTANT_APPLICATION))
            {
                $requestedApplication = APPLICATIONS_PATH . $PERSISTANT_APPLICATION . '/' . $PERSISTANT_APPLICATION . $EXTENTION_CONTROLLERS;

                Require_once $requestedApplication;

            }
            else
            {
                header('HTTP/1.0 404 Not Found');
                exit;
            }
        }
        
        $application = new $serverName;
        $application->run();
    }
    
    /**
     * Based on stored value in session variable in previous core launch, 
     * load the corespandante designe file.
     */
    private final function getDesigneFile()
    {
        session_start();

        $requestUri = $_SERVER['REQUEST_URI'];
        $requestFile = substr($requestUri, strlen(REQUEST_DESIGN) + 1);
        
        $designPath = $_SESSION['APPLICATION_PATH_TEMPLATE'];
        
        $filePath = $designPath . $requestFile;
        
        $fileMimeType = Tools\mimeType($filePath);
        header('Content-type: ' . $fileMimeType);
        echo file_get_contents($filePath); 
    }
    
    /**
     * Based on stored value in session variable in previous core launch,
     * load the corespandante public file.
     */
    private final function getPublicFile()
    {
        session_start();
        
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestFile = substr($requestUri, strlen(REQUEST_PUBLIC) + 1);
        
        $publicPath = $_SESSION['APPLICATION_PATH_PUBLIC'];
        
        $filePath = $publicPath . $requestFile;
        
        $fileMimeType = Tools\mimeType($filePath);
        header('Content-type: ' . $fileMimeType);
        echo file_get_contents($filePath); 
    }
    
    /**
     * Based on stored value in session variable in previous core launch,
     * load the corespandante Ajax function.
     */
    private final function getAjaxFunction()
    {
        session_start();
        
        $domainPath = $_SESSION['APPLICATION_PATH_DOMAINS'];
        $modueAjaxModele = $_SESSION['MODULE_AJAX_MODELE'];
        
        $requestUri = $_SERVER['REQUEST_URI'];
        
        $requestFunctionVirtualPath = substr($requestUri, strlen(REQUEST_AJAX) + 1);
        
        $urlDatas = preg_filter ('#(.[^/]+)/(.[^/]+)/(.[^/]+)/(.*)#i', '$1 $2 $3 $4', $requestFunctionVirtualPath);
        $urlDatas = explode(" ", $urlDatas);
        $domain = $urlDatas[0];
        $module = $urlDatas[1];
        $function = $urlDatas[2];
        $parameter = $urlDatas[3];
        
        $ajaxModelPath = $domainPath . $domain . '/' . $module . '/' . $modueAjaxModele;

        require_once($ajaxModelPath);
        
        $ajaxRequestParam = $_SESSION['AJAX_REQUEST_PARAM_NAME'];
        $_GET[$ajaxRequestParam] = $parameter;
        
        $function();
            
    }
}



 
?>
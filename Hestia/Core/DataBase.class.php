<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Connect to a database, configue connection.
 *
 * Use PDO to connecte to one or multiple databse, and provide a simple interface
 * to use it safely.
 *
 * @version   0.0.1 
 * @since     0.0.1
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC-BY-NC-SA 3.0  
*/

namespace Hestia\Core;

//It mean that PDO is in the global namespace and not in /Hestia/Core/
use PDO as PDO;

final class DataBase extends ApplicationComponent implements \Hestia\Tools\ISingleton
{       
    private $mysql;
    private $sqlite;
    private $sqliteInMemory;
    
    /// Singletron function
    public static function singletron($app)
    {
        static $thisObject = null;
        if ( $thisObject == null ) $thisObject = new DataBase($app);
        else throw('Triying to implement twice the singltron class: ' . __CLASS__);
        return $thisObject;
    }
    
    /// Simple private contructor
    protected function __construct($app)
    {
        parent::__construct($app);
        
    }
    
    
    /**
     * Getter methods.
     *
     * @{
     */
    public final function mysql()
    {
        return $this->mysql;
    }
    
    public final function sqlite()
    {
    
        return $this->sqlite;
    }
    
    public final function sqliteInMemory()
    {
    
        return $this->sqliteInMemory;
    }
    /**@}*/
    
    /**
     * Open a connection to the SQLite DataBase.
     *
     * To change configuration, edit the constants.
     * 
     */
    public function sqliteConnect()
    {
        //Set the default configuration, if it nor configured yet, see file core.conf.
        sqliteDefaultConfiguration();
        
        /**************************************************
         * SQLite connection
        ***************************************************/
        try
        {
            $this->sqlite = new PDO('sqlite:' . SQLITE_PATH);
        
            $this->sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(Exception $e)
        {
            echo 'Error : '.$e->getMessage().'<br />';
            echo 'N°: '.$e->getCode();
        }

        return $this->sqlite;
    }

    public function sqliteDisconnect() { $this->sqlite = null; }
    
        
    
    /**
     * Create/connect to a sql database located in memory.
     *
     * Important ! If $sqliteInMemoryPersistant == False then the database will be destroy after generating the page
     * If it's true, she will survive as long as the session.
     * 
     */
    public function sqliteInMemoryConnect($sqliteInMemoryPersistant = false)
    {
        
        /**************************************************
         * SQLite in memory connection
        ***************************************************/
        try
        {
            $this->sqliteInMemory = new PDO('sqlite::memory:');
        
            $this->sqliteInMemory->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            if($sqliteInMemoryPersistant)
            {
                $this->$connection->setAttribute(PDO::ATTR_PERSISTENT, true);
            }
        }
        catch(Exception $e)
        {
            echo 'Error : '.$e->getMessage().'<br />';
            echo 'N°: '.$e->getCode();
        }
        
        return $this->sqliteInMemory;
    }

    public function sqliteInMemoryDisconnect() { $this->sqliteInMemory = null; }
    
    
    /**
     * Open a connection to the MySQL DataBase.
     *
     * To change configuration, edit the constants.
     * 
     */
    public function mysqlConnect()
    {
        //Set the default configuration, if it nor configured yet, see file core.conf.
        mysqlDefaultConfiguration($this->app->name());
        
        /**************************************************
         * MySQL Connection
        **************************************************/
        try
        {
            $this->mysql = new PDO('mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DATABASE,
                                 MYSQL_USER, MYSQL_PASSWORD);
            
            $this->mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->mysql->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        }
        catch(Exception $e)
        {
            echo 'Error : '.$e->getMessage().'<br />';
            echo 'N°: '.$e->getCode();
        }

        return $this->mysql;
        
    }

    public function mysqlDisconnect() { $this->mysql = null; }

    
}
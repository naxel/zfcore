<?php

/**
 * Class Core_Migration_Abstract
 *
 * abstract migration
 *
 * @category Core
 * @package  Core_Migration
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Abstract.php 219 2010-12-07 10:49:15Z dmitriy.britan $
 */
abstract class Core_Migration_Abstract
{
    const TYPE_INT = 'int';
    const TYPE_BIGINT = 'bigint';
    
    const TYPE_FLOAT = 'float';
    
    const TYPE_TEXT = 'text';
    const TYPE_LONGTEXT = 'longtext';
    
    const TYPE_VARCHAR = 'varchar';
    const TYPE_ENUM = 'enum';
    
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIME = 'time';
    const TYPE_TIMESTAMP = 'timestamp';    
    
    /**
     * Default Database adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbAdapter = null;
    
    /**
     * migration Adapter
     *
     * @var Core_Migration_Adapter_Abstract
     */
    
    protected $_migrationAdapter = null;
    /**
     * up
     *
     * update DB from migration
     * 
     * @return  Core_Migration_Abstract
     */
    abstract public function up();
    
    /**
     * down
     *
     * degrade DB from migration
     * 
     * @return  Core_Migration_Abstract
     */
    abstract public function down();
    
    /**
     * __construct
     *
     * constructor of migration
     *
     * @return  rettype  return
     */
    public function __construct() 
    {

    }

    /**
     * setDbAdapter
     *
     * @param  Zend_Db_Adapter_Abstract $dbAdapter
     * @return return
     */
    function setDbAdapter($dbAdapter = null)
    {
        if ($dbAdapter && ($dbAdapter instanceof Zend_Db_Adapter_Abstract)) {
            $this->_dbAdapter = $dbAdapter;
        } else {
            $this->_dbAdapter = Zend_Db_Table::getDefaultAdapter();
        }
        return $this;
    }
    /**
     * getDbAdapter
     *
     * @return return
     */
    function getDbAdapter()
    {
        if (!$this->_dbAdapter) {
            $this->setDbAdapter();
        }
        return $this->_dbAdapter;
    } 
    
    /**
     * setMigrationAdapter
     *
     * 
     * @return Core_Migration_Abstract
     */
    function setMigrationAdapter()
    {
        if ($this->getDbAdapter() instanceof Zend_Db_Adapter_Pdo_Mysql) {
            $className = 'Core_Migration_Adapter_Mysql';
        } elseif ($this->getDbAdapter() instanceof Zend_Db_Adapter_Pdo_Sqlite ) {
            $className = 'Core_Migration_Adapter_Sqlite';
        } elseif ($this->getDbAdapter() instanceof Zend_Db_Adapter_Pdo_Pgsql) {
            $className = 'Core_Migration_Adapter_Pgsql';
        } else {
            throw new Core_Exception("This type of adapter not suppotred");
        }
        $this->_migrationAdapter = new $className($this->getDbAdapter());
        
        return $this;
    }
    
    /**
     * getMigrationAdapter
     *
     * @return return
     */
    function getMigrationAdapter()
    {
        if (!$this->_migrationAdapter) {
            $this->setMigrationAdapter();
        }
        return $this->_migrationAdapter;
    }
    
    /**
     * stop
     *
     * @throws Exception
     */
    public function stop() 
    {
        throw new Core_Exception('This is final migration');
    }
       
    /**
     * query
     *
     * @param   string     $query
     * @return  Core_Migration_Abstract
     */
    public function query($query) 
    {
        $this->getMigrationAdapter()->query($query);
        return $this;
    }
    
    /**
     * insert
     *
     * @param   string     $table
     * @param   array      $params
     * @return  int The number of affected rows.
     */
    public function insert($table, array $params) 
    {
        $this->getMigrationAdapter()->insert($table, $params);
        
        return $this;
    }
    
    /**
     * Updates table rows with specified data based on a WHERE clause.
     *
     * @param  mixed        $table The table to update.
     * @param  array        $bind  Column-value pairs.
     * @param  mixed        $where UPDATE WHERE clause(s).
     * @return int          The number of affected rows.
     */
    public function update($table, array $bind, $where = '')
    {
        $this->getMigrationAdapter()->update($table, $bind, $where);
        
        return $this;        
    }
     
    /**
     * createTable
     *
     * @param   string $table table name
     * @return  Core_Migration_Abstract
     */
    public function createTable($table) 
    {
        $this->getMigrationAdapter()->createTable($table);
        
        return $this;
        
    }
    
    /**
     * dropTable
     *
     * @param   string     $table  table name
     * @return  Core_Migration_Abstract
     */
    public function dropTable($table) 
    {
        $this->getMigrationAdapter()->dropTable($table);
        
        return $this;
    }
    
    /**
     * createColumn
     *
     * FIXME: requried quoted queries data
     * 
     * @param   string   $table
     * @param   string   $column 
     * @param   string   $datatype
     * @param   string   $length
     * @param   string   $default
     * @param   bool     $notnull
     * @param   bool     $primary
     * @return  Core_Migration_Abstract
     */
    public function createColumn($table, 
                                 $column,
                                 $datatype,
                                 $length = null,
                                 $default = null,
                                 $notnull = false,
                                 $primary = false
                                 ) 
    {
        
//        if ($default && self::DEFAULT_CURRENT_TIMESTAMP == $default) {
//            $default = $this->getMigrationAdapter()->getCurrentTimestamp();
//        }
        $this->getMigrationAdapter()->createColumn(
            $table,
            $column,
            $datatype,
            $length,
            $default,
            $notnull,
            $primary
        );
        
        return $this;
    }
    
    /**
     * dropColumn
     *
     * @param   string   $table
     * @param   string   $name 
     * @return  bool
     */
    public function dropColumn($table, $name) 
    {
        $this->getMigrationAdapter()->dropColumn($table, $name);
        
        return $this;
    }   

    /**
     * createUniqueIndexes
     *
     * @param   string   $table
     * @param   array    $columns 
     * @param   string   $indName 
     * @return  bool
     */
    public function createUniqueIndexes($table, array $columns, $indName = null)
    {
        $this->getMigrationAdapter()
             ->createUniqueIndexes($table, $columns, $indName);
        
        return $this;
    }    
    
    /**
     * dropColumn
     *
     * @param   string   $table
     * @param   array    $columns
     * @return  bool
     */
    public function dropUniqueIndexes($table, $indName) 
    {
        $this->getMigrationAdapter()->dropUniqueIndexes($table, $indName);
        
        return $this;
    }
    
    /**
     * message
     *
     * output message to console
     *
     * @param   string     $message
     * @return  Core_Migration_Abstract
     */
    public function message($message) 
    {
        echo $message . "\n";
        return $this;
    }
}
<?php

/**
 * Stash Queries - PHP SQL layer using stash queries (file) design integration
 * PHP Version 5.3.6+
 * @package Skyfire/DB
 * @link https://github.com/SkyfirePHP/DB The Skyfire DB GitHub project
 * @author Travis Font (tfont) <travis.font@gmail.com>
 * @license MIT
 */

// not defined from Skyfire or outside framework
if (!defined('PARENT_DIRECTORY'))
{
    define('PARENT_DIRECTORY', dirname(getcwd()));
}

// SPL autoloader
spl_autoload_register(function ($classname)
{
    // __DIR__ is only in PHP 5.3+
    // closures / anonymous functions only in PHP 5.3+
    $filename = dirname(__FILE__).DIRECTORY_SEPARATOR.$classname.'.php';

    if (is_readable($filename))
    {
        require_once $filename;
    }
}, TRUE, TRUE);

final class DB extends DB_Connector
{
    public static $exceptionType;

    public function __construct()
    {
        if ($this->dbh == NULL)
        {
            try
            {
                if (self::$persistent === TRUE)
                {
                    $this->dbh = new PDO('mysql:host='.self::$DATABASE_HOST.';dbname='.self::$DATABASE_NAME.';charset='.self::$charset, self::$DATABASE_USER, self::$DATABASE_PASSWORD, array(PDO::ATTR_PERSISTENT => TRUE, PDO::MYSQL_ATTR_FOUND_ROWS => TRUE));

                }
                else
                {
                    $this->dbh = new PDO('mysql:host='.self::$DATABASE_HOST.';dbname='.self::$DATABASE_NAME.';charset='.self::$charset, self::$DATABASE_USER, self::$DATABASE_PASSWORD, array(PDO::MYSQL_ATTR_FOUND_ROWS => TRUE));
                }

                if (self::$errmode === TRUE)
                {
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }
            }
            catch (PDOException $exception)
            {
                echo self::PDOException($exception, self::DISPLAY_TEXT);

                return FALSE;
            }
        }

        return $this->dbh;
    }

    public static function utf8($text)
    {
        return mb_convert_encoding($text, "HTML-ENTITIES", 'UTF-8');
    }

    public static function query($statement, array $parameters = NULL)
    {
        if (self::$db == NULL)
        {
            self::$db = new self();
        }

        return new RawQuery($statement, $parameters, self::$db);
    }

    public static function select($sql_file)
    {
        if (self::$db == NULL)
        {
            self::$db = new self();
        }

        return new ProcessQuery($sql_file, 'select', self::$db);
    }

    public static function update($sql_file)
    {
        if (self::$db == NULL)
        {
            self::$db = new self();
        }

        return new ProcessQuery($sql_file, 'update', self::$db);
    }

    public static function insert($sql_file)
    {
        if (self::$db == NULL)
        {
            self::$db = new self();
        }

        return new ProcessQuery($sql_file, 'insert', self::$db);
    }

    public static function drop($table_name) {}
    public static function truncate($table_name) {}
    public static function fullempty($database_name)
    {
        $sql  = 'SET FOREIGN_KEY_CHECKS=0;';
        $sql .= 'SHOW FULL TABLES FROM `'.$database_name.'` WHERE table_type = \'BASE TABLE\';';
        $sql .= 'SET FOREIGN_KEY_CHECKS=1;';

        /* also to delete views, functions, and events */
        // SELECT `TABLE_NAME` FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA` = 'database_name' AND `TABLE_TYPE` = 'VIEW';
        // SELECT `SPECIFIC_NAME` FROM `INFORMATION_SCHEMA`.`ROUTINES` WHERE `ROUTINE_SCHEMA` = 'database_name' AND ROUTINE_TYPE = 'FUNCTION';
        // SELECT `EVENT_NAME` FROM `INFORMATION_SCHEMA`.`EVENTS` WHERE `EVENT_SCHEMA` = 'database_name' ORDER BY EVENT_NAME;

        $drop = 'SET FOREIGN_KEY_CHECKS=0;';
        foreach (self::query($sql)->execute() as $table)
        {
            $drop .= 'DROP TABLE `'.$database_name.'`.`'.reset($table).'`;';
        }
        $drop .= 'SET FOREIGN_KEY_CHECKS=1;';

        self::query($drop)->execute();
    }

    // this type needs to be a display type (e.g. DISPLAY_TEST)
    public static function setExceptionType($type)
    {
        self::$exceptionType = $type;
    }
}

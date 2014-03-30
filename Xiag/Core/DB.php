<?php

namespace Xiag\Core;

use \PDO, \PDOException;

class DB
{
  private $pdo = null;
  private $statement = null;

  private $affectedRows = 0;
  private $lastId = null;

  //Singleton
  private static $instance = null;
  private function __clone() {}
  private function __wakeup() {}
  public static function getInstance()
  {
    return self::$instance ? : (self::$instance = new self());
  }
  private function __construct()
  {
    $config = Config::getInstance();
    $config = $config['db'];

    $this->dsn      = isset($config['dsn'])      ? $config['dsn']      : 'mysql:host=localhost;dbname=messenger';
    $this->username = isset($config['username']) ? $config['username'] : 'root';
    $this->password = isset($config['password']) ? $config['password'] : '';
    $this->options  = isset($config['options'])  ? $config['options']  : array(
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;"
    );
  }

  private function connect()
  {
    $this->pdo = new PDO($this->dsn, $this->username, $this->password, $this->options);
  }

  private static function getPDOType($var)
  {
    if (is_int($var)) return PDO::PARAM_INT;
    if (is_bool($var)) return PDO::PARAM_BOOL;
    if (is_null($var)) return PDO::PARAM_NULL;
    return PDO::PARAM_STR;
  }

  private function bindParam($field, &$value)
  {
    $this->statement->bindValue(":{$field}", $value, self::getPDOType($value));
    return $this;
  }

  private function bindParams(array &$params)
  {
    foreach ($params as $field => &$value) {
      $this->bindParam($field, $value);
    }
    return $this;
  }

  protected function prepare($sql)
  {
    $this->statement = $this->pdo->prepare($sql);
    return $this;
  }

  protected function execPrepared(array $params = array())
  {
    if ($params) {
      $this->bindParams($params);
      $this->statement->execute($params);
    } else {
      $this->statement->execute();
    }
    return $this;
  }

  public function exec($sql, array $params = array())
  {
    if(!$this->pdo) $this->connect();
    $this->prepare($sql)->execPrepared($params);
    $this->lastId = $this->pdo->lastInsertId();
    $this->affectedRows = $this->statement->rowCount();
    return $this;
  }

  public function getLastId()
  {
    return $this->lastId;
  }

  public function cell($sql, array $params = array(), $col = 0)
  {
    $this->exec($sql, $params);
    return $this->statement->fetchColumn((int)$col);
  }

  public function col($sql, array $params = array(), $col = 0)
  {
    $this->exec($sql, $params);
    return $this->statement->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_COLUMN, (int)$col);
  }

  public function row($sql, array $params = array())
  {
    $this->exec($sql, $params);
    return $this->statement->fetch(PDO::FETCH_ASSOC);
  }

  public function rows($sql, array $params = array())
  {
    $this->exec($sql, $params);
    return $this->statement->fetchAll(PDO::FETCH_ASSOC);
  }

}

<?php

namespace Xiag\Core;

use \PDO, \PDOException;

class DB
{
  private $pdo = null;
  private $statement = null;

  private $affectedRows = 0;
  private $lastId = null;

  private $config = array();

  public function __construct($config)
  {
    $this->config = $config['db'];
  }

  public function setPDO(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  private function connect()
  {
    $dsn      = isset($this->config['dsn'])      ? $this->config['dsn']      : 'mysql:host=localhost;dbname=messenger';
    $username = isset($this->config['username']) ? $this->config['username'] : 'root';
    $password = isset($this->config['password']) ? $this->config['password'] : '';
    $options  = isset($this->config['options'])  ? $this->config['options']  : array(
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;"
    );

    $this->pdo = new PDO($dsn, $username, $password, $options);
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

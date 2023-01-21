<?php

namespace Database\Connection;

use Database\Connection\Config;
use Lib\Helpers;
use PDO;

class DB
{
    use Helpers;

    private PDO $conn;
    private $table;
    private $select = '*';
    private $where;
    private $query;
    private $op = ['like', '=', '!=', '<', '>', '<=', '>=', '<>'];
    private $state = 'AND';

    public function __construct()
    {
        $config = Config::CONFIG;
        $driver = isset($config['driver']) ? $config['driver'] : 'mysql';
        $host = isset($config['host']) ? $config['host'] : 'localhost';
        $port = isset($config['port']) ? $config['port'] : (strstr($host, ':') ? explode(':', $config['host'])[1] : '');
        $charset = isset($config['charset']) ? $config['charset'] : 'utf8';
        $this->prefix = isset($config['prefix']) ? $config['prefix'] : '';

        $dns = $driver . ":host=" . str_replace(':' . $port, '', $host)
            . ($port != '' ? ';port=' . $port : '')
            . ";dbname=" . $config['database']
            . ";charset=" . $charset;

        try {
            $this->conn = new PDO($dns, $config['username'], $config['password']);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function table($table, $as = null)
    {
        $as = !is_null($as) && !is_array($table) ? " AS {$as}" : '';
        $this->table = $this->isTable($table) . $as;

        return $this;
    }

    public function select($field)
    {
        $this->select = $this->isIm($field);
        return $this;
    }

    private function setWhere($column, $op, $value, $sql = '')
    {
        $_where = '';

        if (is_array($column)) {
            $op = is_null($op) ? 'AND' : $op;
            foreach ($column as $keys => $value) {
                if (is_array($value)) {
                    $_where .= $this->setWhere(
                            $value[0],
                            isset($value[1]) ? (is_int($value[1]) ? $value[1] : (in_array($value[1], $this->op) ? $value[1] : "'{$value[1]}'")) : '',
                            isset($value[2]) ? "'{$value[2]}'" : ''
                        ) . $op;
                } else {
                    throw new \Exception("Somethig wrong with SQL");
                }
            }

            $_where = substr($_where, 0, -strlen($op));
        } else {
            if (empty($op) && empty($value)) {
                $_where = " {$sql} id = {$column} ";
            } elseif (empty($value)) {
                $_where = " {$sql} {$column} = " . $this->isText($op);
            } else {
                $_where = " {$sql} {$column} {$op} " . $this->isText($value);
            }
        }

        $this->where .= $this->isState($_where);
    }

    public function where($column, $op = null, $value = null)
    {
        $this->setWhere($column, $op, $value);
        return $this;
    }

    public function limit($field1, $field2 = null)
    {
        $this->query .= " LIMIT {$field1} " . (!is_null($field2) ? ", {$field2}" : '');

        return $this;
    }

    public function offset($field)
    {
        $this->query .= " OFFSET {$field}";
        return $this;
    }

    public function pagination($perPage, $page = 1)
    {
        $this->limit($perPage);

        $page = (($page > 0 ? $page : 1) - 1) * $perPage;

        $this->offset($page);

        return $this;
    }

    private function setExtract()
    {
        $sql = '';

        $sql .= !empty($this->where) ? 'WHERE ' . $this->where : '';
        $sql .= $this->query;

        $this->reset();

        return $sql;
    }

    public function get($select = null)
    {
        if (!empty($select)) {
            $this->select .= ', ' . $this->isIm($select);
        }
        $sql = sprintf("SELECT %s FROM %s %s", $this->select, $this->table, $this->setExtract());

        $result = $this->conn->prepare($sql);
        $result->execute();

        return $result->fetchAll();
    }

    public function first($select = null)
    {
        $this->select .= !empty($select) ? ', ' . $this->isIm($select) : '';

        $sql = sprintf("SELECT %s FROM %s %s LIMIT 1", $this->select, $this->table, $this->setExtract());

        $result = $this->conn->prepare($sql);
        $result->execute();

        return $result->fetchAll()[0]??[];
    }

    public function insert(Array $fields)
    {
        $columns = implode(',', array_keys($fields));
        $values = '';

        foreach ($fields as $key => $val) {
            $values .= (is_int($val) ? $val : "'{$val}'") . ",";
        }

        $sql = sprintf("INSERT INTO %s (%s) VALUES(%s)", $this->table, $columns, substr($values, 0, -1));
        return $this->runQuery($sql);
    }

    public function update(Array $fields)
    {
        $_fields = '';

        foreach ($fields as $key => $value) {
            $_fields .= "{$key} = " . $this->istext($value) . ", ";
        }

        $_fields = substr($_fields, 0, -2);
        $sql = sprintf("UPDATE %s SET %s WHERE %s", $this->table, $_fields, $this->where);
        return $this->runQuery($sql);
    }

    private function runQuery($sql)
    {
        $query = $this->conn->prepare($sql);
        if ($query->execute()) return true;
        else return false;
    }

    private function isState($sql)
    {
        $sql = empty($this->where) ? $sql : " {$this->state} " . $sql;
        $this->state = 'AND';
        return $sql;
    }

    private function reset()
    {
        $this->table = null;
        $this->select = null;
        $this->where = null;
        $this->query = null;
    }

    public function __destruct()
    {
        if (is_resource($this->conn)) $this->conn = null;
    }

    public function exec($statement) {
        $this->conn->exec($statement);
    }

}

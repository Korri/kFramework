<?php

/**
 * Database abstraction layer
 *
 * @author korri
 */
class Database {

    /**
     * Mysql database connection
     * @var resource
     */
    private $con;
    /**
     * Last executed query
     * @var string
     */
    public $last_query = array();
    /**
     * Last result
     * @var array
     */
    public $last_result;
    public function __construct($host, $login, $pass, $db=false) {
        $this->con = mysql_connect($host, $login, $pass);

        if ($db) {
            mysql_select_db($db, $this->con);
        }
        $this->throwErrors();

        $this->query('SET NAMES UTF8');
    }

    private function throwErrors() {
        $errno = mysql_errno($this->con);
        if ($errno) {
            throw new DatabaseException($errno, mysql_error($this->con));
        }
    }

    public function selectObjects($class, $query, $class=null) {
        $args = func_get_args();

        $class = array_shift($args);
        if (func_num_args() >= 3) {
            $query = $this->prepare(array_shift($args), $args);
        }
        $this->last_query = $query;

        $result = mysql_query($query);

        $this->throwErrors();

        $res = array();
        while ($row = mysql_fetch_object($result, $class)) {
            $res[] = $row;
        }
        return $res;
    }
    /**
     * Get an object from database
     * @param class $class Class of the object
     * @param string $query Mysql query
     * @param mixed $args arguments that will be fprintfed to the query
     * @return $class
     */
    public function selectObject($class, $query, $args=null) {
        $args = func_get_args();

        $class = array_shift($args);
        if (func_num_args() >= 3) {
            $query = $this->prepare(array_shift($args), $args);
        }
        $this->last_query = $query;

        $result = mysql_query($query);

        $this->throwErrors();

        return mysql_fetch_object($result, $class);
    }

    public function query($query, $args=false) {
        $args = func_get_args();
        $query = $this->prepare(array_shift($args), $args);

        $this->last_result = array();
        $this->last_query = $query;

        $result = mysql_query($query);

        $this->throwErrors();

        if (strstr($query, 'INSERT') || strstr($query, 'DELETE') || strstr($query, 'UPDATE') || strstr($query, 'REPLACE') || strstr($query, 'ALTER')) {
            return mysql_affected_rows($this->con);
        } else if(strstr($query, 'SELECT')){
            $ret = false;
            while ($row = mysql_fetch_object($result)) {
                $this->last_result[] = $row;
                $ret = true;
            }
            mysql_free_result($result);
            return $ret;
        }else {
            return $result;
        }
    }

    public function prepare($query, $args) {
        $query = str_replace('%s', "'%s'", $query);

        array_walk($args, array(&$this, 'escape'));

        $res =  vsprintf($query, $args);

        return str_replace("'NULL'", 'NULL', $res);
    }

    public function escape($string) {
        /* MYSQL Values */
        if(is_null($string)) {
            return 'NULL';
        }
        if($string === false) {
            return '0';
        }
        
        return mysql_real_escape_string($string, $this->con);
    }

    // Shortcuts //
    public function get_var($query, $args=false) {
        $args = func_get_args();
        $query = $this->prepare(array_shift($args), $args);

        $this->query($query);

        if (!empty($this->last_result[0])) {
            $values = array_values(get_object_vars($this->last_result[0]));
        }

        return (isset($values[0]) && $values[0] !== '') ? $values[0] : null;
    }

    /**
     * Insert array of data in current database
     *
     * @param string $table
     * @param mixed $data
     * @return int Affected rows
     */
    public function insert($table, $data) {
        if (!is_array($data)) {
            throw new DatabaseException('$data must be an array "' . $query . '"');
        } else {
            array_walk($data, array(&$this, 'escape'));
            $fields = array_keys($data);
            return $this->query("INSERT INTO $table (`" . implode('`,`', $fields) . "`) VALUES ('" . implode("','", $data) . "')");
        }
    }

    /**
     * Insert array of data in current database
     *
     * @param string $table
     * @param mixed $data
     * @param mixed $where
     * @return int Affected rows
     */
    public function update($table, $data, $where) {
        if (!is_array($data)) {
            throw new DatabaseException('$data must be an array "' . $query . '"');
        } else if (!is_array($where)) {
            throw new DatabaseException('$where must be an array "' . $query . '"');
        } else {
            array_walk($data, array(&$this, 'escape'));
            $wheres = $fields = array();

            foreach ((array) array_keys($data) as $key) {
                $fields[] = "`$key` = '$data[$key]'";
            }
            foreach ($where as $c => $v) {
                $wheres[] = "$c = '" . $this->escape($v) . "'";
            }

            return $this->query("UPDATE $table SET " . implode(', ', $fields) . ' WHERE ' . implode(' AND ', $wheres));
        }
    }

    public function saveObject($object, $table=false) {
        if ($table === false) {
            $table = Inflect::pluralize(get_class($object));
        }
        $vars = get_object_vars($object);
        if ($vars['id']) {
            $id = $vars['id'];
            unset($vars['id']);
            return $this->update($table, $vars, array('id' => $id));
        } else {
            unset($vars['id']);
            $this->insert($table, $vars);

            $object->id = $this->last_id();
        }
    }

    public function updateObject($object, $table=false, $where=false) {
        if ($table === false) {
            $table = Inflect::pluralize(get_class($object));
        }
        $vars = get_object_vars($object);
        if ($where === false) {
            $where = array('id' => $vars['id']);
            unset($vars['id']);
        }
        return $this->update($table, $vars, $where);
    }

    public function insertObject($object, $table=false, $where=false) {
        if ($table === false) {
            $table = Inflect::pluralize(get_class($object));
        }
        $vars = get_object_vars($object);
        unset($vars['id']);
        return $this->insert($table, $vars);
    }

    /**
     * Return mysql_insert_id for this instance;
     *
     * @return integer
     */
    public function last_id() {
        return mysql_insert_id($this->con);
    }

}

class DatabaseException extends Exception {

    public function __construct($errno, $error) {
        parent::__construct("Error ($errno): $error", $errno);
    }

}

?>

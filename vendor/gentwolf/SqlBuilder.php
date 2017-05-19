<?php

namespace gentwolf;

class SqlBuilder {
	private $fields = NULL;
	private $tables = NULL;
	private $wheres = NULL;
	private $orWheres = NULL;
	private $joins = NULL;
	private $orderBys = NULL;
	private $limit = NULL;

	//单一实例
	public static $instance;
	
	function __construct() {
	}

	public static function instance() {
		if (!self::$instance) self::$instance = new self();
		return self::$instance;
	}

	/**
	 * 输入查询字段
	 * @param string | array $field
	 * @return object
	 */
	public function select($field = '*') {
		if (func_num_args() > 1) {
			$this->fields = implode(', ', func_get_args());
		} else if (is_string($field)) {
			$this->fields = $field;
		} else if (is_array($field)) {
			$this->fields = implode(', ', $field);
		}
		return $this;
	}

	/** 
	 * 输入表名 
	 * @param string | array $table
	 * @return object
	 */
	public function from($table) {
		$this->tables[] = is_string($table) ? $table : implode(', ', $table);
		return $this;
	}

	/**
	 * 输入搜索条件
	 * @param string | array $key
	 * @param string | integer | null $value
	 * @param string $joiner
	 * @return object
	 */
	public function where($key, $value = NULL, $joiner = ' AND ') {
		if (is_string($key)) {
			if (is_scalar($value)) {
				$this->wheres[] = $key .' = "'. addslashes($value) .'"';
			} else {
				$this->wheres[] = $key;
			}
		} else if (is_array($key)) {
			$this->wheres[] = $this->buildWhere($key, $joiner);
		}
		return $this;
	}

	/**
	 * 输入搜索条件
	 * @param string | array $key
	 * @param string | integer | null $value
	 * @return object
	 */
	public function orWhere($key, $value = NULL) {
		$this->where($key, $value, ' OR ');
		return $this;
	}

	/**
	 * 输入模糊搜索
	 * @param string || array $key
	 * @param string $value
	 * @return object
	 */
	public function like($key, $value = NULL) {
		if (is_string($key)) {
			if (is_scalar ($value)) {
				$this->wheres[] = $key .' LIKE "'. addslashes($value) .'"';
			}
		} else if (is_array($key)) {
			foreach ($key as $i => $v) {
				$this->wheres[] = $i .' LIKE "'. addslashes($v) .'"';
			}
		}
		return $this;
	}

	/**
	 * 输入模糊搜索
	 * @param string || array $key
	 * @param string $value
	 * @return object
	 */
	public function orLike($key, $value = NULL) {
		if (is_string($key)) {
			if (is_scalar($value)) {
				$this->orWheres[] = $key .' LIKE "'. addslashes($value) .'"';
			}			
		} else if (is_array($key)) {
			foreach ($key as $i => $v) {
				$this->orWheres[] = $i .' LIKE "'. addslashes($v) .'"';
			}
		}
		return $this;
	}

	/**
	 * 输入JOIN类条件
	 * @param string $table
	 * @param string $where
	 * @param string $type
	 * @return object
	 */
	public function join($table, $where, $type = 'LEFT') {
		$this->joins[] = ' '. $type .' JOIN '. $table .' ON ('. $where .')';
		return $this;
	}

	/**
	 * 输入排序
	 * @param string $field
	 * @param string $order
	 * @return object
	 */
	public function orderBy($field = NULL, $order = NULL) {
        if ($field) {
            if (is_string($field)) {
                $str = $field;
                if ($order) $str .= ' '. $order;
                $this->orderBys[] = $str;
            } else {
                foreach ($field as $k => $v) {
                    $this->orderBys[] = $k .' '. $v;
                }
            }
        }
		return $this;
	}

	/**
	 * 取记录位置
	 * @param integer $offset
	 * @param integer $length
	 * @return object
	 */
	public function limit($offset, $length) {
		$this->limit = ' LIMIT '. $offset .', '. $length;
		return $this;
	}

	/**
	 * 构造SQL语句
	 * @return string
	 */
	public function build() {
		$sql = '';
		if ($this->fields && $this->tables) {
			$sql = 'SELECT '. $this->fields .' FROM '. implode(', ', $this->tables);
			
			if ($this->joins) $sql .= implode(' ', $this->joins);
			
			$tmp = false;
			if ($this->wheres) $tmp[] = '('. implode(' AND ', $this->wheres) .')';
			if ($this->orWheres) $tmp[] = '('. implode(' OR ', $this->orWheres) .')';
			if ($tmp) $sql .= ' WHERE '. implode(' AND ', $tmp);
			
			if ($this->orderBys) $sql .= ' ORDER BY '. implode(', ', $this->orderBys);
			if ($this->limit) $sql .= $this->limit;
		}
		$this->clear();
		return $sql;
	}

	/**
	 * 清除所有条件
	 */
	private function clear() {
		$this->tables = NULL;
		$this->wheres = NULL;
		$this->orWheres = NULL;
		$this->joins = NULL;
		$this->orderBys = NULL;
		$this->limit = NULL;
	}

	/**
	 * 构造搜索条件
	 * @param array $data
	 * @param string $joiner
	 * @return string
	 */
	private function buildWhere($data, $joiner = ' AND ') {
		$where = false;
		if (is_array($data)) {
			$arr = false;
			foreach ($data as $k => $v) {
				$tmp = explode(' ', $k);
				if (isset($tmp[1])) {
					$arr[] = $k .' "'. addslashes($v) .'"';
				} else {
					$arr[] = $k .' = "'. addslashes($v) .'"';
				}
			}
			$where = implode($joiner, $arr);
		} else {
			$where = $data;
		}
		return $where;
	}

	/**
	 * 取所有记录
	 * @param object|null $DB
	 * @return array
	 */
	public function fetchAll($db = 'default') {
		return Database::driver($db)->fetchAll($this->build());
	}

	/**
	 * 取一行记录
	 ** @param object|null $DB
	 * @return array
	 */
	public function fetchRow($db = 'default') {
		return Database::driver($db)->fetchRow($this->build());
	}

	/**
	 * 取记录条数
	 * @param object|null $DB
	 * @return array
	 */
	public function fetchCount($db = 'default') {
		return intval(Database::driver($db)->fetchScalar($DB));
	}

	/**
	 * 取一个字段
	 * @param object|null $DB
	 * @return mixed
	 */
	public function fetchScalar($db = 'default') {
		return Database::driver($db)->fetchScalar($this->build());
	}
}
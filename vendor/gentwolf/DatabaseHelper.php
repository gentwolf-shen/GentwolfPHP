<?php

/**
 * 数据库操作封装
 */
namespace gentwolf;

class DatabaseHelper {

	/**
	 * 取数据
	 * @param string $table
	 * @param string $fields
	 * @param array $where
	 * @param string $dbConfig
	 * @return assoc array
	 */
	public static function fetchAll($table, $fields, $where, $ext = null, $dbConfig = 'default') {
		$params = [];
		$data = [];
		if ($where) foreach ($where as $key => $value) {
			$tmp = explode(' ', $key);
			if (count($tmp) == 1) $tmp[1] = '=';

			$param = $tmp[0];
			if (strpos($param, '.') > 0) $param = explode('.', $param)[1];

			$params[] = $tmp[0] .' '. $tmp[1] .' :'. $param;
			$data[':'. $param] = $value;
		}

		$sql = 'SELECT '. $fields .' FROM '. $table;
		if (count($params) > 0) $sql .= ' WHERE '. implode(' AND ', $params);
		if ($ext != null) $sql .= ' '. $ext;
		$cmd = Database::driver($dbConfig)->createCommand($sql);
		return $cmd->execute($data);
	}

	public static function fetchRow($table, $fields, $where, $ext = null, $dbConfig = 'default') {
		$rows = self::fetchAll($table, $fields, $where, $ext, $dbConfig);
		return count($rows) > 0 ? $rows[0] : null;
	}

	public static function fetchScalar($table, $field, $where, $ext = null, $dbConfig = 'default') {
		$rows = self::fetchAll($table, $field, $where, $ext, $dbConfig);
		$row = count($rows) > 0 ? $rows[0] : null;
		return is_array($row) ? $row[$field] : $row;
	}

	/**
	 * 添加数据
	 * @param string $table
	 * @param array $items
	 * @param string $dbConfig
	 * @return int last insert id
	 */
	public static function add($table, $items, $dbConfig = 'default') {
		$fields = [];
		$params = [];
		$data = [];
		foreach ($items as $key => $value) {
			$fields[] = $key;
			$params[] = ':'. $key;
			$data[':'. $key] = $value;
		}

		$sql = 'INSERT INTO '. $table .'('. implode(',', $fields) .') VALUES('. implode(',', $params) .')';
		$cmd = Database::driver($dbConfig)->createCommand($sql);
		return $cmd->execute($data);
	}

	/**
	 * 更新数据
	 * @param string $table
	 * @param array $items
	 * @param array $where
	 * @param string $dbConfig
	 * @return int affected rows count
	 */
	public static function update($table, $items, $where, $dbConfig = 'default') {
		$data = [];
		$updateData = '';

		if (is_array($items)) {
			$params1 = [];
			foreach ($items as $key => $value) {
				$params1[] = $key .'=:'. $key;
				$data[':'. $key] = $value;
			}
			$updateData = implode(',', $params1);
		} else {
			$updateData = $items;
		}

		$params2 = [];
		foreach ($where as $key => $value) {
			$tmp = explode(' ', $key);
			if (count($tmp) == 1) $tmp[1] = '=';

			$params2[] = $tmp[0] .' '. $tmp[1] .' :'. $tmp[0];
			$data[':'. $tmp[0]] = $value;
		}

		$sql = 'UPDATE '. $table .' SET '. $updateData .' WHERE '. implode(' AND ', $params2);
		$cmd = Database::driver($dbConfig)->createCommand($sql);
		return $cmd->execute($data);
	}

	/**
	 * 删除数据
	 * @param string $table
	 * @param array $where
	 * @param string $dbConfig
	 * @return int affected rows count
	 */
	public static function delete($table, $where, $dbConfig = 'default') {
		$params = [];
		$data = [];
		foreach ($where as $key => $value) {
			$tmp = explode(' ', $key);
			if (count($tmp) == 1) $tmp[1] = '=';

			$params[] = $tmp[0] .' '. $tmp[1] .' :'. $tmp[0];
			$data[':'. $tmp[0]] = $value;
		}

		$sql = 'DELETE FROM '. $table .' WHERE '. implode(' AND ', $params);
		$cmd = Database::driver($dbConfig)->createCommand($sql);
		return $cmd->execute($data);
	}

}
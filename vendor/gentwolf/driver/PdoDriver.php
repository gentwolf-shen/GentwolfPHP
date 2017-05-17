<?php

namespace gentwolf\driver;

use PDO;

class PdoDriver {
	private $link = null;
	private $config = null;
	private $mode = true;

	function __construct($config, $mode = 'debug') {
		$this->config = $config;
		$this->mode = $mode;
	}

	private function openConn() {
		if ($this->link == null) {
			$this->link = new PDO($this->config['dsn'], $this->config['username'], $this->config['password'], $this->config['options']);
		}
	}

	private function showError($sql) {
		if ($this->mode == 'debug') {
			$msg = $this->link->errorInfo();
			echo '<div><strong>', $msg[0], ':', '</strong></div>';
			echo '<div><em>', $sql, '</em></div>';
			exit;
		}
	}

	public function getConn() {
		$this->openConn();
		return $this->link;
	}

	public function fetchAll($sql, $fetchStyle = PDO::FETCH_ASSOC) {
		$this->openConn();

		$rows = null;
		$stmt = $this->link->query($sql);
		if ($stmt) {
			$rows = $stmt->fetchAll($fetchStyle);
		} else {
			$this->showError($sql);
		}

		return $rows;
	}

	public function fetchRow($sql) {
		$rows = $this->fetchAll($sql);
		return $rows ? $rows[0] : null;
	}

	public function fetchScalar($sql) {
		$rows = $this->fetchAll($sql, PDO::FETCH_COLUMN);
		return $rows ? $rows[0][0] : null;
	}

	public function setAutoCommit($bl = true) {
		$this->openConn();
		$auto = $bl ? 1 : 0;
		$this->link->query('SET autocommit = '. $auto);
	}

	public function beginTrans() {
		$this->openConn();
		$this->Link->beginTransaction();
	}

	public function commit() {
		$this->Link->commit();
	}

	public function rollBack() {
		$this->Link->rollBack();
	}

	public function execute($sql, $fetchStyle = PDO::FETCH_ASSOC) {
		$sqlType = strtoupper(substr($sql, 0, 6));

		$rs = null;
		switch ($sqlType) {
			case 'SELECT':
				$rs = $this->fetchAll($sql, $fetchStyle);
				break;
			case 'UPDATE':
			case 'DELETE':
				$rs = $this->link->exec($sql);
				break;
			case 'INSERT':
				$this->link->exec($sql);
				$rs = $this->link->lastInsertId();
		}

		return $rs;
	}

	public function createCommand($sql, $data = null) {
		$this->openConn();

		$cmd = null;
		$stmt = $this->link->prepare($sql);
		if (!$stmt) {
			$this->showError($sql);
		} else {
			$sqlType = strtoupper(substr($sql, 0, 6));
			$cmd = new PdoCommand($sqlType, $stmt, $data);
			
			if ($sqlType == 'INSERT') {
				$cmd->setConn($this->link);
			}
		}
		
		return $cmd;
	}
}

class PdoCommand {
	private $sqlType;
	private $stmt;
	private $link;
	private $data;

	function __construct($sqlType, $stmt, $data) {
		$this->sqlType = $sqlType;
		$this->stmt = $stmt;
		$this->data = $data;
	}

	public function setConn($link) {
		$this->link = $link;
	}

	public function fetchAll($fetchStyle = PDO::FETCH_ASSOC) {		
		return $this->execute($this->data, $fetchStyle);
	}

	public function fetchRow() {
		$rows = $this->fetchAll();
		return $rows ? $rows[0] : null;
	}

	public function fetchScalar() {
		$rows = $this->fetchAll(PDO::FETCH_COLUMN);
		return $rows ? $rows[0][0] : null;
	}

	public function bindParam($name, $value, $dataType = null) {
		$this->stmt->bindParam($name, $value, $dataType);
	}

	public function bindValue($name, $value, $dataType = null) {
		$this->stmt->bindValue($name, $value, $dataType);
	}

	public function execute($data = null, $fetchStyle = PDO::FETCH_ASSOC) {
		$rs = 0;
		$bl = $this->stmt->execute($data);
		if ($bl) {
			switch ($this->sqlType) {
				case 'SELECT':
					$rs = $this->stmt->fetchAll($fetchStyle);
					break;
				case 'UPDATE':
				case 'DELETE':
					$rs = $this->stmt->rowCount();
					break;
				case 'INSERT':
					$rs = $this->link->lastInsertId();
			}		
		}

		return $rs;
	}
}
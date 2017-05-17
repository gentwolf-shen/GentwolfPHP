<?php

namespace site\controller;

use gentwolf\Controller;
use gentwolf\Database;
use gentwolf\DatabaseHelper;

class DbCmdController extends Controller {
	public function defaultAction() {

	}

	public function usersAction() {
		$sql = 'SELECT * FROM user WHERE id > :id';
		$cmd = Database::driver('user')->createCommand($sql);
		$rows = $cmd->execute([':id' => 1]);

		//$rows = Database::driver('user')->fetchAll($sql);
		print_r($rows);
	}

	public function addAction() {
		$sql = 'INSERT INTO user(username, age) VALUES(?, ?)';
		$cmd = Database::driver('user')->createCommand($sql);
		$id = $cmd->execute(['php test', 100]);
		print_r($id);
	}

	public function helperAddAction() {
		$data = [
			'username' => 'new php update',
			'age' => '99',
		];

		$affected = DatabaseHelper::add('user', $data, 'user');
		echo $affected;
	}

	public function helperUpdateAction() {
		$data = [
			'username' => 'new php update2',
			'age' => '999',
		];
		$where = [
			'id <=' => 5,
			'age <' => 24,
		];

		$n = DatabaseHelper::update('user', $data, $where, 'user');
		echo $n;
	}

	public function helperDeleteAction() {
		$where = [
			'id <=' => 5,
			'age <' => 25,
		];

		$n = DatabaseHelper::delete('user', $where, 'user');
		echo $n;
	}

	public function helperQueryAction() {
		$where = [
			'id >' => 1,
			'age <' => 100,
		];

		$rs = DatabaseHelper::fetchAll('user', '*', $where, 'user');
		print_r($rs);
	}
}
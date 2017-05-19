<?php

namespace manage\model;

use gentwolf\DatabaseHelper;
use gentwolf\Model;
use gentwolf\SqlBuilder;

class TableModel extends Model {
    public function getItemList($query, $page, $size) {
        $rs = array(
            'count' => 0,
            'totalPage' => 0,
            'items' => array(),
        );

        $build = $this->buildQuery($query);
        $count = $build->select('COUNT(*)')->fetchScalar();
        $rs['count'] = intval($count);
        if ($rs['count'] > 0) {
            $rs['totalPage'] = ceil($rs['count'] / $size);
            if ($page > $rs['totalPage']) $page = 1;

            $offset = ($page - 1) * $size;

            $build = $this->buildQuery($query);
            $rs['items'] = $build->select($query['field'])
                ->limit($offset, $size)
                ->orderBy($query['order'])
                ->fetchAll();
        }
        $rs['page'] = $page;

        return $rs;
    }

    private function buildQuery($query) {
        $build = SqlBuilder::instance()->from($query['table'])
                ->where($query['where']);
        if (isset($query['join']) && false !== $query['join']) {
            $build->join($query['join']['table'], $query['join']['where']);
        }
        return $build;
    }

    public function delItem($table, $key, $val) {
        return DatabaseHelper::delete($table, array(
            $key => $val
        ));
    }

    public function getItemInfo($table, $key, $val) {
        return DatabaseHelper::fetchRow($table, '*', [
            $key => $val,
        ]);
    }
}
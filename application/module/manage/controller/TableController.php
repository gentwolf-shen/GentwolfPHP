<?php

namespace manage\controller;

use gentwolf\Gentwolf;
use gentwolf\Controller;
use gentwolf\Context;
use gentwolf\Pagination;
use gentwolf\DatabaseUtil;
use manage\model\TableModel;

Class TableController extends Controller {
    /**
     * 加载配置文件
     * @param $m
     * @return null|array
     * @throws Exception
     */
    private function loadConfig($table) {
        $config = Gentwolf::loadConfig($table);
        if (!$config) exit('config/'. $table .'.php config');
        return $config;
    }

    public function listAction($args = null) {
        $table = $args[0];
        $config = $this->loadConfig($table);

        $header = $config['header'];
        $listConfig = $config['list'];

        $page = isset($args[1]) ? $args[1] : 0;
        if (1 > $page) $page = 1;

        $size = $listConfig['size'];

        $model = new TableModel();
        $listItems = $model->getItemList($listConfig['query'], $page, $size);
        $pagination = new Pagination(array(
            'count' => $listItems['count'],
            'page' => $page,
            'size' => $size,
            'url' => '?manage/table/list/'. $table .'/{page}',
        ));

        $action = array(
            'title'        => $header['title'],
            'subTitle'    => $header['listTitle'],
            'eventName'    => $header['addTitle'],
            'eventHref'    => '?manage/table/edit/'. $table .'/0',
        );

        $this->render($config['list']['view'], array(
            'action' => $action,
            'headerItems' => $listConfig['items'],
            'actions' => $listConfig['actions'],
            'listItems' => $listItems,
            'pagination' => $pagination,
        ));
    }

    public function editAction($args) {
        $table = $args[0];
        $config = $this->loadConfig($table);

        $header = $config['header'];

        $action = array(
            'title'     => $header['title'],
            'subTitle'  => $header['addTitle']
        );

        $configEdit = $config['edit'];

        $value = isset($args[1]) ? $args[1] : null;
        $data = false;
        if (null !== $value) {
            $model = new TableModel();
            $data = $model->getItemInfo($configEdit['table'], $configEdit['key'], $value);
        }

        $this->render($configEdit['view'], array(
            'action' => $action,
            'm' => $table,
            'value' => $value,
            'data' => json_encode($data),
            'inputItems' => $configEdit['items'],
        ));
    }

    public function updateAction($args) {
        $table = $args[0];
        $config = $this->loadConfig($table);

        $data = Context::post('data');
        if (!$data){
            Context::error('参数错误');
        } else {
            foreach ($data as &$d) {
                if (is_array($d)) {
                    $d = implode(',', $d);
                }
            }
        }

        $id = 0;
        $value = isset($args[1]) ? $args[1] : null;
        if (!$value) {
            $defaultValue = isset($config['edit']['defaultValue']) ? $config['edit']['defaultValue'] : false;
            if ($defaultValue) {
                foreach ($defaultValue as $name => $value) {
                    $data[$name] = $value;
                }
            }
            $id = DatabaseUtil::add($config['edit']['table'], $data);
        } else {
            DatabaseUtil::update($config['edit']['table'], $data, array(
                $config['edit']['key'] => $value,
            ));
        }

        Context::succeed($id);
    }

    public function delAction($args) {
        $table = $args[0];

        $value = isset($args[1]) ? $args[1] : null;
        if (!$value) Context::error('key错误！');

        $config = $this->loadConfig($table);
        $delConfig = $config['del'];

        $count = DatabaseUtil::delete($delConfig['table'], [
            $delConfig['key'] => $value,
        ]);
        if ($count > 0) {
            Context::succeed('OK');
        } else {
            Context::error('删除失败');
        }
    }
}
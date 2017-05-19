<?php 

namespace manage\controller;

use gentwolf\Controller;
use gentwolf\SqlBuilder;
use gentwolf\Gentwolf;
use gentwolf\Context;
use gentwolf\DatabaseHelper;

class CategoryController extends Controller {
    public function defaultAction() {
        $action = array(
            'title' => '分类管理',
            'subTitle' => '分类列表',
            'eventName' => '添加分类',
            'eventHref' => 'category/edit',
        );

        $rows = SqlBuilder::instance()->select('id,name,parent_id AS pId')
                    ->from('category')
                    ->orderBy('show_order', 'DESC')
                    ->fetchAll();
        if ($rows) {
            foreach ($rows as &$row) {
                if ('0' == $row['pId']) {
                    $row['open'] = true;
                }
            }
        }

        $config = Gentwolf::loadConfig('category', 'edit');
        $this->render('category/default', array(
            'action' => $action,
            'items' => $rows,
            'inputItems' => $config['items'],
        ));
    }

    public function updateAction(){
        $id = Context::getInt('id');

        $data = Context::post('data');
        $data['top_id'] = '0' == $data['parent_id'] ? '0' : $this->getTopId($data['parent_id']);

        if (0 == $id) {
            $id = DatabaseHelper::add('category', $data);
        } else {
			DatabaseHelper::update('category', $data, array(
                'id' => $id,
            ));
        }

        Context::jsonSuccess($id);
    }

    /**
     * 取顶级分类id
     * @param $parentId
     * @return int
     */
    private function getTopId($parentId) {
    	$topId = DatabaseHelper::fetchScalar('category', 'parent_id', array('id' => $parentId));
        return intval($topId);
    }

    public function delAction(){
        $id = Context::postInt('id');
		DatabaseHelper::delete('category', array(
            'id' => $id,
        ));
        Context::jsonSuccess();
    }

    public function infoAction() {
        $id = Context::getInt('id');
        if (0 == $id) Context::jsonError('id错误');

        $fields = 'parent_id AS pId,name,keywords,description,'.
                    'show_order AS showOrder,is_show AS isShow,is_nav AS isNav';
        $row = DatabaseHelper::fetchRow('category', $fields, array('id' => $id));
        Context::jsonSuccess($row);
    }

    public function editOrderAction() {
        $id = Context::postInt('id');
        if (1 > $id) Context::jsonError('id错误');

        $type = Context::postStr('type');
        if (!$type) Context::jsonError('type错误');

        $pId = Context::postInt('pId');
        if (1 > $pId) $pId = 0;

        $data['parent_id'] = $pId;

        if ('inner' != $type){
            $targetId = Context::postInt('targetId');
            if (1 > $targetId) Context::jsonError('target错误');

			$targetOrder = DatabaseHelper::fetchScalar('category', 'show_order', array('id' => $targetId));
            $targetOrder = intval($targetOrder);
            if ('prev' == $type) {
                $targetOrder += 1;
            } else {
                $targetOrder -= 1;
                if ($targetOrder < 0) $targetOrder = 0;
            }
            $data['show_order'] = $targetOrder;
        }

		DatabaseHelper::update('category', $data, array('id' => $id));
        Context::jsonSuccess();
    }
}
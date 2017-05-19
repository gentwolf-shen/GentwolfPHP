<?php

return array(
    'header' => array(
        'title' => '标签管理',
        'listTitle' => '标签列表',
        'addTitle' => '添加标签',
        'editTitle' => '编辑标签',
    ),
    'list' => array(
        'query' => array(
            'field' => 'id,name,name_en AS nameEn,show_order AS showOrder',
            'table' => 'tag',
            'where' => false,
            'order' => 'showOrder DESC',
        ),
        'actions' => array(
            'edit' => array(
                'icon' => 'glyphicon glyphicon-edit',
                'text' => '编辑',
                'key' => 'id',
                'url' => 'table/edit/tag/{KEY}',                
                'confirm' => false,
            ),
            'delete' => array(
                'icon' => 'glyphicon glyphicon-remove',
                'text' => '删除',
                'url' => 'table/del/tag/{KEY}',
                'key' => 'id',
                'confirm' => array(
                    'isAjax' => true,
                    'message' => '确定要删除？'
                ),
            ),
        ),
        'size' => 20,
        'view' => 'list',
        'items' => array(
            'id' => array(
                'name' => '编号',
                'width' => '',
                'attr' => array(
                    'class' => 'text-center',
                ),
                'callback' => false,
            ),            
            'name' => array(
                'name' => '名称',
                'width' => '',
                'attr' => array(
                    'class' => 'text-center',
                ),
                'callback' => false,
            ),
            'nameEn' => array(
                'name' => '英文名称',
                'width' => '',
                'attr' => array(
                    'class' => 'text-center',
                ),
            ),
            'showOrder' => array(
                'name' => '排序',
                'width' => '',
                'attr' => array(
                    'class' => 'text-center',
                ),
                'callback' => false,
            ),
        ),
    ),
    'edit' => array(
        'table' => 'tag',
        'key' => 'id',
        'view' => 'edit',
        'items' => array(
            'name' => array(
                'name' => '中文名称',
                'input' => array(
                    'type' => 'text',
                    'valid' => array(
                        'method' => 'string',
                        'min' => 2,
                        'max' => 10,
                        'maxLength' => 10,
                        'title' => '请输入2~10位中文名称',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ),
                ),
            ),
            'name_en' => array(
                'name' => '英文名称',
                'input' => array(
                    'type' => 'text',
                    'valid' => array(
                        'method' => 'string',
                        'min' => 2,
                        'max' => 20,
                        'maxLength' => 20,
                        'title' => '请输入2~10位英文名称',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ),
                ),
            ),
            'show_order' => array(
                'name' => '排序',
                'input' => array(
                    'type' => 'text',
                    'value' => '1',
                    'valid' => array(
                        'method' => 'integer',
                        'min' => 1,
                        'max' => 65535,
                        'maxLength' => 4,
                        'title' => '排序值为大于0且小9999的数字',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ),
                ),
            ),
        ),
    ),
    'del' => array(
        'table' => 'tag',
        'key' => 'id',
    ),
);
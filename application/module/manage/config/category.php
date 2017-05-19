<?php

return array(
    'edit' => array(
        'table' => 'category',
        'key' => 'id',
        'view' => 'category/edit',
        'items' => array(
            'name' => array(
                'name' => '名称',
                'input' => array(
                    'type' => 'text',
                    'value' => '',
                    'valid' => array(
                        'method' => 'string',
                        'min' => 2,
                        'max' => 10,
                        'maxLength' => 10,
                        'title' => '请输入2~10位名称',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ),
                ),
            ),
            'is_nav' => array(
                'name' => '设为导航',
                'input' => array(
                    'type' => 'radio',
                    'value' => '0',
                    'settings' => array(
                        'type' => 'config',
                        'name' => 'dict',
                        'key' => 'bool',
                    ),
                    'valid' => array(
                        'method' => 'radio',
                        'title' => '请选择一个',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ),
                ),
            ),
            'is_show' => array(
                'name' => '设置上线',
                'input' => array(
                    'type' => 'radio',
                    'value' => '1',
                    'settings' => array(
                        'type' => 'config',
                        'name' => 'dict',
                        'key' => 'bool',
                    ),
                    'valid' => array(
                        'method' => 'radio',
                        'title' => '请选择是否上线',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ),
                ),
            ),
            'show_order' => array(
                'name' => '显示排序',
                'input' => array(
                    'type' => 'text',
                    'value' => '1',
                    'valid' => array(
                        'method' => 'integer',
                        'min' => 1,
                        'max' => 65535,
                        'maxLength' => 4,
                        'title' => '排序值为大于1且小9999的数字',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ),
                ),
            ),
            'keywords' => array(
                'name' => '关键词',
                'input' => array(
                    'type' => 'text',
                    'value' => '',
                    'valid' => array(
                        'method' => 'string',
                        'min' => 0,
                        'max' => 250,
                        'maxLength' => 250,
                        'title' => '请输入0~250位关键词',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ),
                ),
            ),
            'description' => array(
                'name' => '描述',
                'input' => array(
                    'type' => 'textarea',
                    'value' => '',
                    'valid' => array(
                        'method' => 'string',
                        'min' => 0,
                        'max' => 250,
                        'maxLength' => 250,
                        'title' => '请输入0~250位描述',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ),
                ),
            ),
        ),
    ),
);
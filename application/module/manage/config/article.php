<?php

return [
	'header' => [
		'title' => '文章管理',
		'listTitle' => '文章列表',
		'addTitle' => '添加文章',
		'editTitle' => '编辑文章',
	],
	'list' => [
		'query' => [
			'field' => 'id,title',
			'table' => 'article',
			'where' => false,
			'order' => 'id DESC'
		],
		'actions' => [
			'edit' => [
				'icon' => 'glyphicon glyphicon-edit',
				'text' => '编辑',
				'key' => 'id',
				'url' => 'table/edit/article/{KEY}',
				'confirm' => false,
			],
			'delete' => [
				'icon' => 'glyphicon glyphicon-remove',
				'text' => '删除',
				'url' => 'table/del/article/{KEY}',
				'key' => 'id',
				'confirm' => [
					'isAjax' => true,
					'message' => '确定要删除？',
				],
			],			
		],
		'size' => 20,
		'view' => 'table/list',
		'items' => [
			'id' => [
				'name' => '编号',
				'width' => '',
				'attr' => [
					'class' => 'text-center',
				],
				'callback' => false
			],
			'title' => [
				'name' => '标题',
				'width' => '',
				'attr' => [
					'class' => 'text-center',
				],
				'callback' => false,
			],

			/*'addTime' => [
				'name' => '添加时间',
				'width' => '',
				'attr' => [
					'class' => 'text-center',
				],
				'callback' => false,
			],*/
		],
	],
	'edit' => [
		'table' => 'article',
		'key' => 'id',
		'view' => 'table/edit',
		'items' => [
			'title' => [
				'name' => '标题',
				'input' => [
					'type' => 'text',
					'valid' => [
						'method' => 'string',
						'min' => '2',
						'max' => '10',
						'maxLength' => 10,
						'title' => '请至少输入2个字的标题',
						'data-toggle' => 'tooltip',
						'data-placement' => 'left',
					],
				],
			],
			'category_id' => [
				'name' => '分类',
				'input' => [
					'type' => 'category',
					'settings' => [
						'default' => ['0', '请选择分类'],
						'topCategoryId' => '0',
					],
					'valid' => [
						'method' => 'integer',
						'min' => '1',
						'max' => '999999',
						'maxlength' => 99999,
						'title' => '请选择分类',
						'data-toggle' => 'tooltip',
						'data-placement' => 'left',
					],
				],
			],
			'keywords' => [
                'name' => '关键词',
                'input' => [
                    'type' => 'text',
                    'valid' => [
                        'method' => 'string',
                        'min' => 0,
                        'max' => 250,
                        'maxLength' => 250,
                        'title' => '请输入0~250位关键词',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ],
                ],
            ],
            'description' => [
                'name' => '简介',
                'input' => [
                    'type' => 'textarea',
                    'value' => '',
                    'valid' => [
                        'method' => 'string',
                        'min' => 0,
                        'max' => 250,
                        'maxLength' => 250,
                        'title' => '请输入0~250简介',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ],
                ],
            ],
            'is_show' => [
                'name' => '是否上线',
                'input' => [
                    'type' => 'radio',
                    'value' => '1',
                    'settings' => [
                        'type' => 'config',
                        'name' => 'dict',
                        'key' => 'bool',
                    ],
                    'valid' => [
                        'method' => 'radio',
                        'title' => '请选择是否上线',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ],
                ],
            ],
            /*'tags' => [
                'name' => '标签',
                'input' => [
                    'type' => 'checkbox',
                    'settings' => [
                        'type' => 'db',
                        'field' => 'id AS k,name AS v',
                        'table' => 'tag',
                        'where' => NULL,
                        'order' => 'show_order DESC',
                    ],
                    'valid' => [
                        'method' => 'count',
                        'min' => 2,
                        'max' => 9999,
                        'title' => '请至少选择2个标签',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ],
                ],
            ],*/
            'content' => [
                'name' => '内容',
                'input' => [
                    'type' => 'editor',
                    'settings' => [
                        'height' => '500px',
                    ],
                    'valid' => [
                        'method' => 'string',
                        'min' => 1,
                        'max' => 99999999,
                        'title' => '请输入文章内容',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                    ],
                ],
            ],
        ],
        'defaultValue' => [
            'add_time' => time()
        ],
	],
	'del' => array(
		'table' => 'article',
		'key' => 'id',
	),
];
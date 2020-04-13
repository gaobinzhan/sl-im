<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */
return [
    [
        'title' => '好友管理',
        'child' => [
            [
                'title' => '创建分组',
                'id' => 'createFriendGroup',
                'url' => '/static/createFriendGroup',
                'width' => '550px',
                'height' => '400px',
            ],
            [
                'title' => '查找好友',
                'id' => 'findUser',
                'url' => '/static/findUser',
                'width' => '1000px',
                'height' => '520px',
            ]
        ]
    ],
    [
        'title' => '群管理',
        'child' => [
            [
                'title' => '创建群',
                'id' => 'createGroup',
                'url' => '/static/createGroup',
                'width' => '550px',
                'height' => '480px',
            ],
            [
                'title' => '查找群',
                'id' => 'findGroup',
                'url' => '/static/findGroup',
                'width' => '1000px',
                'height' => '520px',
            ]
        ]
    ],
    [
        'title' => '其它',
        'child' => [
            [
                'title' => '作者博客',
                'id' => 'blog',
                'url' => 'https://blog.gaobinzhan.com/',
                'width' => '1000px',
                'height' => '520px',
            ],
            [
                'title' => '关于',
                'id' => 'about',
                'url' => '/static/about',
                'width' => '1000px',
                'height' => '520px',
            ]
        ]
    ]
];

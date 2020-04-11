<?php

return [
    'fdToUser' => [
        'size' => 1024 * 5,
        'columns' => [
            'userId' => [
                'type' => \Swoole\Table::TYPE_INT,
                'size' => 4
            ]
        ]
    ],
    'userToFd' => [
        'size' => 1024 * 5,
        'columns' => [
            'fd' => [
                'type' => \Swoole\Table::TYPE_INT,
                'size' => 4
            ]
        ]
    ]
];

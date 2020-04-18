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
    ],
    'subjectFdToUser' => [
        'size' => 1024 * 5,
        'columns' => [
            'userId' => [
                'type' => \Swoole\Table::TYPE_INT,
                'size' => 4
            ]
        ]
    ],
    'subjectUserToFd' => [
        'size' => 1024 * 5,
        'columns' => [
            'fd' => [
                'type' => \Swoole\Table::TYPE_INT,
                'size' => 4
            ]
        ]
    ],
    'subjectToUser' => [
        'size' => 1024 * 5,
        'columns' => [
            'userId' => [
                'type' => \Swoole\Table::TYPE_STRING,
                'size' => 40
            ]
        ]
    ],
    'userToSubject' => [
        'size' => 1024 * 5,
        'columns' => [
            'subject' => [
                'type' => \Swoole\Table::TYPE_STRING,
                'size' => 32
            ]
        ]
    ]
];

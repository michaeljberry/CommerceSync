<?php

$sku = 'sku';
$where = 'stuff';
//    $subQuery = [
//        'SELECT' => [
//            [
//                'IFITEM',
//                'IFQOH',
//                ['row_number() OVER (PARTITION BY IFITEM ORDER BY IFITEM)' => 'SEQNUM']
//            ],
//            'R37MODSDTA/VINITMB'
//        ],
//        'WHERE' => [
//            [
//                [
//                    [
//                        'R37MODSDTA/VINITMB',
//                        'IFDEL',
//                        '=',
//                        'I'
//                    ],
//                    'AND',
//                    [
//                        'R37MODSDTA/VINITMB',
//                        'IFQOH',
//                        '>',
//                        '2'
//                    ]
//                ],
//                'OR',
//                [
//                    'R37MODSDTA/VINITMB',
//                    'IFDEL',
//                    '!=',
//                    'I'
//                ]
//            ],
//            'AND',
//            [
//                'R37MODSDTA/VINITMB',
//                'IFCOMP',
//                '!=',
//                '5'
//            ],
//            'AND',
//            [
//                [
//                    'R37MODSDTA/VINITMB',
//                    'IFLOC',
//                    '=',
//                    '1'
//                ],
//                'OR',
//                [
//                    'R37MODSDTA/VINITMB',
//                    'IFLOC',
//                    '=',
//                    '1Z'
//                ]
//            ]
//        ],
//        'GROUP BY' => [
//            [
//                'R37MODSDTA/VINITMB',
//                'IFITEM'
//            ],
//            [
//                'R37MODSDTA/VINITMB',
//                'IFQOH'
//            ]
//        ]
//    ];
$sql = [
    'SELECT' => [
        [
            'ROUND(SUM(price), 2)' => 'sales',
            'SUM(quantity)' => 'units_sold'
        ],
        'order_item'
    ],
    'JOIN' => [
        [
            'JOIN',
            'order',
            'id',
            '=',
            'order_item',
            'order_id',
            'DATE(date)'
        ],
        [
            'JOIN',
            'order_sync',
            'order_id',
            '=',
            'order',
            'order_num',
            'type'
        ]
    ],
    'WHERE' => [
        'order',
        'date',
        'BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND NOW()'
    ],
    'GROUP BY' => [
        [
            'order',
            'DATE(date)'
        ],
        [
            'order_sync',
            'type'
        ]
    ]
//    'SELECT' => [
//        [
//            'order_id',
//            'type',
//            'item_id',
//            'processed'
//        ],
//        'order_sync'
//    ],
//    'JOIN' => [
//        [
//            'JOIN',
//            'order',
//            'order_num',
//            '=',
//            'order_sync',
//            'order_id',
//            'processed'
//        ],
//        [
//            'JOIN',
//            'order_item',
//            'order_id',
//            '=',
//            'order_sync',
//            'id',
//            'item_id'
//        ]
//    ],
//    'WHERE' => [
//        [
//            'order_sync',
//            'track_successful',
//            'IS NULL',
//        ],
//        'AND',
//        [
//            'order',
//            'cancelled',
//            'NOT IN',
//            '(1,2,3,4,5)',
//        ],
//        'AND',
//        [
//            'order_sync',
//            'type',
//            '=',
//            ':channel'
//        ]
//    ],
//    'ORDER BY' => [
//        'order_sync',
//        'processed'
//    ]
//        'SELECT' => [
//            'selectColumns' =>
//                [
//                    'sku',
//                    ['price' => 'cost'],
//                    'url'
//                ],
//            'selectTable' => 'listing_ebay'
//        ],
//        'SELECT' => [
//            [
//                $sku,
//                ['price' => 'cost'],
//                'url'
//            ],
//            'listing_ebay'
//        ],
//        'SELECT' => [
//            'sku',
//            'listing_ebay'
//        ],
//        'SELECT' => [
//            ['price' => 'cost'],
//            'listing_ebay'
//        ],
//        'JOIN' => [
//            [
//                'joinType' => 'LEFT OUTER JOIN',
//                'joinTable' => 'sku',
//                'joinTableColumnName' => 'id',
//                'joinOperator' => '>=',
//                'joinToTable' => 'listing_ebay',
//                'joinToTableColumnName' => 'sku_id',
//                'joinColumns' => ['msrp', 'name'],
//            ],
//            [
//                'joinType' => 'INNER JOIN',
//                'joinTable' => 'stock',
//                'joinTableColumnName' => 'sku_id',
//                'joinOperator' => '=',
//                'joinToTable' => 'sku',
//                'joinToTableColumnName' => 'id',
//                'joinColumns' => [
//                    ['qoh' => 'stock'],
//                    'last_updated'
//                ],
//            ],
//        ],
//        'JOIN' => [
//            [
//                'LEFT OUTER JOIN',
//                'sku',
//                'id',
//                '>=',
//                'listing_ebay',
//                'sku_id',
//                ['msrp', 'name'],
//            ],
//            [
//                'INNER JOIN',
//                'stock',
//                'sku_id',
//                '=',
//                'sku',
//                'id',
//                [
//                    ['qoh' => 'stock'],
//                    'last_updated'
//                ],
//            ],
//            [
//                'LEFT INNER JOIN',
//                'VINITMB',
//                'IFITEM',
//                '=',
//                'listing_ebay',
//                'id',
//                [
//                    ['IFDEL' => 'deleted'],
//                    'IFQOH'
//                ],
//            ]
//        ],
//        'JOIN' => [
//            'LEFT OUTER JOIN',
//            'sku',
//            'id',
//            '>=',
//            'listing_ebay',
//            'sku_id',
//            [['msrp' => 'cost'], 'name'],
//        ],
//        'JOIN' => [
//            'LEFT OUTER JOIN',
//            'sku',
//            'id',
//            '>=',
//            'listing_ebay',
//            'sku_id',
//            'msrp',
//        ],
//        'JOIN' => [
//            'joinType' => 'LEFT OUTER JOIN',
//            'joinTable' => 'sku',
//            'joinTableColumnName' => 'id',
//            'joinOperator' => '>=',
//            'joinToTable' => 'listing_ebay',
//            'joinToTableColumnName' => 'sku_id',
//            'joinColumns' => 'msrp',
//        ],
//        'WHERE' => [
//            [
//                'whereTable' => 'sku',
//                'whereTableColumnName' => 'sku',
//                'whereOperator' => 'LIKE',
//                'whereValue' => '%PAP%',
//            ],
//            'AND',
//            [
//                'whereTable' => 'stock',
//                'whereTableColumnName' => 'qoh',
//                'whereOperator' => '>',
//                'whereValue' => '1',
//            ],
//            'OR',
//            [
//                'whereTable' => 'listing_ebay',
//                'whereTableColumnName' => 'store_id',
//                'whereOperator' => 'NOT IN',
//                'whereValue' => '(1,2,3,4,5)',
//            ],
//        ],
//        'WHERE' => [
//            [
//                'sku',
//                'sku',
//                '=',
//                "%$where%",
//            ],
//            'AND',
//            [
//                'listing_ebay',
//                'store_id',
//                'NOT IN',
//                '(1,2,3,4,5)',
//            ],
//            'AND',
//            [
//                'listing_ebay',
//                'sku',
//                '!=',
//                '2',
//            ],
//        ],
//        'WHERE' =>
//        [
//            [
//                [
//                    [
//                        'VINITMB',
//                        'IFDEL',
//                        '=',
//                        'I',
//                    ],
//                    'AND',
//                    [
//                        'VINITMB',
//                        'IFWOH',
//                        '<',
//                        '3'
//                    ]
//                ],
//                'OR',
//                [
//                    'VINITMB',
//                    'IFDEL',
//                    '=',
//                    'N'
//                ],
//                'OR',
//                [
//                    [
//                        'VINITMB',
//                        'IFQOH',
//                        '<',
//                        '2'
//                    ],
//                    'AND',
//                    [
//                        'VIOSELLCRA',
//                        'IFLOD',
//                        '=',
//                        '8'
//                    ]
//                ]
//            ],
//            'AND',
//            [
//                [
//                    'VINITMB',
//                    'IFCOMP',
//                    '!=',
//                    '5'
//                ],
//                'OR',
//                [
//                    'VINITMB',
//                    'IFCOMP',
//                    '!=',
//                    '2'
//                ]
//            ],
//            'AND',
//            [
//                'VINITMB',
//                'IFLOC',
//                '=',
//                '4'
//            ],
//            'OR',
//            [
//                'VINITMB',
//                'IFLOC',
//                '=',
//                '4'
//            ]
//        ],
//        'WHERE' => [
//            [
//                'listing_ebay',
//                'store_id',
//                'NOT IN',
//                '(1,2,3,4,5)',
//                'AND',
//            ],
//        ],
//        'WHERE' => [
//            'listing_ebay',
//            'store_id',
//            'NOT IN',
//            '(1,2,3,4,5)'
//        ],
//        'WHERE' => [
//            'whereTable' => 'listing_ebay',
//            'whereTableColumnName' => 'store_id',
//            'whereOperator' => 'NOT IN',
//            'whereValue' => '(1,2,3,4,5)'
//        ],
//        'GROUP BY' => [
//            [
//                'groupByTable' => 'sku',
//                'groupByColumn' => 'sku',
//                'groupByOrder' => 'DESC'
//            ],
//            [
//                'groupByTable' => 'listing_ebay',
//                'groupByColumn' => 'country',
//                'groupByOrder' => 'ASC'
//            ],
//        ],
//        'GROUP BY' => [
//            [
//                'sku',
//                'sku',
//                'DESC'
//            ],
//            [
//                'listing_ebay',
//                'country',
//                'ASC'
//            ],
//            [
//                'stock',
//                'qoh',
//                'ASC'
//            ],
//        ],
//        'GROUP BY' => [
//            [
//                'sku',
//                'sku',
//                'DESC'
//            ],
//        ],
//        'GROUP BY' => [
//            'groupByTable' => 'sku',
//            'groupByColumn' => 'sku',
//            'groupByOrder' => 'DESC'
//        ],
//        'GROUP BY' => [
//            'sku',
//            'sku',
////            'DESC'
//        ],
//        'ORDER BY' => [
//            [
//                'orderByTable' => 'listing_ebay',
//                'orderByColumn' => 'sku',
//                'sortOrder' => 'DESC'
//            ],
//            [
//                'orderByTable' => 'stock',
//                'orderByColumn' => 'qoh',
//                'sortOrder' => 'ASC'
//            ],
//        ],
//        'ORDER BY' => [
//            [
//                'listing_ebay',
//                'sku',
//                'DESC'
//            ],
//            [
//                'stock',
//                'qoh',
//                'ASC'
//            ],
//            [
//                'sku',
//                'name',
//                'ASC'
//            ],
//        ],
//        'ORDER BY' => [
//            [
//                'listing_ebay',
//                'sku',
//                'DESC'
//            ],
//        ],
//        'ORDER BY' => [
//            'listing_ebay',
//            'sku',
//            'DESC'
//        ],
//        'ORDER BY' => [
//            'listing_ebay',
//            'sku',
//        ],
//        'SELECT' => [
//            [
//                'IFITEM',
//                'IFQOH'
//            ],
//            [$subQuery]
//        ],
//        'WHERE' => [
//            '**SUB**',
//            'seqnum',
//            '=',
//            '1'
//        ],
//        'ORDER BY' => [
//            '**SUB**',
//            'IFITEM'
//        ]
];
<?php

return [
    'rbac' => [
        'OrderCompleted' => ['OrderService', 'Admin'],
        'OrderPaid' => ['OrderService', 'Admin'],
    ],
];
<?php

return [
    'default_guard' => 'web',
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],
    'policies' => [
        'employee' => [
            'routes' => [
                '/',
                '/dashboard',
                '/hours',
                '/submit',
                '/payslips',
            ],
        ],
        'admin' => [
            'routes' => [
                '/admin',
                '/admin/approvals',
                '/admin/employees',
                '/admin/pay-runs',
                '/admin/reports',
                '/admin/config',
                '/admin/forms',
                '/admin/audit',
            ],
        ],
    ],
];

<?php
/**
 * Set all needed imports
 *
 * @package Apison
 */

return  [
    'shared' => [
        'shared/option',
        'shared/transient'
    ],
    'admin' => [
        'admin/defaults',
        'admin/validation',
        'admin/settings'
    ],
    'frontend' => [
        'frontend/api',
        'frontend/endpoints'
    ]
];

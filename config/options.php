<?php

/**
 * @var $config DT\Home\CodeZone\WPSupport\Config\ConfigInterface
 */

$config->merge( [
    'options' => [
        'prefix' => 'dt_home',
        'defaults' => [
            'require_login' => true,
            'apps' => [],
            'trainings' => [],
        ],
    ]
] );
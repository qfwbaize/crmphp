<?php


// 中间件配置


return [
    'alias' => [
        'priority' => app\middleware\encryption::class,
        'verification' => app\middleware\verification::class,
        'auths' => app\middleware\Auths::class,
        'log' => app\middleware\Log::class,
        'company' => app\middleware\company::class,

        ],
    ];

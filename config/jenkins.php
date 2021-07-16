<?php

return [
    'username' => env('JENKINS_USERNAME'),
    'api_token' => env('JENKINS_API_TOKEN'),
    'webhook' => env('JENKINS_WEBHOOK'),
    'webhooks' => [
        'neural' => env('JENKINS_WEBHOOK_NEURAL'),
        'furniture' => env('JENKINS_WEBHOOK_FURNITURE'),
        'unreal' => env('JENKINS_WEBHOOK_UNREAL'),
    ],
    'paths' => [
        'neural' => env('JENKINS_PATH_NEURAL'),
        'furniture' => env('JENKINS_PATH_FURNITURE'),
        'unreal' => env('JENKINS_PATH_UNREAL'),
    ],
];

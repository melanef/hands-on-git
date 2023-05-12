<?php

$container->loadFromExtension('framework', [
    'http_method_override' => false,
    'validation' => [
        'mapping' => [
            'paths' => [
                '%kernel.project_dir%/Fixtures/TestBundle/Resources/config/validation_mapping/files',
                '%kernel.project_dir%/Fixtures/TestBundle/Resources/config/validation_mapping/validation.yml',
                '%kernel.project_dir%/Fixtures/TestBundle/Resources/config/validation_mapping/validation.yaml',
            ],
        ],
    ],
]);

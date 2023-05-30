<?php return array(
    'root' => array(
        'name' => 'srag/h5p-cron',
        'pretty_version' => 'dev-develop',
        'version' => 'dev-develop',
        'reference' => 'c9da048f77ba217fd19d72779e9d6acd351a4ae4',
        'type' => 'project',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'roave/security-advisories' => array(
            'pretty_version' => 'dev-latest',
            'version' => 'dev-latest',
            'reference' => '2d73bad6cb1c8cda0ab4e4208f4ee2d70e472879',
            'type' => 'metapackage',
            'install_path' => NULL,
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => true,
        ),
        'srag/h5p-cron' => array(
            'pretty_version' => 'dev-develop',
            'version' => 'dev-develop',
            'reference' => 'c9da048f77ba217fd19d72779e9d6acd351a4ae4',
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);

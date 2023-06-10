<?php return array(
    'root' => array(
        'name' => '__root__',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => NULL,
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        '__root__' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => NULL,
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'fgrosse/phpasn1' => array(
            'pretty_version' => 'v2.5.0',
            'version' => '2.5.0.0',
            'reference' => '42060ed45344789fb9f21f9f1864fc47b9e3507b',
            'type' => 'library',
            'install_path' => __DIR__ . '/../fgrosse/phpasn1',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'mdanter/ecc' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '1.0.0',
            ),
        ),
        'public-square/phpecc' => array(
            'pretty_version' => 'v0.1.2',
            'version' => '0.1.2.0',
            'reference' => '683178803e97406ad16a0ef811768eecdc6ef33e',
            'type' => 'library',
            'install_path' => __DIR__ . '/../public-square/phpecc',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);

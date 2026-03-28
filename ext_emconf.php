<?php

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2026 by C. Gogolin <service@cylancer.net>
 *
 */

 $EM_CONF[$_EXTKEY] = [
    'title' => 'Download library',
    'description' => 'By means of this extension FE users can provide downloads.',
    'category' => 'plugin',
    'author' => 'C. Gogolin',
    'author_email' => 'service@cylancer.net',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'version' => '3.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.00-13.4.99',
            'bootstrap_package' => '15.0.0-16.9.99'
            
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];



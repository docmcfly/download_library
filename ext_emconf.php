<?php

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C. Gogolin <service@cylancer.net>
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
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.00-13.4.99',
            'bootstrap_package' => '15.0.0-15.9.99'
            
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];


/** CHANGE-LOG
 * 3.0.0   UPD  :: to TYPO3 13.4.x
 * 2.0.0   UPD  :: to TYPO3 12.4.x
 * 1.0.1   FIX  :: Remove button is visible for document owner. 
 * 1.0.0   FIX  :: Fix the plugin configuration/registry.
 * 0.1.1   BUG  :: Remove debug outout. 
 * 0.1.0   BUG  :: The document remove button is display only for the document owner. 
 * 0.0.13  BUG  :: fixing the caching
 * 0.0.12  BUG  :: fix the redirect after upload.
 * 0.0.9   INIT :: First beta',
 */
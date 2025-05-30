<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C. Gogolin <service@cylancer.net>
 *
 */

return [
    'ctrl' => [
        'title' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download',
        'label' => 'title',
        'descriptionColumn' => 'status',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'status' => 'status',
        ],
        'searchFields' => 'status',
        'iconfile' => 'EXT:cy_download_library/Resources/Public/Icons/document.svg'
    ],
    'types' => [
        '1' => [
            'showitem' => 'file, owner, status, final, archived, '
        ]
    ],
    'columns' => [
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.title',
            'config' => [
                'readOnly' => true,
                'type' => 'input',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'file' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.file',
            'config' => [
                'type' => 'file',
                'maxitems' => 1,
                'minitems' => 1,
                'readOnly' => false,
            ]
        ],
        'owner' => [
            'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.owner',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'minitems' => 1,
                'maxitems' => 1,
                'readOnly' => false,
            ]
        ],
        'final' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.properties',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                // 'readOnly' => true,
                'items' => [
                    [
                        'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.property.final',
                    ], 
                ]
            ]
        ],
        'archived' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.properties',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'readOnly' => false,
                'items' => [
                    [
                        'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.property.archived',
                    ], 
                ]

            ]
        ],
        'status' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.status',
            'config' => [ 
                'type' => 'datetime',
                'format' => 'date',
                'default' => 0,
                'readOnly' => true,
            ]
        ],

    ]
];

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
        'cruser_id' => 'cruser_id',
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
            'showitem' => 'file, owner, status, final, archived'
        ]
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    [
                        '',
                        0
                    ]
                ],
                'foreign_table' => 'tx_cydownloadlibrary_domain_model_document',
                'foreign_table_where' => 'AND {#tx_cydownloadlibrary_domain_model_document}.{#pid}=###CURRENT_PID### AND {#tx_cydownloadlibrary_domain_model_document}.{#sys_language_uid} IN (-1,0)'
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ]
            ]
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],
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
            'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.final',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
               // 'readOnly' => true,
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ]

            ]
        ],
        'archived' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.archived',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'readOnly' => false,
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ]

            ]
        ],
        'status' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_download_library/Resources/Private/Language/locallang_db.xlf:tx_downloadlibrary_domain_model_download.status',
            'config' => [
                'readOnly' => true,
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date,int',
                'dbType' => 'date',
                'default' => 0,
            ]
        ],

    ]
];

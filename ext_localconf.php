<?php
use Cylancer\DownloadLibrary\Controller\DocumentBoardController;

defined('TYPO3') || die('Access denied.');

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'DownloadLibrary',
        'DocumentBoard',
        [
            DocumentBoardController::class => 'show, upload, removeDocument, archiveDocument'
        ],
        // non-cacheable actions
        [
            DocumentBoardController::class => 'show, upload, removeDocument, archiveDocument'
        ]
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('mod {
        wizards.newContentElement.wizardItems.plugins {
            elements {
                downloadlibrary-plugin-documentboard {
                    iconIdentifier = downloadlibrary-plugin-documentboard
                    title = LLL:EXT:download_library/Resources/Private/Language/locallang_be_documentboard.xlf:plugin.name
                    description = LLL:EXT:download_library/Resources/Private/Language/locallang_be_documentboard.xlf:plugin.description
                    tt_content_defValues {
                        CType = list
                        list_type = downloadlibrary_documentboard
                    }
                }
            }
            show = *
        }
    }');

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon('downloadlibrary-plugin-documentboard', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:download_library/Resources/Public/Icons/downloadlibrary_plugin_documentboard.svg'
    ]);
});


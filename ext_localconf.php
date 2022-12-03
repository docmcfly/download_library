<?php
use Cylancer\DownloadLibrary\Controller\DocumentBoardController;

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin( //
    'Cylancer.DownloadLibrary', //
    'DocumentBoard', //
    [
        DocumentBoardController::class => 'show, upload, removeDocument, archiveDocument'
    ], 
        // non-cacheable actions
        [
            DocumentBoardController::class => 'show, upload, removeDocument, archiveDocument'
        ]);

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
    // $iconRegistry->registerIcon('downloadlibrary-plugin-settings', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
    // 'source' => 'EXT:download_library/Resources/Public/Icons/downloadlibrary_plugin_settings.svg'
    // ]);
});

// // Add download for optimizing database tables
// $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['downloads'][\Cylancer\DownloadLibrary\Download\DownloadLibraryInformerDownload::class] = [
//     'extension' => 'downloadlibrary',
//     'title' => 'LLL:EXT:download_library/Resources/Private/Language/locallang.xlf:download.downloadLibraryInformer.title',
//     'description' => 'LLL:EXT:download_library/Resources/Private/Language/locallang.xlf:download.downloadLibraryInformer.description',
//     'additionalFields' => \Cylancer\DownloadLibrary\Download\DownloadLibraryInformerAdditionalFieldProvider::class
// ];
    

    



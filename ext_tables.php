<?php
use Cylancer\DownloadLibrary\Controller\DocumentBoardController;

defined('TYPO3_MODE') || die('Access denied.');


call_user_func(
    function()
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Cylancer.DownloadLibrary',
            'DocumentBoard',
            'LLL:EXT:download_library/Resources/Private/Language/locallang_be_documentboard.xlf:plugin.name',
            'EXT:download_library/Resources/Public/Icons/downloadlibrary_plugin_documentboard.svg'
            );
//         \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
//             'Cylancer.DownloadLibrary',
//             'Settings',
//             'LLL:EXT:download_library/Resources/Private/Language/locallang_be_settings.xlf:plugin.name',
//             'EXT:download_library/Resources/Public/Icons/downloadlibrary_plugin_settings.svg'
//             );
        
}
);

// \TYPO3\CMS\Core\Utility\ExtensionLibraryUtility::addStaticFile('download_library', 'Configuration/TypoScript', 'DownloadLibrary');
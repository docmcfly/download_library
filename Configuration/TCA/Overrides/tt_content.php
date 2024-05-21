<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

(static function (): void{

    ExtensionUtility::registerPlugin(
        'DownloadLibrary',
        'DocumentBoard',
        'LLL:EXT:download_library/Resources/Private/Language/locallang_be_documentboard.xlf:plugin.name',
        'EXT:download_library/Resources/Public/Icons/downloadlibrary_plugin_documentboard.svg'
    );


    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['downloadlibrary_documentboard'] = 'pi_flexform';

    ExtensionManagementUtility::addPiFlexFormValue(
        // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
        'downloadlibrary_documentboard',
        // Flexform configuration schema file
        'FILE:EXT:download_library/Configuration/FlexForms/DocumentBoard.xml'
    );

})();
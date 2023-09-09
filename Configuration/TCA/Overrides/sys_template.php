<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function () {

    ExtensionManagementUtility::addStaticFile('download_library', 'Configuration/TypoScript', 'DownloadLibrary');

});
<?php
use Cylancer\CyDownloadLibrary\Controller\DocumentBoardController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C. Gogolin <service@cylancer.net>
 *
 */

 defined('TYPO3') || die('Access denied.');

 ExtensionUtility::configurePlugin(
    'CyDownloadLibrary',
    'DocumentBoard',
    [
        DocumentBoardController::class => 'show, upload, removeDocument, archiveDocument'
    ],
    // non-cacheable actions
    [
        DocumentBoardController::class => 'show, upload, removeDocument, archiveDocument'
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

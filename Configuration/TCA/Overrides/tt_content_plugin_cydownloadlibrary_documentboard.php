<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Schema\Struct\SelectItem;

defined('TYPO3') or die();

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C. Gogolin <service@cylancer.net>
 *
 */

 $extension = 'cyDownloadLibrary';
$extensionDir = 'cy_download_library';
$plugin = 'documentBoard';


$signatur = strtolower("{$extension}_{$plugin}");
$iconIdentifier = "{$extension}-{$plugin}";

$translationPath = "LLL:EXT:{$extensionDir}/Resources/Private/Language/locallang_be_{$plugin}.xlf:";

ExtensionManagementUtility::addPlugin(
    new SelectItem(
        'select',
        "{$translationPath}plugin.name",
        $signatur,
        $iconIdentifier,
        $extension,
        "{$translationPath}plugin.description",
    ),
    'CType',
    $extension
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    "--div--;{$translationPath}flexforms_general.title,pi_flexform, pages",
    $signatur,
    'after:palette:headers'
);

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    "FILE:EXT:{$extensionDir}/Configuration/Flexforms/{$plugin}.xml",
    $signatur,
);

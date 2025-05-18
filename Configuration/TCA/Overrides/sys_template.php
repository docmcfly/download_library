<?php
declare(strict_types=1);

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C. Gogolin <service@cylancer.net>
 *
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die('Access denied.');

call_user_func(function () {

    ExtensionManagementUtility::addStaticFile('download_library', 'Configuration/TypoScript', 'DownloadLibrary');

});
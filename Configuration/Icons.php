<?php

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C. Gogolin <service@cylancer.net>
 *
 */

 $icons = [];
foreach (['documentBoard'] as $key) {
    $icons['cyDownloadLibrary-' . $key] = [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:cy_download_library/Resources/Public/Icons/Plugins/' . $key . '.svg',
    ];

}
return $icons;

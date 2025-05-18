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

use Cylancer\CyDownloadLibrary\Domain\Model\FrontendUser;
use Cylancer\CyDownloadLibrary\Domain\Model\FrontendUserGroup;

return [
    FrontendUser::class => [
        'tableName' => 'fe_users'
    ],
    FrontendUserGroup::class => [
        'tableName' => 'fe_groups'
    ],
];
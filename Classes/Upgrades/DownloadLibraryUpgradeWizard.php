<?php

declare(strict_types=1);

namespace Cylancer\CyDownloadLibrary\Upgrades;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C. Gogolin <service@cylancer.net>
 *
 */

#[UpgradeWizard('downloadLibrary_downloadLibraryUpgradeWizard')]
final class DownloadLibraryUpgradeWizard implements UpgradeWizardInterface
{

    private PersistenceManager $persistentManager;

    private ResourceFactory $resourceFactory;

    public function __construct()
    {
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
    }


    public function getTitle(): string
    {
        return 'Migration the download library to the new database table';
    }

    public function getDescription(): string
    {
        return "Creates for all old download library entries new download entries and fix the file references. ";
    }

    public function executeUpdate(): bool
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connectionPool
            ->getConnectionForTable('tx_cydownloadlibrary_domain_model_document')
            ->prepare('INSERT INTO `tx_cydownloadlibrary_domain_model_document` '
                . '( `uid`, `pid`, `tstamp`, `crdate`, `deleted`, `hidden`, `starttime`, `endtime`, `sys_language_uid`,'
                .' `l10n_parent`, `l10n_state`, `l10n_diffsource`, `t3ver_oid`, `t3ver_wsid`, `t3ver_state`, `t3ver_stage`,'
                .' `title`, `file`, `owner`, `final`, `archived`, `status`)'
                . ' SELECT '
                . ' `uid`, `pid`, `tstamp`, `crdate`, `deleted`, `hidden`, `starttime`, `endtime`, `sys_language_uid`,'
                .' `l10n_parent`, `l10n_state`, `l10n_diffsource`,  `t3ver_oid`, `t3ver_wsid`, `t3ver_state`, `t3ver_stage`,'
                .' `title`, `file_reference`,`owner`, `final`, `archived`, `status` '
                . ' FROM `tx_downloadlibrary_domain_model_document`')->executeStatement();

        $connectionPool->getConnectionForTable('sys_file_reference')->prepare("UPDATE `sys_file_reference` SET `tablenames` = 'tx_cydownloadlibrary_domain_model_document', `fieldname` = 'file' WHERE `tablenames` = 'tx_downloadlibrary_domain_model_document' ")->executeStatement();
        return true;
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     *
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function updateNecessary(): bool
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool
            ->getConnectionForTable('sys_file_reference')
            ->count(
                '*',
                'sys_file_reference',
                ['tablenames' => 'tx_downloadlibrary_domain_model_document'],
            ) > 0;
    }

    /**
     * Returns an array of class names of prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [];
    }
}
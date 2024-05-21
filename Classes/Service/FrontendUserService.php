<?php
namespace Cylancer\DownloadLibrary\Service;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Context\Context;
use Cylancer\DownloadLibrary\Domain\Repository\FrontendUserRepository;
use Cylancer\DownloadLibrary\Domain\Model\FrontendUser;
use Cylancer\DownloadLibrary\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 by Clemens Gogolin <service@cylancer.net>
 *
 * @package Cylancer\DownloadLibrary\Service
 */
class FrontendUserService implements SingletonInterface
{

    /** @var FrontendUserRepository   */
    private $frontendUserRepository = null;

    /**
     *
     * @param FrontendUserRepository $frontendUserRepository
     */
    public function __construct(FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     *
     * @param object $obj
     * @return int
     */
    public static function getUid(AbstractEntity $entity): int
    {
        return $entity->getUid();
    }

    /**
     *
     * @return FrontendUser Returns the current frontend user
     */
    public function getCurrentUser(): FrontendUser|bool
    {
        if (!$this->isLogged()) {
            return false;
        }
        return $this->frontendUserRepository->findByUid($this->getCurrentUserUid());
    }

    /**
     *
     * @return int
     */
    public function getCurrentUserUid(): int
    {
        if (!$this->isLogged()) {
            return false;
        }
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('frontend.user', 'id');
    }

    /**
     * Check if the user is logged
     *
     * @return bool
     */
    public function isLogged(): bool
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

    /**
     *
     * @param FrontendUserGroup $userGroup
     * @param integer $fegid
     * @param array $loopProtect
     * @return boolean
     */
    public function contains($userGroup, $feugid, &$loopProtect = array()): bool
    {
        if ($userGroup->getUid() == $feugid) {
            return true;
        } else {
            if (!in_array($userGroup->getUid(), $loopProtect)) {
                $loopProtect[] = $userGroup->getUid();
                foreach ($userGroup->getSubgroup() as $sg) {
                    if ($this->contains($sg, $feugid, $loopProtect)) {
                        return true;
                    }
                }
            }
            return false;
        }
    }



    /**
     *
     * @param string $table
     * @return QueryBuilder
     */
    protected function getQueryBuilder(string $table): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    }

    /**
     * Returns an array with all subgroups of the frontend user to the root of groups...
     *
     * @param FrontendUser $frontendUser
     * @return array
     */
    public function getUserSubGroups(FrontendUser $frontendUser): array
    {
        $return = [];
        foreach ($frontendUser->getUsergroup() as $ug) {
            $return = array_merge($return, $this->_getSubgroups($ug, $return));
        }
        return $return;
    }



    private function _getSubgroups(FrontendUserGroup $frontendUserGroup, array &$return = []): array
    {
        $return[] = $frontendUserGroup->getUid();
        foreach ($frontendUserGroup->getSubgroup() as $ug) {
            $uid = $ug->getUid();
            if (!in_array($uid, $return)) {
                $return = array_unique(array_merge($return, $this->_getSubgroups($ug, $return)));
            }
        }
        return $return;
    }


}

<?php
namespace Cylancer\CyDownloadLibrary\Service;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Context\Context;
use Cylancer\CyDownloadLibrary\Domain\Repository\FrontendUserRepository;
use Cylancer\CyDownloadLibrary\Domain\Model\FrontendUser;
use Cylancer\CyDownloadLibrary\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C.Gogolin <service@cylancer.net>
 */
class FrontendUserService implements SingletonInterface
{


    public function __construct(
        private readonly FrontendUserRepository $frontendUserRepository,
        private readonly Context $context,
        private readonly ConnectionPool  $connectionPool
    ) {
    }

    public static function getUid(AbstractEntity $entity): int
    {
        return $entity->getUid();
    }

    public function getCurrentUser(): FrontendUser|bool
    {
        if (!$this->isLogged()) {
            return false;
        }
        return $this->frontendUserRepository->findByUid($this->getCurrentUserUid());
    }

    public function getCurrentUserUid(): int
    {
        if (!$this->isLogged()) {
            return false;
        }
        return $this->context->getPropertyFromAspect('frontend.user', 'id');
    }

    public function isLogged(): bool
    {
        return $this->context->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

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

    protected function getQueryBuilder(string $table): QueryBuilder
    {
        return $this->connectionPool->getQueryBuilderForTable($table);
    }

    /**
     * Returns an array with all subgroups of the frontend user to the root of groups...
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

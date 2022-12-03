<?php
namespace Cylancer\DownloadLibrary\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;

/**
 * *
 *
 * This file is part of the "Message board" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 C. Gogolin <service@cylancer.net>
 * C. Gogolin <service@cylancer.net>
 *
 */
class FeUserUtility implements SingletonInterface
{

    /**
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var FrontendUserRepository
     */
    public $userRepository = null;

    /**
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var FrontendUserGroupRepository
     */
    public $frontendUserGroupRepository = null;
    
    
    /**
     *
     * @param string $table
     * @return QueryBuilder
     */
    protected function getQueryBuilder(String $table): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    }
    
    /**
     *
     * @return FrontendUser Returns the current frontend user
     */
    public function getCurrentUser(): FrontendUser
    {
        if (! $this->isLogged()) {
            return false;
        }
        return $this->userRepository->findByUid(FeUserUtility::getCurrentUserUid());
    }

    /**
     * Returns the current frontend user uid.
     * @return int
     */
    public function getCurrentUserUid(): int
    {
        if (! $this->isLogged()) {
            return false;
        }
        $context = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class);
        return $context->getPropertyFromAspect('frontend.user', 'id');
    }

    /**
     * Check if the user is logged
     *
     * @return bool
     */
    public function isLogged(): bool
    {
        $context = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class);
        return $context->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }
    
    
    /**
     * Returns an array with all subgroups of the frontend user group to the root of groups...
     * @param FrontendUserGroup $userGroup
     * @return array
     */
    public function getSubGroups(FrontendUserGroup $userGroup): array
    {
        return $this->_getSubgroups($userGroup);
    }
    
    private function _getSubgroups(FrontendUserGroup $userGroup, array &$return = []): array
    {
        $return[] = $userGroup->getUid();
        foreach ($userGroup->getSubgroup() as $ug) {
            $uid = $ug->getUid();
            if (! in_array($uid, $return)) {
                $return = array_unique(array_merge($return, $this->_getSubgroups($ug, $return)));
            }
        }
        return $return;
    }

    /**
     * Returns all groups from the frontend user to all his leafs in the hierachy tree...
     * @param FrontendUser $user
     * @return array
     */
    public function getUserTopGroups(FrontendUser $user): array
    {
        $return = [];
        foreach ($user->getUsergroup() as $ug) {
            $return = array_merge($return, $this->_getTopGroups($ug->getUid(), $return));
        }
        return $return;
    }
    
    /**
     * Returns all groups from the frontend user group to all his leafs in the hierachy tree...
     * @param FrontendUserGroup $userGroup
     * @return array
     */
    public function getTopGroups(FrontendUserGroup $userGroup): array
    {
        return $this->_getTopGroups($userGroup->getUid());
    }
    
    private function _getTopGroups(int $ug, array &$return = []): array
    {
        $return[] = $ug;
        $qb = $this->getQueryBuilder('fe_groups');
        $s = $qb->select('fe_groups.uid')
        ->from('fe_groups')
        ->where($qb->expr()
            ->inSet('subgroup', $ug))
            ->execute();
            while ($row = $s->fetch()) {
                $uid = intVal($row['uid']);
                if (! in_array($uid, $return)) {
                    $return = array_unique(array_merge($return, $this->_getTopGroups($uid, $return)));
                }
            }
            return $return;
    }
    
    public function getInformFrontendUser(array $frontendUserGroupUids ){
        
        // debug($frontendUserGroupUids);
        $_frontendUserGroupUids = array();
       
        /**
         * @var FrontendUserGroup $frontendUserGroup
         */
        foreach ($frontendUserGroupUids as $guid) {
         //    debug($guid);
            $_frontendUserGroupUids = array_merge($frontendUserGroupUids, $this->getTopGroups($this->frontendUserGroupRepository->findByUid($guid)));
        }
        $_frontendUserGroupUids = array_unique($_frontendUserGroupUids);
        $qb = $this->getQueryBuilder('fe_user');
        $qb->select('uid')->from('fe_users');
        foreach ($_frontendUserGroupUids as $guid) {
            $qb->orWhere($qb->expr()->inSet('usergroup', $guid));
        }
        $qb->andWhere($qb->expr()->eq('info_mail_when_repeated_download_added', 1)); 
       //  debug($qb->getSQL());
        $s = $qb->execute();
        $return = array(); 
        while ($row = $s->fetch()) {
            $return[] =intVal($row['uid']);
        }
        return $return; 
    }
    
}

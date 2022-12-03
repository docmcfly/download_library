<?php
namespace Cylancer\DownloadLibrary\Download;

use TYPO3\CMS\Scheduler\Download\AbstractDownload;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use Cylancer\DownloadLibrary\Domain\Repository\DownloadRepository;
use Cylancer\DownloadLibrary\Utility\FeUserUtility;
use Cylancer\DownloadLibrary\Utility\EmailUtility;
use Cylancer\DownloadLibrary\Domain\Model\Download;
use Cylancer\DownloadLibrary\Domain\Model\User;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;
use Cylancer\DownloadLibrary\Domain\Repository\UserRepository;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;

class DownloadLibraryInformerDownload extends AbstractDownload
{

    // ------------------------------------------------------
    // input fields
    const TASK_MANAGEMENT_STORAGE_UID = 'downloadLibraryStorageUid';

    const INFORM_FE_USER_GROUP_UIDS = 'informFeUserGroupUids';

    const INFO_MAIL_TARGET_URL = 'infoMailTargetUrl';

    public $downloadLibraryStorageUid = 0;

    public $informFeUserGroupUids = '';

    public $infoMailTargetUrl = 'http://googel.de';

    // ------------------------------------------------------
    // debug switch
    const DISABLE_PERSISTENCE_MANAGER = false;

    const EXTENSION_NAME = 'DownloadLibrary';

    // ------------------------------------------------------

    /**
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var \Cylancer\DownloadLibrary\Utility\FeUserUtility
     */
    private $feUserUtility = null;

    /**
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var UserRepository
     */
    private $userRepository = null;

    /**
     * messageRepository
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var \Cylancer\DownloadLibrary\Domain\Repository\DownloadRepository
     */
    private $downloadRepository = null;

    /**
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var \TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    private $pageRepository = null;

    /**
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var FrontendUserGroupRepository
     */
    private $frontendUserGroupRepository = null;

    /**
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    private $persistenceManager = null;

    /**
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var \Cylancer\DownloadLibrary\Utility\EmailUtility
     *
     */
    public $emailUtility = null;

    /**
     *
     * @var array
     */
    private $targetGroups = null;

    /**
     *
     * @var int
     */
    private $now;

    private function initialize()
    {
        $this->now = time();

        $this->downloadLibraryStorageUid = intval($this->downloadLibraryStorageUid);
        $this->informFeUserGroupUids = GeneralUtility::intExplode(',', $this->informFeUserGroupUids);

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->persistenceManager = $objectManager->get(PersistenceManager::class);

        $feUserStorageUids = [];
        /**
         *
         * @var QueryBuilder $qb
         */
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $s = $qb->select('uid')
            ->from('pages')
            ->where($qb->expr()
            ->eq('module', $qb->createNamedParameter('fe_users')))
            ->execute();
        while ($row = $s->fetch()) {
            $feUserStorageUids[] = $row['uid'];
        }

        $this->pageRepository = $objectManager->get(PageRepository::class);

        $this->userRepository = $objectManager->get(UserRepository::class);
        $querySettings = $this->userRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds($feUserStorageUids);
        $this->userRepository->setDefaultQuerySettings($querySettings);

        $this->frontendUserGroupRepository = $objectManager->get(FrontendUserGroupRepository::class);
        $querySettings = $this->frontendUserGroupRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds($feUserStorageUids);
        $this->frontendUserGroupRepository->setDefaultQuerySettings($querySettings);

        $this->downloadRepository = $objectManager->get(DownloadRepository::class);
        $querySettings = $this->downloadRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds([
            $this->downloadLibraryStorageUid
        ]);
        $this->downloadRepository->setDefaultQuerySettings($querySettings);

        $this->feUserUtility = $objectManager->get(FeUserUtility::class);

        $this->emailUtility = $objectManager->get(EmailUtility::class);
    }

    private function validate()
    {
        $valid = true;

        $valid &= $this->pageRepository != null;
        $valid &= $this->downloadRepository != null;
        $valid &= $this->userRepository != null;
        $valid &= $this->frontendUserGroupRepository != null;

        $valid &= $this->isPageUidValid($this->downloadLibraryStorageUid);
        $valid &= $this->areUserGroupsUidsValid($this->informFeUserGroupUids);
        $valid &= $this->isUrlValid($this->infoMailTargetUrl);
        return $valid;
    }

    /**
     *
     * @param array $uids
     * @return bool
     */
    private function areUserGroupsUidsValid(array $uids): bool
    {
        foreach ($uids as $uid) {
            if ($this->frontendUserGroupRepository->findByUid($uid) == null) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @param int $id
     * @return bool
     */
    private function isPageUidValid(int $id): bool
    {
        return $this->pageRepository->getPage($id) != null;
    }

    /**
     *
     * @param String $url
     * @return bool
     */
    private function isUrlValid(String $url): bool
    {
        return is_string($url) && strlen($url) > 5 && filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     *
     * @return \DateTime
     */
    private function createNow(): \DateTime
    {
        $return = new \DateTime();
        $return->setTimestamp($this->now);
        return $return;
    }

    public function execute()
    {
        $this->initialize();

        /**
         *
         * @var Download $download
         * @var Download $replicat
         */
        if ($this->validate()) {
            $sendInfoMail = false;
            foreach ($this->downloadRepository->findRepeatDownloads() as $download) {
                $replica = new Download();
                $replica->setTitle($download->getTitle());
                $replica->setUser($download->getUser());
                $replica->setRepeatPeriodCount($download->getRepeatPeriodCount());
                $replica->setRepeatPeriodUnit($download->getRepeatPeriodUnit());
                $replica->setPid($this->downloadLibraryStorageUid);
                $this->downloadRepository->add($replica);

                $download->setNextRepetition(null);
                $this->downloadRepository->update($download);
                $sendInfoMail = true;
            }
            $this->persistenceManager->persistAll();
            if ($sendInfoMail) {
                $this->sendInfoMails();
            }

            return true;
        } else {
            return false;
        }
    }

    private function sendInfoMails()
    {
        foreach ($this->feUserUtility->getInformFrontendUser($this->informFeUserGroupUids) as $userUid) {
            // debug($userUid);
            $this->sendInfoMail($this->userRepository->findByUid($userUid));
        }
    }

    private function sendInfoMail(User $user)
    {
        if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $recipient = [
                $user->getEmail() => $user->getFirstName() . ' ' . $user->getLastName()
            ];
            $sender = [
                \TYPO3\CMS\Core\Utility\MailUtility::getSystemFromAddress() => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('download.downloadLibraryInformer.informMail.senderName', DownloadLibraryInformerDownload::EXTENSION_NAME)
            ];
            $subject = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('download.downloadLibraryInformer.informMail.senderName', DownloadLibraryInformerDownload::EXTENSION_NAME);

            $data = [
                'user' => $user,
                'url' => $this->infoMailTargetUrl
            ];

            $this->emailUtility->sendTemplateEmail($recipient, $sender, $subject, 'DownloadLibraryInfoMail', DownloadLibraryInformerDownload::EXTENSION_NAME, $data);
        }
    }

    /**
     * This method returns the sleep duration as additional information
     *
     * @return String Information to display
     */
    public function getAdditionalInformation(): String
    {
        return 'Downloads storage uid: ' . $this->downloadLibraryStorageUid . ' / Frontend user group: ' . $this->informFeUserGroupUids;
    }

    /**
     *
     * @param String $key
     * @throws \Exception
     * @return number|String
     */
    public function get(String $key)
    {
        switch ($key) {
            case DownloadLibraryInformerDownload::TASK_MANAGEMENT_STORAGE_UID:
                return $this->downloadLibraryStorageUid;
            case DownloadLibraryInformerDownload::INFORM_FE_USER_GROUP_UIDS:
                return $this->informFeUserGroupUids;
            case DownloadLibraryInformerDownload::INFO_MAIL_TARGET_URL:
                return $this->infoMailTargetUrl;
            default:
                throw new \Exception("Unknown key: $key");
        }
    }

    /**
     *
     * @param String $key
     * @param String|number $value
     * @throws \Exception
     */
    public function set(String $key, $value)
    {
        switch ($key) {
            case DownloadLibraryInformerDownload::TASK_MANAGEMENT_STORAGE_UID:
                $this->downloadLibraryStorageUid = $value;
                break;
            case DownloadLibraryInformerDownload::INFORM_FE_USER_GROUP_UIDS:
                $this->informFeUserGroupUids = $value;
                break;
            case DownloadLibraryInformerDownload::INFO_MAIL_TARGET_URL:
                $this->infoMailTargetUrl = $value;
                break;
            default:
                throw new \Exception("Unknown key: $key");
        }
    }
}



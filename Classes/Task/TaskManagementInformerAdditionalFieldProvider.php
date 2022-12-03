<?php
namespace Cylancer\DownloadLibrary\Download;

use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Download\Enumeration\Action;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\Download\AbstractDownload;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;

class DownloadLibraryInformerAdditionalFieldProvider extends AbstractAdditionalFieldProvider
{

    /**
     *
     * @param array $downloadInfo
     * @param DownloadLibraryInformerDownload|null $download
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @param array $additionalFields
     * @return void
     */
    private function initHintText(array &$additionalFields)
    {
        // Write the code for the field
        $fieldID = 'download_hint';
        $fieldCode = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('download.downloadLibraryInformer.hint.text', DownloadLibraryInformerDownload::EXTENSION_NAME);
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => 'LLL:EXT:download_library/Resources/Private/Language/locallang.xlf:download.downloadLibraryInformer.hint.title',
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     *
     * @param array $downloadInfo
     * @param DownloadLibraryInformerDownload|null $download
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @param array $additionalFields
     * @return void
     */
    private function initIntegerAddtionalField(array &$downloadInfo, $download, SchedulerModuleController $schedulerModule, String $key, array &$additionalFields)
    {
        $currentSchedulerModuleAction = $schedulerModule->getCurrentAction();

        // Initialize extra field value
        if (empty($downloadInfo[$key])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                // In case of new download and if field is empty, set default sleep time
                $downloadInfo[$key] = 0;
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                // In case of edit, set to internal value if no data was submitted already
                $downloadInfo[$key] = $download->get($key);
            } else {
                // Otherwise set an empty value, as it will not be used anyway
                $downloadInfo[$key] = 0;
            }
        }

        // Write the code for the field
        $fieldID = 'download_' . $key;
        $fieldCode = '<input type="number" min="0" max="99999" class="form-control" name="tx_scheduler[' . $key . ']" id="' . $fieldID . '" value="' . $downloadInfo[$key] . '" >';
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => 'LLL:EXT:download_library/Resources/Private/Language/locallang.xlf:download.downloadLibraryInformer.' . $key,
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     *
     * @param array $downloadInfo
     * @param DownloadLibraryInformerDownload|null $download
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @param array $additionalFields
     * @return void
     */
    private function initStringAddtionalField(array &$downloadInfo, $download, SchedulerModuleController $schedulerModule, String $key, array &$additionalFields)
    {
        $currentSchedulerModuleAction = $schedulerModule->getCurrentAction();

        // Initialize extra field value
        if (empty($downloadInfo[$key])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                // In case of new download and if field is empty, set default sleep time
                $downloadInfo[$key] = '';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                // In case of edit, set to internal value if no data was submitted already
                $downloadInfo[$key] = $download->get($key);
            } else {
                // Otherwise set an empty value, as it will not be used anyway
                $downloadInfo[$key] = '';
            }
        }

        // Write the code for the field
        $fieldID = 'download_' . $key;
        $fieldCode = '<input type="text" class="form-control" name="tx_scheduler[' . $key . ']" id="' . $fieldID . '" value="' . $downloadInfo[$key] . '" >';
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => 'LLL:EXT:download_library/Resources/Private/Language/locallang.xlf:download.downloadLibraryInformer.' . $key,
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     *
     * @param array $downloadInfo
     * @param DownloadLibraryInformerDownload|null $download
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @param array $additionalFields
     * @return void
     */
    private function initUrlAddtionalField(array &$downloadInfo, $download, SchedulerModuleController $schedulerModule, String $key, array &$additionalFields)
    {
        $currentSchedulerModuleAction = $schedulerModule->getCurrentAction();

        // Initialize extra field value
        if (empty($downloadInfo[$key])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                // In case of new download and if field is empty, set default sleep time
                $downloadInfo[$key] = '';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                // In case of edit, set to internal value if no data was submitted already
                $downloadInfo[$key] = $download->get($key);
            } else {
                // Otherwise set an empty value, as it will not be used anyway
                $downloadInfo[$key] = '';
            }
        }

        // Write the code for the field
        $fieldID = 'download_' . $key;
        $fieldCode = '<input type="url" class="form-control" name="tx_scheduler[' . $key . ']" id="' . $fieldID . '" value="' . $downloadInfo[$key] . '" >';
        $additionalFields[$fieldID] = [
            'code' => $fieldCode,
            'label' => 'LLL:EXT:download_library/Resources/Private/Language/locallang.xlf:download.downloadLibraryInformer.' . $key,
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldID
        ];
    }

    /**
     * This method is used to define new fields for adding or editing a download
     * In this case, it adds a sleep time field
     *
     * @param array $downloadInfo
     *            Reference to the array containing the info used in the add/edit form
     * @param DownloadLibraryInformerDownload|null $download
     *            When editing, reference to the current download. NULL when adding.
     * @param SchedulerModuleController $schedulerModule
     *            Reference to the calling object (Scheduler's BE module)
     * @return array Array containing all the information pertaining to the additional fields
     */
    public function getAdditionalFields(array &$downloadInfo, $download, SchedulerModuleController $schedulerModule)
    {
        $additionalFields = [];
        $this->initHintText($additionalFields);
        $this->initIntegerAddtionalField($downloadInfo, $download, $schedulerModule, DownloadLibraryInformerDownload::TASK_MANAGEMENT_STORAGE_UID, $additionalFields);
        $this->initStringAddtionalField($downloadInfo, $download, $schedulerModule, DownloadLibraryInformerDownload::INFORM_FE_USER_GROUP_UIDS, $additionalFields);
        $this->initUrlAddtionalField($downloadInfo, $download, $schedulerModule, DownloadLibraryInformerDownload::INFO_MAIL_TARGET_URL, $additionalFields);

        // debug($additionalFields);
        return $additionalFields;
    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @return boolean
     */
    private function validatePageAdditionalField(array &$submittedData, SchedulerModuleController $schedulerModule, String $key)
    {
        $result = true;
        if (! $this->validatePage($submittedData[$key])) {
            $this->addMessage($this->getLanguageService()
                ->sL('LLL:EXT:download_library/Resources/Private/Language/locallang.xlf:download.downloadLibraryInformer.error.invalidPage.' . $key), FlashMessage::ERROR);
            $result = false;
        }

        return $result;
    }

    private function validatePage($pid)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $pageRepository = $objectManager->get(PageRepository::class);
        return trim($pid) == strval(intval($pid)) && $pageRepository->getPage($pid) != null;
    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @return boolean
     */
    private function validateFrontendUserGroupsAdditionalField(array &$submittedData, SchedulerModuleController $schedulerModule, String $key)
    {
        $result = true;
        $uids = GeneralUtility::intExplode(',', $submittedData[$key]);
        foreach ($uids as $uid) {
            if (! $this->validateFrontendGroup($uid)) {
                $this->addMessage(str_replace('%1', $uid, $this->getLanguageService()
                    ->sL('LLL:EXT:download_library/Resources/Private/Language/locallang.xlf:download.downloadLibraryInformer.error.invalidFrontendUserGroup.' . $key)), FlashMessage::ERROR);
                $result = false;
            }
        }
        return $result;
    }

    private function validateFrontendGroup($uid)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /**
         *
         * @var FrontendUserGroupRepository $frontendGroupsRepository
         */
        $frontendGroupsRepository = $objectManager->get(FrontendUserGroupRepository::class);
        return trim($uid) == strval(intval($uid)) && $frontendGroupsRepository->findByUid($uid) != null;
    }

    /**
     *
     * @param array $submittedData
     * @param SchedulerModuleController $schedulerModule
     * @param String $key
     * @return boolean
     */
    private function validateUrlAdditionalField(array &$submittedData, SchedulerModuleController $schedulerModule, String $key)
    {
        $url = $submittedData[$key];
        return is_string($url) && strlen($url) > 5 && filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * This method checks any additional data that is relevant to the specific download
     * If the download class is not relevant, the method is expected to return TRUE
     *
     * @param array $submittedData
     *            Reference to the array containing the data submitted by the user
     * @param SchedulerModuleController $schedulerModule
     *            Reference to the calling object (Scheduler's BE module)
     * @return bool TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
     */
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $schedulerModule)
    {
        $result = true;
        $result &= $this->validatePageAdditionalField($submittedData, $schedulerModule, DownloadLibraryInformerDownload::TASK_MANAGEMENT_STORAGE_UID);
        $result &= $this->validateFrontendUserGroupsAdditionalField($submittedData, $schedulerModule, DownloadLibraryInformerDownload::INFORM_FE_USER_GROUP_UIDS);
        $result &= $this->validateUrlAdditionalField($submittedData, $schedulerModule, DownloadLibraryInformerDownload::INFO_MAIL_TARGET_URL);
        return $result;
    }

    /**
     *
     * @param array $submittedData
     * @param AbstractDownload $download
     * @param String $key
     * @return void
     */
    public function saveAdditionalField(array $submittedData, AbstractDownload $download, String $key)
    {
        /**
         *
         * @var DownloadLibraryInformerDownload $download
         */
        $download->set($key, $submittedData[$key]);
    }

    /**
     * This method is used to save any additional input into the current download object
     * if the download class matches
     *
     * @param array $submittedData
     *            Array containing the data submitted by the user
     * @param DownloadLibraryInformerDownload $download
     *            Reference to the current download object
     */
    public function saveAdditionalFields(array $submittedData, AbstractDownload $download)
    {
        $this->saveAdditionalField($submittedData, $download, DownloadLibraryInformerDownload::TASK_MANAGEMENT_STORAGE_UID);
        $this->saveAdditionalField($submittedData, $download, DownloadLibraryInformerDownload::INFORM_FE_USER_GROUP_UIDS);
        $this->saveAdditionalField($submittedData, $download, DownloadLibraryInformerDownload::INFO_MAIL_TARGET_URL);
    }

    /**
     *
     * @return LanguageService|null
     */
    protected function getLanguageService(): ?LanguageService
    {
        return $GLOBALS['LANG'] ?? null;
    }
}

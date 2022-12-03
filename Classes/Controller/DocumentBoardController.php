<?php
namespace Cylancer\DownloadLibrary\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Cylancer\DownloadLibrary\Domain\Model\Document;
use Cylancer\DownloadLibrary\Domain\Repository\DocumentRepository;
use Cylancer\DownloadLibrary\Service\FrontendUserService;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use Cylancer\DownloadLibrary\Domain\Model\ValidationResults;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use Symfony\Component\Config\Resource\FileResource;
use Psr\Http\Message\ResponseInterface;

/**
 * This file is part of the "document board" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2020 C. Gogolin <service@cylancer.net>
 * C. Gogolin <service@cylancer.net>
 *
 * @package Cylancer\DownloadLibrary\Controller
 */
class DocumentBoardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    const GERMAN_DATE_FORMAT = 'd.m.Y';

    const STANDARD_DATE_FORMAT = 'Y-m-d';

    const VALIDATION_RESULTS = 'validationResults';

    const DOCUMENT = 'document';

    private $_validationResults = null;

    /** @var FrontendUserService    */
    private $frontendUserService = null;

    /** @var DocumentRepository    */
    private $documentRepository = null;

    /** @var PersistenceManager */
    private $persistenceManager = null;

    public function __construct(DocumentRepository $documentRepository, FrontendUserService $frontendUserService, PersistenceManager $persistenceManager)
    {
        $this->documentRepository = $documentRepository;
        $this->frontendUserService = $frontendUserService;
        $this->persistenceManager = $persistenceManager;
    }

    public function showAction(): ResponseInterface
    {
        $validationResults = $this->getValidationResults();

        /** @var Document $document */
        $document = ($this->request->hasArgument(DocumentBoardController::DOCUMENT)) ? $this->request->getArgument(DocumentBoardController::DOCUMENT) : new Document();

        $this->view->assign('document', $document);
        $allDocs = $this->documentRepository->getSortedDocuments();

        $this->view->assign('openDocuments', $allDocs['open']);
        $this->view->assign('archivedDocuments', $allDocs['archived']);
        $this->view->assign('validationResults', $validationResults);

        $canAddDocuments = false;
        if ($this->settings['providedByGroup'] != null && is_numeric($this->settings['providedByGroup'])) {
            $canAddDocuments = in_array(intval($this->settings['providedByGroup']), $this->frontendUserService->getUserSubGroups($this->frontendUserService->getCurrentUser()));
        }

        $this->view->assign('canAddDocuments', $canAddDocuments);
        return $this->htmlResponse();
    }

    public function removeDocumentAction(Document $document): ResponseInterface
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->validateRemove($document);

        if (! $validationResults->hasErrors()) {

            // one image exists:

            /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileReference **/
            $fileReference = $document->getFileReference()->getArray()[0];

            /** @var FileResource $fileResource */
            $fileResource = GeneralUtility::makeInstance(ResourceFactory::class)->getFileReferenceObject($fileReference->getUid());
            $fileResource->getOriginalFile()->delete();

            $this->documentRepository->remove($document);
            $this->persistenceManager->persistAll();
        }

        return (new ForwardResponse('show'))
            ->withArguments([
            DocumentBoardController::VALIDATION_RESULTS => $validationResults,
            DocumentBoardController::DOCUMENT => new Document()
        ]);
    }

    /**
     *
     * @param Document $document
     * @return ValidationResults
     */
    private function validateRemove(Document $document): ValidationResults
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if ($this->frontendUserService->getCurrentUserUid() !== $document->getOwner()->getUid()) {
            $validationResults->addError('notOwner');
        }

        return $validationResults;
    }

    public function archiveDocumentAction(Document $document)
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->validateRemove($document);

        if (! $validationResults->hasErrors()) {
            $document->setArchived(true);
            $this->documentRepository->update($document);
            $this->persistenceManager->persistAll();
        }

        return (new ForwardResponse('show'))
            ->withArguments([
            DocumentBoardController::VALIDATION_RESULTS => $validationResults
        ]);
    }

    /**
     *
     * @param Document $document
     * @return ValidationResults
     */
    private function validateArchive(Document $document): ValidationResults
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if ($this->frontendUserService->getCurrentUserUid() !== $document->getOwner()->getUid()) {
            $validationResults->addError('notOwner');
        }
        if (! $document->getFinal()) {
            $validationResults->addError('notFinal');
        }

        return $validationResults;
    }

    public function uploadAction(Document $document)
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->validateUpload($document);

        if (! $validationResults->hasErrors()) {

            $document->setOwner($this->frontendUserService->getCurrentUser());
            $document->setStatus($this->transformDate($document->getStatus()));
            $document->setArchived(false);
            if ($this->frontendUserService->isLogged()) {
                $document->setOwner($this->frontendUserService->getCurrentUser());
            }
            $this->addFile($document);
            $this->documentRepository->add($document);
            $this->persistenceManager->persistAll();
            return $this->redirect('show');
        } else {
            return (new ForwardResponse('show'))
                ->withArguments([
                DocumentBoardController::VALIDATION_RESULTS => $validationResults,
                DocumentBoardController::DOCUMENT => $document
            ]);
        }
    }

    /**
     * transform from dd.mm.yyyy to yyyy-mm-dd
     *
     * @param
     *            String germanDate
     * @return String
     */
    private function transformDate(String $germanDate): String
    {
        return \DateTime::createFromFormat('!' . DocumentBoardController::GERMAN_DATE_FORMAT, $germanDate)->format(DocumentBoardController::STANDARD_DATE_FORMAT);
    }

    /**
     *
     * @param Document $document
     * @return ValidationResults
     */
    private function validateUpload(Document $document): ValidationResults
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if (empty(trim($this->settings['documentsFolder']))) {
            $validationResults->addError('documentsFolderNotSpecified');
        } else {
            /** @var StorageRepository $storageRepository **/
            $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
            $storage = $storageRepository->getDefaultStorage();
            /** @var Folder $folder **/
            if (! $storage->checkFolderActionPermission('write', $storage->getFolder($this->settings['documentsFolder']))) {
                $validationResults->addError('documentsFolderPermissionDenied');
            }
        }

        if (empty(trim($document->getTitle()))) {
            $validationResults->addError('title.empty');
        }
        if (empty(trim($document->getStatus()))) {
            $validationResults->addError('status.invalid');
        } else {
            $status = \DateTime::createFromFormat('!' . DocumentBoardController::GERMAN_DATE_FORMAT, $document->getStatus());
            if ($status === false) {
                $validationResults->addError('status.invalid');
            }
        }

        $error = $document->getUploadedFile()['error'];
        if ($error !== UPLOAD_ERR_NO_FILE) {
            $fileExtension = PathUtility::pathinfo($document->getUploadedFile()['name'], PATHINFO_EXTENSION);
            if (! GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], strtolower($fileExtension))) {
                $validationResults->addError('uploadFile.fileFormatNotSupported', [
                    $fileExtension
                ]);
            }
        } else {
            $validationResults->addError('uploadFile.noFile');
        }

        return $validationResults;
    }

    private function getValidationResults()
    {
        if ($this->_validationResults == null) {
            $this->_validationResults = ($this->request->hasArgument(DocumentBoardController::VALIDATION_RESULTS)) ? //
            $this->request->getArgument(DocumentBoardController::VALIDATION_RESULTS) : //
            new ValidationResults();
        }
        return $this->_validationResults;
    }

    /**
     *
     * @param array $uploadFileData
     * @param Document $document
     * @return void
     */
    private function addFile(Document $document): void
    {
        $tmpFile = $document->getUploadedFile()['tmp_name'];

        // Save the imgae in the storage...
        /** @var StorageRepository $storageRepository **/
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $storage = $storageRepository->getDefaultStorage();

        /** @var Folder $folder **/
        $folder = $storage->getFolder($this->settings['documentsFolder']);

        /** @var FileInterface $imageFile **/
        $imageFile = $folder->addFile($tmpFile, $document->getUploadedFile()['name'], DuplicationBehavior::RENAME);

        $storageRepository->flush();

        /** @var File $file **/
        $coreFile = $storage->getFileByIdentifier($imageFile->getIdentifier());

        /** @var \TYPO3\CMS\Extbase\Domain\Model\File $fileModel **/
        $file = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Domain\Model\File::class);
        $file->setOriginalResource($coreFile);

        /** @var FileReference $coreFileReference **/
        $coreFileReference = GeneralUtility::makeInstance(FileReference::class, [
            'uid_local' => $coreFile->getUid()
        ]);

        /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileReference **/
        if ($document->getFileReference()->count() > 0) {
            /** @var FileReference $fileReference */
            foreach ($document->getFileReference()->getArray() as $fileReference) {
                $document->getFileReference()->remove($fileReference);
                $fileResource = GeneralUtility::makeInstance(ResourceFactory::class)->getFileReferenceObject($fileReference->getUid());
                $fileResource->getOriginalFile()->delete();
            }
        }

        $fileReference = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Domain\Model\FileReference::class);
        $fileReference->setOriginalResource($coreFileReference);

        $document->addFileReference($fileReference);
    }
}
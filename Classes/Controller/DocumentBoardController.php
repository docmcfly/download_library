<?php
namespace Cylancer\CyDownloadLibrary\Controller;

use Cylancer\CyDownloadLibrary\Domain\Model\Document;
use Cylancer\CyDownloadLibrary\Domain\Repository\DocumentRepository;
use Cylancer\CyDownloadLibrary\Service\FrontendUserService;
use Cylancer\CyDownloadLibrary\Domain\Model\ValidationResults;

use Psr\Http\Message\ResponseInterface;

use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Extbase\Validation\Validator\MimeTypeValidator;
use TYPO3\CMS\Extbase\Mvc\Controller\FileUploadConfiguration;
use TYPO3\CMS\Extbase\Mvc\Controller\Argument;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Http\ForwardResponse;


/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C. Gogolin <service@cylancer.net>
 *
 */
class DocumentBoardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    private const GERMAN_DATE_FORMAT = 'd.m.Y';

    private const STANDARD_DATE_FORMAT = 'Y-m-d';

    private const VALIDATION_RESULTS = 'validationResults';

    private const DOCUMENT = 'document';

    private array $acceptedFileTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'text/plain',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation'
    ];


    private ?ValidationResults $_validationResults = null;

    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private readonly FrontendUserService $frontendUserService,
        private readonly PersistenceManager $persistenceManager,
        private readonly ResourceFactory $resourceFactory
    ) {
    }

    public function showAction(): ResponseInterface
    {
        $validationResults = $this->getValidationResults();

        /** @var Document $document */
        $document = ($this->request->hasArgument(DocumentBoardController::DOCUMENT))
            ? $this->request->getArgument(DocumentBoardController::DOCUMENT)
            : new Document();
        $this->view->assign('document', $document);

        $allDocs = $this->documentRepository->getSortedDocuments();
        $this->view->assign('openDocuments', $allDocs['open']);
        $this->view->assign('archivedDocuments', $allDocs['archived']);
        $this->view->assign('validationResults', $validationResults);
        $this->view->assign('acceptedFileTypes', implode(", ", $this->acceptedFileTypes));

        $this->view->assign('user', $this->frontendUserService->getCurrentUser());
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

        if (!$validationResults->hasErrors()) {

            $fileResource = $this->resourceFactory->getFileReferenceObject($document->getFile()->getUid());
            $fileResource->getOriginalFile()->delete();

            $this->documentRepository->remove($document);
            $this->persistenceManager->persistAll();
        }

        return GeneralUtility::makeInstance(ForwardResponse::class, 'show')
            ->withArguments([
                DocumentBoardController::VALIDATION_RESULTS => $validationResults,
                DocumentBoardController::DOCUMENT => new Document()
            ]);
    }

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

        if (!$validationResults->hasErrors()) {
            $document->setArchived(true);
            $this->documentRepository->update($document);
            $this->persistenceManager->persistAll();
        }

        return GeneralUtility::makeInstance(ForwardResponse::class, 'show')
            ->withArguments([
                DocumentBoardController::VALIDATION_RESULTS => $validationResults
            ]);
    }

    private function validateArchive(Document $document): ValidationResults
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if ($this->frontendUserService->getCurrentUserUid() !== $document->getOwner()->getUid()) {
            $validationResults->addError('notOwner');
        }
        if (!$document->getFinal()) {
            $validationResults->addError('notFinal');
        }

        return $validationResults;
    }

    public function initializeUploadAction(): void
    {
        $mimeTypeValidator = GeneralUtility::makeInstance(MimeTypeValidator::class);
        $mimeTypeValidator->setOptions([
            'allowedMimeTypes' => $this->acceptedFileTypes
        ]);

        /** @var Argument */
        $document = $this->arguments->getArgument('document');
        $fileHandlingServiceConfiguration = $document->getFileHandlingServiceConfiguration();
        $uploadConfiguration = (new FileUploadConfiguration('uploadedFile'))
            ->addValidator($mimeTypeValidator)
            ->setMaxFiles(1)
            ->setUploadFolder($this->settings['documentsFolder'] . '/');

        $uploadConfiguration->setDuplicationBehavior(DuplicationBehavior::RENAME);
        $fileHandlingServiceConfiguration->addFileUploadConfiguration($uploadConfiguration);
        $document->getPropertyMappingConfiguration()->skipProperties('uploadedFile');

    }


    public function uploadAction(Document $document)
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->validateUpload($document);

        if (!$validationResults->hasErrors()) {

            $document->setOwner($this->frontendUserService->getCurrentUser());
            $document->setStatus($this->transformDate($document->getStatus()));
            $document->setArchived(false);
            if ($this->frontendUserService->isLogged()) {
                $document->setOwner($this->frontendUserService->getCurrentUser());
            }
            $document->setFile($document->getUploadedFile());
            $document->setUploadedFile(null);

            $this->documentRepository->add($document);
            $this->persistenceManager->persistAll();
            return $this->redirect('show');

        } else {
            return GeneralUtility::makeInstance(ForwardResponse::class, 'show')
                ->withArguments([
                    DocumentBoardController::VALIDATION_RESULTS => $validationResults,
                    DocumentBoardController::DOCUMENT => $document
                ]);
        }
    }

    /**
     * transform from dd.mm.yyyy to yyyy-mm-dd
     */
    private function transformDate(string $germanDate): string
    {
        return \DateTime::createFromFormat('!' . DocumentBoardController::GERMAN_DATE_FORMAT, $germanDate)->format(DocumentBoardController::STANDARD_DATE_FORMAT);
    }

    private function validateUpload(Document $document): ValidationResults
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

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
        if (empty(trim($this->settings['documentsFolder']))) {
            $validationResults->addError('documentsFolderNotSpecified');
        }
        if ($document->getUploadedFile() == null) {
            $validationResults->addError('uploadFile.noFile');
        }

        return $validationResults;
    }

    private function getValidationResults(): ValidationResults
    {
        if ($this->_validationResults == null) {
            $this->_validationResults = ($this->request->hasArgument(DocumentBoardController::VALIDATION_RESULTS)) ? //
                $this->request->getArgument(DocumentBoardController::VALIDATION_RESULTS) : //
                new ValidationResults();
        }
        return $this->_validationResults;
    }


}
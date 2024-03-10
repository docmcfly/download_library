<?php
declare(strict_types = 1);
namespace Cylancer\DownloadLibrary\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 by Clemens Gogolin <service@cylancer.net>
 */
class Document extends AbstractEntity
{

    /** @var ObjectStorage<FileReference>  */
    protected $fileReference = null;

    /** @var bool */
    protected $final = false;

    /** @var bool */
    protected $archived = false;

    /** @var string */
    protected $status = null;

    /** @var FrontendUser */
    protected $owner = null;

    /**  @var array */
    protected $uploadedFile;

    /** @var string */
    protected $title = '';

    /**
     * Constructs a new Front-End User
     */
    public function __construct()
    {
        $this->fileReference = new ObjectStorage();
    }

    /**
     *
     * @return boolean
     */
    public function getFinal(): bool
    {
        return $this->final;
    }

    /**
     *
     * @param boolean $final
     * @return void
     */
    public function setFinal($final): void
    {
        $this->final = $final;
    }

    /**
     *
     * @return string
     */
    public function getStatus():? string
    {
        return $this->status;
    }

    /**
     *
     * @param string $status
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     *
     * @return FrontendUser
     */
    public function getOwner(): ?FrontendUser
    {
        return $this->owner;
    }

    /**
     *
     * @param FrontendUser $owner
     * @return void
     */
    public function setOwner($owner): void
    {
        $this->owner = $owner;
    }

    /**
     * Sets the fileReferences.
     * Keep in mind that the property is called "fileReference"
     * although it can hold several fileReferences.
     *
     * @param ObjectStorage<FileReference> $fileReference
     */
    public function setFileReference(ObjectStorage $fileReference): void
    {
        $this->fileReference = $fileReference;
    }

    /**
     * Adds a fileReference to the frontend user
     *
     * @param FileReference $fileReference
     */
    public function addFileReference(FileReference $fileReference)
    {
        $this->fileReference->attach($fileReference);
    }

    /**
     * Removes a fileReference from the frontend user
     *
     * @param FileReference $fileReference
     */
    public function removeFileReference(FileReference $fileReference)
    {
        $this->fileReference->detach($fileReference);
    }

    /**
     * Returns the fileReferences.
     * Keep in mind that the property is called "fileReference"
     * although it can hold several fileReferences.
     *
     * @return ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> An object storage containing the fileReference
     */
    public function getFileReference(): ObjectStorage
    {
        return $this->fileReference;
    }

    /**
     *
     * @return array
     */
    public function getUploadedFile(): ?array
    {
        return $this->uploadedFile;
    }

    /**
     *
     * @param array $uploadedFile
     * @return void
     */
    public function setUploadedFile($uploadedFile): void
    {
        $this->uploadedFile = $uploadedFile;
    }

    /**
     *
     * @return boolean
     */
    public function getArchived(): bool
    {
        return $this->archived;
    }

    /**
     *
     * @param boolean $archived
     * @return void
     */
    public function setArchived($archived): void
    {
        $this->archived = $archived;
    }

    /**
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }
}

<?php
declare(strict_types=1);
namespace Cylancer\CyDownloadLibrary\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C. Gogolin <service@cylancer.net>
 *
 */
class Document extends AbstractEntity
{

    protected ?FileReference $file = null;
    
    protected ?FileReference $uploadedFile = null;

    protected bool $final = false;

    protected bool $archived = false;

    protected ?string $status = null;

    protected ?FrontendUser $owner = null;

    protected string $title = '';

    public function getFinal(): bool
    {
        return $this->final;
    }

    public function setFinal($final): void
    {
        $this->final = $final;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getOwner(): ?FrontendUser
    {
        return $this->owner;
    }

    public function setOwner($owner): void
    {
        $this->owner = $owner;
    }
    
    public function getFile(): ?FileReference
    {
        return $this->file;
    }

    public function setFile(?FileReference $file): void
    {
        $this->file = $file;
    }

    public function getUploadedFile(): ?FileReference
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(?FileReference $uploadedFile): void
    {
        $this->uploadedFile = $uploadedFile;
    }

    public function getArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived($archived): void
    {
        $this->archived = $archived;
    }

    public function getTitle(): string
    {
        return $this->title;
    } 
    
    public function setTitle($title): void
    {
        $this->title = $title;
    }
}

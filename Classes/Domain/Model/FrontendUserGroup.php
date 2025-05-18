<?php
namespace Cylancer\CyDownloadLibrary\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 *
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C.Gogolin <service@cylancer.net>
 *
 */
class FrontendUserGroup extends AbstractEntity
{

    /** @var ObjectStorage<FrontendUserGroup> */
     protected ?ObjectStorage $subgroup;

    /**
     * Constructs a new Frontend User Group
     */
    public function __construct()
    {
        $this->subgroup = new ObjectStorage();
    }

    public function setSubgroup(ObjectStorage $subgroup): void
    {
        $this->subgroup = $subgroup;
    }

    public function addSubgroup(FrontendUserGroup $subgroup): void
    {
        $this->subgroup->attach($subgroup);
    }

    public function removeSubgroup(FrontendUserGroup $subgroup): void
    {
        $this->subgroup->detach($subgroup);
    }

    public function getSubgroup(): ObjectStorage
    {
        return $this->subgroup;
    }
}

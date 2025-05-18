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
class FrontendUser extends AbstractEntity
{
    /** @var ObjectStorage<FrontendUserGroup> */

    protected ?ObjectStorage $usergroup;
    protected ?string $telephone = '';
    protected ?string $username = '';
    protected ?string $name = '';
    protected ?string $firstName = '';
    protected ?string $lastName = '';
    protected ?string $password = '';
    protected ?string $email = '';

    public function __construct()
    {
        $this->usergroup = new ObjectStorage();
    }

    public function initializeObject()
    {
        $this->usergroup = $this->usergroup ?? new ObjectStorage();
    }

    public function setUsergroup(ObjectStorage $usergroup): void
    {
        $this->usergroup = $usergroup;
    }

    public function addUsergroup(FrontendUserGroup $usergroup): void
    {
        $this->usergroup->attach($usergroup);
    }

    public function removeUsergroup(FrontendUserGroup $usergroup): void
    {
        $this->usergroup->detach($usergroup);
    }

    public function getUsergroup(): ObjectStorage
    {
        return $this->usergroup;
    }

    public function setTelephone(?string $telephone)
    {
        $this->telephone = $telephone;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setFirstName(?string $firstName)
    {
        $this->firstName = $firstName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setLastName(?string $lastName)
    {
        $this->lastName = $lastName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}

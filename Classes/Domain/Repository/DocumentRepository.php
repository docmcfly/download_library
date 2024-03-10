<?php
declare(strict_types=1);
namespace Cylancer\DownloadLibrary\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Cylancer\DownloadLibrary\Domain\Model\Document;

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 by Clemens Gogolin <service@cylancer.net>
 */
class DocumentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{


    public function getSortedDocuments(int $months = 12)
    {
        $months = max($months, 0);
        /* @var \DateTime $now */
        $now = new \DateTime();

        /* @var QueryInterface $query */
        $query = $this->createQuery();
        $query->lessThan('status', $now->add(new \DateInterval('P' . $months . 'M')));
        $query->setOrderings(['status' => QueryInterface::ORDER_DESCENDING]);
        $return = array();
        $return['archived'] = array();
        $return['open'] = array();
        /* @var Document $document */
        foreach ($query->execute() as $document) {
            if ($document->getArchived()) {
                $return['archived'][explode('-', $document->getStatus())[1]][] = $document;
            } else {
                $return['open'][] = $document;
            }
        }
        $return['open'] = array_reverse($return['open'], true);
        return $return;
    }
}

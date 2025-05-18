<?php
declare(strict_types=1);
namespace Cylancer\CyDownloadLibrary\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * This file is part of the "Download library" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 by C.Gogolin <service@cylancer.net>
 */
class DocumentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{


    public function getSortedDocuments(int $months = 120): array
    {
        $months = max($months, 0);
        /* @var \DateTime $now */
        $now = new \DateTime();

        /* @var QueryInterface $query */
        $query = $this->createQuery();
        $query->lessThan('status', $now->add(new \DateInterval('P' . $months . 'M')));
        $query->setOrderings(['status' => QueryInterface::ORDER_DESCENDING]);
        $return = array();
        $return['archived'] = [];
        $return['open'] = [];
        /* @var Document $document */
        foreach ($query->execute() as $document) {
            if ($document->getArchived()) {
                $splitStatus = explode('-', $document->getStatus());
                $return['archived'][$splitStatus[0]][$splitStatus[1]][] = $document;
            } else {
                $return['open'][] = $document;
            }
        }
        $return['open'] = array_reverse($return['open'], true);
        return $return;
    }
}

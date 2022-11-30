<?php
namespace Awin\Tools\CoffeeBreak\Repository;

use Doctrine\ORM\EntityRepository;

class CoffeeBreakPreferenceRepository extends EntityRepository
{
    public function getPreferencesForToday()
    {
        $alias = "cbp";
        return $this->createQueryBuilder($alias)
            ->where("$alias.requested_on BETWEEN :from AND :to")
            ->setParameter("from", new \DateTime("today"))
            ->setParameter("to", new \DateTime("tomorrow"))
            ->getQuery()
            ->getArrayResult();
    }

}

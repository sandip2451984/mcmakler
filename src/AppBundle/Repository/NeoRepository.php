<?php

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class NeoRepository
 *
 * @package AppBundle\Repository
 */
class NeoRepository extends DocumentRepository
{
    /**
     * Retrieves all NEO
     *
     * @param bool $isHazardous
     *
     * @return mixed
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findNeo($isHazardous = false)
    {
        return $this->createQueryBuilder()
            ->getQuery()
            ->execute();
    }

    /**
     * Retrtieves the fastest NEO
     *
     * @param bool $isHazardous
     *
     * @return mixed
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findFastest($isHazardous = false)
    {
        return $this->createQueryBuilder()
            ->field('is_hazardous')->equals($isHazardous)
            ->sort('speed', 'DESC')
            ->limit(1)
            ->getQuery()
            ->execute();
    }

    /**
     * Retrieves Neo list inside the best month
     *
     * @param bool $isHazardous
     *
     * @return mixed|null
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getBestMonth($isHazardous = false)
    {
        // Prepares an aggregation in order to get the best month 
        $builder = $this->dm->createAggregationBuilder(\AppBundle\Document\NeoDocument::class);
        $bestMonth = $builder->match()
            ->field('is_hazardous')->equals($isHazardous)
            ->group()
            ->field('id')
            ->expression(
                $builder->expr()
                    ->field('year')
                    ->year('$date')
                    ->field('month')
                    ->month('$date')
            )
            ->field("count")
            ->sum(1)
            ->sort("count", -1)
            ->limit(1)
            ->execute();

        if ($bestMonth->count() !== 1) {
            return null;
        }
        // Prepares dates
        $year = $bestMonth->toArray()[0]['_id']['year'];
        $month = $bestMonth->toArray()[0]['_id']['month'];
        $date = new \DateTime($year . "-" . $month . "-01 00:00:00");
        $endDate = new \DateTime($year . "-" . $month . "-01 23:59:59");

        // Gets last day of this month
        $endDate = $endDate->modify("last day of this month");

        // Executes request
        return $this->createQueryBuilder()
            ->field('is_hazardous')->equals($isHazardous)
            ->field('date')->gte($date)
            ->field('date')->lte($endDate)
            ->sort('date', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Retrieves Neo list inside the best year
     *
     * @param bool $isHazardous
     *
     * @return mixed|null
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getBestYear($isHazardous = false)
    {
        // Prepares an aggregation in order to get the best year 
        $builder = $this->dm->createAggregationBuilder(\AppBundle\Document\NeoDocument::class);
        $bestYear = $builder->match()
            ->field('is_hazardous')->equals($isHazardous)
            ->group()
            ->field('id')
            ->expression(
                $builder->expr()
                    ->year('$date')
            )
            ->field("count")
            ->sum(1)
            ->sort("count", -1)
            ->limit(1)
            ->execute();

        // Checks if we have gotten the best year
        if ($bestYear->count() !== 1) {
            return NULL;
        }
        // Gets the year
        $year = $bestYear->toArray()[0]['_id'];

        // Executes request
        return $this->createQueryBuilder()
            ->field('is_hazardous')->equals($isHazardous)
            ->field('date')->gte(new \DateTime($year . "-01-01 00:00:00"))
            ->field('date')->lte(new \DateTime($year . "-12-31 23:59:59"))
            ->sort('date', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * remove all data
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function clearAllDocument()
    {
        $collection = $this->dm->getDocumentCollection(\AppBundle\Document\NeoDocument::class);

        $collection->remove([]);
    }
}
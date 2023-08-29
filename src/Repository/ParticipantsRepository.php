<?php

namespace App\Repository;

use App\Entity\Participants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participants>
 *
 * @method Participants|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participants|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participants[]    findAll()
 * @method Participants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantsRepository extends ServiceEntityRepository
{
    # Todo have to fix these queries
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participants::class);
    }

    public function findParticipants($title)
    {
        return $this->createQueryBuilder('p');
    }

    public function getAverageFinishTimeForLongistance($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare('WITH avgtime as ( SELECT *, CAST( AVG(CAST(`finish_time` AS TIME)) AS TIME) averageFinishTimeForLongDistance FROM participants WHERE races_id=:id GROUP BY races_id ) SELECT averageFinishTimeForLongDistance FROM avgtime');

        $result = $stmt->executeQuery([
            ':id' => $id,
        ]);

        return $result->fetchOne();
    }

    public function getAverageFinishTimeForMediumDistance($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare('WITH avgtime as ( SELECT *, CAST( AVG(CAST(`finish_time` AS TIME)) AS TIME) averageFinishTimeForMediumDistance FROM participants WHERE races_id=:id GROUP BY races_id ) SELECT averageFinishTimeForMediumDistance FROM avgtime');

        $result = $stmt->executeQuery([
            ':id' => $id,
        ]);

        return $result->fetchOne();
    }

    # Medium distance doesnt get a placement / rank
    public function getOverallPlacement(int $id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare('WITH score AS (SELECT *, CASE WHEN `distance`=\'D\' THEN RANK() OVER( PARTITION BY races_id, distance ORDER BY `finish_time` ASC ) ELSE NULL END AS overall_placement FROM participants GROUP BY id) SELECT overall_placement FROM score WHERE id=:id');

        $result = $stmt->executeQuery([
            ':id' => $id,
        ]);

        return $result->fetchOne();
    }

    public function getAgeCategoryPlace(int $id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare('WITH score AS (SELECT *, CASE WHEN `distance`=\'long\' THEN RANK() OVER( PARTITION BY races_id, distance, age_category ORDER BY `finish_time` ASC ) ELSE NULL END AS ageCategoryPlace FROM participants GROUP BY id) SELECT ageCategoryPlace FROM score WHERE id=:id');

        $result = $stmt->executeQuery([
            ':id' => $id,
        ]);

        return $result->fetchOne();
    }
}

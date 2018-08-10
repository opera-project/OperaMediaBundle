<?php

namespace Opera\MediaBundle\Repository;

use Opera\MediaBundle\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function findBySourceRootFolder($sourceName): array
    {
        return $this->createQueryBuilder('m')
                    ->andWhere('m.folder is NULL')
                    ->andWhere('m.source = :sourceName')
                    ->setParameter('sourceName', $sourceName)
                    ->getQuery()
                    ->getResult();
    }

}

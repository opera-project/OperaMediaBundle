<?php

namespace Opera\MediaBundle\Repository;

use Opera\MediaBundle\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Opera\MediaBundle\Entity\Folder;

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

    public function queryBuilderBySourceAndFolder($sourceName, Folder $folder = null, ?string $querySearch = null)
    {
        $qb = $this->createQueryBuilder('m')
                    ->andWhere('m.source = :sourceName')
                    ->setParameter('sourceName', $sourceName);
        
        if ($folder) {
            $qb->innerJoin('m.folder', 'f')
                ->andWhere('f.id = :folderId')
                ->setParameter('folderId', $folder->getId());
        } else {
            $qb->andWhere('m.folder is NULL');
        }

        if ($querySearch) {
            $qb->andWhere('m.name LIKE :querySearch')
               ->setParameter('querySearch', '%'.$querySearch.'%');
        }
        
        return $qb->getQuery();
    }

}

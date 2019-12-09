<?php

namespace Opera\MediaBundle\Repository;

use Opera\MediaBundle\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Opera\MediaBundle\Entity\Folder;
use Opera\MediaBundle\MediaManager\Source;
use Opera\MediaBundle\SearchManager\Search;

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

    public function queryBuilderBySourceAndFolder($sourceName, Folder $folder = null)
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
        
        return $qb->getQuery();
    }

    public function search(Search $search,  Source $source, ?Folder $currentFolder = null)
    {
        $qb = $this->createQueryBuilder('m');

        $qb->andWhere('m.source = :sourceName')
             ->setParameter('sourceName', $source->getName());

        if ($search->getWhere() == Search::SEARCH_WHERE_FOLDER && $currentFolder) {
            $qb->andWhere('m.folder = :currentFolderId')
                ->setParameter('currentFolderId', $currentFolder->getId());
        }

        if ($search->getWhere() == Search::SEARCH_WHERE_FOLDERS && $currentFolder) {
            $qb
                ->innerJoin('m.folder', 'f')
                ->leftJoin('f.childs', 'c')
                ->andWhere('c.parent_id = :currentFolderId')
                ->setParameter('currentFolderId', $currentFolder->getId());
        }

        if ($search->getSearch()) {
            $qb->andWhere('m.name LIKE :searchQuery OR m.slug LIKE :searchQuery')
                 ->setParameter('searchQuery', '%'.$search->getSearch().'%');
        }

        return $qb->getQuery()->getResult();
    }

}

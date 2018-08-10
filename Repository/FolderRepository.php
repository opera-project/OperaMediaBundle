<?php

namespace Opera\MediaBundle\Repository;

use Opera\MediaBundle\Entity\Folder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Folder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Folder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Folder[]    findAll()
 * @method Folder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FolderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    public function findBySourceRootFolder($sourceName): array
    {
        return $this->createQueryBuilder('f')
                    ->andWhere('f.parent is NULL')
                    ->andWhere('f.source = :sourceName')
                    ->setParameter('sourceName', $sourceName)
                    ->getQuery()
                    ->getResult();
    }

    public function findOneBySourceAndId(string $source, string $id) : ?Folder
    {
        return $this->findOneBy([
            'source' => $source,
            'id' => $id,
        ]);
    }

    public function getSourceFolders($sourceName)
    {
        return $this->createQueryBuilder('f')
                  ->andWhere('f.source = :source')
                  ->setParameter('source', $sourceName);
    }

    public function getAvailableParentFolder($sourceName, Folder $folder)
    {
        return $this->createQueryBuilder('f')
                    ->andWhere('f.source = :source')
                    ->andWhere('f.id != :folder_id')
                    ->leftJoin('f.parent', 'p')
                    ->andWhere('p.id != :folder_id OR p.id is NULL')
                    ->setParameter('source', $sourceName)
                    ->setParameter('folder_id', (string) $folder->getId());
    }

}

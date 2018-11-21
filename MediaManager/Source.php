<?php

namespace Opera\MediaBundle\MediaManager;

use Gaufrette\Filesystem;
use Opera\MediaBundle\Repository\FolderRepository;
use Opera\MediaBundle\Repository\mediaRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Opera\MediaBundle\Entity\Folder;
use Opera\MediaBundle\Entity\Media;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class Source
{
    private $filesystem;

    private $name;

    private $folderRepository;

    private $mediaRepository;

    public function __construct(Filesystem $filesystem,
                                string $name,
                                FolderRepository $folderRepository, 
                                mediaRepository $mediaRepository)
    {
        $this->filesystem = $filesystem;
        $this->name = $name;
        $this->folderRepository = $folderRepository;
        $this->mediaRepository = $mediaRepository;
    }

    public function getName() : string
    {
       return $this->name;
    }

    /**
     * List all media and folder of this source and folder. (by default: folder root (null))
     */
    public function list(?Folder $folder = null) : array
    {
        if ($folder && $folder->getSource() != $this->getName()) {
            throw new \LogicException("Folder source ".$folder->getSource()." not from source ".$this->getName());
        }

        if ($folder === null) {
            $subfolders = $this->folderRepository->findBySourceRootFolder($this->name);
            $mediaInFolder = $this->mediaRepository->findBySourceRootFolder($this->name);
        } else {
            $subfolders = $folder->getChilds()->getValues();
            $mediaInFolder = $folder->getMedias()->getValues();
        }

        return array_merge($subfolders ?? [], $mediaInFolder ?? []);
    }

    /**
     * List all media of this source and folder using a pagination
     */
    public function listMedias(?Folder $folder = null, ?int $page = 1)
    {
        if ($folder && $folder->getSource() != $this->getName()) {
            throw new \LogicException("Folder source ".$folder->getSource()." not from source ".$this->getName());
        }

        $qb = $this->mediaRepository->queryBuilderBySourceAndFolder($this->name, $folder);

        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(16);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }

    /**
     * list all folders of this source and folder. No pagination
     */
    public function listFolders(?Folder $folder = null)
    {
        if ($folder) {
            return $folder->getChilds()->getValues();
        }

        return $this->folderRepository->findBySourceRootFolder($this->name);
    }

    public function upload(Media $media) : Media
    {
        if (!$media->getPath() instanceof UploadedFile) {
            throw new \InvalidArgumentException('path have to be one of '.UploadedFile::class);
        }

        $file = $media->getPath();
        $content = file_get_contents($file->getPathname());
        $md5sum = md5($content);

        // Generate a unique path based on the date and add file extension of the uploaded file
        $path = sprintf('%s/%s/%s/%s.%s', substr($md5sum, 0, 2), substr($md5sum, 2, 2), substr($md5sum, 4, 2), $md5sum, $file->getClientOriginalExtension());
        $this->filesystem->write($path, $content, true);

        $media->setPath($path);
        $media->setMime($file->getMimeType());
        $media->setSource($this->getName());

        return $media;
    }

    public function has(Media $media)
    {
        return $this->filesystem->has($media->getPath());
    }

    public function read(Media $media)
    {
        return $this->filesystem->read($media->getPath());
    }

    public function delete(Media $media)
    {
        if (!$this->has($media)) {
            return;
        }

        $this->filesystem->delete($media->getPath());
    }

    public function size(Media $media)
    {
        return $this->filesystem->size($media->getPath());
    }
    
    public function mimeType(Media $media)
    {
        return $this->filesystem->mimeType($media->getPath());
    }
}

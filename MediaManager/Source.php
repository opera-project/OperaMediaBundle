<?php

namespace Opera\MediaBundle\MediaManager;

use Gaufrette\Filesystem;
use Opera\MediaBundle\Repository\FolderRepository;
use Opera\MediaBundle\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Opera\MediaBundle\Entity\Folder;
use Opera\MediaBundle\Entity\Media;

class Source
{
    private $filesystem;

    private $name;

    private $folderRepository;

    private $mediarepository;

    public function __construct(Filesystem $filesystem,
                                string $name,
                                FolderRepository $folderRepository, 
                                MediaRepository $mediarepository)
    {
        $this->filesystem = $filesystem;
        $this->name = $name;
        $this->folderRepository = $folderRepository;
        $this->mediarepository = $mediarepository;
    }

    public function getName() : string
    {
       return $this->name;
    }

    public function list(?Folder $folder = null) : array
    {
        if ($folder && $folder->getSource() != $this->getName()) {
            throw new \LogicException("Folder source ".$folder->getSource()." not from source ".$this->getName());
        }

        if ($folder === null) {
            $subfolders = $this->folderRepository->findBySourceRootFolder($this->name);
            $mediaInFolder = $this->mediarepository->findBySourceRootFolder($this->name);
        } else {
            $subfolders = $folder->getChilds()->getValues();
            $mediaInFolder = $folder->getMedias()->getValues();
        }

        return array_merge($subfolders ?? [], $mediaInFolder ?? []);
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

}
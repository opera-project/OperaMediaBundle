<?php

namespace Opera\MediaBundle\MediaManager;

use Gaufrette\Filesystem;
use Opera\MediaBundle\Repository\FolderRepository;
use Opera\MediaBundle\Repository\MediaRepository;

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
            throw new \LogicException("Folder source ".$folder->getSource()." different than");
        }

        if ($folder === null) {
            $subfolders = $folderRepository->findBySourceRootFolder($this->name);
            $mediaInFolder = $folderRepository->findBySourceRootFolder($this->name);
        } else {
            $subfolders = $folder->getFolders();
            $mediaInFolder = $folder->getMedias();
        }

        return array_concat($subfolders, $mediaInFolder);
    }
}
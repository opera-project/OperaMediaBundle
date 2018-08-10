<?php

namespace Opera\MediaBundle\MediaManager;
use Opera\MediaBundle\Entity\Folder;
use Opera\MediaBundle\Entity\Media;
use Symfony\Component\Form\FormFactoryInterface;
use Opera\MediaBundle\Form\FolderType;
use Opera\MediaBundle\Form\MediaType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Opera\MediaBundle\Repository\FolderRepository;

class MediaManager
{
    private $formFactory;
    private $folderRepository;

    private $sources = [];

    public function __construct(FormFactoryInterface $formFactory,
                                FolderRepository $folderRepository)
    {
        $this->formFactory = $formFactory;
        $this->folderRepository = $folderRepository;
    }

    public function registerSource(Source $source)
    {
        $this->sources[$source->getName()] = $source;
    }

    public function getSources() : ?array
    {
        return $this->sources;
    }

    public function getSource(string $sourceSlug)
    {
        return $this->sources[$sourceSlug];
    }

    public function hasSource(string $sourceSlug)
    {
        return isset($this->sources[$sourceSlug]);
    }

    public function prepareMediaForm(?Media $media, string $sourceName, ?string $parentFolderId = null): Form
    {
        if (!$media) {
            $media = new Media();
            $media->setSource($sourceName);
            $media->setFolder($parentFolderId ? $this->folderRepository->findOneBySourceAndId($sourceName, $parentFolderId) : null);

            return $this->formFactory->create(MediaType::class, $media);
        } 

        return $this->formFactory->create(MediaType::class, $media, [
            'mode' => 'edit',
            'source' => $media->getSource(),
        ]);
    }

    public function uploadAndPrepareMediaForSave(UploadedFile $file, Media $media)
    {
        $source = $this->getSource($media->getSource());

        $path = $source->upload($file);

        $media->setMime($file->getMimeType());
        $media->setPath($path);
    }

    public function prepareFolderForm(?Folder $folder, string $sourceName, ?string $parentFolderId = null): Form
    {
        if (!$folder) {
            $folder = new Folder();
            $folder->setSource($sourceName);
            $folder->setParent($parentFolderId ? $this->folderRepository->findOneBySourceAndId($sourceName, $parentFolderId) : null);

            return $this->formFactory->create(FolderType::class, $folder);
        }
        
        return $this->formFactory->create(FolderType::class, $folder, [
            'mode' => 'edit',
            'source' => $folder->getSource(),
            'folder' => $folder,
        ]);
    }

}
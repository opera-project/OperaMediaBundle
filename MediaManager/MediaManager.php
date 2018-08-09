<?php

namespace Opera\MediaBundle\MediaManager;
use Opera\MediaBundle\Entity\Folder;
use Opera\MediaBundle\Entity\Media;
use Symfony\Component\Form\FormFactoryInterface;
use Opera\MediaBundle\Form\FolderType;
use Opera\MediaBundle\Form\MediaType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManager
{
    private $formFactory;

    private $sources = [];

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
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
        isset($this->sources[$sourceSlug]);
    }

    public function prepareMediaForm(?Media $media): Form
    {
        if (!$media) {
            $media = new Media();
            $form = $this->formFactory->create(MediaType::class, $media, ['mode' => 'new']);
        } else {
            $form = $this->formFactory->create(MediaType::class, $media, [
                'mode' => 'edit',
                'source' => $media->getSource(),
            ]);
        }

        return $form;
    }

    public function uploadAndPrepareMediaForSave(UploadedFile $file, Media $media)
    {
        $source = $this->getSource($media->getSource());

        $path = $source->upload($file);

        $media->setMime($file->getMimeType());
        $media->setPath($path);
    }

    public function prepareFolderForm(?Folder $folder): Form
    {
        if (!$folder) {
            $folder = new Folder();
            $form = $this->formFactory->create(FolderType::class, $folder, ['mode' => 'new']);
        } else {
            $form = $this->formFactory->create(FolderType::class, $folder, [
                'mode' => 'edit',
                'source' => $folder->getSource(),
                'folder_id' => $folder->getId()->toString(),
            ]);
        }

        return $form;
    }

}
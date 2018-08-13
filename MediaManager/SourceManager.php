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

class SourceManager
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

}
<?php

namespace Opera\MediaBundle\SearchManager;

use Opera\MediaBundle\Repository\FolderRepository;
use Opera\MediaBundle\Repository\MediaRepository;
use Opera\MediaBundle\MediaManager\Source;
use Opera\MediaBundle\Entity\Folder;

class SearchManager
{
    public function __construct(
        FolderRepository $folderRepository,
        MediaRepository $mediaRepository
    ) {
        $this->folderRepository = $folderRepository;
        $this->mediaRepository = $mediaRepository;
    }

    public function search(Search $search, Source $source, ?Folder $currentFolder = null)
    {
        $folders = null;
        if ($search->getWhat() == Search::SEARCH_WHAT_ONLY_FOLDER || $search->getWhat() == Search::SEARCH_WHAT_ALL) {
            $folders = $this->folderRepository->search($search, $source, $currentFolder);
        }

        $medias = null;
        if ($search->getWhat() == Search::SEARCH_WHAT_ONLY_MEDIA || $search->getWhat() == Search::SEARCH_WHAT_ALL) {
            $medias = $this->mediaRepository->search($search, $source, $currentFolder);
        }

        return array_merge($folders, $medias);
    }

}
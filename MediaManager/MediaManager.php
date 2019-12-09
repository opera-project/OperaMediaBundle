<?php

namespace Opera\MediaBundle\MediaManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Opera\MediaBundle\Repository\FolderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Opera\MediaBundle\MediaManager\SourceManager;
use Opera\MediaBundle\Entity\Folder;
use Opera\MediaBundle\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Opera\MediaBundle\SearchManager\SearchManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MediaManager
{
    public function __construct(
        FolderRepository $folderRepository,
        SourceManager $sourceManager,
        SearchManager $searchManager,
        FormFactoryInterface $formFactory,
        ParameterBagInterface $params
    ) {
        $this->folderRepository = $folderRepository;
        $this->sourceManager = $sourceManager;
        $this->searchManager = $searchManager;
        $this->formFactory = $formFactory;
        $this->filterSets = $params->get('liip_imagine.filter_sets');
    }

    /**
     * Get all mediatheque vars for mediatheque view
     */
    public function getMediathequeVars(Request $request, ?string $source_name = null, ?Folder $folder = null)
    {
        $sources = $this->sourceManager->getSources();
        $selectedSource = $source_name ? $this->sourceManager->getSource($source_name) : array_values($sources)[0];
        if (!$folder && $request->query->get('folder')) {
            $folder = $this->folderRepository->findOneBy(["id" => $request->query->get('folder')]);
        }
        $pagerFantaMedia = $selectedSource->listMedias($folder, $request->get('page', 1));
        $folders = ($request->get('page') == 1 || !$request->get('page')) ? $selectedSource->listFolders($folder) : [];

        $breadCrumb = [];

        if ($folder) {
            $breadCrumb = $folder->getBreadcrumbArray();
        }

        /**
         * SEARCH
         */
        $searchResult = null;
        $searchForm = $this->formFactory->create(SearchType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->getData();
            $searchResult = $this->searchManager->search($search, $selectedSource, $folder);
        }

        return [
            'sources' => $sources,
            'mode' => $request->isXmlHttpRequest() ? 'ajax' : ($request->get('mode') ? $request->get('mode') : 'html'),
            'selected_folder' => $folder,
            'selected_source' => $selectedSource,
            'pagerFantaMedia' => $pagerFantaMedia,
            'folders' => $folders,
            'filter_sets' => $this->filterSets,
            'breadcrumb' => $breadCrumb,
            'searchForm' => $searchForm->createView(),
            'searchResult' => $searchResult
        ];
    }
}
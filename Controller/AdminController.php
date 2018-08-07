<?php

namespace Opera\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Opera\MediaBundle\Repository\FolderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Opera\MediaBundle\MediaManager\MediaManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opera\MediaBundle\Entity\Folder;

class AdminController extends Controller
{
    /**
     * @Route("/media/{source_name}/{folder_id}", defaults={ "folder_id": null, "source_name": null }, name="opera_admin_media_list")
     * @Entity("folder", expr="folder_id ? repository.findOneBySourceAndId(source_name, folder_id) : null")
     * @Template
     */
    public function view(?string $source_name = null, ?Folder $folder = null, MediaManager $mediaManager)
    {
        $sources = $mediaManager->getSources();

        return [
            'sources' => $sources,
            'folder' => $folder,
            'source' => $source_name ? $mediaManager->getSource($source_name) : array_values($sources)[0],
        ];
    }
}

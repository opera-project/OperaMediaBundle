<?php

namespace Opera\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Opera\MediaBundle\Repository\FolderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Routing\Annotation\Route;
use Opera\MediaBundle\MediaManager\SourceManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Opera\MediaBundle\Entity\Folder;
use Opera\MediaBundle\Entity\Media;
use Opera\MediaBundle\Form\FolderType;
use Opera\MediaBundle\Form\MediaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class AdminController extends Controller
{
    /**
     * @Route("/media/folder/form/{id}", defaults={ "id": null }, name="opera_admin_media_folder_form")
     * @Template
     */
    public function formFolder(FolderRepository $folderRepository, FormFactoryInterface $formFactory, SourceManager $sourceManager, Request $request, Folder $folder = null)
    {
        $form = $this->createFolderForm($folderRepository, $formFactory, $request, $folder);
        $result = $this->handleForm($sourceManager, $form, $request);
        
        return $result ? $result : [
            'folder' => $folder,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/media/media/form/{id}", defaults={ "id": null }, name="opera_admin_media_media_form")
     */
    public function formMedia(FolderRepository $folderRepository, FormFactoryInterface $formFactory, SourceManager $sourceManager, Request $request, ?Media $media = null)
    {
        $media != null ? $editForm = true : $editForm = false;

        $form = $this->createMediaForm($folderRepository, $formFactory, $request, $media);
        $result = $this->handleForm($sourceManager, $form, $request);
        
        if ($result) {
            return $result;
        }
        return $this->render(
            $editForm ? '@OperaMedia/admin/form_media_edit.html.twig' : '@OperaMedia/admin/form_media.html.twig', [
                'media' => $media,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/media/{source_name}/{folder_id}", defaults={ "folder_id": null, "source_name": null }, name="opera_admin_media_list")
     * @Entity("folder", expr="folder_id ? repository.findOneBySourceAndId(source_name, folder_id) : null")
     * @Template
     */
    public function view(?string $source_name = null, ?Folder $folder = null, SourceManager $sourceManager, Request $request)
    {
        $sources = $sourceManager->getSources();
        $selectedSource = $source_name ? $sourceManager->getSource($source_name) : array_values($sources)[0];
        $pagerFantaMedia = $selectedSource->listMedias($folder, $request->get('page', 1));
        $folders = ($request->get('page') == 1 || !$request->get('page')) ? $selectedSource->listFolders($folder) : [];

        $breadCrumb = [];
        if ($folder) {
            $selectedFolder = $folder;
            $breadCrumb[] = $selectedFolder;
            while ($selectedFolder->getParent()) {
                $breadCrumb[] = $selectedFolder->getParent();
                $selectedFolder = $selectedFolder->getParent();
            }
        }

        return [
            'sources' => $sources,
            'mode' => $request->isXmlHttpRequest() ? 'ajax' : ($request->get('mode') ? $request->get('mode') : 'html'),
            'selected_folder' => $folder,
            'selected_source' => $selectedSource,
            'pagerFantaMedia' => $pagerFantaMedia,
            'folders' => $folders,
            'filter_sets' => $this->container->getParameter('liip_imagine.filter_sets'),
            'breadcrumb' => array_reverse($breadCrumb)
        ];
    }


    /**
     * @Route("/media/folder/{id}/delete", name="opera_admin_media_delete_folder")
     */
    public function deleteFolder(Folder $folder, Request $request)
    {
        $sourceName = $folder->getSource();
        $parentFolderId = $folder->getParent() ? $folder->getParent()->getId() : null;
    
        $em = $this->getDoctrine()->getManager();
        $em->remove($folder);
        $em->flush();

        return $this->redirectToRoute('opera_admin_media_list', [
            'source_name' => $sourceName,
            'mode'      => $request->isXmlHttpRequest() ? 'ajax' : 'html',
            'folder_id' => $parentFolderId,
        ]);
    }

    /**
     * @Route("/media/media/{id}/delete", name="opera_admin_media_delete_media")
     */
    public function deleteMedia(SourceManager $sourceManager, Media $media, Request $request)
    {
        $sourceName = $media->getSource();
        $parentFolderId = $media->getFolder() ? $media->getFolder()->getId() : null;
        
        $source = $sourceManager->getSource($media->getSource());
        $source->delete($media);

        $em = $this->getDoctrine()->getManager();
        $em->remove($media);
        $em->flush();

        return $this->redirectToRoute('opera_admin_media_list', [
            'source_name' => $sourceName,
            'mode'      => $request->isXmlHttpRequest() ? 'ajax' : 'html',
            'folder_id' => $parentFolderId,
        ]);
    }

    private function handleForm(SourceManager $sourceManager, FormInterface $form, Request $request)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $item = $form->getData();

            if ($item instanceof Media && $request->files && $request->files->get('media') && isset($request->files->get('media')['path'])) {
                $sourceManager->getSource($item->getSource())->upload($item);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($item);
            $entityManager->flush();
    
            return $this->redirectToRoute('opera_admin_media_list', [
                'source_name' => $item->getSource(),
                'mode'      => $request->isXmlHttpRequest() ? 'ajax' : 'html',
                'folder_id' => $item->getFolder() ? $item->getFolder()->getId() : null,
            ]);
        }

        return null;
    }

    private function createFolderForm(FolderRepository $folderRepository, FormFactoryInterface $formFactory, Request $request, ?Folder $folder)
    {
        if (!$folder) {
            $folder = new Folder();

            if ($request->get('source')) {
                $folder->setSource($request->get('source'));
            }
            $folder->setParent($request->get('parentFolder') ? $folderRepository->findOneBySourceAndId($request->get('source'), $request->get('parentFolder')) : null);

            return $formFactory->create(FolderType::class, $folder);
        }
        
        return $formFactory->create(FolderType::class, $folder, [
            'mode' => 'edit',
            'source' => $folder->getSource(),
            'folder' => $folder,
        ]);
    }

    private function createMediaForm(FolderRepository $folderRepository, FormFactoryInterface $formFactory, Request $request, ?Media $media)
    {
        if (!$media) {
            $media = new Media();
            $media->setSource($request->get('source'));
            $media->setFolder($request->get('parentFolder') ? $folderRepository->findOneBySourceAndId($request->get('source'), $request->get('parentFolder')) : null);
            
            return $formFactory->create(MediaType::class, $media);
        }

        return $form = $formFactory->create(MediaType::class, $media, [
            'mode' => 'edit',
            'source' => $media->getSource(),
        ]);
    }

}

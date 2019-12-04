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
use Opera\MediaBundle\SearchManager\SearchManager;
use Opera\MediaBundle\MediaManager\MediaManager;

class AdminController extends Controller
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("/media/folder/form/{id}", defaults={ "id": null }, name="opera_admin_media_folder_form")
     * @Template
     */
    public function formFolder(FolderRepository $folderRepository, SourceManager $sourceManager, Request $request, Folder $folder = null)
    {
        $form = $this->createFolderForm($folderRepository, $request, $folder);
        $result = $this->handleForm($sourceManager, $form, $request);
        
        return $result ? $result : [
            'folder' => $folder,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/media/media/form/{id}", defaults={ "id": null }, name="opera_admin_media_media_form")
     */
    public function formMedia(FolderRepository $folderRepository, SourceManager $sourceManager, Request $request, ?Media $media = null)
    {
        $media != null ? $editForm = true : $editForm = false;

        $form = $this->createMediaForm($folderRepository, $request, $media);
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
    public function view(
        ?string $source_name = null,
        ?Folder $folder = null,
        Request $request,
        MediaManager $mediaManager
    ) {
        return $mediaManager->getMediathequeVars($request, $source_name, $folder);
    }

    /**
     * @Route("/view-modal", name="opera_admin_choose_media")
     * @Template
     */
    public function viewModal(
    ?string $source_name = null,
    ?Folder $folder = null,
    Request $request,
    MediaManager $mediaManager
    ) {
        return $mediaManager->getMediathequeVars($request, $source_name, $folder);
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

    private function createFolderForm(FolderRepository $folderRepository, Request $request, ?Folder $folder)
    {
        if (!$folder) {
            $folder = new Folder();

            if ($request->get('source')) {
                $folder->setSource($request->get('source'));
            }
            $folder->setParent($request->get('parentFolder') ? $folderRepository->findOneBySourceAndId($request->get('source'), $request->get('parentFolder')) : null);

            return $this->formFactory->create(FolderType::class, $folder);
        }
        
        return $this->formFactory->create(FolderType::class, $folder, [
            'mode' => 'edit',
            'source' => $folder->getSource(),
            'folder' => $folder,
        ]);
    }

    private function createMediaForm(FolderRepository $folderRepository, Request $request, ?Media $media)
    {
        if (!$media) {
            $media = new Media();
            $media->setSource($request->get('source'));
            $media->setFolder($request->get('parentFolder') ? $folderRepository->findOneBySourceAndId($request->get('source'), $request->get('parentFolder')) : null);
            
            return $this->formFactory->create(MediaType::class, $media);
        }

        return $form = $this->formFactory->create(MediaType::class, $media, [
            'mode' => 'edit',
            'source' => $media->getSource(),
        ]);
    }

}

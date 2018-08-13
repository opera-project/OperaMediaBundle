<?php

namespace Opera\MediaBundle\Listener;

use Opera\MediaBundle\Entity\Folder;
use Doctrine\ORM\EntityManagerInterface;
use Opera\MediaBundle\MediaManager\SourceManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class FolderListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private function getSourceManager() : SourceManager
    {
        return $this->container->get(SourceManager::class);
    }

    public function preRemove(Folder $folder)
    {
        foreach ($folder->getChilds() as $subfolder) {
            $this->preRemove($subfolder);
        }

        foreach ($folder->getMedias() as $media) {
            $source = $this->getSourceManager()->getSource($media->getSource());
            $source->delete($media);
        }
    }

}

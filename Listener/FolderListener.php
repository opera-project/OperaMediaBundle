<?php

namespace Opera\MediaBundle\Listener;

use Opera\MediaBundle\Entity\Folder;
use Doctrine\ORM\EntityManagerInterface;
use Opera\MediaBundle\MediaManager\MediaManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class FolderListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private function getMediaManager() : MediaManager
    {
        return $this->container->get(MediaManager::class);
    }

    public function preRemove(Folder $folder)
    {
        foreach ($folder->getChilds() as $subfolder) {
            $this->preRemove($subfolder);
        }

        foreach ($folder->getMedias() as $media) {
            $source = $this->getMediaManager()->getSource($media->getSource());
            $source->delete($media);
        }
    }

}

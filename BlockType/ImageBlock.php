<?php

namespace Opera\MediaBundle\BlockType;

use Opera\CoreBundle\BlockType\BaseBlock;
use Opera\CoreBundle\BlockType\BlockTypeInterface;
use Opera\CoreBundle\Form\Type\CkEditorOrTextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Opera\CoreBundle\Entity\Block;
use Opera\MediaBundle\Form\MediaTextType;

use Opera\MediaBundle\Repository\MediaRepository;

class ImageBlock extends BaseBlock implements BlockTypeInterface
{
    private $mediaRepository;

    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function getType() : string
    {
        return 'image';
    }

    public function getTemplate(Block $block) : string
    {
        return sprintf('@OperaMedia/blocks/%s.html.twig', $this->getType());
    }

    public function createAdminConfigurationForm(FormBuilderInterface $builder)
    {
        $builder->add('image', MediaTextType::class);
    }

    public function getVariables(Block $block) : array
    {
        $config = $block->getConfiguration();

        return [
            'media' => isset($config['image']) ? $this->mediaRepository->find($config['image']) : null
        ];
    }

}

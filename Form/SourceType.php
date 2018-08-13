<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Opera\MediaBundle\MediaManager\SourceManager;

class SourceType extends AbstractType
{
    private $sourceManager;

    public function __construct(SourceManager $sourceManager)
    {
        $this->sourceManager = $sourceManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_map(function($value) {
                return $value->getName();
            }, $this->sourceManager->getSources()),
        ]);
    }

    public function getParent()
    {
        return \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class;
    }
}
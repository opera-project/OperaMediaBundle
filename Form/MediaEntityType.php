<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Opera\MediaBundle\Entity\Media;
use Opera\MediaBundle\MediaManager\SourceManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MediaEntityType extends AbstractType
{
    private $sourceManager;

    private $registry;

    public function __construct(SourceManager $sourceManager, RegistryInterface $registry)
    {
        $this->sourceManager = $sourceManager;
        $this->registry = $registry;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $sources = $this->sourceManager->getSources();

        $resolver->setDefaults([
            'class' => Media::class,
            'sources' => $sources,
            'selected_source' => array_values($sources)[0],
            'selected_folder' => null,
            'items' => array_values($sources)[0]->list(),
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $sources = $this->sourceManager->getSources();
    
        $view->vars['sources'] = $options['sources'];
        $view->vars['selected_source'] = $options['selected_source'];
        $view->vars['selected_folder'] = $options['selected_folder'];
        $view->vars['items'] = $options['items'];
        $view->vars['current_image'] = $form->getData() ? $this->registry->getRepository(Media::class)->find($form->getData()) : null;
    }
}
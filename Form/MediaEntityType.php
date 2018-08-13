<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Opera\MediaBundle\Entity\Media;
use Opera\MediaBundle\MediaManager\SourceManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class MediaEntityType extends AbstractType
{
    private $sourceManager;

    public function __construct(SourceManager $sourceManager)
    {
        $this->sourceManager = $sourceManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $sources = $this->sourceManager->getSources();

        $resolver->setDefaults([
            'class' => Media::class,
            // 'sources' => $sources,
            // 'selected_source' => array_values($sources)[0],
            // 'selected_folder' => null,
            // 'items' => array_values($sources)[0]->list(),
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $sources = $this->sourceManager->getSources();

        $config = $form->getConfig();
    
        $view->vars['sources'] = $sources;
        $view->vars['selected_source'] = array_values($sources)[0];
        $view->vars['selected_folder'] = null;
        $view->vars['items'] = array_values($sources)[0]->list();


        // $view->vars['sources'] = $config->getAttribute('sources');
        // $view->vars['selected_source'] = $config->getAttribute('selected_source');
        // $view->vars['selected_folder'] = $config->getAttribute('selected_folder');
        // $view->vars['items'] = $config->getAttribute('items');
    }
}
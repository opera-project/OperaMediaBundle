<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Opera\MediaBundle\Entity\Media;
use Opera\MediaBundle\MediaManager\SourceManager;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Opera\CoreBundle\Form\DataTransformer\IdToModelTransformer;
use Symfony\Component\Form\FormBuilderInterface;

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
            'sources' => $sources,
            'selected_source' => array_values($sources)[0],
            'selected_folder' => null,
            'pagerFantaMedia' => array_values($sources)[0]->listMedias(null, 1),
            'folders' => array_values($sources)[0]->listFolders(),
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'media_entity';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new IdToModelTransformer($this->registry, Media::class));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $sources = $this->sourceManager->getSources();
    
        $view->vars['sources'] = $options['sources'];
        $view->vars['selected_source'] = $options['selected_source'];
        $view->vars['selected_folder'] = $options['selected_folder'];
        $view->vars['pagerFantaMedia'] = $options['pagerFantaMedia'];
        $view->vars['folders'] = $options['folders'];

        $view->vars['current_image'] = $form->getData() ? $this->registry->getRepository(Media::class)->find($form->getData()) : null;
    }
}
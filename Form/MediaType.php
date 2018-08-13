<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityRepository;
use Opera\MediaBundle\MediaManager\SourceManager;

use Opera\MediaBundle\Entity\Folder;
use Opera\MediaBundle\Entity\Media;


class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
                ->add('slug');

        if ($options['mode'] === 'new') {
            $builder->add('source', SourceType::class, array('disabled' => true))
                    ->add('path', FileType::class);
        }

        if ($options['mode'] === 'edit') {
            $builder->add('folder', EntityType::class, array(
                'class' => Folder::class,
                'required'   => false,
                'placeholder' => 'Root',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->getSourceFolders($options['source']);
                }
            ));
        }

        //todoremove
        // $builder->add('blah', MediaEntityType::class, ['mapped' => false]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'mode' => 'new', // 'new' or 'edit'
            'source' => null,
        ]);

    }

}
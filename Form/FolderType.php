<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityRepository;
use Opera\MediaBundle\MediaManager\SourceManager;

use Opera\MediaBundle\Entity\Folder;

class FolderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
                ->add('slug');

        if ($options['mode'] === 'edit') {
            $builder->add('parent', EntityType::class, array(
                'class' => Folder::class,
                'required'   => false,
                'placeholder' => 'Root',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->getAvailableParentFolder($options['source'], $options['folder']);
                }
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Folder::class,
            'mode' => 'new', // 'new' or 'edit'
            'source' => null,
            'folder' => null,
        ]);
    }


}
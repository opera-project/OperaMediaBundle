<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use Opera\MediaBundle\Entity\Folder;

class FolderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
                ->add('slug');

        if ($options['mode'] === 'new') {
            $builder->add('source');
        }

        if ($options['mode'] === 'edit') {
            $builder->add('parent', EntityType::class, array(
                'class' => Folder::class,
                'required'   => false,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $res = $er->createQueryBuilder('f')
                            ->andWhere('f.source = :source')
                            ->andWhere('f.id != :folder_id')
                            ->leftJoin('f.parent', 'p')
                            ->andWhere('p.id != :folder_id OR p.id is NULL')
                            ->setParameter('source', $options['source'])
                            ->setParameter('folder_id', $options['folder_id']);
                    return $res;
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
            'folder_id' => null,
        ]);
    }


}
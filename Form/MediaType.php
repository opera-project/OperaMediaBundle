<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityRepository;
use Opera\MediaBundle\MediaManager\MediaManager;

use Opera\MediaBundle\Entity\Folder;
use Opera\MediaBundle\Entity\Media;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;


class MediaType extends AbstractType
{
    private $mediaManager;

    public function __construct(MediaManager $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
                ->add('slug');

        if ($options['mode'] === 'new') {
            $builder->add('source');
            $builder->add('path', FileType::class, array(
                // 'mapped' => false,
                'data_class' => null, // required ?
            ));
        }

        if ($options['mode'] === 'edit') {
            $builder->add('folder', EntityType::class, array(
                'class' => Folder::class,
                'required'   => false,
                'query_builder' => function (EntityRepository $er) use ($options) {
                  return $er->createQueryBuilder('f')
                            ->andWhere('f.source = :source')
                            ->setParameter('source', $options['source']);
                }
            ));
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT,  function(FormEvent $event) use ($options) {
            $datas = $event->getData();
            $form = $event->getForm();

            if ($options['mode'] === 'new' && isset($datas['source'])
                && !$this->mediaManager->hasSource($datas['source'])) {
                    $form->addError(new FormError('Source'.$datas['source']." don't exist"));
            }
        });
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
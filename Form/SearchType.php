<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Opera\MediaBundle\SearchManager\Search;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('what', ChoiceType::class, [
                'label' => 'What to search',
                'choices' => [
                    Search::SEARCH_WHAT_ALL => Search::SEARCH_WHAT_ALL,
                    Search::SEARCH_WHAT_ONLY_FOLDER => Search::SEARCH_WHAT_ONLY_FOLDER,
                    Search::SEARCH_WHAT_ONLY_MEDIA => Search::SEARCH_WHAT_ONLY_MEDIA,
                ],
            ])
            ->add('where', ChoiceType::class, [
                'label' => 'Where to search',
                'choices' => [
                    Search::SEARCH_WHERE_SOURCE => Search::SEARCH_WHERE_SOURCE,
                    Search::SEARCH_WHERE_FOLDER => Search::SEARCH_WHERE_FOLDER,
                    Search::SEARCH_WHERE_FOLDERS => Search::SEARCH_WHERE_FOLDERS,
                ],
            ])
            ->add('search', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // todo
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'data_class' => Search::class,
        ]);
    }


}
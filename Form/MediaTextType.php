<?php

namespace Opera\MediaBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class MediaTextType extends MediaEntityType
{

    public function getParent()
    {
        return TextType::class;
    }

}
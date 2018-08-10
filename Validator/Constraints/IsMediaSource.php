<?php

namespace Opera\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsMediaSource extends Constraint
{
    public $message = 'The source name "{{ string }}" is not a valid source.';
}
<?php

namespace Opera\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Opera\MediaBundle\MediaManager\MediaManager;

class IsMediaSourceValidator extends ConstraintValidator
{
    private $mediaManager;

    public function __construct(MediaManager $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$this->mediaManager->hasSource($value)) {
            $this->context->addViolation($constraint->message, ['{{ string }}' => $value]);
        }
    }
}
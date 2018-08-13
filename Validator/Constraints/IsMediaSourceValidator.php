<?php

namespace Opera\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Opera\MediaBundle\MediaManager\SourceManager;

class IsMediaSourceValidator extends ConstraintValidator
{
    private $sourceManager;

    public function __construct(SourceManager $sourceManager)
    {
        $this->sourceManager = $sourceManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$this->sourceManager->hasSource($value)) {
            $this->context->addViolation($constraint->message, ['{{ string }}' => $value]);
        }
    }
}
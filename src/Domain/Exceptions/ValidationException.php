<?php

namespace App\Domain\Exceptions;

class ValidationException extends DomainException
{
    protected $errors;

    public function __construct(array $errors)
    {
        parent::__construct('validation_exception.message');

        $this->errors = $errors;
    }

    public function errors(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $errors[$error->getPropertyPath()][] = $error->getMessage();
        }
        return $errors;
    }
}
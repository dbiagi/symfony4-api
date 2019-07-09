<?php declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidEntityException extends Exception
{
    /** @var ConstraintViolationListInterface */
    private $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        parent::__construct('Entity not valid.', 0, null);

        $this->violations = $violations;
    }

    public function getErrors(): array
    {
        $errors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($this->violations as $violation) {
            $errors[] = [
                'property' => $violation->getPropertyPath(),
                'message'  => $violation->getMessage(),
            ];
        }

        return $errors;
    }
}
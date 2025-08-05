<?php

namespace App\Exception;

class SeedException extends AppException
{
    /**
     * @var string[]
     */
    private array $errors;

    public function __construct(
        array $errors,
    ) {
        parent::__construct('Errors occurred while seeding', 0, null);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }


}

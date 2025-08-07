<?php

namespace App\Exception;

class BulkOperationException extends AppException
{
    /**
     * @var string[]
     */
    private array $errors;

    public function __construct(
        array $errors,
    ) {
        parent::__construct('Errors occurred in bulk operation', 0, null);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

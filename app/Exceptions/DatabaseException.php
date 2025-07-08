<?php
namespace App\Exceptions;

use RuntimeException;

class DatabaseException extends RuntimeException {
    public function __construct(string $message = "Erro no banco de dados", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
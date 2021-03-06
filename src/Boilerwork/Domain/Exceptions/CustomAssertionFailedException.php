<?php

namespace Boilerwork\Domain\Exceptions;

// use Assert\InvalidArgumentException;
// use Assert\LazyAssertionException;

class CustomAssertionFailedException extends \Assert\LazyAssertionException
{
    /**
     * @param InvalidArgumentException[] $errors
     */
    public static function fromErrors(array $errors): self
    {
        // Format output
        $parsedErrors = [];

        foreach ($errors as $item) {
            $parsedErrors[] = [
                'code' => $item->getPropertyPath(),
                'message' => $item->getMessage(),
                'valueReceived' => $item->getValue(),
            ];
        }
        $message = json_encode($parsedErrors);

        return new static($message, $errors);
    }

    public function __construct($message, array $errors)
    {
        parent::__construct($message, $errors);

        $this->errors = $errors;
    }
}

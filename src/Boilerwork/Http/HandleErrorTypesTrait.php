#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Boilerwork\Http;

use Boilerwork\Support\Exceptions\CustomException;
use Boilerwork\Validation\CustomAssertionFailedException;
use Throwable;
use TypeError;

use function json_decode;
use function ltrim;
use function preg_match;

trait HandleErrorTypesTrait
{
    private static function getErrorDetails(Throwable $th): array
    {
        if ($th instanceof TypeError) {
            return self::handleTypeError($th);
        } elseif ($th instanceof CustomAssertionFailedException || $th instanceof \Assert\InvalidArgumentException) {
            return self::handleValidationErrors($th);
        } elseif ($th instanceof CustomException) {
            return self::handleCustomException($th);
        }
        return self::handleServerError($th);
    }

    private static function handleTypeError(TypeError $th): array
    {
        $status = 400;
        $code = "typeError";
        $message = "Wrong parameters given";
        preg_match('/Argument \#(\d+) \((.*?)\) must be of type (.*?), (.*?) given/', $th->getMessage(), $matches);

        if (count($matches) >= 5) {
            $variableName = ltrim($matches[2], '$');
            $expectedType = $matches[3];
            $actualType = $matches[4];
            $message = "$variableName must be of type $expectedType, $actualType given";
        }
        $errors = [[
            "code" => "$variableName.invalid",
            "message" => $message,
            "valueReceived" => $actualType
        ]];

        return [$status, $code, $message, $errors];
    }

    private static function handleValidationErrors(Throwable $th): array
    {
        $status = 422;
        $code = "validationError";
        $message = "Request is invalid or malformed";
        $errors = json_decode($th->getMessage());

        return [$status, $code, $message, $errors];
    }

    private static function handleCustomException(CustomException $th): array
    {
        $parse = json_decode($th->getMessage());

        $status = $th->getCode();
        $code = $parse->error->code;
        $message = $parse->error->message;
        $errors = [];

        return [$status, $code, $message, $errors];
    }

    private static function handleServerError(Throwable $th): array
    {
        $status = $th->getCode() >= 100 && $th->getCode() <= 599 ? $th->getCode() : 500;
        $code = "serverError";
        $message = $th->getMessage() ?: 'Server error.';
        $errors = [];

        return [$status, $code, $message, $errors];
    }
}

<?php

namespace TouristAttractionFinder\Presentation\Middleware;

use Respect\Validation\Validator;
use Respect\Validation\Exceptions\ValidationException;

class ValidationMiddleware
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                $errors[$field] = "Field '$field' is required";
                continue;
            }

            try {
                $rule->assert($data[$field]);
            } catch (ValidationException $e) {
                $errors[$field] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            http_response_code(400);
            throw new \InvalidArgumentException(json_encode([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
                'data' => null
            ]));
        }

        return $data;
    }

    public static function validateEmail(string $email): bool
    {
        return Validator::email()->validate($email);
    }

    public static function validatePassword(string $password): bool
    {
        return Validator::stringType()->length(8, null)->validate($password);
    }

    public static function validateName(string $name): bool
    {
        return Validator::stringType()->length(2, 100)->validate($name);
    }
}
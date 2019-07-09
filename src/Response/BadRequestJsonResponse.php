<?php


namespace App\Response;


use Symfony\Component\HttpFoundation\JsonResponse;

class BadRequestJsonResponse extends JsonResponse
{
    public function __construct(string $errorMessage = null, array $errors = [])
    {
        $data['message'] = $errorMessage;

        if (!empty($errors)) {
            $data['errors'] = $errors;
        }

        parent::__construct($data, self::HTTP_BAD_REQUEST);
    }
}
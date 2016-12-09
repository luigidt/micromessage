<?php

namespace MicroMessage\Helpers;

use Symfony\Component\Validator\ConstraintViolationList;

class ViolationsHelper
{
    /**
     * Returns an array with all the violations property name and messages
     * suitable for return in the JsonResponse
     *
     * @param ConstraintViolationList $violations
     * @return mixed
     */
    public static function toJson(ConstraintViolationList $violations)
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ];
        }
        return ['errors' => $errors];
    }
}

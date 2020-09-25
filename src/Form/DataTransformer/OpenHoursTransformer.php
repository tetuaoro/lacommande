<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class OpenHoursTransformer implements DataTransformerInterface
{
    // array to string
    public function transform($value): string
    {
        return implode(', ', null === $value ? [] : $value);
    }

    // string to array
    public function reverseTransform($string): array
    {
        return array_unique(array_filter(array_map('trim', explode(',', $string))));
    }
}

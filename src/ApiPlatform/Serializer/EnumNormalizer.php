<?php

namespace Riverwaysoft\ApiTools\ApiPlatform\Serializer;

use MyCLabs\Enum\Enum;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EnumNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        throw new \RuntimeException("Should not be denormalize");
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return false;
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof Enum) {
            throw new InvalidArgumentException('The object must implement the "Enum".');
        }

        return $object->getValue();
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Enum;
    }

    public function getSupportedTypes($format)
    {
        return [
            Enum::class => true,
        ];
    }
}

<?php

namespace Riverwaysoft\ApiTools\ApiPlatform\Serializer;

use MyCLabs\Enum\Enum;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EnumNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        throw new \RuntimeException("Should not be denormalized");
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return false;
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|null
    {
        if (!$object instanceof Enum) {
            throw new InvalidArgumentException('The object must implement the "Enum".');
        }

        return $object->getValue();
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Enum;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Enum::class => true,
        ];
    }
}

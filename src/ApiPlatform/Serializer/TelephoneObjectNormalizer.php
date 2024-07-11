<?php

namespace Riverwaysoft\ApiTools\ApiPlatform\Serializer;

use Riverwaysoft\ApiTools\Telephone\ParseTelephoneException;
use Riverwaysoft\ApiTools\Telephone\TelephoneObject;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use ArrayObject;

class TelephoneObjectNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        if ('' === $data || null === $data) {
            return null;
        }
        try {
            return TelephoneObject::fromString($data);
        } catch (ParseTelephoneException) {
            return TelephoneObject::fromRawInput($data);
        }
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $type === TelephoneObject::class && $data !== null;
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return ArrayObject|array|string|int|float|bool|null
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): ArrayObject|array|string|int|float|bool|null
    {
        if (!$object instanceof TelephoneObject) {
            throw new InvalidArgumentException('The object must implement the "TelephoneObject".');
        }

        return TelephoneObject::fromString($object)->__toString();
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof TelephoneObject;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            TelephoneObject::class => true,
        ];
    }
}

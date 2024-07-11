<?php

namespace Riverwaysoft\ApiTools\ApiPlatform\Serializer;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MoneyNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Money
    {
        if ('' === $data || null === $data) {
            throw new NotNormalizableValueException('The data is either an empty string or null, you should pass a string that can be parsed with the passed format or a valid Money value.');
        }

        return new Money((int)$data['amount'], new Currency($data['currency']));
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Money::class && $data !== null;
    }

    /**
     * @param Money $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Money) {
            throw new InvalidArgumentException('The object must implement the "Money".');
        }
        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        $formatted = $moneyFormatter->format($object);

        return array_merge($object->jsonSerialize(), ['formatted' => $formatted, 'symbol' => \Symfony\Component\Intl\Currencies::getSymbol($object->getCurrency()->getCode())]);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Money;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Money::class => true,
        ];
    }
}

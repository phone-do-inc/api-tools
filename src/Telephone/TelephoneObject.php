<?php

namespace Riverwaysoft\ApiTools\Telephone;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;

final class TelephoneObject implements \Stringable
{
    public const FORMAT_E164 = 0;
    public const FORMAT_INTERNATIONAL = 1;
    public const FORMAT_NATIONAL = 2;
    public const FORMAT_RFC3966 = 3;
    public const UNKNOWN_REGION = 'ZZ';

    private function __construct(private PhoneNumber $_phone)
    {
    }

    /**
     * @param string|int $code
     * @param string $number
     * @return static
     * @throws ParseTelephoneException
     */
    public static function fromCodeAndNationalNumber(string|int $code, string $number): self
    {
        $parsed = new PhoneNumber();
        $parsed->setCountryCode($code);
        $parsed->setNationalNumber($number);
        if (!PhoneNumberUtil::getInstance()->isValidNumber($parsed)) {
            throw new ParseTelephoneException("Not valid phone number: {$code}{$number}");
        }
        return new self($parsed);
    }

    /**
     * @param string $originTelephone
     * @return static
     * @throws ParseTelephoneException
     */
    public static function fromString(string $originTelephone, string|null $region = null): self
    {
        $phoneNumber = '+' . str_replace('+', '', $originTelephone);
        if (str_starts_with($phoneNumber, '+00')) {
            $phoneNumber = str_replace('+00', '+', $phoneNumber);
        }
        if (str_starts_with($phoneNumber, '+044')) {
            $phoneNumber = str_replace('+044', '+44', $phoneNumber);
        }
        try {
            $parsed = PhoneNumberUtil::getInstance()->parse($phoneNumber, $region ?? PhoneNumberUtil::UNKNOWN_REGION);
            if (!PhoneNumberUtil::getInstance()->isValidNumber($parsed)) {
                throw new ParseTelephoneException("Not valid phone number: {$originTelephone}");
            }
            return new self($parsed);
        } catch (NumberParseException $e) {
            throw new ParseTelephoneException($e->getMessage(), 0, $e);
        }
    }
    public static function fromRawInput(string $rawInput): self
    {
        $parsed = new PhoneNumber();
        $parsed->setRawInput($rawInput);
        return new self($parsed);
    }

    public function getCountryCode(): ?string
    {
        return PhoneNumberUtil::getInstance()->getRegionCodeForNumber($this->_phone);
    }

    public function format(int $format = self::FORMAT_E164): string
    {
        return PhoneNumberUtil::getInstance()->format($this->_phone, $format);
    }

    public function __toString(): string
    {
        return $this->format();
    }

    public function equals(TelephoneObject $object): bool
    {
        return $this->__toString() === (string)$object;
    }
}

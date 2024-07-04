<?php

declare(strict_types=1);

namespace Riverwaysoft\ApiTools\ApiPlatform\Filter;

use ApiPlatform\Api\IdentifiersExtractorInterface;
use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Example of usage:
 *
 * #[ApiFilter(
 *      RiverAdminEnumSearchFilter::class,
 *      properties: ['someProperty'],
 *      arguments: ['enum' => ChatTypeEnum::class])
 * ]
 */
class RiverAdminEnumSearchFilter extends SearchFilter
{
    public function __construct(private string $enum, ManagerRegistry $managerRegistry, ?RequestStack $requestStack, IriConverterInterface $iriConverter, PropertyAccessorInterface $propertyAccessor = null, LoggerInterface $logger = null, array $properties = null, IdentifiersExtractorInterface $identifiersExtractor = null, NameConverterInterface $nameConverter = null)
    {
        parent::__construct($managerRegistry, $requestStack, $iriConverter, $propertyAccessor, $logger, $properties, $identifiersExtractor, $nameConverter);
    }

    public function getDescription(string $resourceClass): array
    {
        $description = parent::getDescription($resourceClass);
        $properties = $this->getProperties();

        foreach ($properties as $property => $value) {
            if ($property && !empty($description[$property])) {
                $description[$property]['property'] = sprintf('riveradmin_enum:%s', $this->serializeEnum($this->enum));
            }
        }

        return $description;
    }

    private function serializeEnum(string $enumFQCN): string
    {
        if (method_exists($enumFQCN, 'toArray')) {
            return json_encode(call_user_func([$enumFQCN, 'toArray']));
        } else {
            return json_encode($this->nativeEnumToArray($enumFQCN));
        }
    }

    private function nativeEnumToArray(string $enumFQCN): array
    {
        $array = [];
        foreach (call_user_func([$enumFQCN, 'cases']) as $case) {
            $array[$case->value] = $case->name;
        }
        return $array;
    }
}

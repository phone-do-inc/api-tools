<?php

declare(strict_types=1);

namespace Riverwaysoft\ApiTools\ApiPlatform\Search;

use ApiPlatform\Core\Api\IdentifiersExtractorInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class EnumSearchFilter extends SearchFilter
{
    public function __construct(private string $enum, ManagerRegistry $managerRegistry, ?RequestStack $requestStack, IriConverterInterface $iriConverter, PropertyAccessorInterface $propertyAccessor = null, LoggerInterface $logger = null, array $properties = null, IdentifiersExtractorInterface $identifiersExtractor = null, NameConverterInterface $nameConverter = null)
    {
        parent::__construct($managerRegistry, $requestStack, $iriConverter, $propertyAccessor, $logger, $properties, $identifiersExtractor, $nameConverter);
    }

    public function getDescription(string $resourceClass): array
    {
        $description = parent::getDescription($resourceClass);

        $properties = $this->getProperties();

        if (count($properties) > 2) {
            throw new \Exception('EnumSearchFilter - Multiple properties are not supported');
        }

        $property = array_key_first($properties);
        if ($property && !empty($description[$property])) {
            $description[$property]['property'] = sprintf('riveradmin_enum:%s', $this->serializeEnum($this->enum));
        }

        return $description;
    }

    private function serializeEnum(string $enumFQCN): string
    {
        return json_encode(call_user_func([$enumFQCN, 'toArray']));
    }
}
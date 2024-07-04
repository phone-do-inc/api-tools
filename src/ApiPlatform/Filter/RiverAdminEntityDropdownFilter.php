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
 * Example usage:
 *
 * #[ApiFilter(
 *      RiverAdminEntityDropdownFilter::class,
 *      properties: ['progresses.user'],
 *      arguments: [
 *          'endpoint' => '/api/visible_users',
 *          // A human-readable value to display in dropdown (object property from the dropdown list)
 *          'labelKey' => 'username',
 *          // Set it to true if you want to render component that loads options from a remote source as the user types: https://react-select.com/async
 *          // Set it to false if you have a static list
 *          'async' => false
 *          // An entity prefix (Iri) that API platform requires to send when using SearchFilter
 *          // Usually the Iri prefix is not different from the endpoint property.
 *          // But if the endpoint is custom (for example /api/visible_users), then iri prefix should still point to an entity (for example /api/users/)
 *          'iriPrefix' => '/api/users',
 *      ])
 * ]
 */
class RiverAdminEntityDropdownFilter extends SearchFilter
{
    public function __construct(
        private string $endpoint,
        private string $labelKey,
        private string $iriPrefix,
        ManagerRegistry $managerRegistry,
        ?RequestStack $requestStack,
        IriConverterInterface $iriConverter,
        PropertyAccessorInterface $propertyAccessor = null,
        LoggerInterface $logger = null,
        array $properties = null,
        IdentifiersExtractorInterface $identifiersExtractor = null,
        NameConverterInterface $nameConverter = null,
        private bool $async = false,
    )
    {
        parent::__construct($managerRegistry, $requestStack, $iriConverter, $propertyAccessor, $logger, $properties, $identifiersExtractor, $nameConverter);
    }

    public function getDescription(string $resourceClass): array
    {
        $description = parent::getDescription($resourceClass);
        $properties = $this->getProperties();

        foreach ($properties as $property => $value) {
            if ($property && !empty($description[$property])) {
                $description[$property]['property'] = sprintf('riveradmin_entity_dropdown:%s', json_encode([
                    'endpoint' => $this->endpoint,
                    'labelKey' => $this->labelKey,
                    'async' => $this->async,
                    'iriPrefix' => $this->iriPrefix,
                ]));

            }
        }

        return $description;
    }
}

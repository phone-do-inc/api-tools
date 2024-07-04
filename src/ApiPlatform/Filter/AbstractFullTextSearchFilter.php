<?php

namespace Riverwaysoft\ApiTools\ApiPlatform\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Metadata;

abstract class AbstractFullTextSearchFilter extends AbstractContextAwareFilter
{
    abstract protected function configureQuery(QueryBuilder $queryBuilder, string $alias, mixed $value): void;

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
    ) : void {
        if ($property !== 'fullText') {
            return;
        }
        if (empty($value)) {
            return;
        }
        $alias = $queryBuilder->getRootAliases()[0];
        $this->configureQuery($queryBuilder, $alias, $value);
    }


    public function getDescription(string $resourceClass): array
    {
        $description = [];
        $description['fullText'] = [
            'property' => null,
            'type' => 'string',
            'required' => false,
            'swagger' => ['description' => 'Find collection by main properties'],
        ];

        return $description;
    }
}

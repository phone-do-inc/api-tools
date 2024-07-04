<?php

declare(strict_types=1);

namespace Riverwaysoft\ApiTools\ApiPlatform\Filter;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

class RiverAdminSearchFilter extends SearchFilter
{
    public function getDescription(string $resourceClass): array
    {
        $description = parent::getDescription($resourceClass);
        $properties = $this->getProperties();

        foreach ($properties as $property => $value) {
            if ($property && !empty($description[$property])) {
                $description[$property]['property'] = sprintf('riveradmin_input:%s', $property);
            }
        }

        return $description;
    }
}

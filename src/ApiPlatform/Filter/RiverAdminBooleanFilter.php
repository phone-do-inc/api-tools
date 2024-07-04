<?php

namespace Riverwaysoft\ApiTools\ApiPlatform\Filter;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

class RiverAdminBooleanFilter extends SearchFilter
{
    public function getDescription(string $resourceClass): array
    {
        $description = parent::getDescription($resourceClass);
        $properties = $this->getProperties();

        foreach ($properties as $property => $value) {
            if ($property && !empty($description[$property])) {
                $description[$property]['property'] = sprintf('riveradmin_bool[%s]', $property);
            }
        }

        return $description;
    }
}

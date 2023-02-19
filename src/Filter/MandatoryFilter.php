<?php

namespace App\Filter;

use ApiPlatform\Core\Api\FilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

class MandatoryFilter implements FilterInterface
{
    public function filter(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, $resourceClass, $operationName = null)
    {

    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'raceMaster' => [
                'property' => 'raceMaster',
                'type' => 'entity',
                'required' => true,
                'description' => 'Filter entities by raceMaster'
            ]
        ];
    }

    public function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName($property);
        $queryBuilder
            ->andWhere(sprintf('%s.%s = :%s', $rootAlias, $property, $parameterName))
            ->setParameter($parameterName, $value);
    }
}

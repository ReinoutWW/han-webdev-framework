<?php

namespace App\Repository\Filters;

class SearchFilters {
    private const FILTER_PREFIX = "filter";

    public function __construct(
        private array $filters = []
    ) { }

    public function hasFilter(string $key) {
        return \array_key_exists($key, $this->filters);
    }

    public function addFilter(string $key, $value) {
        $this->filters[$key] = $value;
    }

    public function getFilter(string $key) {
        return $this->filters[$key] ?? null;
    }

    public function getFilters(): array {
        return $this->filters;
    }

    /**
     * Parse an array of filters to a SearchFilters object
     * Example filter: filter_keyName=value, will be parsed to ['keyName' => 'value']
     * Allowed filters are optional, if provided only filters that are in the allowedFilters array will be parsed
     */
    public static function parseArrayToFilters(array $filters, array $allowedFilters = null): self {
        // All filters are like: filter_KeyName
        // Take all filters from the array and add them to the SearchFilters object
        $parsedFilters = [];
        foreach ($filters as $filter => $value) {
            if(!self::isStringFilter($filter) || !$value) {
                continue;
            }

            $filterName = self::getFilterName($filter);

            if($allowedFilters) {
                if(!in_array($filterName, $allowedFilters)) {
                    continue;
                }
            } 

            $parsedFilters[$filterName] = self::parseStringToFilterValue($value);
        }

        return new self($parsedFilters);
    }

    private static function parseStringToFilterValue(string $value): mixed {
        // Check if string is in DateTime format, parse
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T([01][0-9]|2[0-3]):([0-5][0-9])$/", $value)) {
            return new \DateTimeImmutable($value);
        } else {
            return $value;
        }
    }

    private static function isStringFilter(string $key): bool {
        return explode("_", $key)[0] == self::FILTER_PREFIX;
    }

    public static function getFilterName(string $key): string {
        return explode("_", $key)[1];
    }
}
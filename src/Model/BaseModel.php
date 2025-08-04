<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model;

/**
 * Base model class for all models in the system
 */
class BaseModel
{
    /** @var array */
    protected array $data = [];

    /** @var array Model mapping configuration */
    protected array $modelMapping = [];

    /**
     * BaseModel constructor.
     * @param array|object $data
     */
    public function __construct($data = [])
    {
        $this->data = $this->convertToArray($data);
        $this->processNestedModels();
    }

    /**
     * Convert data to array recursively.
     */
    protected function convertToArray($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                // Eğer value bir array ise ve içinde array'ler varsa, düzleştir
                if (is_array($value)) {
                    $convertedValue = $this->convertToArray($value);
                    if (is_array($convertedValue)) {
                        // Düzleştirilmiş array'i ana seviyeye taşı
                        $result = array_merge($result, $convertedValue);
                    } else {
                        $result[$key] = $convertedValue;
                    }
                } else {
                    $result[$key] = $this->convertToArray($value);
                }
            }
            return $result;
        }

        if (is_object($data)) {
            return $this->convertToArray((array) $data);
        }

        return $data;
    }

    /**
     * Check if array is sequential (numeric keys starting from 0)
     */
    protected function isSequentialArray(array $array): bool
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Process nested models based on modelMapping configuration
     */
    protected function processNestedModels(): void
    {
        foreach ($this->modelMapping as $field => $modelClass) {
            if (isset($this->data[$field])) {
                $this->data[$field] = $this->createNestedModels($this->data[$field], $modelClass);
            }
        }
    }

    /**
     * Create nested model instances
     * @param mixed $data
     * @param string $modelClass
     * @return mixed
     */
    protected function createNestedModels($data, string $modelClass)
    {
        if (is_array($data)) {
            // Check if it's a sequential array (list of models)
            if (array_keys($data) === range(0, count($data) - 1)) {
                return array_map(function ($item) use ($modelClass) {
                    return new $modelClass($item);
                }, $data);
            } else {
                // Single model
                return new $modelClass($data);
            }
        }

        return $data;
    }

    /**
     * Magic getter method.
     */
    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Magic setter method.
     */
    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Check if a property exists.
     * @param string $name Property name to check
     * @return bool True if the property exists, false otherwise
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Convert the model to an array.
     */
    public function toArray(): array
    {
        $data = $this->data;

        // Convert nested models back to arrays
        foreach ($this->modelMapping as $field => $modelClass) {
            if (isset($data[$field])) {
                $data[$field] = $this->convertNestedModelsToArray($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Convert nested model instances back to arrays
     * @param mixed $data
     * @return mixed
     */
    protected function convertNestedModelsToArray($data)
    {
        if (is_array($data)) {
            return array_map(function ($item) {
                if ($item instanceof BaseModel) {
                    return $item->toArray();
                }
                return $item;
            }, $data);
        }

        if ($data instanceof BaseModel) {
            return $data->toArray();
        }

        return $data;
    }

    /**
     * Add a nested model to a field
     * @param string $field
     * @param BaseModel $model
     */
    public function addNestedModel(string $field, BaseModel $model): void
    {
        if (!isset($this->data[$field])) {
            $this->data[$field] = [];
        }

        if (is_array($this->data[$field])) {
            $this->data[$field][] = $model;
        } else {
            $this->data[$field] = [$model];
        }
    }

    /**
     * Get nested models for a field
     * @param string $field
     * @return array
     */
    public function getNestedModels(string $field): array
    {
        $data = $this->data[$field] ?? [];
        
        if (is_array($data)) {
            return array_filter($data, function ($item) {
                return $item instanceof BaseModel;
            });
        }

        return [];
    }

    /**
     * Get count of nested models for a field
     * @param string $field
     * @return int
     */
    public function getNestedModelCount(string $field): int
    {
        return count($this->getNestedModels($field));
    }
} 
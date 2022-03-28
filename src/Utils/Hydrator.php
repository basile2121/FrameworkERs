<?php
declare(strict_types=1);
namespace App\Utils;

use App\Utils\Attribute\EntityParameter;
use DateTime;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Hydrator
{
    protected ?ReflectionClass $reflector = null;

    /**
     * @throws Exception
     */
    private function hydrateDate(string $value): DateTime
    {
        return new DateTime($value);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function hydrateAll(array $data, string $classStrName): array
    {
        $models = [];
        $arguments = [];
        $arguments = $this->getAttributes(new $classStrName());
        foreach ($data as $d){
            $object = new $classStrName();
            array_push($models, $this->hydrate($d, $object, $arguments));
        }
        return $models;
    }

    /**
     * @throws ReflectionException
     */
    private function getAttributes($model){
        $argumentsList = [];
        if ($this->reflector === null) {
            $this->reflector = new ReflectionClass($model);
        }

        foreach ($this->reflector->getProperties() as $property){
            foreach ($property->getAttributes(EntityParameter::class) as $attribute ){
                array_push($argumentsList, $attribute->getArguments());
            }
        }
        $this->reflector = null;
        return $argumentsList;
    }


    /**
     * @throws Exception
     */
    public function hydrate(array $data, object $model, array $arguments): object
    {
        foreach ($data as $key => $value) {
            foreach ($arguments as $argument){
                if (isset($argument[2]) && $key === $argument[2]) {
                    if ($argument[1] === 'DateTime') {
                        $value = $this->hydrateDate($value);
                    }
                    $methodName = 'set' . ucfirst($argument[0]);
                    $model->{$methodName}($value);
                    break;
                }
                if ($key === $argument[0]) {
                    if ($argument[1] === 'DateTime') {
                        $value = $this->hydrateDate($value);
                    }
                    $methodName = 'set' . ucfirst($key);
                    $model->{$methodName}($value);
                    break;
                }
            }
        }
        return $model;
    }

    public function extract(object $model): array
    {
        return get_object_vars($model);
    }
}
<?php
declare(strict_types=1);
namespace App\Utils;

use App\DependencyInjection\Container;
use App\Utils\Attribute\EntityParameter;
use DateTime;
use Exception;
use ReflectionClass;
use ReflectionException;

class Hydrator
{
    private Container $container;
    protected ?ReflectionClass $reflector = null;

    public function __construct(Container $container){
        $this->container = $container;
    }

    /**
     * Hydratation d'une string en date
     * @throws Exception
     */
    private function hydrateDate(string $value): DateTime
    {
        return new DateTime($value);
    }

    /**
     * Hydradation de toute les données du tableau
     * @throws ReflectionException
     * @throws Exception
     */
    public function hydrateAll(array $data, string $classStrName): array
    {
        $models = [];
        foreach ($data as $d){
            $object = new $classStrName();
            array_push($models, $this->hydrate($d, $object, $this->getParameters($object)));
        }
        return $models;
    }

    /**
     * Recuperation de l'ensemble des parametres ayant un attribut de type EntityParameter dans le model
     * @throws ReflectionException
     */
    public function getParameters($model): array
    {
        $parametersList = [];
        // Initialisation Reflection class en fonction du model
        if ($this->reflector === null) {
            $this->reflector = new ReflectionClass($model);
        }

        foreach ($this->reflector->getProperties() as $property){
            foreach ($property->getAttributes(EntityParameter::class) as $attribute ){
                array_push($parametersList, $attribute->getArguments());
            }
        }
        $this->reflector = null;
        return $parametersList;
    }


    /**
     * @throws Exception
     */
    public function hydrate(array $data, object $model, array $parameters): object
    {
        foreach ($data as $key => $value) {
            foreach ($parameters as $parameter){
                // On verifie si un nom d'entity est definis dans nos parametres et qu'une key correspond
                if(isset($parameter[3]) && $key === $parameter[2]) {
                    // On verifie son type
                    if ($parameter[1] === 'Object'){
                        // Recuperation de son repository pour recuperer l'entity demander en parametre
                        $objectDatas = $this->container->get($parameter[4])->selectOneById($value);

                        // Attribution de la valeur a notre model
                        $methodName = 'set' . ucfirst($parameter[0]);
                        $model->{$methodName}($objectDatas);
                        break;
                    }
                }
                if (isset($parameter[2]) && $key === $parameter[2]) {
                    if ($parameter[1] === 'DateTime') {
                        $value = $this->hydrateDate($value);
                    }
                    $methodName = 'set' . ucfirst($parameter[0]);
                    $model->{$methodName}($value);
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
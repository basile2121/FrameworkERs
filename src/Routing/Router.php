<?php

namespace App\Routing;

use App\Routing\Attribute\Route as RouteAttribute;
use App\Utils\Filesystem;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionMethod;

class Router
{
  /** @var Route[] */
  private $routes = [];
  private ContainerInterface $container;
  private ArgumentResolver $argumentResolver;
  private const CONTROLLERS_NAMESPACE = "App\\Controller\\";
  private const CONTROLLERS_DIR = __DIR__ . "/../Controller";

  public function __construct(
    ContainerInterface $container,
    ArgumentResolver $argumentResolver
  ) {
    $this->container = $container;
    $this->argumentResolver = $argumentResolver;
  }

  /**
   * Add a route into the router internal array
   *
   * @param string $name
   * @param string $url
   * @param string $httpMethod
   * @param string $controller Controller class
   * @param string $method
   * @return self
   */
  public function addRoute(Route $route): self
  {
    $this->routes[] = $route;

    return $this;
  }

  /**
   * Executes a route based on provided URI and HTTP method.
   *
   * @param string $uri
   * @param string $httpMethod
   * @return void
   * @throws RouteNotFoundException
   */
  public function execute(string $uri, string $httpMethod)
  {
    $route = $this->getRoute($uri, $httpMethod);

    if ($route === null) {
      throw new RouteNotFoundException();
    }
    
    
    $controllerName = $route->getController();
    $constructorParams = $this->getMethodServiceParams($controllerName, '__construct');
    $controller = new $controllerName(...$constructorParams);
    
    $method = $route->getMethod();
    $servicesParams = $this->getMethodServiceParams($controllerName, $method);
    $getParams = $route->getGetParams();
    

    call_user_func_array(
      [$controller, $method],
      array_merge($servicesParams, $getParams)
    );
  }

    /**
     * Get a route. Returns null if not found
     *
     * @param string $uri
     * @param string $httpMethod
     * @return Route|null
     */
    public function getRoute(string $uri, string $httpMethod): ?Route
    {
        $uri = explode('?' , $uri);
        foreach ($this->routes as $route) {
            if ($this->argumentResolver->match($uri[0], $route) && $route->getHttpMethod() === $httpMethod) {
                $params = $this->argumentResolver->getGetParams($uri[0], $route);

                $route->setGetParams($params);
                return $route;
            }
        }

        return null;
    }

  /**
   * Resolve method's parameters from the service container
   *
   * @param string $controller name of controller
   * @param string $method name of method
   * @return array
   */
  private function getMethodServiceParams(string $controller, string $method): array
  {
    $methodInfos = new ReflectionMethod($controller . '::' . $method);
    $methodParameters = $methodInfos->getParameters();

    $params = [];

    foreach ($methodParameters as $param) {
      $paramName = $param->getName();
      $paramType = $param->getType()->getName();

      if ($this->container->has($paramType)) {
        $params[$paramName] = $this->container->get($paramType);
      }
    }

    return $params;
  }

  public function registerRoutes(): void
  {
    $classNames = Filesystem::getClassNames(self::CONTROLLERS_DIR);

    foreach ($classNames as $class) {
      $this->registerClassRoutes($class);
    }
  }

  public function registerClassRoutes(string $className): void
  {
    $fqcn = self::CONTROLLERS_NAMESPACE . $className;
    $reflection = new ReflectionClass($fqcn);

    if ($reflection->isAbstract()) {
      return;
    }

    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

    foreach ($methods as $method) {
      $attributes = $method->getAttributes(RouteAttribute::class);

      foreach ($attributes as $attribute) {
        /** @var RouteAttribute */
        $route = $attribute->newInstance();

        $this->addRoute(new Route(
          $route->getPath(),
          $fqcn,
          $method->getName(),
          $route->getHttpMethod(),
          $route->getName()
        ));
      }
    }
  }
}

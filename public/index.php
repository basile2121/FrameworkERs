<?php

// Inclut l'autoloader généré par Composer
require_once __DIR__ . "/../vendor/autoload.php";

if (
  php_sapi_name() !== 'cli' &&
  preg_match('/\.(?:png|jpg|jpeg|gif|ico)$/', $_SERVER['REQUEST_URI'])
) {
  return false;
}

use App\Config\PdoConnection;
use App\Config\TwigEnvironment;
use App\DependencyInjection\Container;
use App\Repository\AdressesRepository;
use App\Repository\AppartientRepository;
use App\Repository\CategoriesRepository;
use App\Repository\EcolesRepository;
use App\Repository\EvenementsRepository;
use App\Repository\MediasRepository;
use App\Repository\ParticipeRepository;
use App\Repository\PromotionsRepository;
use App\Repository\RolesRepository;
use App\Repository\StatutsRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\ArgumentResolver;
use App\Routing\RouteNotFoundException;
use App\Routing\Router;
use App\Session\Session;
use App\Utils\Hydrator;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

// Env vars - Possibilité d'utiliser le pattern Adapter
// Pour pouvoir varier les dépendances qu'on utilise
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

// Container
$container = new Container();
// PDO
$pdoConnection = new PdoConnection();
// Hydrator
$hydrator = new Hydrator($container);
$pdoConnection->init(); // Connexion à la BDD

// Repository
$utilisateursRepository = new UtilisateursRepository($pdoConnection->getPdoConnection(), $hydrator);
$statutsRepository = new StatutsRepository($pdoConnection->getPdoConnection(), $hydrator);
$rolesRepository = new RolesRepository($pdoConnection->getPdoConnection(), $hydrator);
$promotionsRepository = new PromotionsRepository($pdoConnection->getPdoConnection(), $hydrator);
$participeRepository = new ParticipeRepository($pdoConnection->getPdoConnection(), $hydrator);
$mediasRepository = new MediasRepository($pdoConnection->getPdoConnection(), $hydrator);
$evenementsRepository = new EvenementsRepository($pdoConnection->getPdoConnection(), $hydrator);
$ecolesRepository = new EcolesRepository($pdoConnection->getPdoConnection(), $hydrator);
$categoriesRepository = new CategoriesRepository($pdoConnection->getPdoConnection(), $hydrator);
$appartientRepository = new AppartientRepository($pdoConnection->getPdoConnection(), $hydrator);
$adressesRepository = new AdressesRepository($pdoConnection->getPdoConnection(), $hydrator);
//Session
$session = new Session();
$session->ensureStarted();

// Request
$request = Request::createFromGlobals();
$container->set(Request::class, $request);

// Twig - Vue
$twigEnvironment = new TwigEnvironment();
$twig = $twigEnvironment->init();
$twig->addGlobal('session_utilisateur_id', $session->get('id'));
$twig->addGlobal('session_utilisateur_nom', $session->get('nom'));
$twig->addGlobal('session_utilisateur_prenom', $session->get('prenom'));
$twig->addGlobal('global_categories', $categoriesRepository->selectAll());
$twig->addGlobal('path_logo', "http://localhost:8000/image");

// Service Container
$container->set(Environment::class, $twig);
$container->set(Session::class, $session);
$container->set(UtilisateursRepository::class, $utilisateursRepository);
$container->set(StatutsRepository::class, $statutsRepository);
$container->set(RolesRepository::class, $rolesRepository);
$container->set(ParticipeRepository::class, $participeRepository);
$container->set(MediasRepository::class, $mediasRepository);
$container->set(EvenementsRepository::class, $evenementsRepository);
$container->set(EcolesRepository::class, $ecolesRepository);
$container->set(CategoriesRepository::class, $categoriesRepository);
$container->set(AppartientRepository::class, $appartientRepository);
$container->set(AdressesRepository::class, $adressesRepository);
$container->set(PromotionsRepository::class, $promotionsRepository);


// Routage
$router = new Router($container, new ArgumentResolver());
$router->registerRoutes();

if (php_sapi_name() === 'cli') {
  return;
}

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
  $router->execute($requestUri, $requestMethod);
} catch (RouteNotFoundException $e) {
  http_response_code(404);
  echo $twig->render('utils/404.html.twig', ['title' => $e->getMessage()]);
}

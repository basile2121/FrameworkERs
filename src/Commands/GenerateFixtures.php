<?php

namespace App\Commands;

use App\DependencyInjection\Container;
use App\Entity\Adresses;
use App\Entity\Appartient;
use App\Entity\Categories;
use App\Entity\Ecoles;
use App\Entity\Evenements;
use App\Entity\Medias;
use App\Entity\Participe;
use App\Entity\Promotions;
use App\Entity\Roles;
use App\Entity\Statuts;
use App\Entity\Utilisateurs;
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
use Faker\Factory;
use Faker\Generator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-fixtures',
    description: 'Generate fake datas',
    hidden: false,
)]
class GenerateFixtures extends Command
{
    private Container $container;
    private Generator $faker;

    private const NB_UTILISATEURS = 10;
    private const NB_PROMOTION = 5;
    private const NB_ECOLES = 5;
    private const NB_PARTICIPES = 8;
    private const NB_EVENEMENTS = 8;
    private const NB_MEDIAS = 1;
    private const NB_ADRESSES = 4;
    private const NB_APPARTIENT = 3;

    private array $idArray = [
        'Utilisateurs' => [],
        'Promotions' => [],
        'Ecoles' => [],
        'Participes' => [],
        'Evenements' => [],
        'Medias' => [],
        'Adresses' => [],
        'Appartient' => [],
        'Categories' => [],
        'Roles' => [],
        'Statuts' => [],
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure(): void
    {
        // ...
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $outputStyle = new OutputFormatterStyle('green', '#fff', ['bold', 'blink']);
        $output->getFormatter()->setStyle('sucess', $outputStyle);

        $this->faker = $this->container->get(Factory::class);
        $tabRepos =  [
            AppartientRepository::class,
            ParticipeRepository::class,
            EvenementsRepository::class,
            UtilisateursRepository::class,
            PromotionsRepository::class,
            AdressesRepository::class,
            EcolesRepository::class,
            CategoriesRepository::class,
            RolesRepository::class,
            StatutsRepository::class,
        ];

        $output->writeln('<comment>Vidage des tables</comment>');
        $this->_deleteAll($tabRepos);

        // Chargement des données dans un ordre logique
        $output->writeln('<comment>Chargement des Status</comment>');
        $this->_loadStatuts();
        $output->writeln('<comment>Chargement des Roles</comment>');
        $this->_loadRoles();
        $output->writeln('<comment>Chargement des Categories</comment>');
        $this->_loadCategories();
        $output->writeln('<comment>Chargement des Medias</comment>');
        //$this->_loadMedias();
        $output->writeln('<comment>Chargement des Ecoles</comment>');
        $this->_loadEcoles();
        $output->writeln('<comment>Chargement des Adresses</comment>');
        $this->_loadAdresses();
        $output->writeln('<comment>Chargement des Promotions</comment>');
        $this->_loadPromotions();
        $output->writeln('<comment>Chargement des Utilisateurs</comment>');
        $this->_loadUtilisateurs();
        $output->writeln('<comment>Chargement des Evenements</comment>');
        $this->_loadEvenements();
        $output->writeln('<comment>Chargement Participe</comment>');
        $this->_loadParticipe();
        $output->writeln('<comment>Chargement Appartient</comment>');
        $this->_loadAppartient();

        $output->writeln('<sucess>Fin de la commande</sucess>');
        return Command::SUCCESS;
    }

    /**
     * Vidages de toutes les tables
     * @param array $tabRepos
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function _deleteAll(array $tabRepos){
        foreach ($tabRepos as $repo) {
            $this->container->get($repo)->deleteAll();
        }
    }

    /**
     * Chargement des utilisateurs
     * Fake données grace au bundle faker
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function _loadUtilisateurs(){
        // Creation de NB_UTILISATEURS utilisateurs
        for ($i = 0; $i < static::NB_UTILISATEURS; $i++) {
            $user = new Utilisateurs();
            $user->setNom($this->faker->firstName);
            $user->setPrenom($this->faker->lastName);
            $user->setDateNaissance($this->faker->dateTime);
            $user->setDateInscription($this->faker->dateTime);
            $user->setMail($this->faker->email);
            $user->setTelephone('0609213456');
            $user->setPassword($this->faker->password);
            // Generation d'un id aleatoire entre les bornes des id des tables
            $user->setIdPromotion($this->faker->numberBetween($this->idArray['Promotions'][0], $this->idArray['Promotions'][1]));
            $user->setIdRole($this->faker->numberBetween($this->idArray['Roles'][0], $this->idArray['Roles'][1]));

            $this->container->get(UtilisateursRepository::class)->save($user);
        }

        /**
         * Ajout des bornes des id utilisateurs qui sont déclarés dans la bdd
         * Le but est de savoir qu'elle id est utilisable par les autres tables pour faire des liasons
         */
        $utilisateursBDD = $this->container->get(UtilisateursRepository::class)->selectAll('id_utilisateur');
        // Premier et dernier id utilisateurs
        array_push($this->idArray['Utilisateurs'], $utilisateursBDD[0]->getIdUtilisateur(), end($utilisateursBDD)->getIdUtilisateur());
    }

    private function _loadStatuts()
    {
        $statutsLibelles = ['Statut 1', 'Statut 2', 'Statut 3'];
        foreach ($statutsLibelles as $libelle){
            $statuts = new Statuts();
            $statuts->setCouleurStatus($this->faker->hexColor);
            $statuts->setLibelleStatut($libelle);

            $this->container->get(StatutsRepository::class)->save($statuts);
        }

        // Ajout des id déclarés dans la bdd
        $statutsBDD = $this->container->get(StatutsRepository::class)->selectAll('id_statut');
        array_push($this->idArray['Statuts'], $statutsBDD[0]->getIdStatut(), end($statutsBDD)->getIdStatut());
    }

    private function _loadRoles()
    {
        $rolesLibelles = ['Role 1', 'Role 2', 'Role 3'];
        foreach ($rolesLibelles as $libelle){
            $role = new Roles();
            $role->setLibelleRole($libelle);

            $this->container->get(RolesRepository::class)->save($role);
        }

        // Ajout des id déclarés dans la bdd
        $rolesBDD = $this->container->get(RolesRepository::class)->selectAll('id_role');
        array_push($this->idArray['Roles'], $rolesBDD[0]->getIdRole(), end($rolesBDD)->getIdRole());
    }

    private function _loadPromotions(){
        for ($i = 0; $i < static::NB_PROMOTION; $i++) {
            $promotion = new Promotions();
            $promotion->setLibellePromotion($this->faker->languageCode);
            $promotion->setIdEcole($this->faker->numberBetween($this->idArray['Ecoles'][0], $this->idArray['Ecoles'][1]));

            $this->container->get(PromotionsRepository::class)->save($promotion);
        }

        // Ajout des id déclarés dans la bdd
        $promotionsBDD = $this->container->get(PromotionsRepository::class)->selectAll('id_promotion');
        array_push($this->idArray['Promotions'], $promotionsBDD[0]->getIdPromotion(), end($promotionsBDD)->getIdPromotion());
    }

    private function _loadParticipe(){
        for ($i = 0; $i < static::NB_PARTICIPES; $i++) {
            $participe = new Participe();
            $participe->setIdEvenement($this->faker->numberBetween($this->idArray['Evenements'][0], $this->idArray['Evenements'][1]));
            $participe->setIdUtilisateur($this->faker->numberBetween($this->idArray['Utilisateurs'][0], $this->idArray['Utilisateurs'][1]));

            $this->container->get(ParticipeRepository::class)->save($participe);
        }
        // Ajout des id déclarés dans la bdd
        $participesBDD = $this->container->get(ParticipeRepository::class)->selectAll('id');
        array_push($this->idArray['Participes'], $participesBDD[0]->getId(), end($participesBDD)->getId());
    }

    private function _loadMedias(){
        for ($i = 0; $i < static::NB_MEDIAS; $i++) {
            $media = new Medias();
            $media->setNom($this->faker->name);
            $media->setPath('test');
            $media->setType($this->faker->file);

            $this->container->get(MediasRepository::class)->save($media);
        }
        // Ajout des id déclarés dans la bdd
        $mediasBDD = $this->container->get(MediasRepository::class)->selectAll('id_media');
        array_push($this->idArray['Medias'], $mediasBDD[0]->getIdMedia(), end($mediasBDD)->getIdMedia());
    }

    private function _loadEvenements(){
        for ($i = 0; $i < static::NB_EVENEMENTS; $i++) {
            $evenement = new Evenements();
            $evenement->setTitre($this->faker->jobTitle);
            $evenement->setSousTitre($this->faker->text(20));
            $evenement->setDescription($this->faker->text(200));
            $evenement->setNbParticipantsMax($this->faker->numberBetween(0,2000));
            $evenement->setPrix($this->faker->randomFloat(2,1,2000));
            $evenement->setDate($this->faker->dateTime);
            $evenement->setCreatedAt($this->faker->dateTime);
            $evenement->setUpdatedAt($this->faker->dateTime);
            $evenement->setIdUtilisateur($this->faker->numberBetween($this->idArray['Utilisateurs'][0], $this->idArray['Utilisateurs'][1]));
            $evenement->setIdAdresse($this->faker->numberBetween($this->idArray['Adresses'][0], $this->idArray['Adresses'][1]));
            $evenement->setIdMedia($this->faker->numberBetween(1, 2));
            $evenement->setIdCategorie($this->faker->numberBetween($this->idArray['Categories'][0], $this->idArray['Categories'][1]));
            $evenement->setIdStatut($this->faker->numberBetween($this->idArray['Statuts'][0], $this->idArray['Statuts'][1]));

            $this->container->get(EvenementsRepository::class)->save($evenement);
        }
        // Ajout des id déclarés dans la bdd
        $evenementsBDD = $this->container->get(EvenementsRepository::class)->selectAll('id_evenement');
        array_push($this->idArray['Evenements'], $evenementsBDD[0]->getIdEvenement(), end($evenementsBDD)->getIdEvenement());
    }

    private function _loadEcoles(){
        for ($i = 0; $i < static::NB_ECOLES; $i++) {
            $ecole = new Ecoles();
            $ecole->setNomEcole('Ecole ' . $this->faker->name);

            $this->container->get(EcolesRepository::class)->save($ecole);
        }
        // Ajout des id déclarés dans la bdd
        $ecolesBDD = $this->container->get(EcolesRepository::class)->selectAll('id_ecole');
        array_push($this->idArray['Ecoles'], $ecolesBDD[0]->getIdEcole(), end($ecolesBDD)->getIdEcole());
    }

    private function _loadCategories()
    {
        $categoriesLibelles = ['Categorie 1', 'Categorie 2', 'Categorie 3'];
        foreach ($categoriesLibelles as $libelle){
            $categorie = new Categories();
            $categorie->setLibelleCategorie($libelle);

            $this->container->get(CategoriesRepository::class)->save($categorie);
        }
        // Ajout des id déclarés dans la bdd
        $categoriesBDD = $this->container->get(CategoriesRepository::class)->selectAll('id_categorie');
        array_push($this->idArray['Categories'], $categoriesBDD[0]->getIdCategorie(), end($categoriesBDD)->getIdCategorie());
    }

    private function _loadAppartient(){
        for ($i = 0; $i < static::NB_APPARTIENT; $i++) {
            $appartient = new Appartient();
            $appartient->setIdCategorie($this->faker->numberBetween($this->idArray['Categories'][0],  $this->idArray['Categories'][1]));
            $appartient->setIdEvenement($this->faker->numberBetween($this->idArray['Evenements'][0],  $this->idArray['Evenements'][1]));

            $this->container->get(AppartientRepository::class)->save($appartient);
        }
        // Ajout des id déclarés dans la bdd
        $appartientBDD = $this->container->get(AppartientRepository::class)->selectAll('id');
        array_push($this->idArray['Appartient'], $appartientBDD[0]->getId(), end($appartientBDD)->getId());
    }

    private function _loadAdresses(){
        for ($i = 0; $i < static::NB_ADRESSES; $i++) {
            $adresse = new Adresses();
            $adresse->setLibelleAdresse($this->faker->streetAddress);
            $adresse->setCoordonneeLongitude($this->faker->longitude);
            $adresse->setCoordonneLatitude($this->faker->latitude);
            $adresse->setVilleLibelle($this->faker->city);
            $adresse->setCpVille(strval($this->faker->numberBetween(10000, 55555)));

            $this->container->get(AdressesRepository::class)->save($adresse);
        }

        // Ajout des id déclarés dans la bdd
        $adresseBDD = $this->container->get(AdressesRepository::class)->selectAll('id_adresse');
        array_push($this->idArray['Adresses'], $adresseBDD[0]->getIdAdresse(), end($adresseBDD)->getIdAdresse());
    }
}

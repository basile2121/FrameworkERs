<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Medias
{
    #[EntityParameter('idMedia', 'int' , 'id_media')]
    private int $idMedia;

    #[EntityParameter('nom', 'string', 'nom')]
    private string $nom;

    #[EntityParameter('path', 'string', 'path')]
    private string $path;

    #[EntityParameter('type', 'string', 'type')]
    private string $type;

    /**
     * @return int
     */
    public function getIdMedia(): int
    {
        return $this->idMedia;
    }

    /**
     * @param int $idMedia
     */
    public function setIdMedia(int $idMedia): void
    {
        $this->idMedia = $idMedia;
    }

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

}

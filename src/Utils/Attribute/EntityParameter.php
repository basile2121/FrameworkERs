<?php

namespace App\Utils\Attribute;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EntityParameter {
    private string $name;
    private string $type;
    private string $nameBdd;
    private string $entityName;
    private string $repoName;

    public function __construct(string $name, string $type, string $nameBdd, string $entityName = null, string $repoName = null){
        $this->name = $name;
        $this->type = $type;
        $this->nameBdd = $nameBdd;
        $this->entityName = $entityName;
        $this->repoName = $repoName;
    }
}
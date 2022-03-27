<?php

namespace App\Utils\Attribute;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EntityParameter {
    private string $name;
    private string $type;
    private string $nameBdd;

    public function __construct(string $name, string $type, string $nameBdd){
        $this->name = $name;
        $this->type = $type;
        $this->nameBdd = $nameBdd;
    }
}
<?php

namespace App\Entity;

interface ConfigurationInterface
{
    public function getName(): ?string;

    public function setName(string $name);

    public function getValue(): ?string;

    public function setValue(?string $value);
}
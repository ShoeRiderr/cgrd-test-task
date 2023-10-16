<?php

namespace App\Handler\Entity;

interface IEntity
{
    public function getId(): int;
    public function setId(int $id): void;
}
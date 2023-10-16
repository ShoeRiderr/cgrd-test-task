<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Handler\Repository\Repository;
use PDO;

class UserRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function findOneByNameWithPassword(string $name)
    {
        $this->validateColumn('name');

        $dbh = $this->conn->prepare('SELECT ' . implode(',', $this->allCols) .  ' FROM ' . $this->table . ' WHERE name = :name');

        $dbh->bindValue(':name' , $name, PDO::PARAM_STR);

        $dbh->execute();

        return $dbh->fetch();
    }
}

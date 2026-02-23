<?php
namespace App\Repositories;

use PDO;
use App\Core\Config;

abstract class BaseRepository {
    protected PDO $pdo;
    protected string $prefix;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->prefix = Config::get('db.prefix') ?? '';
    }

    public function table(string $name): string {
        return $this->prefix . $name;
    }
}

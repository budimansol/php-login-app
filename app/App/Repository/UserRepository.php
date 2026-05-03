<?php

namespace Budimansol\PHP\MVC\App\Repository;

use Budimansol\PHP\MVC\App\Domain\User;
use PDO;

class UserRepository {
    private PDO $connection;
    
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }
    
    public function create(User $user): User{
        $statement =  $this->connection->prepare("INSERT INTO users (id, name, email, password) values (?,?,?,?)");
        $statement->execute([$user->id, $user->name, $user->email, $user->password]);
        return $user;
    }
    
    public function update(User $user): User {
        $statement = $this->connection->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        $statement-> execute([
            $user->name, $user->email, $user->password, $user->id
        ]);
        return $user;
    }
    
    public function getById(string $id): ?User {
        $statement = $this->connection->prepare("SELECT id, name, email, password FROM users WHERE id = ?");
        $statement->execute([$id]);
        
        try{
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->email = $row['email'];
                $user->password = $row['password'];

                return $user;
            } else {
                return null;
            }
        }finally{
            $statement->closeCursor();
        }
    }

    public function getByEmail(string $email): ?User
    {
        $statement = $this->connection->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $statement->execute([$email]);

        try {
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->email = $row['email'];
                $user->password = $row['password'];

                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }
    
    public function deleteAll(){
        $this->connection->exec("DELETE from users");
    }
    
}

?>
<?php

namespace Budimansol\PHP\MVC\App\Repository;

use Budimansol\PHP\MVC\App\Domain\Sessions;
use PDO;

class SessionsRepository {
    private PDO $connection;
    
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }
    
    public function create(Sessions $sessions) : Sessions {
        $statement = $this->connection->prepare("INSERT INTO sessions (id, user_id) VALUES (?,?)");
        $statement->execute([$sessions->id, $sessions->user_id]);
        return $sessions;
    }
    
    public function getById(string $id) : ?Sessions {
        $statement = $this->connection->prepare("SELECT id, user_id FROM sessions WHERE id = ?");
        $statement->execute([$id]);
        
        try {
            if($row = $statement->fetch()){
                $session = new Sessions();
                $session->id = $row['id'];
                $session->user_id = $row['user_id'];
                
                return $session;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }
    
    public function deleteById(string $id) : void {
        $statement = $this->connection->prepare("DELETE FROM sessions WHERE id = ?");
        $statement->execute([$id]);
    }
    
    public function deleteAll(): void {
        $this->connection->exec("DELETE FROM sessions");
    }
}

?>
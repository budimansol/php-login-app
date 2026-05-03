<?php

namespace Budimansol\PHP\MVC\App;

class View {
    public static function render(string $view, $model){
        require __DIR__ . '/../View/header.php';
        require __DIR__ . '/../View/' . $view . '.php';
        require __DIR__ . '/../View/footer.php';
    }
    
    public static function redirect(string $url){
        if(getenv("mode") != 'test'){
            header("Location: $url");
            exit();
        } else {
            echo "Location: $url";
        }
        
        
    }
}

?>
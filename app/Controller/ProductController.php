<?php

namespace Budimansol\PHP\MVC\Controller;

class ProductController{
    function categories(string $productId, string $categoryId): void {
        echo "Product ID $productId, Categories ID $categoryId";
    }
}

?>
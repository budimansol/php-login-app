<?php

namespace Budimansol\PHP\MVC\Middleware;

interface Middleware {
    function before(): void;
}

?>
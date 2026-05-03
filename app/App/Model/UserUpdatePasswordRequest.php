<?php

namespace Budimansol\PHP\MVC\App\Model;

class UserUpdatePasswordRequest {
    public ?string $id = null;
    public ?string $oldPassword = null;
    public ?string $newPassword = null;
}

?>
<?php
declare(strict_types=1);

namespace Gewaer\Domains\Lounges\Mappers\Dto;

class User
{
    public int $id;
    public string $displayname;
    public ?string $description = null;
    public int $roles_id = 0;
    public int $status = 0;
    public int $is_active = 0;
    public ?object $photo = null;
}

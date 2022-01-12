<?php
declare(strict_types=1);

namespace Gewaer\Domains\Rooms\Mappers\Dto;

class Room
{
    public int $id;
    public array $lounge;
    public int $users_id;
    public string $name;
    public ?string $description = null;
    public int $is_live = 0;
    public int $is_public = 0;
    public int $is_following = 0;
    public int $total_followers = 0;
    public array $files = [];
}

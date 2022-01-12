<?php
declare(strict_types=1);

namespace Gewaer\Domains\Lounges\Mappers\Dto;

class Lounge
{
    public int $id;
    public string $name;
    public ?string $description = null;
    public int $is_following = 0;
    public int $total_followers = 0;
    public int $users_id = 0;
    public array $reading_media = [];
    public array $files = [];
    public array $role = [];
    public array $topics = [];
    public array $media = [];
}

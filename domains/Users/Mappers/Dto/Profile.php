<?php
declare(strict_types=1);

namespace Gewaer\Domains\Users\Mappers\Dto;

class Profile
{
    public int $id;
    public ?string $uuid = null;
    public string $displayname;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $description = null;
    public string $email;
    public ?string $dbo = null;
    public int $is_following = 0;
    public int $total_followers = 0;
    public int $total_following = 0;
    public int $total_lounges = 0;
    public int $total_rooms = 0;
    public int $verify = 0;
    public int $welcome = 0;
    public array $files = [];
    public array $role = [];
    public $photo;
}

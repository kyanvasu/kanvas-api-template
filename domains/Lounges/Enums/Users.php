<?php
declare(strict_types=1);

namespace Gewaer\Domains\Lounges\Enums;

class Users
{
    public const ACTIVE = 1;
    public const INACTIVE = 2;
    public const INVITE = 0;

    public const ROLES_ADMIN = 1;
    public const ROLES_MODS = 2;
    public const ROLES_USERS = 3;
}

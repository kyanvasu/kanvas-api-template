<?php

declare(strict_types=1);

namespace Gewaer\Domains\Rooms\Mappers;

use Canvas\Contracts\Mapper\RelationshipTrait;
use Gewaer\Domains\Rooms\Enums\Users;
use Gewaer\Domains\Users\Mappers\Profile as MappersProfile;

class Profile extends MappersProfile
{
    use RelationshipTrait;

    /**
     * Undocumented function.
     *
     * @param Users $source
     * @param Profile $destination
     * @param array $context
     *
     * @return void
     */
    public function mapToObject($source, $destination, array $context = [])
    {
        $roles = [
            Users::ROLES_ADMIN => 'admins',
            Users::ROLES_MODS => 'mod',
            Users::ROLES_USERS => 'user',
        ];

        $roomUser = $source;
        $source = $source->user;
        $destination = parent::mapToObject($source, $destination, $context);
        $destination->role = [
            'id' => $roomUser->roles_id,
            'name' => $roles[$roomUser->roles_id]
        ];
        $destination->is_active = $roomUser->is_active;
        $destination->has_raised_hand = $roomUser->has_raised_hand;
        $destination->status = $roomUser->status;
        $destination->description = $roomUser->description;

        return $destination;
    }
}

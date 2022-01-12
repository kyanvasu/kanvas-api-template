<?php

declare(strict_types=1);

namespace Gewaer\Domains\Lounges\Mappers;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Canvas\Contracts\Mapper\RelationshipTrait;
use Gewaer\Domains\Lounges\Enums\Users;

class User extends CustomMapper
{
    use RelationshipTrait;

    /**
     * Undocumented function.
     *
     * @param User $source
     * @param Users $destination
     * @param array $context
     *
     * @return void
     */
    public function mapToObject($source, $destination, array $context = [])
    {
        $destination->id = $source->users_id;
        $destination->displayname = $source->user->displayname;
        $destination->description = $source->description;
        $destination->roles_id = $source->roles_id;
        $destination->status = $source->status;
        $destination->is_active = $source->is_active;
        $destination->status = (int) $source->status;
        $destination->photo = $source->user->getPhoto();
        $destination->role = [
            'id' => $source->roles_id,
            'name' => $source->roles_id === Users::ROLES_ADMIN ? 'admins' : 'mod'

        ];
        return $destination;
    }
}

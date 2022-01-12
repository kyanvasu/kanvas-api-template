<?php

declare(strict_types=1);

namespace Gewaer\Domains\Users\Mappers;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Canvas\Contracts\Mapper\RelationshipTrait;
use Phalcon\Di;

class Profile extends CustomMapper
{
    use RelationshipTrait;

    /**
     * Undocumented function.
     *
     * @param Users $user
     * @param Profile $profile
     * @param array $context
     *
     * @return void
     */
    public function mapToObject($user, $profile, array $context = [])
    {
        $profile->id = $user->getId();
        $profile->displayname = $user->displayname;
        $profile->files = $user->getFiles();
        $profile->uuid = $user->uuid;
        $profile->firstname = $user->firstname;
        $profile->lastname = $user->lastname;
        $profile->description = $user->description;
        $profile->email = '';
        $profile->welcome = $user->welcome;
        $profile->photo = $user->getPhoto();
        $profile->role = $user->getPermission() ? [
            'id' => $user->getPermission()->getId(),
            'name' => strtolower($user->getPermission()->roles->name),
        ] : [];

        $userData = Di::getDefault()->get('userData');

        //hide user info
        if ($userData->getId() === $user->getId()) {
            $profile->email = $user->email;
        }


        return $profile;
    }
}

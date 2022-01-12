<?php
declare(strict_types=1);

namespace Gewaer\Domains\Rooms;

use Baka\Contracts\Auth\UserInterface;
use Gewaer\Domains\Rooms\Enums\Users as EnumsUsers;
use Gewaer\Domains\Rooms\Models\Rooms;

class Room
{
    protected Rooms $room;

    /**
     * Constructor.
     *
     * @param Rooms $lounge
     */
    public function __construct(Rooms $room)
    {
        $this->room = $room;
    }

    /**
     * Can the given user modify this entity?
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function canEdit(UserInterface $user) : bool
    {
        return (bool) $this->room->countMembers(
            'roles_id in (' . EnumsUsers::ROLES_ADMIN . ',' . EnumsUsers::ROLES_MODS . ') AND users_id = ' . $user->getId()
        );
    }

    /**
     * Is Admin.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isAdmin(UserInterface $user) : bool
    {
        return (bool) $this->room->countMembers(
            'roles_id = ' . EnumsUsers::ROLES_ADMIN . ' AND users_id = ' . $user->getId()
        );
    }
}

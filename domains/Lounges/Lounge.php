<?php
declare(strict_types=1);

namespace Gewaer\Domains\Lounges;

use Baka\Contracts\Auth\UserInterface;
use Gewaer\Domains\Lounges\Enums\Users as EnumsUsers;
use Gewaer\Domains\Lounges\Models\Lounges;
use Gewaer\Domains\Lounges\Models\Users;
use Phalcon\Mvc\Model\Resultset;

class Lounge
{
    protected Lounges $lounge;

    /**
     * Constructor.
     *
     * @param Lounges $lounge
     */
    public function __construct(Lounges $lounge)
    {
        $this->lounge = $lounge;
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
        return (bool) $this->lounge->countMembers(
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
        return (bool) $this->lounge->countMembers(
            'roles_id = ' . EnumsUsers::ROLES_ADMIN . ' AND users_id = ' . $user->getId()
        );
    }

    /**
     * Given a result set of members from a lounge return the users id.
     *
     * @param Resultset $users
     *
     * @return array<int>
     */
    public static function getMembersList(Resultset $users) : array
    {
        if ($users->count() > 0) {
            return array_map(
                function ($user) {
                    return $user['users_id'];
                },
                $users->toArray()
            );
        }

        return [0];
    }
}

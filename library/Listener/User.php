<?php

declare(strict_types=1);

namespace Gewaer\Listener;

use Canvas\Listener\User as ListenerUser;
use Canvas\Models\Users;
use Phalcon\Events\Event;
use Phalcon\Security\Random;

class User extends ListenerUser
{
    /**
     *  Event to run after a user signs up.
     *
     * @param Event $event
     * @param Users $user
     * @param bool $isFirstSignup
     *
     * @return void
     */
    public function afterSignup(Event $event, Users $user, bool $isFirstSignup) : void
    {
        parent::afterSignup($event, $user, $isFirstSignup);

        $random = new Random();
        $user->set('activation_number', $random->base58(6));
    }
}

<?php

namespace Gewaer\Providers;

use Canvas\Listener\Company;
use Canvas\Listener\Notification;
use Canvas\Listener\Subscription;
use Canvas\Providers\EventsManagerProvider as CanvasEventsManagerProvider;
use Gewaer\Listener\User as ListenerUser;

class EventsManagerProvider extends CanvasEventsManagerProvider
{
    /**
     * List of the listeners use by the app.
     *
     * [
     *  'eventName' => 'className',
     *  'subscription' => Subscription::class,
     *  'user' => User::class,
     * ];
     *
     * @var array
     */
    protected $listeners = [
    ];

    protected $canvasListeners = [
        'subscription' => Subscription::class,
        'user' => ListenerUser::class,
        'company' => Company::class,
        'notification' => Notification::class,
    ];
}

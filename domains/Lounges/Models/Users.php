<?php
declare(strict_types=1);

namespace Gewaer\Domains\Lounges\Models;

use Baka\Contracts\Auth\UserInterface;
use Gewaer\Domains\Lounges\Enums\Users as EnumsUsers;
use Gewaer\Models\BaseModel;
use Gewaer\Models\Users as ModelsUsers;

class Users extends BaseModel
{
    public int $lounges_id;
    public int $users_id;
    public ?string $description = null;
    public int $roles_id;
    public int $is_active = 1;
    public int $status = 1;

    /**
     * Initialize method for model.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('lounges_users');

        $this->belongsTo(
            'lounges_id',
            Lounges::class,
            'id',
            [
                'alias' => 'lounges',
            ]
        );

        $this->belongsTo(
            'users_id',
            ModelsUsers::class,
            'id',
            [
                'alias' => 'user',
                'reusable' => true,
            ]
        );
    }

    /**
     * Validate fields.
     *
     * @return void
     */
    public function beforeValidation()
    {
        if ($this->status > EnumsUsers::INACTIVE) {
            $this->status = EnumsUsers::INVITE;
        }
    }

    /**
     * Get user lounge.
     *
     * @param UserInterface $user
     * @param Lounges $lounge
     *
     * @return self|null
     */
    public static function findFirstByUsersAndLounge(UserInterface $user, Lounges $lounge) : ?self
    {
        return self::findFirst([
            'conditions' => 'lounges_id = :lounges_id: AND users_id = :users_id: AND is_deleted = 0',
            'bind' => [
                'lounges_id' => $lounge->getId(),
                'users_id' => $user->getId()
            ]
        ]);
    }
}

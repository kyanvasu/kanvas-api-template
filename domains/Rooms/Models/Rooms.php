<?php
declare(strict_types=1);

namespace Gewaer\Domains\Rooms\Models;

use Canvas\Contracts\CustomFields\CustomFieldsTrait;
use Canvas\Contracts\FileSystemModelTrait;
use Gewaer\Domains\Lounges\Models\Lounges;
use Gewaer\Domains\Rooms\Enums\Users as EnumsUsers;
use Gewaer\Models\BaseModel;
use Kanvas\Packages\AppSearch\Contracts\SearchableModelsTrait;

class Rooms extends BaseModel
{
    use FileSystemModelTrait;
    use CustomFieldsTrait;
    use SearchableModelsTrait;

    public ?string $name = null;
    public int $users_id;
    public int $lounge_id;
    public ?string $description = null;
    public int $is_live = 1;
    public int $is_public = 1;

    /**
     * Initialize method for model.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('rooms');

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
            Users::class,
            'id',
            [
                'alias' => 'user',
            ]
        );

        $this->hasMany(
            'id',
            Users::class,
            'rooms_id',
            [
                'alias' => 'users',
                'params' => [
                    'conditions' => 'is_deleted = 0 AND roles_id = ' . EnumsUsers::ROLES_USERS
                ]
            ]
        );

        $this->hasMany(
            'id',
            Users::class,
            'rooms_id',
            [
                'alias' => 'admins',
                'params' => [
                    'conditions' => 'is_deleted = 0 AND roles_id = ' . EnumsUsers::ROLES_ADMIN
                ]
            ]
        );

        $this->hasMany(
            'id',
            Users::class,
            'rooms_id',
            [
                'alias' => 'mods',
                'params' => [
                    'conditions' => 'is_deleted = 0 AND roles_id in ' . EnumsUsers::ROLES_MODS
                ]
            ]
        );

        $this->hasMany(
            'id',
            Users::class,
            'rooms_id',
            [
                'alias' => 'members',
                'params' => [
                    'conditions' => 'is_deleted = 0 AND roles_id in (' . EnumsUsers::ROLES_ADMIN . ',' . EnumsUsers::ROLES_MODS . ')'
                ]
            ]
        );
    }

    /**
     * After save.
     *
     * @return void
     */
    public function afterSave()
    {
        $this->associateFileSystem();
    }

    /**
     * After create.
     *
     * @return void
     */
    public function afterCreate()
    {
        $loungeAdmin = new Users();
        $loungeAdmin->rooms_id = $this->getId();
        $loungeAdmin->users_id = $this->users_id;
        $loungeAdmin->roles_id = EnumsUsers::ROLES_ADMIN;
        $loungeAdmin->status = EnumsUsers::ACTIVE;
        $loungeAdmin->is_active = 1;
        $loungeAdmin->has_raised_hand = 0;
        $loungeAdmin->saveOrFail();
    }
}

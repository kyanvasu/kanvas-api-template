<?php
declare(strict_types=1);

namespace Gewaer\Domains\Lounges\Models;

use Canvas\Contracts\CustomFields\CustomFieldsTrait;
use Canvas\Contracts\FileSystemModelTrait;
use Gewaer\Domains\Lounges\Enums\Users as EnumUsers;
use Gewaer\Models\BaseModel;
use Kanvas\Packages\AppSearch\Contracts\SearchableModelsTrait;

class Lounges extends BaseModel
{
    use FileSystemModelTrait;
    use CustomFieldsTrait;
    use SearchableModelsTrait;

    public ?string $name = null;
    public int $users_id;
    public ?string $description = null;
    public int $is_public = 1;

    /**
     * Initialize method for model.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('lounges');

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
            'lounges_id',
            [
                'alias' => 'users',
                'params' => [
                    'conditions' => 'is_deleted = 0 AND roles_id = ' . EnumUsers::ROLES_USERS
                ]
            ]
        );

        $this->hasMany(
            'id',
            Users::class,
            'lounges_id',
            [
                'alias' => 'admins',
                'params' => [
                    'conditions' => 'is_deleted = 0 AND roles_id = ' . EnumUsers::ROLES_ADMIN
                ]
            ]
        );

        $this->hasMany(
            'id',
            Users::class,
            'lounges_id',
            [
                'alias' => 'mods',
                'params' => [
                    'conditions' => 'is_deleted = 0 AND roles_id in ' . EnumUsers::ROLES_MODS
                ]
            ]
        );

        $this->hasMany(
            'id',
            Users::class,
            'lounges_id',
            [
                'alias' => 'members',
                'params' => [
                    'conditions' => 'is_deleted = 0 AND roles_id in (' . EnumUsers::ROLES_ADMIN . ',' . EnumUsers::ROLES_MODS . ')'
                ]
            ]
        );
    }

    /**
     * After create.
     *
     * @return void
     */
    public function afterCreate()
    {
        $loungeAdmin = new Users();
        $loungeAdmin->lounges_id = $this->getId();
        $loungeAdmin->users_id = $this->users_id;
        $loungeAdmin->roles_id = EnumUsers::ROLES_ADMIN;
        $loungeAdmin->is_active = 1;
        $loungeAdmin->saveOrFail();
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
}

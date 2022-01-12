<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers\Lounges;

use Canvas\Contracts\Controllers\ProcessOutputMapperTrait;
use Canvas\Http\Exception\NotFoundException;
use Gewaer\Api\Controllers\BaseController;
use Gewaer\Domains\Lounges\Mappers\Dto\User as DtoUser;
use Gewaer\Domains\Lounges\Mappers\User;
use Gewaer\Domains\Lounges\Models\Lounges;
use Gewaer\Domains\Lounges\Models\Users;

class UsersController extends BaseController
{
    use ProcessOutputMapperTrait;
    protected int $parentId = 0;

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = [
    ];

    /**
     * fields we accept to update.
     *
     * @var array
     */
    protected $updateFields = [
    ];

    /**
     * set objects.
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->softDelete = 1;
        $this->model = new Users();
        $this->dto = DtoUser::class;
        $this->dtoMapper = new User();

        $this->parentId = (int) $this->router->getParams()['loungeId'];

        if (!$this->parentId) {
            throw new NotFoundException('Lounge not found');
        }

        $pipeline = Lounges::findFirstOrFail([
            'conditions' => 'id = :id: AND is_deleted = 0',
            'bind' => [
                'id' => $this->parentId,
            ]
        ]);

        $this->model->lounges_id = $this->parentId;

        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['lounges_id', ':', $this->parentId],
        ];
    }
}

<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers\Rooms;

use Baka\Contracts\Http\Api\CrudBehaviorRelationshipsTrait;
use Baka\Http\Exception\NotFoundException;
use Baka\Http\Exception\UnauthorizedException;
use Canvas\Contracts\Controllers\ProcessOutputMapperTrait;
use Gewaer\Api\Controllers\BaseController;
use Gewaer\Domains\Lounges\Models\Lounges;
use Gewaer\Domains\Rooms\Mappers\Dto\Room;
use Gewaer\Domains\Rooms\Mappers\Rooms as MappersRooms;
use Gewaer\Domains\Rooms\Models\Rooms;
use Gewaer\Domains\Rooms\Room as RoomsRoom;
use Phalcon\Http\RequestInterface;
use Phalcon\Mvc\ModelInterface;

class RoomsController extends BaseController
{
    use ProcessOutputMapperTrait, CrudBehaviorRelationshipsTrait {
        ProcessOutputMapperTrait::processOutput  insteadof  CrudBehaviorRelationshipsTrait;
    }

    //protected int $parentId = 0;

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = [
        'name',
        'description',
        'is_public',
        'is_live',
    ];

    /**
     * fields we accept to update.
     *
     * @var array
     */
    protected $updateFields = [
        'name',
        'description',
        'is_public',
        'is_live',
    ];

    /**
     * set objects.
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Rooms();
        $this->softDelete = 1;
        $this->dto = Room::class;
        $this->dtoMapper = new MappersRooms();

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
            ['is_public', ':', 1],
            ['lounges_id', ':', $this->parentId],
        ];
    }

    /**
     * Process the create request and records the object.
     *
     * @return ModelInterface
     *
     * @throws Exception
     */
    protected function processCreate(RequestInterface $request) : ModelInterface
    {
        $request->validate([
            'name' => 'string|min:3|max:255|required',
            'description' => 'required|string',
        ]);

        $this->model->users_id = $this->userData->getId();

        return parent::processCreate($request);
    }

    /**
     * Process the update request and return the object.
     *
     * @param RequestInterface $request
     * @param ModelInterface $record
     *
     * @throws UnauthorizedException
     *
     * @return ModelInterface
     */
    protected function processEdit(RequestInterface $request, ModelInterface $record) : ModelInterface
    {
        $request->validate([
            'name' => 'string|min:3|max:255|required',
            'description' => 'required',
        ]);

        $room = new RoomsRoom($record);

        if (!$room->canEdit($this->userData)) {
            throw new UnauthorizedException('Unauthorized Action for the current user');
        }

        return parent::processEdit($request, $record);
    }
}

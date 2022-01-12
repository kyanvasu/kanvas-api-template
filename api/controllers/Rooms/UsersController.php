<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers\Rooms;

use Baka\Http\Exception\NotFoundException;
use Baka\Http\Exception\UnauthorizedException;
use Canvas\Http\Response;
use Gewaer\Domains\Lounges\Models\Lounges;
use Gewaer\Domains\Rooms\Enums\Users as EnumsUsers;
use Gewaer\Domains\Rooms\Mappers\Profile as RoomsMappersProfile;
use Gewaer\Domains\Rooms\Models\Rooms;
use Gewaer\Domains\Rooms\Models\Users as ModelsUsers;
use Gewaer\Domains\Rooms\Room;
use Gewaer\Domains\Users\Mappers\Dto\Profile;
use Gewaer\Models\Users;
use RuntimeException;

class UsersController extends RoomsController
{
    protected int $roomsId;

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
        $this->model = new ModelsUsers();
        $this->softDelete = 1;
        $this->dto = Profile::class;
        $this->dtoMapper = new RoomsMappersProfile();

        $this->parentId = (int) $this->router->getParams()['loungeId'];
        $this->roomsId = (int) $this->router->getParams()['id'];

        if (!$this->parentId) {
            throw new NotFoundException('Lounge not found');
        }

        $lounge = Lounges::findFirstOrFail([
            'conditions' => 'id = :id: AND is_deleted = 0',
            'bind' => [
                'id' => $this->parentId,
            ]
        ]);

        $lounge = Rooms::findFirstOrFail([
            'conditions' => 'id = :id: AND is_deleted = 0',
            'bind' => [
                'id' => $this->roomsId,
            ]
        ]);

        $this->model->rooms_id = $this->roomsId;

        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['rooms_id', ':', $this->roomsId],
        ];
    }

    /**
     * Add a new user to the lounge.
     *
     * @param int $loungeId
     *
     * @return Response
     */
    public function addUser(int $loungeId, int $id) : Response
    {
        $this->request->validate([
            'users_id' => 'int|required',
            'roles_id' => 'int|required',
            'is_active' => 'int|required|',
        ]);
        $request = $this->request->getPostData();

        $model = Rooms::findFirstOrFail($id);
        $user = Users::findFirstOrFail($request['users_id']);

        $room = new Room($model);

        if (!$room->canEdit($this->userData)) {
            throw new UnauthorizedException('Unauthorized Action for the current user');
        }

        if (ModelsUsers::findFirstByUsersAndRoom($user, $model)) {
            throw new RuntimeException('User already exist');
        }

        if (!$room->isAdmin($user) && $request['roles_id'] == EnumsUsers::ROLES_ADMIN) {
            throw new UnauthorizedException('Unauthorized Action for the current user');
        }

        $userLounge = new ModelsUsers();
        $userLounge->rooms_id = $model->getId();
        $userLounge->users_id = $user->getId();
        $userLounge->roles_id = $request['roles_id'] ?? EnumsUsers::ROLES_USERS;
        $userLounge->is_active = $request['is_active'] ?? EnumsUsers::ACTIVE;
        $userLounge->status = $request['status'] ?? EnumsUsers::ACTIVE;
        $userLounge->saveOrFail();

        return $this->response($this->processOutput($userLounge));
    }

    /**
     * Add a new user to the lounge.
     *
     * @param int $loungeId
     *
     * @return Response
     */
    public function removeUser(int $loungeId, int $roomsId, int $userId) : Response
    {
        $model = Rooms::findFirstOrFail($roomsId);

        $user = Users::findFirstOrFail($userId);

        $room = new Room($model);

        if ($room->isAdmin($user)) {
            throw new UnauthorizedException('Unauthorized Admin cant be removed');
        }

        if (!$room->canEdit($this->userData) && $this->userData->getId() !== $user->getId()) {
            throw new UnauthorizedException('Unauthorized Action for the current user');
        }

        if (!ModelsUsers::findFirstByUsersAndRoom($user, $model)) {
            throw new RuntimeException('User already doesn\'t exist');
        }

        $userRoom = ModelsUsers::findFirst([
            'conditions' => 'rooms_id = :rooms_id: AND users_id = :users_id:',
            'bind' => [
                'rooms_id' => $model->getId(),
                'users_id' => $user->getId()
            ]
        ]);
        $userRoom->is_deleted = 1;
        $userRoom->saveOrFail();

        return $this->response('User Removed');
    }
}

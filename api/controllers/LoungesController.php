<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Baka\Http\Exception\NotFoundException;
use Baka\Http\Exception\UnauthorizedException;
use Canvas\Contracts\Controllers\ProcessOutputMapperTrait;
use Canvas\Http\Response;
use Gewaer\Domains\Lounges\Enums\Users as EnumsUsers;
use Gewaer\Domains\Lounges\Lounge;
use Gewaer\Domains\Lounges\Mappers\Dto\Lounge as LoungeDto;
use Gewaer\Domains\Lounges\Mappers\Lounge as MappersLounge;
use Gewaer\Domains\Lounges\Models\Lounges;
use Gewaer\Domains\Lounges\Models\Users as UserLounge;
use Gewaer\Domains\Users\Mappers\Dto\Profile;
use Gewaer\Domains\Users\Mappers\Profile as ProfileMapper;
use Gewaer\Models\Users;
use Phalcon\Http\RequestInterface;
use Phalcon\Mvc\ModelInterface;

class LoungesController extends BaseController
{
    use ProcessOutputMapperTrait;

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = [
        'name',
        'description',
        'is_public',
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
    ];

    /**
     * set objects.
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Lounges();
        $this->softDelete = 1;
        $this->dto = LoungeDto::class;
        $this->dtoMapper = new MappersLounge();

        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['is_public', ':', 1],
        ];
    }

    /**
     * List all the followers for this current lounge.
     *
     * @return Response
     */
    public function followers(int $id) : Response
    {
        $lounge = Lounges::findFirstOrFail($id);

        $this->model = new Users();
        $this->dto = Profile::class;
        $this->dtoMapper = new ProfileMapper();

        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['id', ':', implode('|', Lounge::getMembersList($lounge->getUsers(['columns' => 'users_id'])))],
        ];

        return $this->index();
    }

    /**
     * List all the current members of this lounge.
     *
     * @return Response
     */
    public function members(int $id) : Response
    {
        $lounge = Lounges::findFirstOrFail($id);

        $this->model = new Users();
        $this->dto = Profile::class;
        $this->dtoMapper = new ProfileMapper();

        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['id', ':', implode('|', Lounge::getMembersList($lounge->getMembers(['columns' => 'users_id'])))],
        ];

        return $this->index();
    }

    /**
     * Process creation.
     *
     * @param RequestInterface $request
     *
     * @return ModelInterface
     */
    protected function processCreate(RequestInterface $request) : ModelInterface
    {
        $request->validate([
            'name' => 'string|min:3|max:255|required',
            'description' => 'required|string',
        ]);

        $this->model->users_id = $this->userData->getId();
        $request = $this->processInput($request->getPostData());

        $this->model->saveOrFail($request, $this->createFields);
        $this->model->set('books_id', $request['book_id']);

        return $this->model;
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

        $lounge = new Lounge($record);

        if (!$lounge->canEdit($this->userData)) {
            throw new UnauthorizedException('Unauthorized Action for the current user');
        }

        return parent::processEdit($request, $record);
    }

    /**
     * Update user information.
     *
     * @param int $loungeId
     * @param int $userId
     *
     * @return Response
     */
    public function updateUser(int $loungeId, int $userId) : Response
    {
        $model = Lounges::findFirstOrFail($loungeId);
        $user = Users::findFirstOrFail($userId);

        $lounge = new Lounge($model);

        if (!$lounge->canEdit($this->userData)) {
            throw new UnauthorizedException('Unauthorized Action for the current user');
        }

        if (!$userLounge = UserLounge::findFirstByUsersAndLounge($user, $model)) {
            throw new NotFoundException('User not a member of this lounge');
        }

        $this->request->validate([
            'roles_id' => 'int|required',
            'is_active' => 'int|required',
        ]);
        $request = $this->request->getPutData();

        if (!$lounge->isAdmin($user) && $request['roles_id'] == EnumsUsers::ROLES_ADMIN) {
            throw new UnauthorizedException('Unauthorized Action for the current user');
        }

        if ($this->userData->getId() !== $userLounge->users_id) {
            $userLounge->roles_id = $request['roles_id'];
            $userLounge->is_active = $request['is_active'];
            $userLounge->saveOrFail();
        }

        $this->model = new Users();
        $this->dto = Profile::class;
        $this->dtoMapper = new ProfileMapper();

        return $this->response($this->processOutput($user));
    }

    /**
     * Add a new user to the lounge.
     *
     * @param int $loungeId
     *
     * @return Response
     */
    public function addUser(int $loungeId) : Response
    {
        $this->request->validate([
            'users_id' => 'int|required',
            'roles_id' => 'int|required',
            'is_active' => 'int|required|',
        ]);
        $request = $this->request->getPostData();

        $model = Lounges::findFirstOrFail($loungeId);
        $user = Users::findFirstOrFail($request['users_id']);

        $lounge = new Lounge($model);

        if (!$lounge->canEdit($this->userData)) {
            throw new UnauthorizedException('Unauthorized Action for the current user');
        }

        if (UserLounge::findFirstByUsersAndLounge($user, $model)) {
            throw new NotFoundException('User already exist');
        }

        if (!$lounge->isAdmin($user) && $request['roles_id'] == EnumsUsers::ROLES_ADMIN) {
            throw new UnauthorizedException('Unauthorized Action for the current user');
        }

        $userLounge = new UserLounge();
        $userLounge->lounges_id = $model->getId();
        $userLounge->users_id = $user->getId();
        $userLounge->roles_id = $request['roles_id'];
        $userLounge->is_active = $request['is_active'] ?? EnumsUsers::ACTIVE;
        $userLounge->status = $request['status'] ?? EnumsUsers::ACTIVE;
        $userLounge->saveOrFail();

        $this->model = new Users();
        $this->dto = Profile::class;
        $this->dtoMapper = new ProfileMapper();

        return $this->response($this->processOutput($user));
    }
}

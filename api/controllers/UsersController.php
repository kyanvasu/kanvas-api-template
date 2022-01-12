<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Baka\Http\Exception\BadRequestException;
use Canvas\Api\Controllers\UsersController as CanvasUsersController;
use Gewaer\Domains\Users\Mappers\Dto\Profile;
use Gewaer\Domains\Users\Mappers\Profile as MappersProfile;
use Phalcon\Http\Response;

class UsersController extends CanvasUsersController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = [
        'name',
        'firstname',
        'lastname',
        'description',
        'displayname',
        'language',
        'country_id',
        'timezone',
        'email',
        'password',
        'roles_id',
        'created_at',
        'updated_at',
        'default_company',
        'default_company_branch',
        'cell_phone_number',
        'country_id',
        'location',
        'welcome'
    ];

    /**
     * Initialize the Controller.
     *
     * @return void
     */
    public function onConstruct()
    {
        parent::onConstruct();
        $this->dto = Profile::class;
        $this->dtoMapper = new MappersProfile();
    }

    /**
     * Get User.
     *
     * @param mixed $id
     *
     * @method GET
     * @url /v1/users/{id}
     *
     * @return Response
     */
    public function getById($id) : Response
    {
        //if its a string lets get it by displayname
        if (preg_match('/([a-zA-Z]+)/i', $id)) {
            return $this->getByDisplayname((string) $id);
        }

        //get current user
        if ($this->userData->isLoggedIn() && (int) $id === 0) {
            $id = $this->userData->getId();
        }

        /**
         * @todo filter only by user from this app / company
         */
        $user = $this->model->findFirstOrFail([
            'conditions' => 'id = ?0  AND is_deleted = 0',
            'bind' => [$id],
        ]);

        return $this->response(
            $this->processOutput($user)
        );
    }

    /**
     * Get user by its displayname.
     *
     * @param string $displayname
     *
     * @return Response
     */
    public function getByDisplayname(string $displayname) : Response
    {
        $user = $this->model->findFirstOrFail([
            'conditions' => ' displayname = ?0 AND is_deleted = 0',
            'bind' => [
                $displayname
            ],
        ]);

        return $this->response(
            $this->processOutput($user)
        );
    }

    /**
     * Given the activation code, activate the user account.
     *
     * @return Response
     */
    public function activate(int $id) : Response
    {
        $this->request->validate([
            'activation_number' => 'required|string'
        ]);

        $request = $this->request->getPostData();

        if ($request['activation_number'] !== $this->userData->get('activation_number')) {
            throw new BadRequestException('Invalid activation number');
        }

        return $this->response(
            'Account is Active'
        );
    }
}

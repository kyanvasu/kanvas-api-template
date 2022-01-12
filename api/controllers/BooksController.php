<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Domains\Books\Models\Books;

class BooksController extends BaseController
{

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = [
        'title',
        'description',
    ];

    /**
     * fields we accept to update.
     *
     * @var array
     */
    protected $updateFields = [
        'title',
        'description',
    ];

    /**
     * set objects.
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Books();

        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['is_published', ':', 1],
        ];
    }
}

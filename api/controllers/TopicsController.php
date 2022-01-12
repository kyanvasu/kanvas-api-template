<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Canvas\Contracts\Controllers\ProcessOutputMapperTrait;
use Canvas\Models\Apps;
use Gewaer\Domains\Topics\Mappers\Dto\Topic;
use Gewaer\Domains\Topics\Mappers\Topics as MappersTopics;
use Kanvas\Social\Models\Topics;

class TopicsController extends BaseController
{
    use ProcessOutputMapperTrait;

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
        $this->model = new Topics();
        $this->dto = Topic::class;
        $this->dtoMapper = new MappersTopics();

        $this->additionalSearchFields = [
            ['apps_id', ':', $this->app->getId()],
            ['companies_id', ':', Apps::CANVAS_DEFAULT_COMPANY_ID],
            ['is_deleted', ':', 0],
        ];
    }
}

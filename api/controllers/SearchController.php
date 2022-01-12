<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use function Baka\envValue;
use Baka\Support\Str;
use Canvas\Api\Controllers\UsersController as CanvasUsersController;
use Canvas\Contracts\Controllers\ProcessOutputMapperTrait;
use Gewaer\Domains\Lounges\Mappers\Dto\Lounge;
use Gewaer\Domains\Lounges\Mappers\Lounge as MappersLounge;
use Gewaer\Domains\Lounges\Models\Lounges;
use Gewaer\Domains\Rooms\Mappers\Dto\Room;
use Gewaer\Domains\Rooms\Mappers\Rooms as MappersRooms;
use Gewaer\Domains\Rooms\Models\Rooms;
use Gewaer\Domains\Users\Mappers\Dto\Profile;
use Gewaer\Domains\Users\Mappers\Profile as MappersProfile;
use Gewaer\Models\Users;
use Phalcon\Http\Response;

class SearchController extends CanvasUsersController
{
    use ProcessOutputMapperTrait;

    const DEFAULT_SCORE = 0.5;

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
        $this->model = new Lounges();
        $this->dto = Lounge::class;
        $this->dtoMapper = new MappersLounge();
    }

    /**
     * Overwrite the index for searching.
     *
     * @return Response
     */
    public function index() : Response
    {
        $limit = $this->request->getQuery('limit', 'int', 25);
        $page = $this->request->getQuery('page', 'int', 1);

        $searchParams = [
            'page' => [
                'current' => $page,
                'size' => $limit,
            ]
        ];

        $engine = $this->request->getQuery('index', 'string', envValue('ELASTIC_APP_DEFAULT_ENGINE', 'lounges'));
        $searchString = $this->request->getQuery('q', 'string', 'empty-state');

        if (empty($searchString)) {
            $searchString = 'empty-state';
        }

        $results = $this->elasticApp->search(
            $engine,
            $searchString,
            $searchParams
        );

        //overwrite the result
        $results['results'] = $this->parseResults($engine, $results['results']);

        //overwrite the total # of pages based on filter by score
        $newTotal = count($results['results']);
        $previousTotal = $results['meta']['page']['total_results'];
        $results['meta']['page']['total_results'] = $newTotal > $limit ? ($previousTotal - $limit) + $newTotal : $newTotal;
        $results['meta']['page']['total_pages'] = ceil($results['meta']['page']['total_results'] / $limit);

        return $this->response(
            $results
        );
    }

    /**
     * Get the suggestion strings from a query.
     *
     * @param string $name
     *
     * @return Response
     */
    public function suggestion(string $query) : Response
    {
        $engine = $this->request->getQuery('index', 'string', envValue('ELASTIC_APP_DEFAULT_ENGINE', 'lounges'));

        $results = $this->elasticApp->querySuggestion(
            $engine,
            strip_tags(urldecode($query))
        );

        if (!empty($results)) {
            foreach ($results['results']['documents'] as $key => $result) {
                $suggestion = strtok(strip_tags($result['suggestion']), ',');

                //don't know why the search engine is not allow using to block some thing from the suggestion
                if (!Str::contains($suggestion, '@')) {
                    $results['results']['documents'][$key]['suggestion'] = strtok(strip_tags(Str::cleanup($result['suggestion'])), ',');
                } else {
                    unset($results['results']['documents'][$key]);
                }
            }
        }

        return $this->response(
            $results
        );
    }

    /**
     * Parse the result set to a storm type.
     *
     * @param string $engine
     * @param array $results
     *
     * @return array
     */
    protected function parseResults(string $engine, array $results)
    {
        $cache = false;

        /**
         * Overwrite the mappers.
         */
        switch ($engine) {
            case 'users':
                $this->model = new Users();
                $this->dto = Profile::class;
                $this->dtoMapper = new MappersProfile();
                break;
            case 'rooms':
                $this->model = new Rooms();
                $this->dto = Room::class;
                $this->dtoMapper = new MappersRooms();
                break;
            case 'lounges':
            default:
                $this->model = new Lounges();
                $this->dto = Lounge::class;
                $this->dtoMapper = new MappersLounge();
                break;
        }

        foreach ($results as $key => $result) {
            //only show relevant results
            if ((float) $result['_meta']['score'] < self::DEFAULT_SCORE) {
                unset($results[$key]);
                continue;
            }

            /**
             * we need the user id of the memo so we assign it.
             *
             * @todo add it to the index
             */
            if ($engine == getenv('ELASTIC_APP_DEFAULT_ENGINE')) {
                $result['users_id']['raw'] = json_decode($result['writer']['raw'])->id;
            }

            $results[$key] = new $this->model(array_map(
                function ($record) {
                    return $record['raw'] ?? null;
                },
                $result
            ));
        }

        if (empty($results)) {
            return $results;
        }

        return $this->processOutput($results);
    }

    /**
     * If we have relationships send them as additional context to the mapper.
     *
     * @return array
     */
    protected function getMapperOptions() : array
    {
        $context = [];
        if ($this->request->hasQuery('relationships')) {
            $context['relationships'] = explode(',', $this->request->getQuery('relationships'));
        }

        if ($this->request->hasQuery('light')) {
            $context['light'] = true;
        }

        return $context;
    }
}

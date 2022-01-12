<?php

declare(strict_types=1);

namespace Gewaer\Domains\Rooms\Mappers;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Canvas\Contracts\Mapper\RelationshipTrait;

class Rooms extends CustomMapper
{
    use RelationshipTrait;

    /**
     * Undocumented function.
     *
     * @param Rooms $source
     * @param RoomsDto $destination
     * @param array $context
     *
     * @return void
     */
    public function mapToObject($source, $destination, array $context = [])
    {
        $destination->id = $source->getId();
        $destination->lounge = $source->lounges->toArray();
        $destination->users_id = $source->users_id;
        $destination->name = $source->name;
        $destination->description = $source->description;
        $destination->is_live = $source->is_live;
        $destination->is_public = $source->is_public;
        $destination->files = $source->getFiles();
        return $destination;
    }
}

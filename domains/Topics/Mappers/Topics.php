<?php

declare(strict_types=1);

namespace Gewaer\Domains\Topics\Mappers;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Gewaer\Domains\Topics\Mappers\Dto\Topic;
use Kanvas\Social\Models\Topics as ModelsTopics;

class Topics extends CustomMapper
{

    /**
     * Undocumented function.
     *
     * @param ModelsTopics $source
     * @param Topic $destination
     * @param array $context
     *
     * @return void
     */
    public function mapToObject($source, $destination, array $context = [])
    {
        $destination->id = $source->getId();
        $destination->name = $source->name;

        return $destination;
    }
}

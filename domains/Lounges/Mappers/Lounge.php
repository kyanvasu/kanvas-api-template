<?php

declare(strict_types=1);

namespace Gewaer\Domains\Lounges\Mappers;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Canvas\Contracts\Mapper\RelationshipTrait;

class Lounge extends CustomMapper
{
    use RelationshipTrait;

    /**
     * Undocumented function.
     *
     * @param Lounge $lounge
     * @param LoungeDto $loungeDto
     * @param array $context
     *
     * @return void
     */
    public function mapToObject($lounge, $loungeDto, array $context = [])
    {
        $loungeDto->id = $lounge->getId();
        $loungeDto->name = $lounge->name;
        $loungeDto->description = $lounge->description;
        $loungeDto->is_public = $lounge->is_public;
        $loungeDto->users_id = $lounge->users_id;
        $loungeDto->files = $lounge->getFiles();
        return $loungeDto;
    }
}

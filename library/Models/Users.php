<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Canvas\Models\Users as CanvasUsers;
use Kanvas\Packages\AppSearch\Contracts\SearchableModelsTrait;

class Users extends CanvasUsers
{
    use SearchableModelsTrait;
}

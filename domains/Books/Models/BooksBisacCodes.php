<?php
declare(strict_types=1);

namespace Gewaer\Domains\Books\Models;

use Gewaer\Models\BaseModel;

class BooksBisacCodes extends BaseModel
{
    public int $books_id;
    public int $bisac_codes_id;
    public int $categories_id;

    /**
     * Initialize method for model.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('books_bisac_codes');
    }
}

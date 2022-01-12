<?php
declare(strict_types=1);

namespace Gewaer\Domains\Books\Models;

use Gewaer\Models\BaseModel;

class BooksAuthors extends BaseModel
{
    public int $books_id;
    public int $authors_id;

    /**
     * Initialize method for model.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('books_authors');
    }
}

<?php
declare(strict_types=1);

namespace Gewaer\Domains\Books\Models;

use Canvas\Contracts\CustomFields\CustomFieldsTrait;
use Canvas\Contracts\FileSystemModelTrait;
use Gewaer\Models\BaseModel;

class Books extends BaseModel
{
    use FileSystemModelTrait;
    use CustomFieldsTrait;

    public ?int $books_series_id = 0;
    public ?int $publishers_id = 0;
    public ?int $platform_id = 0;
    public ?int $duration = 0;
    public ?int $family_safe = 0;
    public ?int $is_published = 0;
    public ?string $non_public_link = null;
    public ?float $rating = 0;
    public ?int $narrators_count = 0;
    public ?int $authors_count = 0;
    public ?int $series_count = 0;
    public ?string $title = null;
    public ?string $short_title = null;
    public ?string $description = null;
    public ?string $excerpt = null;
    public ?float $book_order = null;
    public ?string $published_date = null;
    public ?int $status = 0;
    public ?string $release_date = null;
    public ?string $isbn = null;
    public ?int $total_chapters = 0;

    /**
     * Initialize method for model.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('books');
    }

    /**
     * After save.
     *
     * @return void
     */
    public function afterSave()
    {
        $this->associateFileSystem();
    }
}

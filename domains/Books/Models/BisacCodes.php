<?php
declare(strict_types=1);

namespace Gewaer\Domains\Books\Models;

use Canvas\Contracts\CustomFields\CustomFieldsTrait;
use Canvas\Contracts\FileSystemModelTrait;
use Gewaer\Models\BaseModel;

class BisacCodes extends BaseModel
{
    use FileSystemModelTrait;
    use CustomFieldsTrait;

    public ?string $name = null;
    public ?int $categories_id = null;
    public ?string $code = null;
    public ?string $description = null;

    /**
     * Initialize method for model.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('bisac_codes');
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

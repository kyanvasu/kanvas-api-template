<?php
declare(strict_types=1);

namespace Gewaer\Domains\Books\Models;

use Canvas\Contracts\CustomFields\CustomFieldsTrait;
use Canvas\Contracts\FileSystemModelTrait;
use Gewaer\Models\BaseModel;

class Authors extends BaseModel
{
    use FileSystemModelTrait;
    use CustomFieldsTrait;

    public ?string $name = null;
    public ?string $biography = null;

    /**
     * Initialize method for model.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('authors');
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

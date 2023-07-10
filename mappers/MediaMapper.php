<?php

namespace app\mappers;

use app\core\Model;
use app\models\Media;
use PhpParser\Node\Expr\Array_;

class MediaMapper extends \app\core\Mapper
{
    private ?\PDOStatement $insert;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("INSERT into media (file_name, message_id) VALUES (:file_name, :message_id)");
    }

    /**
     * @param Media $model
     * @return Model
     */
    protected function doInsert(Model $model): Model
    {
        $this->insert->execute([
            ":file_name" => $model->getFileName(),
            ":message_id" => $model->getMessageId()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId($id);
        return $model;
    }
    function createObject(array $data): Model
    {
        return new Media(
            array_key_exists("id", $data) ? $data["id"] : null,
            array_key_exists("file_name", $data) ? $data["file_name"] : null,
            array_key_exists("message_id", $data) ? $data["message_id"] : null
        );
    }

    public function getInstance()
    {
        return $this;
    }

    protected function doUpdate(Model $model): void
    {
        // TODO: Implement doUpdate() method.
    }

    protected function doDelete(Model $model): void
    {
        // TODO: Implement doDelete() method.
    }
    protected function doSelect(int $id): array
    {
        // TODO: Implement doSelect() method.
        return [];
    }

    protected function doSelectAll(): array
    {
        // TODO: Implement doSelectAll() method.
        return [];
    }
}
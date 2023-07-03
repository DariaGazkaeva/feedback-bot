<?php

namespace app\mappers;

use app\core\Model;
use app\models\BotMessage;
use PhpParser\Node\Expr\Array_;

class BotMessageMapper extends \app\core\Mapper
{
    private ?\PDOStatement $insert_b;

    public function __construct()
    {
        parent::__construct();
        $this->insert_b = $this->getPdo()->prepare("INSERT into bot_message (user_message_id, text, date) VALUES (:user_message_id, :text, :date)");
    }

    /**
     * @param BotMessage $model
     * @return Model
     */
    protected function doInsert(Model $model): Model
    {
        $this->insert_b->execute([
            ":user_message_id" => $model->getUserMessageId(),
            ":text" => $model->getText(),
            ":date" => $model->getDate()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId($id);
        return $model;
    }
    function createObject(array $data): Model
    {
        return new BotMessage(
            array_key_exists("id", $data) ? $data["id"] : null,
            $data["text"],
            $data["date"],
            array_key_exists("user_message_id", $data) ? $data["user_message_id"] : null
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
<?php

namespace app\mappers;

use app\core\Model;
use app\models\UserMessage;
use PhpParser\Node\Expr\Array_;

class UserMessageMapper extends \app\core\Mapper
{
    private ?\PDOStatement $insert;
    private ?\PDOStatement $selectAll;
    private ?\PDOStatement $selectUserIdByUserMessageId;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("INSERT into user_message (user_id, text, date, media) VALUES (:user_id, :text, :date, :media)");
        $this->selectAll = $this->getPdo()->prepare("SELECT * FROM user_message");
        $this->selectUserIdByUserMessageId = $this->getPdo()->prepare("SELECT user_id FROM user_message WHERE id = :id");
    }

    /**
     * @param UserMessage $model
     * @return Model
     */
    protected function doInsert(Model $model): Model
    {
        $this->insert->execute([
            ":user_id" => $model->getUserId(),
            ":text" => $model->getText(),
            ":date" => $model->getDate(),
            ":media" => $model->getMedia()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId($id);
        return $model;
    }
    protected function doSelectAll(): array
    {
        if ($this->selectAll->execute()) {
            $fetched = $this->selectAll->fetchAll();
            if ($fetched !== false) return $fetched;
        }
        return [];
    }
    public function doSelectUserIdByUserMessageId(int $userMessageId) : int {
        if ($this->selectUserIdByUserMessageId->execute([$userMessageId])) {
            $fetched = $this->selectUserIdByUserMessageId->fetch();
            if ($fetched !== false) return $fetched["user_id"];
        }
        return -1;
    }
    function createObject(array $data): Model
    {
        return new UserMessage(
            array_key_exists("id", $data) ? $data["id"] : null,
            array_key_exists("text", $data) ? $data["text"] : null,
            array_key_exists("media", $data) ? $data["media"] : null,
            $data["date"],
            array_key_exists("user_id", $data) ? $data["user_id"] : null
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
}
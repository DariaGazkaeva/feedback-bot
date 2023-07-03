<?php

namespace app\mappers;

use app\core\Model;
use app\models\UserMessage;
use PhpParser\Node\Expr\Array_;

class UserMessageMapper extends \app\core\Mapper
{
    private ?\PDOStatement $insert_u;
    private ?\PDOStatement $selectAll_u;
    private ?\PDOStatement $selectUserIdByUserMessageId;

    public function __construct()
    {
        parent::__construct();
        $this->insert_u = $this->getPdo()->prepare("INSERT into user_message (user_id, text, date) VALUES (:user_id, :text, :date)");
        $this->selectAll_u = $this->getPdo()->prepare("SELECT * FROM user_message");
        $this->selectUserIdByUserMessageId = $this->getPdo()->prepare("SELECT user_id FROM user_message WHERE id = :id");
    }

    /**
     * @param UserMessage $model
     * @return Model
     */
    protected function doInsert(Model $model): Model
    {
        $this->insert_u->execute([
            ":user_id" => $model->getUserId(),
            ":text" => $model->getText(),
            ":date" => $model->getDate()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId($id);
        return $model;
    }
    protected function doSelectAll(): array
    {
        if ($this->selectAll_u->execute()) {
            $fetched = $this->selectAll_u->fetchAll();
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
            $data["text"],
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
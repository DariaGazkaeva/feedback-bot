<?php

namespace app\mappers;

use app\core\Model;
use app\models\Media;
use app\models\UserMessage;
use PhpParser\Node\Expr\Array_;

class UserMessageMapper extends \app\core\Mapper
{
    private ?\PDOStatement $insert;
    private ?\PDOStatement $selectByMediaGroupId;
    private ?\PDOStatement $selectAll;
    private ?\PDOStatement $selectAllWithMedia;
    private ?\PDOStatement $selectUserIdByUserMessageId;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("INSERT into user_message (id, user_id, text, date, media_group_id) VALUES (:id, :user_id, :text, :date, :media_group_id)");
        $this->selectByMediaGroupId = $this->getPdo()->prepare("SELECT * FROM user_message WHERE media_group_id = :media_group_id");
        $this->selectAll = $this->getPdo()->prepare("SELECT * FROM user_message");
        $this->selectAllWithMedia = $this->getPdo()->prepare("SELECT user_message.id, user_message.user_id, user_message.text, user_message.date, media.file_name FROM user_message LEFT JOIN media ON user_message.id = media.message_id ORDER BY user_message.id");
        $this->selectUserIdByUserMessageId = $this->getPdo()->prepare("SELECT user_id FROM user_message WHERE id = :id");
    }

    /**
     * @param UserMessage $model
     * @return Model
     */
    protected function doInsert(Model $model): Model
    {
        $this->insert->execute([
            ":id" => $model->getId(),
            ":user_id" => $model->getUserId(),
            ":text" => $model->getText(),
            ":date" => $model->getDate(),
            ":media_group_id" => $model->getMediaGroupId()
        ]);
        return $model;
    }
    public function selectAllWithMedia(): array
    {
        if ($this->selectAllWithMedia->execute()) {
            $fetched = $this->selectAllWithMedia->fetchAll();

            if ($fetched === false) return [];
            else {
                $messages = [];
                $message_id = -1;
                $media = [];
                foreach ($fetched as $item) {
                    if ($message_id !== $item['id']) {
                        $media = [];
                        $message_id = $item['id'];
                        $user_id = $item['user_id'];
                        $text = $item['text'];
                        $date = $item['date'];
                        $file_name = $item['file_name'];
                        if ($file_name !== null and $file_name !== '' and $file_name !== 'NULL') {
                            $m = new Media(null, $file_name, $message_id);
                            array_push($media, $m);
                        }
                        array_push($messages, new UserMessage($message_id, $user_id, $date, null, $text, $media));
                    } else {
                        $file_name = $item['file_name'];
                        $text = $item['text'];
                        if ($file_name !== null and $file_name !== '' and $file_name !== 'NULL') {
                            array_push($media, new Media(null, $file_name, $message_id));
                            end($messages)->setMedia($media);
                        }
                        if ($text !== null and $text !== '' and $text !== 'NULL') {
                            end($messages)->setText($text);
                        }
                    }
                }
                return $messages;
            }
        }
        return [];
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
    public function findByMediaGroupId(string $id) : Model|null {
        if ($this->selectByMediaGroupId->execute([$id])) {
            $fetched = $this->selectByMediaGroupId->fetch();
            if ($fetched !== false and $fetched !== null) {
                return $this->createObject($fetched);
            }
        }
        return null;
    }
    function createObject(array $data): Model
    {
        return new UserMessage(
            array_key_exists("id", $data) ? $data["id"] : null,
            array_key_exists("user_id", $data) ? $data["user_id"] : null,
            $data["date"],
            array_key_exists("media_group_id", $data) ? $data["media_group_id"] : null,
            array_key_exists("text", $data) ? $data["text"] : null,
            array_key_exists("media", $data) ? $data["media"] : null,
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
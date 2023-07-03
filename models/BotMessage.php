<?php

namespace app\models;

use app\core\Model;

class BotMessage extends Model
{

    private string $text;

    private string $date;

    private int $user_message_id;

    public function __construct(
        ?int $id,
        string $text,
        string $date,
        int $user_message_id
    ) {
        parent::__construct($id);
        $this->text = $text;
        $this->date = $date;
        $this->user_message_id = $user_message_id;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int|null
     */
    public function getUserMessageId(): ?int
    {
        return $this->user_message_id;
    }

    /**
     * @param int|null $user_message_id
     */
    public function setUserMessageId(?int $user_message_id): void
    {
        $this->user_message_id = $user_message_id;
    }


}
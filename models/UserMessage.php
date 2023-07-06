<?php

namespace app\models;

use app\core\Model;

class UserMessage extends Model
{

    private string $text;

    private string $date;

    private int $user_id;

    public function __construct(
        ?int $id,
        string $text,
        string $date,
        int $user_id
    ) {
        parent::__construct($id);
        $this->text = $text;
        $this->date = $date;
        $this->user_id = $user_id;
    }

    public function __toString(): string
    {
        return "Message(id=" . $this->getId() . ",user_id=" . $this->user_id . ",date=" . $this->date . ",text=" . $this->text . ")";
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
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

}
<?php

namespace app\models;

use app\core\Model;

class UserMessage extends Model
{
    private int $id;
    private int $user_id;
    private string $date;
    private ?string $media_group_id;
    private ?string $text;
    private ?array $media;

    public function __construct(
        int $id,
        int $user_id,
        string $date,
        ?string $media_group_id,
        ?string $text,
        ?array $media,
    ) {
        parent::__construct($id);
        $this->text = $text;
        $this->media = $media;
        $this->date = $date;
        $this->user_id = $user_id;
        $this->media_group_id = $media_group_id;
    }

    public function __toString(): string
    {
        return "Message(id=" . $this->getId() . ",user_id=" . $this->user_id . ",date=" . $this->date . ",text=" . $this->getText() . ")";
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int|null $id): void
    {
        $this->id = $id;
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
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return array|null
     */
    public function getMedia(): ?array
    {
        return $this->media;
    }

    /**
     * @param array|null $media
     */
    public function setMedia(?array $media): void
    {
        $this->media = $media;
    }

    /**
     * @return string|null
     */
    public function getMediaGroupId(): ?string
    {
        return $this->media_group_id;
    }

    /**
     * @param string|null $media_group_id
     */
    public function setMediaGroupId(?string $media_group_id): void
    {
        $this->media_group_id = $media_group_id;
    }
}
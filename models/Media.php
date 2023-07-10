<?php

namespace app\models;

use app\core\Model;

class Media extends Model
{
    private string $fileName;
    private int $messageId;

    public function __construct(
        ?int $id,
        string $fileName,
        int $messageId
    ) {
        parent::__construct($id);
        $this->fileName = $fileName;
        $this->messageId = $messageId;
    }

    public function __toString(): string
    {
        return "Media(id=" . $this->getId() . ",fileName=" . $this->fileName . ",messageId=" . $this->messageId . ")";
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    /**
     * @param int $messageId
     */
    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
    }
}
<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Application;
use app\mappers\BotMessageMapper;
use app\mappers\UserMessageMapper;
use app\models\BotMessage;
use app\models\UserMessage;
use function React\Promise\map;

class MainController
{
    private string $token = "6381506117:AAHRZUBQBL43RjVGJkY3zAPZJILA_6vm8pw";
    public function get(): void
    {

        $this->update();

        $mapper = new UserMessageMapper();
        $messages = $mapper->SelectAll()->getNextRow();
        Application::$app->getRouter()->renderTemplate("main",
            ["messages"=>$messages]);
    }

    public function answer(): void
    {
        $userMessageId = (int)$_POST["user-message-id"];
        if ($userMessageId === 0) {
            Application::$app->getRouter()->renderStatic("400.html");
        } else {
            $userMapper = new UserMessageMapper();
            $chatId = $userMapper->doSelectUserIdByUserMessageId($userMessageId);
            if ($chatId !== -1) {
                if ($this->sendMessage($chatId, $_POST["text"]) !== false) {
                    $message = new BotMessage(null, $_POST["text"], date("y-m-d"), $userMessageId);
                    $botMapper = new BotMessageMapper();
                    $botMapper->Insert($message);

                    header('Location: /');
                    return;
                }
            }
            Application::$app->getRouter()->renderStatic("502.html");
        }
    }

    public function update(): void
    {
        if (!array_key_exists("last-update-id", $_SESSION)) {
            $_SESSION["last-update-id"] = 0;
        }
        $updates = json_decode($this->getUpdates($_SESSION["last-update-id"] + 1), true);

        if (count($updates["result"]) > 0 and $updates["ok"] === true) {
            $_SESSION["last-update-id"] = end($updates["result"])["update_id"];
            $mapper = new UserMessageMapper();

            foreach ($updates["result"] as $update) {
                $message = new UserMessage(
                    null,
                    $update["message"]["text"],
                    date("Y-m-d", $update["message"]["date"]),
                    $update["message"]["from"]["id"]);
                $mapper->Insert($message);
            }
        } else if ($updates["ok"] === false) {
            Application::$app->getRouter()->renderStatic("502.html");
        }
    }
    private function getUpdates($offset=0): string
    {
        $params = array("offset" => $offset);
        $ch = curl_init("https://api.telegram.org/bot". $this->token ."/getUpdates?" . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $resultQuery = curl_exec($ch);
        curl_close($ch);
        return $resultQuery;
    }

    private function sendMessage($chatId, $text): bool|string
    {
        $getQuery = array(
            "chat_id" 	=> $chatId,
            "text"  	=> $text,
            "parse_mode" => "html"
        );
        $ch = curl_init("https://api.telegram.org/bot". $this->token ."/sendMessage?" . http_build_query($getQuery));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $resultQuery = curl_exec($ch);
        curl_close($ch);
        return $resultQuery;
    }
}

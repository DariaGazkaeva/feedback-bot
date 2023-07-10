<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Application;
use app\mappers\BotMessageMapper;
use app\mappers\MediaMapper;
use app\mappers\UserMessageMapper;
use app\models\BotMessage;
use app\models\Media;
use app\models\UserMessage;
use function React\Promise\map;

class MainController
{
    private string $token = "6381506117:AAHRZUBQBL43RjVGJkY3zAPZJILA_6vm8pw";
    private string $secretKey = 'sadf54f-BDFSHFJNfdsuy12m__dsf';
    private string $secretKeyHeader = 'HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN';
    public function get(): void
    {
        $mapper = new UserMessageMapper();
        $messages = $mapper->selectAllWithMedia();
        Application::$app->getRouter()->renderTemplate("main",
            ["messages"=>array_reverse($messages)]);
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
                    $message = new BotMessage(null, $_POST["text"], date("Y-m-d"), $userMessageId);
                    $botMapper = new BotMessageMapper();
                    $botMapper->Insert($message);

                    header('Location: /');
                    return;
                }
            }
            Application::$app->getRouter()->renderStatic("502.html");
        }
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

    public function getAnswers(): void
    {
        $mapper = new BotMessageMapper();
        $answers = $mapper->doSelectAllByUserMessageId((int)$_GET["user-message-id"]);
        Application::$app->getRouter()->renderJson($answers);
    }

    public function update(): void
    {
        $request_body = json_decode(file_get_contents('php://input'), true);
        $request_message = $request_body['message'];
        $secret_key = $_SERVER[$this->secretKeyHeader];

        Application::$app->getLogger()->debug(json_encode($request_message));

        if ($secret_key !== $this->secretKey) {
            Application::$app->getRouter()->renderStatic("403.html");
            http_response_code(403);
            return;
        }

        $userMessageMapper = new UserMessageMapper();
        $mediaMapper = new MediaMapper();

        $messageId = (int)$request_message['message_id'];
        $photoArrayTg = $request_message['photo'];
        $animationTg = $request_message['animation'];
        $mediaGroupId = $request_message['media_group_id'];
        $caption = $request_message['caption'];
        $userId = $request_message["from"]["id"];
        $date = date("Y-m-d", $request_message["date"]);
        $text = $request_message['text'];
        $fileName = null;

        if ($photoArrayTg !== null) {
            $photoTg = end($photoArrayTg);
            $fileName = $this->doDownloadMedia($photoTg);
        } else if ($animationTg !== null) {
            $fileName = $this->doDownloadMedia($animationTg);
        }

        if ($mediaGroupId === null) {
            $text = $text !== null ? $text : $caption;
            $message = new UserMessage($messageId, $userId, $date, null, $text,null);
            $userMessageMapper->Insert($message);
            if ($fileName !== null) {
                $media = new Media(null, $fileName, $messageId);
                $mediaMapper->Insert($media);
            }
        } else {
            $message = $userMessageMapper->findByMediaGroupId($mediaGroupId);
            if ($message === null) {
                $text = $text !== null ? $text : $caption;
                $message = new UserMessage($messageId, $userId, $date, $mediaGroupId, $text,null);
                $userMessageMapper->Insert($message);
                if ($fileName !== null) {
                    $media = new Media(null, $fileName, $messageId);
                    $mediaMapper->Insert($media);
                }
            } else {
                if ($fileName !== null) {
                    $media = new Media(null, $fileName, $message->getId());
                    $mediaMapper->Insert($media);
                }
            }
        }
    }

    private function getFileData($requestParams): mixed
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . $this->token . '/getFile?'.http_build_query($requestParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array();
        $headers[] = "Accept: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $fileDataString = curl_exec($ch);
        if (curl_errno($ch)) {
            Application::$app->getLogger()->warning('Error:' . curl_error($ch));
        }
        curl_close($ch);
        return json_decode($fileDataString, true);
    }

    private function generateFileName(string $filePathSource): string {
        $array = explode(".", $filePathSource);
        $ext = end($array);
        return time().".".$ext;
    }

    private function generateFilePath(string $fileName): string {
        return PROJECT_ROOT."/web/media/".$fileName;
    }

    private function downloadMedia(string $filePathSource, string $filePathDist): void
    {
        $realSource = "https://api.telegram.org/file/bot".$this->token."/".$filePathSource;
        copy($realSource, $filePathDist);
    }

    private function doDownloadMedia(array $dataTg): string {
        $fileId = $dataTg['file_id'];
        $fileDataTg = $this->getFileData(['file_id' => $fileId]);
        $filePathTg = $fileDataTg['result']['file_path'];
        $fileName = $this->generateFileName($filePathTg);
        $filePathDist = $this->generateFilePath($fileName);
        $this->downloadMedia($filePathTg, $filePathDist);
        return $fileName;
    }
}

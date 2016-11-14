<?php

namespace vr\pushes\aws;

use Aws\Credentials\Credentials;
use Aws\Sns\SnsClient;
use vr\pushes\PushMessage;
use vr\pushes\PushProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class AwsPushProvider
 * @package vr\pushes\aws
 */
class AwsPushProvider extends PushProvider
{
    /**
     * @var
     */
    public $accessKey;
    /**
     * @var
     */
    public $secretKey;
    /**
     * @var
     */
    public $appArn;
    /**
     * @var string. Region like eu-west-1
     */
    public $region;

    /**
     *
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @param PushMessage $message
     *
     * @return mixed|void
     */
    public function deliver(PushMessage $message)
    {
        foreach ($message->getRecipients() as $recipient) {
            $this->sendOne($recipient, $message);
        }
    }

    /**
     * @param             $token
     * @param PushMessage $message
     *
     * @return mixed
     */
    private function sendOne($token, $message)
    {
        $client = new SnsClient([
            'credentials' => new Credentials($this->accessKey, $this->secretKey),
            'region'      => $this->region,
            'version'     => 'latest'
        ]);

        $endpoint = $client->createPlatformEndpoint([
            'PlatformApplicationArn' => $this->appArn,
            'Token'                  => $token
        ]);

        $published = $client->publish([
            'MessageStructure' => 'json',
            'Message'          => Json::encode([
                'default'              => $message->getBody(),
                $message->getService() => $this->formatMessage($message)

            ]),
            'TargetArn'        => $endpoint['EndpointArn'],
        ]);

        return $published['MessageId'];
    }

    /**
     * @param PushMessage $message
     *
     * @return string
     */
    private function formatMessage($message)
    {
        $handlers = [
            PushMessage::SERVICE_IOS     => function (PushMessage $message) {
                return [
                    'aps'  => [
                        'alert'    => $message->getBody(),
                        'badge'    => $message->getBadge(),
                        'sound'    => 'default',
                        'category' => $message->getCategory(),
                    ],
                    'data' => $message->getData()
                ];
            },
            PushMessage::SERVICE_ANDROID => function (PushMessage $message) {
                return [
                    'data' => array_merge(
                        ['message' => $message->getBody()],
                        $message->getData()
                    )
                ];
            },
        ];

        return Json::encode(call_user_func(ArrayHelper::getValue($handlers, $message->getService()), $message));
    }
}
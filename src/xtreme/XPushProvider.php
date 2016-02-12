<?php
namespace vm\pushes\xtreme;

use Curl\Curl;
use vm\pushes\PushMessage;
use vm\pushes\PushProvider;
use yii\helpers\ArrayHelper;

/**
 * Class XPushProvider
 * @package vm\pushes\xtreme
 */
class XPushProvider extends PushProvider
{
    /**
     * @var
     */
    public $appKey;

    /**
     * @var
     */
    public $appToken;

    /**
     * @var string
     */
    private $baseUrl = 'https://xtremepush.com/api/external/';

    /**
     * @param PushMessage $message
     *
     * @return mixed
     * @throws \Exception
     */
    public function deliver(PushMessage $message)
    {
        $campaign = ArrayHelper::getValue($this->createCampaign($message), ['model', 'id']);
        if (!$campaign) {
            throw new \Exception('XtremePush: cannot create campaign');
        }

        $result = $this->send($campaign);

        return ArrayHelper::getValue($result, 'success');
    }

    /**
     * @param PushMessage $message
     * @param GeoLocation $location
     *
     * @return mixed
     */
    public function pin(PushMessage $message, GeoLocation $location)
    {
        if (!$this->createLocation($location)) {
            return false;
        }

        return $this->execute('create/campaign', [
            'locations'  => [$location->id],
            'title'      => $message->getBody(),
            'text'       => $message->getBody(),
            'tokenArray' => $message->getRecipients(),
            'ios'        => [
                'active'      => 1,
                'environment' => YII_DEBUG ? 'sandbox' : 'production'
            ],
        ]);
    }

    /**
     * @param GeoLocation $location
     *
     * @return mixed
     */
    private function createLocation(GeoLocation $location)
    {
        $result = $this->execute('create/location', [
            'apptoken'  => $this->appToken,
            'title'     => $location->getTitle(),
            'address'   => $location->address,
            'latitude'  => $location->latitude,
            'longitude' => $location->longitude,
            'radius'    => $location->radius,
            'type'      => 0 // See [https://dashboard.xtremepush.com/docs/external-api/external_api_2.0/#location-methods]
        ]);

        $location->id = ArrayHelper::getValue($result, ['model', 'id']);

        return ArrayHelper::getValue($result, 'success');
    }

    /**
     * @param PushMessage $message
     *
     * @return null
     */
    private function createCampaign($message)
    {
        return $this->execute('create/campaign', [
            'title'      => $message->getBody(),
            'text'       => $message->getBody(),
            'tokenArray' => $message->getRecipients(),
            'ios'        => [
                'active'      => 1,
                'environment' => YII_DEBUG ? 'sandbox' : 'production'
            ],
        ]);
    }

    /**
     * @param $campaign
     *
     * @return mixed
     *
     */
    private function send($campaign)
    {
        return $this->execute('send/campaign', [
            'id' => $campaign,
        ]);
    }

    /**
     * @param $method
     * @param $params
     *
     * @return null
     */
    private function execute($method, $params)
    {
        $params['apptoken'] = $this->appToken;

        $url = $this->baseUrl . $method;

        $curl = new Curl();
        $curl->post($url, $params);

        return $curl->response;
    }
}
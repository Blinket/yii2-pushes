<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 07/05/15
 * Time: 22:37
 */

namespace vr\pushes;

use yii\base\Component;

/**
 * Class PushMessage
 * @package vr\pushes
 */
class PushMessage extends Component
{
    /**
     *
     */
    const SERVICE_IOS = 'APNS';
    /**
     *
     */
    const SERVICE_ANDROID = 'GCM';

    /**
     * @var
     */
    public $provider;
    /**
     * @var null
     */
    private $recipients = null;
    /**
     * @var null
     */
    private $body = null;

    /**
     * @var null
     */
    private $badge = null;

    /**
     * @var null
     */
    private $category = null;

    /**
     * @var null
     */
    private $data = null;

    /**
     * @var string
     */
    private $service = self::SERVICE_IOS;

    /**
     * @param $token
     *
     * @return $this
     */
    public function setTo($token)
    {
        $this->recipients[] = $token;

        return $this;
    }

    /**
     *
     */
    public function send()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->provider->deliver($this);
    }

    /**
     * @return null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return null
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @return null
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param $badge
     *
     * @return $this
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param $service
     *
     * @return $this
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

}
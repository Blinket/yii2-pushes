<?php
namespace vr\pushes\xtreme;

use yii\base\Model;

/**
 * Class GeoLocation
 * @package vr\pushes\xpush
 *
 * @property string $title
 */
class GeoLocation extends Model
{
    /**
     *
     */
    const DEFAULT_RADIUS = 100;

    /**
     * @var
     */
    public $id;
    /**
     * @var
     */
    public $latitude;
    /**
     * @var
     */
    public $longitude;
    /**
     * @var
     */
    public $address;

    /**
     * @var int
     */
    public $radius = self::DEFAULT_RADIUS;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->address ?: sprintf('%f,%f', $this->latitude, $this->longitude);
    }

    public function rules()
    {
        return [
            [['id', 'latitude', 'longitude', 'address'], 'safe']
        ];
    }

}
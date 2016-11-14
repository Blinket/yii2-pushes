<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 07/05/15
 * Time: 22:35
 */

namespace vr\pushes;

use yii\base\Component;

/**
 * Class PushProvider
 * @package vr\pushes
 */
abstract class PushProvider extends Component
{
    /**
     * @return PushMessage
     */
    public function compose()
    {
        return new PushMessage([
            'provider' => $this
        ]);
    }

    /**
     * @param PushMessage $message
     *
     * @return mixed
     */
    public abstract function deliver(PushMessage $message);
}
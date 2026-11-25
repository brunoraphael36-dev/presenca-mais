<?php
/**
 * Created by PhpStorm.
 * User: leonelc
 * Date: 06/11/2018
 * Time: 15:05
 */

namespace app\services\interfaces;

interface IAuthorizationService
{
    /**
     * @return mixed
     */
    public function getToken($authParams = []);
}

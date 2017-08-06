<?php
/**
 * Created by PhpStorm.
 * User: lunguandrei
 * Date: 06.08.17
 * Time: 19:06
 */

namespace App\Entities;


class EntrieExitEntity
{
    const OPEN = 1;
    const CLOSE = 2;

    private $status;

    public function __construct()
    {
        $this->status = self::OPEN;
    }
}
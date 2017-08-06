<?php
/**
 * Created by PhpStorm.
 * User: lunguandrei
 * Date: 06.08.17
 * Time: 19:26
 */

namespace App\Entities;


class CarEntryEntity
{
    const IN = 0;
    const OUT = 1;
    const PARK = 2;
    const UN_PARK = 3;
    const FOLLOW_ENTER = 4;
    const FOLLOW_EXIT = 5;

    private $status;

    /**
     * @return $this
     */
    public function followEnter() {
        $this->status = self::FOLLOW_ENTER;
        return $this;
    }

    /**
     * @return $this
     */
    public function entry() {
        $this->status = self::IN;
        return $this;
    }

    public function park() {
        $this->status = self::PARK;
        return $this;
    }

    /**
     * @return $this
     */
    public function unPark() {
        $this->status = self::UN_PARK;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @return $this
     */
    public function followExit() {
        $this->status = self::FOLLOW_EXIT;
        return $this;
    }
}
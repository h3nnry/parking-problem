<?php
/**
 * Created by PhpStorm.
 * User: lunguandrei
 * Date: 06.08.17
 * Time: 18:15
 */

namespace App\Entities;


class ParkingPlaceEntity
{
    const FREE = 0;
    const BUSY = 1;

    private $status;
    private $car;

    public function __construct()
    {
        $this->status = self::FREE;
    }

    /**
     * @return bool
     */
    public function isBusy() {
        return $this->status == self::BUSY;
    }

    /**
     * @return bool
     */
    public function isFree() {
        return $this->status == self::FREE;
    }

    /**
     * @return $this
     */
    public function freePlace() {
        $this->car->unPark();
        unset($this->car);
        $this->status = self::FREE;
        return $this;
    }

    /**
     * @param CarEntryEntity $car
     * @return $this
     */
    public function parkCar(CarEntryEntity $car) {
        $this->car = $car;
        $car->park();
        $this->status = self::BUSY;
        return $this;
    }
}
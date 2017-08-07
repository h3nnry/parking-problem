<?php
/**
 * Created by PhpStorm.
 * User: lunguandrei
 * Date: 06.08.17
 * Time: 16:36
 */

namespace App;

use App\Utils\Registry;
use App\Entities\ParkingPlaceEntity;
use App\Entities\EntrieExitEntity;
use App\Entities\CarEntryEntity;

class ParkingProcessor
{
    const PUBLIC_FILE_PATH = PROJECT_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
    const INPUT_FILE_NAME = 'input.txt';
    private $processedChars;
    private $totalParkingPlaces;
    private $totalEntriesExits;
    private $carsEntries;
    private $polutionBuffer;

    public function __construct()
    {
        $this->processedChars = (new Registry())
            ->set('N', (new Registry())->set('action', 'handleParkingPlaces'))
            ->set('B', (new Registry())->set('action', 'handlePolutionBuffer'))
            ->set('X', (new Registry())->set('action', 'handleTotalEntries'))
            ->set('I', (new Registry())->set('action', 'handleCarsEntries'))
            ->set('P', (new Registry())->set('action', 'handleCarPark'))
            ->set('E', (new Registry())->set('action', 'handleCarExit'));
    }

    public function init()
    {
        $this->inputFileHandler();
        $totalParkingPlaces = $this->totalParkingPlaces->getData();
        $freeParkingPlaces =0; $busyParkingPlaces = 0;
        array_map(function ($value) use(&$freeParkingPlaces, &$busyParkingPlaces) {
            if ($value->isFree()) {
                $freeParkingPlaces++;
            } else {
                $busyParkingPlaces++;
            }
        },$totalParkingPlaces);
        echo nl2br("Statistic:\n");
        printf('Free parking places - %s, busy parking places - %s;<br/>', $freeParkingPlaces, $busyParkingPlaces);
        printf('Entry cars - %s, no entry cars - %s<br/>', $this->carsEntries->get('entry')->count(),
            $this->carsEntries->get('noEntry')->count());

        printf('Cars with running Engine - %s', $this->countCarsWithRunningEngine());
    }

    private function inputFileHandler()
    {
        $inputFile = self::PUBLIC_FILE_PATH.self::INPUT_FILE_NAME;
        if (!is_file($inputFile)) {
            throw new \Exception('FileNotFoundException: File ' . self::INPUT_FILE_NAME . ' is missing!');
        }

        //Processing input file
        $fileHandler = fopen($inputFile,'r');
        $fileLines = 0;
        while ($line = fgets($fileHandler)) {
            if (strlen($line) >= 3) {
                $fileLines++;
                $firstChar = strtoupper($line[0]);
                if ($this->processedChars->get($firstChar) && $this->processedChars->get($firstChar)->get('action')
                    && method_exists($this, $this->processedChars->get($firstChar)->get('action'))) {
                    call_user_func(array($this, $this->processedChars->get($firstChar)->get('action')), trim(substr($line, 2)));
                }
            }
        }

        if ($fileLines === 0) {
            throw new \Exception('FileIsEmptyException: File ' . self::INPUT_FILE_NAME . ' is empty!');
        }


    }

    /**
     * Handle Parking Places
     * @param $num
     * @return $this
     */
    private function handleParkingPlaces($num) {
        $this->totalParkingPlaces = new Registry();
        if (is_numeric($num)) {
            for ($i = 0; $i < $num; $i++) {
                //Setting up parking places
                $this->totalParkingPlaces->set($i, new ParkingPlaceEntity());
            }
        }

        return $this;
    }

    private function handlePolutionBuffer($num)
    {
        if (is_numeric($num)) {
            $this->polutionBuffer = (new Registry())->set('polutionBuffer', $num);
        }
        return $this;
    }

    /**
     * Handle entries and exits
     * @param $num
     * @return $this
     */
    private function handleTotalEntries($num)
    {
        $this->totalEntriesExits = new Registry();
        $entries = [];
        if (is_numeric($num)) {
            for ($i = 0; $i < $num; $i++) {
                $entries[] = new EntrieExitEntity();
            }
        }
        //Setting up entries
        $this->totalEntriesExits->set('entries', $entries);
        //Setting up one exit
        $this->totalEntriesExits->set('exits', [new EntrieExitEntity()]);

        return  $this;
    }

    /**
     * @param $entryActionSequence
     */
    private function handleCarsEntries($entryActionSequence)
    {
        if (is_null($this->carsEntries)) {
            $this->carsEntries = new Registry();
            $this->carsEntries = (new Registry())
                ->set('entry', new Registry())
                ->set('noEntry', new Registry());
        }
        $entriesCount = $this->carsEntries->get('entry')->count();
        $noEntriesCount = $this->carsEntries->get('noEntry')->count();
        if (is_string($entryActionSequence)) {
            $entryActionSequenceLength = strlen($entryActionSequence);
            if ($entryActionSequenceLength > 0) {
                for ($i = 0; $i < $entryActionSequenceLength; $i++) {
                    switch ($entryActionSequence[$i]) {
                        case 0:
                            $this->carsEntries->get('noEntry')->set($noEntriesCount++, (new CarEntryEntity())->followEnter());
                            break;
                        case 1:
                            $this->carsEntries->get('entry')->set($entriesCount++, (new CarEntryEntity())->entry());
                            break;
                    }
                }
            }
        }
    }

    /**
     * @param $parkActionSequence
     * @throws \Exception
     */
    private function handleCarPark($parkActionSequence)
    {
        if (is_string($parkActionSequence)) {
            $parkActionSequenceLength = strlen($parkActionSequence);
            if ($parkActionSequenceLength > $this->carsEntries->get('entry')->count()) {
                throw new \Exception('DataInputException: Cars in action sequence for parking cannot be 
                more than in car entries!');
            }
            for ($i = 0; $i < $parkActionSequenceLength; $i++) {
                switch ($parkActionSequence[$i]) {
                    case 0:
                        if ($parkCar = $this->searchBusyPlaces()) {
                            $this->checkPolutionPercentage();
                            $parkCar->freePlace();
                        }
                        break;
                    case 1:
                        if ($parkCar = $this->searchFreePlaces()) {
                            if ($car = $this->getCarEntry()) {
                                $parkCar->parkCar($car);
                            }
                            else {
                                throw new \Exception('DataInputException: Cars in action sequence for parking cannot be 
                                    more than in car entries!');
                            }
                        }
                        else {
                            throw new \Exception('ParkingPlacesException: There is no more free park place!');
                        }
                        break;
                    case 2:
                        if ($car = $this->searchUnParkCar()) {
                            $this->checkPolutionPercentage();
                            $car->followExit();
                        }
                        break;
                }
            }
        }
    }

    /**
     * @return bool
     */
    private function searchBusyPlaces() {
        foreach ($this->totalParkingPlaces->getData() as $parkingPlace) {
            if ($parkingPlace->isBusy()) {
                return $parkingPlace;
            }
        }
        return FALSE;
    }

    /**
     * @return bool
     */
    private function searchFreePlaces() {
        foreach ($this->totalParkingPlaces->getData() as $parkingPlace) {
            if ($parkingPlace->isFree()) {
                return $parkingPlace;
            }
        }
        return FALSE;
    }

    /**
     * @return bool
     */
    private function getCarEntry() {
        return $this->getCar(CarEntryEntity::IN);
    }

    /**
     * @param $status
     * @return bool
     */
    private function getCar($status) {
        foreach ($this->carsEntries->get('entry')->getData() as $carEntry) {
            if ($carEntry->getStatus() == $status) {
                return $carEntry;
            }
        }
        return FALSE;
    }

    /**
     * @return bool
     */
    private function searchUnParkCar() {
        return $this->getCar(CarEntryEntity::UN_PARK);
    }

    /**
     * @return int
     */
    private function countCarsWithRunningEngine()
    {
        $numCarsWithRunningEngine = 0;
        $carEntries = $this->carsEntries->get('entry')->getData();
        foreach ($carEntries as $car) {
            switch ($car->getStatus()) {
                case CarEntryEntity::IN:
                case CarEntryEntity::UN_PARK:
                case CarEntryEntity::FOLLOW_EXIT:
                    $numCarsWithRunningEngine++;
                    break;
            }
        }
        return $numCarsWithRunningEngine;
    }

    /**
     * @throws \Exception
     */
    private function checkPolutionPercentage()
    {
        $carsWithEngineRunning = $this->countCarsWithRunningEngine();
        $totalParkingPlaces = $this->totalParkingPlaces->count();
        $polutionPercentage = number_format(($carsWithEngineRunning / $totalParkingPlaces), 2) * 100;
        $polutionDifference = $polutionPercentage - $this->polutionBuffer->get('polutionBuffer');
        if ($polutionDifference > 0) {
            throw new \Exception('PolutionBufferException: Cars with runing engine exceded the limit with %s%%', $polutionDifference);
        }
    }

}
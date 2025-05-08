<?php

namespace App\Modules\WeatherGov;

class PointsModel
{
    public Object $payloadObject;

    /**
     * @param $result
     * @return $this
     * @throws \Exception
     */
    public function newInstance($result = null): PointsModel
    {
        if ($result === null) {
            throw new \Exception("Could not create points object, no data");
        }
        $instance = new static();

        $instance->payloadObject = $result;
        return $instance;
    }

    public function formatGridPointString(): string
    {
        if (!isset($this->payloadObject->properties->forecast)) {
            return '';
        }

        $gridPointString = $this->payloadObject->properties->forecast;
        $explode = explode('gridpoints/', $gridPointString);
        $explode2 = explode('/forecast', $explode[1]);

        return $explode2[0];
    }
}

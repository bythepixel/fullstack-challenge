<?php

namespace App\Modules\WeatherGov;

class ForecastModel
{
    public int $forecastTTL;//int number of seconds
    public  $forecasts;

    public function newInstance(&$result, &$request=null): ForecastModel
    {
        $instance = new static();

        $instance->forecasts = $result!==null ? $result->properties->periods : [];

        //TODO check the expires header and set valid for that long, but we can start with 1hr since it was in reqs
        $instance->setTTL($request);
        return $instance;

    }

    public function format($limit)
    {
        $output = [];

        $ctr = 0;
        foreach ($this->forecasts as $forecast) {
            if($ctr >= $limit){
                break;
            }

            $output[] = (object)[
                'name'=>$forecast->name,
                "temperature"=> $forecast->temperature .' '. $forecast->temperatureUnit,
                "icon" => $forecast->icon,
                "shortForecast" => $forecast->shortForecast,
                "detailedForecast" => $forecast->detailedForecast,
                "windSpeed" => $forecast->windSpeed,
                "windDirection" => $forecast->windDirection,
            ];

            $ctr++;
        }

        return $output;
    }

    public function setTTL(&$request)
    {
        //TODO set ttl from the cache control header, not needed on first run since req is no more than 1 hr old
        $this->forecastTTL = 6000;
    }


}

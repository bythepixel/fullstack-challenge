<?php

namespace App\Services;

use App\Exceptions\GridPointException;
use App\Modules\WeatherGov\ForecastModel;
use App\Modules\WeatherGov\PointsModel;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherFetchService
{
    public LoggableHttpWrapper $httpWrapper;
    public PointsModel $pointsModel;
    public ForecastModel $forcastModel;

    protected array $defaultHeaders = [];
    public array $cachesUsed = [
        'grid' => true,
        'weather' => true,
    ];


    public function __construct(
        LoggableHttpWrapper $httpWrapper,
        PointsModel $pointsModel,
        ForecastModel $forcastModel
    ) {
        $this->httpWrapper = $httpWrapper;
        $this->pointsModel = $pointsModel;
        $this->forcastModel = $forcastModel;

        $this->defaultHeaders = [
            'headers' => [
                'User-Agent' => config('app.name')
            ],
            'verify' => false,
            //dont have ssl cert chain resolved on this setup, mark this true when configured in higher env
        ];
    }

    /**
     * Takes lat/lon and a forced refresh option,
     * cacheOnly - allows getting a result only if cached, which allows for a couple layers of cache / service data being missed
     * returns array of weather based on what is returned by the model instance,
     *
     * @param string $lat --using as string to prevent type juggling etc
     * @param string $lon
     * @param bool $force
     * @return array
     */
    public function getforecast(
        string $lat,
        string $lon,
        bool $force = false,
        bool $cacheOnly = false
    ): array {
        try {
            $forecastCacheKeyBase = config('weather_gov_api.cacheKeys.weather');

            $gridPointResult = $this->coordinatesToGridpoint($lat, $lon, $cacheOnly);

            $forecast = Cache::get($forecastCacheKeyBase . '_' . $gridPointResult, []);
//echo 'cached forecast: ' . print_r($forecast,true) . '<br>\n';
            if (($forecast === [] || $force === true) && $cacheOnly === false) {
                $forecastResult = $this->getHourlyForecastAtGridPoint($gridPointResult);
                $forecast = $forecastResult->format(config('weather_gov_api.forecastLimit'));
//echo 'forecast for ' . $lat . ' ' . $lon . ' ' . print_r($forecast, true) . PHP_EOL . '\n';

                Cache::put($forecastCacheKeyBase . '_' . $gridPointResult, $forecast, $forecastResult->forecastTTL);
            }
            return $forecast;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getExtendedForecast(
        string $lat,
        string $lon,
        bool $force = false,
        bool $cacheOnly = false
    ): array {
        try {
            $extendedForecastCacheKeyBase = config('weather_gov_api.cacheKeys.weather_extended');

            $gridPointResult = $this->coordinatesToGridpoint($lat, $lon, $cacheOnly);

            $forecast = Cache::get($extendedForecastCacheKeyBase . '_' . $gridPointResult, []);
//echo 'cached extended forecast: ' . print_r($forecast,true) . '<br>\n';
            if (($forecast === [] || $force) && $cacheOnly === false) {
                $forecastResult = $this->getExtendedForecastAtGridPoint($gridPointResult);
                $forecast = $forecastResult->format(config('weather_gov_api.extendedForecastLimit'));
//echo 'extended forecast for ' . $lat . ' ' . $lon . ' ' . print_r($forecast, true) . PHP_EOL . '\n';
                Cache::put(
                    $extendedForecastCacheKeyBase . '_' . $gridPointResult,
                    $forecast,
                    $forecastResult->forecastTTL
                );
            }
            return $forecast;
        } catch (\Exception $e) {
//echo 'error in extended forecast: ' . $e->getMessage() . '\n' ;
            Log::error('error in extended forecast: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     *  //string of the form 'TOP/32,81' which should be the forecasting center corresponding to the bounding box
     *  //TODO can cut out a cache use if we set this in the db instead
     * @param string $lat
     * @param string $lon
     * @return string
     * @throws GridPointException
     * @throws GuzzleException
     */
    public function coordinatesToGridpoint(string $lat, string $lon, bool $cacheOnly = false): string
    {
        try {
            $gridpointsCacheKeyBase = config('weather_gov_api.cacheKeys.gridpoints');
            $cacheKey = str_replace(['-', '.'], ['', ''], $gridpointsCacheKeyBase . '_' . $lat . '_' . $lon);

            $gridCache = Cache::get($cacheKey, '');
//echo print_r(['found', $gridCache, $lat, $lon], true) . '<br>' . PHP_EOL;
            if (!empty($gridCache) || $cacheOnly) {
                return $gridCache;
            }

            $this->cachesUsed['grid'] = false;
            $pointString = $this->pointString($lat, $lon);

            $result = $this->httpWrapper->request(
                'GET',
                config('weather_gov_api.baseUrl') . 'points/' . $pointString,
                $this->defaultHeaders
            );

            $json = json_decode((string)$result->getBody());

            $model = $this->pointsModel->newInstance($json);
            $formatted = $model->formatGridPointString();

            //
            Cache::put(
                $cacheKey,
                $formatted,
                (60 * 60 * 24 * 7)
            );

//echo print_r(['putting new', $formatted, $lat, $lon], true) . ' ' . PHP_EOL;
            return $formatted;
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            $this->throwCoordinateFetchError($e);
        }
    }

    /**
     * The point api appears to require 4 digits of precision, truncate to that
     * @param string $lat
     * @param string $lon
     * @return string
     */
    public function pointString(string $lat, string $lon): string
    {
        return substr($lat, 0, strpos($lat, '.'))
            . substr($lat, strpos($lat, '.'), 5)
            . ','
            . substr($lon, 0, strpos($lon, '.'))
            . substr($lon, strpos($lon, '.'), 5);
    }

    public function getHourlyForecastAtGridPoint(
        string &$gridPoint
    ): ForecastModel {
        $this->cachesUsed['weather'] = false;

        //try{
        //https://api.weather.gov/gridpoints/TOP/32,81/forecast/hourly
        $result = $this->httpWrapper->request(
            'GET',
            config('weather_gov_api.baseUrl') . 'gridpoints/' . $gridPoint . '/forecast/hourly',
            $this->defaultHeaders
        );
        $body = json_decode((string)$result->getBody());
        //do the body get then json decode then dump in
        return $this->forcastModel->newInstance($body, $result);
        //} catch(\Exception $e){
        // $this->forcastModel->newInstance($result)
        // }
    }

    public function getExtendedForecastAtGridPoint(
        string &$gridPoint
    ): ForecastModel {
        $this->cachesUsed['weather'] = false;

        //try{
        //https://api.weather.gov/gridpoints/TOP/32,81/forecast
        $result = $this->httpWrapper->request(
            'GET',
            config('weather_gov_api.baseUrl') . 'gridpoints/' . $gridPoint . '/forecast',
            $this->defaultHeaders
        );
        $body = json_decode((string)$result->getBody());
        //do the body get then json decode then dump in
        return $this->forcastModel->newInstance($body, $result);
        //} catch(\Exception $e){
        // $this->forcastModel->newInstance($result)
        // }
    }


    public function getWeatherByIDSet(
        Collection &$users,
        bool $force = false,
        bool $cacheOnly = false,
        bool $extended = false
    ) {
        $ids = $users->pluck('id')->toArray();
        $key = config('weather_gov_api.cacheKeys.currentUserWeather')
            . ($extended ? '_extended' : '')
            . '_' . implode('.', $ids);

   // echo 'cache key'. $key . PHP_EOL;
        $weather = Cache::get($key, null);
//    echo 'cache weather: ' . $weather . PHP_EOL;
        if ($weather !== null && ($cacheOnly || !$force) ) {
            $weatherJson = json_decode($weather, true);
        } else {
            $weatherJson = [];
            foreach ($users as $user) {
                if ($extended) {
                    $weatherJson[$user->id] = $this->getExtendedForecast(
                        $user->latitude,
                        $user->longitude,
                        $force,
                        $cacheOnly
                    );
                } else {
                    $weatherJson[$user->id] = $this->getforecast(
                        $user->latitude,
                        $user->longitude,
                        $force,
                        $cacheOnly
                    );
                }
            }
            Cache::put($key, json_encode($weatherJson), 60*60);
        }

        return $weatherJson;
    }

    /**
     * @param \Exception $exception
     * @return GridPointException
     * @throws GridPointException
     */
    public function throwCoordinateFetchError(\Exception $exception): GridPointException
    {
        throw new GridPointException('Grid Point Exception: ' . $exception->getMessage());
    }
}

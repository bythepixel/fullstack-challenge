<?php

return [
    'baseUrl' => 'http://api.weather.gov/',
    'cacheKeys' => [
        'gridpoints'=>'lat_lon_gridpoints',
        'weather'=>'gridpoint_weather',
        'weather_extended'=>'gridpoint_weather_extended',
        'currentUserWeather'=>'hourly_weather'
    ],
    'forecastLimit'=>1,
    'extendedForecastLimit'=>5,
    'dashboard_page_size'=>20,
];

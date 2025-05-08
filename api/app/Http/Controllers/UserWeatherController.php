<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WeatherFetchService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserWeatherController extends Controller
{
    /**
     * This is a compromise on cache space to allow for fastest base case page selection
     * If we had literally any other requirements this would be written to grab the cached user record,
     * or pulling gridset coords from the db, then pulling only the user cached record
     * Cache expires might also be a little loose given the req for no more than 1 hr old, but that can change based on the run time of the cache builders
     * TODO pagination, ret the complete object from getWeatherByIDSet
     * @param Request $request
     * @param User $user
     * @param WeatherFetchService $weatherFetchService
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request, User $user, WeatherFetchService $weatherFetchService)
    {
        try {
            $output = [];

            $all = $user->query()
                ->select('id', 'name', 'latitude', 'longitude')
                ->get();

            $weather = $weatherFetchService->getWeatherByIDSet($all, false, true);

            foreach ($all as $item) {
                $item->forecast = $weather[$item->id] ?? [];
                $output[] =  $item;
            }

            $meta = [
                'recordCount' => count($output),
                'function' => 'dashboard',
                'requestTime' => Carbon::now()->toDateTimeString()
            ];

            return $this->jsonResponseFormatter($output, $meta, 200);
        } catch (\Exception $exception) {
            $error = ['error' => 'Could not complete request', 'message' => 'please contact support'];
            return $this->jsonResponseFormatter($output, $error, 500);
        }
    }

    public function userForecast(int $userId, User $user, WeatherFetchService $weatherFetchService)
    {
        try {
            $user = $user->query()
                ->select('id', 'name', 'latitude', 'longitude')
                ->where("id", '=', $userId)
                ->first();

            if ($user === null) {
                return response()->json(['message' => 'User not found', 'data' => []], 404);
            }

            $weatherFormatted = $weatherFetchService->getExtendedForecast($user->latitude, $user->longitude);
            $meta = [
                'recordCount' => count($weatherFormatted),
                'function' => 'UserForecast',
                'requestTime' => Carbon::now()->toDateTimeString(),
                'cacheHits' => $weatherFetchService->cachesUsed,
            ];

            return $this->jsonResponseFormatter($weatherFormatted, $meta, 200);
        } catch (\Exception $exception) {
            $error = ['error' => 'Could not complete request', 'message' => 'please contact support'];
            return $this->jsonResponseFormatter($output, $error, 500);
        }
    }

    /**
     * Too much duplication, maybe just make this trigger the job instead?
     * @param int $userId
     * @param User $user
     * @param WeatherFetchService $weatherFetchService
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(int $userId, User $user, WeatherFetchService $weatherFetchService)
    {
        //  try {
        $user = $user->query()
            ->where("id", '=', $userId)
            ->first();

        if ($user === null) {
            return response()->json(['message' => 'User not found', 'data' => []], 404);
        }

        $weatherFormatted = $weatherFetchService->getForecast($user->latitude, $user->longitude, true,false);
        $extendedWeatherFormatted = $weatherFetchService->getExtendedForecast($user->latitude, $user->longitude, true,false);

        $out = [
            'weather' => $weatherFormatted,
            'extended' => $extendedWeatherFormatted,
        ];

        $meta = [
            'recordCount' => count($weatherFormatted),
            'recordCountExtended' => count($extendedWeatherFormatted),
            'function' => 'UserRefresh',
            'requestTime' => Carbon::now()->toDateTimeString(),
            'cacheHits' => $weatherFetchService->cachesUsed,
        ];

        return $this->jsonResponseFormatter($out, $meta, 200);
        /*  } catch (\Exception $exception) {

              return response()->json(['message' => 'Weather could not be fetched', 'data'=>[]], 500);
          }*/
    }
}

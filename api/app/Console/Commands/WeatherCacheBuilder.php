<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\WeatherFetchService;
use Illuminate\Console\Command;

class WeatherCacheBuilder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache-weather {--extended}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Weather Caches';

    protected WeatherFetchService $weatherFetchService;
    protected User $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user, WeatherFetchService $weatherFetchService)
    {
        parent::__construct();

        $this->user = $user;
        $this->weatherFetchService = $weatherFetchService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //TODO set the page size and cache the pages of ids to allow a solid baseline    then foreach over the pages
        $users = $this->user->query()
            ->select('id', 'name', 'latitude', 'longitude')
            //->whereIn('id',[1,2,3,4,5])
            ->get();

        if ($this->option('extended')===true) {
            $this->weatherFetchService->getWeatherByIDSet($users, true, false, true);
        } else {
            $this->weatherFetchService->getWeatherByIDSet($users, true, false, false);
        }
    }


}

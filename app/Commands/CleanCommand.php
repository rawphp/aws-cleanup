<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CleanCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'clean';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Deletes all aws resources in the default aws profile';

    public function handle(): int
    {
        /*
         * following is just an idea of how to structure the cleanup work
         */
        $resources = ['S3 bucket 1' => true, 'S3 bucket 2' => false];

        foreach ($resources as $resource => $result)
        {
            $this->task("deleting $resource", function () use ($result) {
                return $result;
            });
        }

        return self::SUCCESS;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}

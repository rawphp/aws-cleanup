<?php

namespace App\Commands;

use App\Services\AWSService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = "fetch
    {--onlyRegions= : Limit regions to this space-delimited list}
    {--outputJson : Output data as JSON}
    ";

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all AWS resources in the account.';

    public function handle(AWSService $awsService): int
    {
        $outputJson = $this->option('outputJson');
        $regions = array_filter(explode(' ', $this->option('onlyRegions', '')));

        $onlyRegions = empty($regions) ? config('aws.regions') : $regions;

        $resourceGroups = $awsService->list($onlyRegions);

        $list = [];

        foreach ($resourceGroups as $group => $groupRegions) {
            foreach ($groupRegions as $region => $resources) {
                foreach ($resources as $resource) {
                    $list[] = [
                        'type' => $group,
                        'id' => $resource['id'],
                        'name' => $resource['name'],
                        'region' => $region,
                    ];
                }
            }
        }

        if ($outputJson) {
            dump(json_encode($list, JSON_PRETTY_PRINT));
        } else {
            $this->table(
                ['type', 'id', 'name', 'region'],
                $list
            );
        }

        return self::SUCCESS;
    }
}

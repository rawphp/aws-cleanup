<?php

namespace App\Services;

use Aws\Ec2\Ec2Client;

class AWSService
{
    public function list(): array
    {
        // security groups
        $securityGroups = $this->getSecurityGroups();

        // ec2 instances
        $instances = $this->getEc2Instances();

        // ec2 load balancers
        // volumes
        // auto-scaling groups
        // elastic IPs
        // key pairs
        // snapshots



        return [
            'securityGroups' => $securityGroups,
            'ec2Instances' => $instances,
        ];
    }

    public function getSecurityGroups(): array
    {
        $groups = [];

        foreach (config('aws.regions') as $region) {
            dump('Checking security groups for region: ' . $region);
            $groupsByRegion = [];

            $ec2Client = resolve(Ec2Client::class, ['region' => $region]);

            $securityGroups = $ec2Client->describeSecurityGroups();

            foreach ($securityGroups->get('SecurityGroups') as $group) {
                $groupsByRegion[] = [
                    'name' => $group['GroupName'],
                    'id' => $group['GroupId'],
                ];
            }

            $groups[$region] = $groupsByRegion;
        }

        return $groups;
    }

    public function getEc2Instances(): array
    {
        $instances = [];

        foreach (config('aws.regions') as $region) {
            dump('Checking ec2 instances for region: ' . $region);
            $instancesByRegion = [];

            /** @var Ec2Client $ec2Client */
            $ec2Client = resolve(Ec2Client::class, ['region' => $region]);

            $result = $ec2Client->describeInstances();

            foreach ($result->get('Reservations') as $reservation) {
                foreach ($reservation['Instances'] as $instance) {
                    $instancesByRegion[] = [
                        'id' => $instance['InstanceId'],
                        'name' => $instance['Tags'][0]['Value'],
                    ];
                }
            }

            $instances[$region] = $instancesByRegion;
        }

        return $instances;
    }
}

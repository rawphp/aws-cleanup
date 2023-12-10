<?php

namespace App\Services;

use Aws\Ec2\Ec2Client;
use Aws\S3\S3Client;
use Exception;

class AWSService
{
    protected array $regions = [];

    public function list(array $regions): array
    {
        $this->validateRegions($regions);

        // security groups
        $securityGroups = $this->getSecurityGroups($regions);

        // ec2 instances
        $instances = $this->getEc2Instances($regions);

        // ec2 load balancers
        // volumes
        $volumes = $this->getVolumes($regions);

        // auto-scaling groups
        // elastic IPs
        // key pairs
        // snapshots

        // S3 buckets
        $buckets = $this->getS3Buckets($regions[0]);


        return [
            'securityGroups' => $securityGroups,
            'ec2Instances' => $instances,
            'volumes' => $volumes,
            's3Buckets' => $buckets,
        ];
    }

    public function getSecurityGroups(array $regions): array
    {
        $groups = [];

        foreach ($regions as $region) {
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

    public function getEc2Instances(array $regions): array
    {
        $instances = [];

        foreach ($regions as $region) {
            dump('Checking ec2 instances for region: ' . $region);
            $instancesByRegion = [];

            /** @var Ec2Client $ec2Client */
            $ec2Client = resolve(Ec2Client::class, ['region' => $region]);

            $result = $ec2Client->describeInstances();

            foreach ($result->get('Reservations') as $reservation) {
                foreach ($reservation['Instances'] as $instance) {
                    $instancesByRegion[] = [
                        'id' => $instance['InstanceId'],
                        'name' => $this->getNameIfAvailable($instance)
                    ];
                }
            }

            $instances[$region] = $instancesByRegion;
        }

        return $instances;
    }

    public function getVolumes(array $regions): array
    {
        $instances = [];

        foreach ($regions as $region) {
            dump('Checking volumes for region: ' . $region);
            $instancesByRegion = [];

            /** @var Ec2Client $ec2Client */
            $ec2Client = resolve(Ec2Client::class, ['region' => $region]);

            $result = $ec2Client->describeVolumes();

            foreach ($result->get('Volumes') as $volume) {
                    $instancesByRegion[] = [
                        'id' => $volume['VolumeId'],
                        'name' => $this->getNameIfAvailable($volume)
                    ];
            }

            $instances[$region] = $instancesByRegion;
        }

        return $instances;
    }

    public function getS3Buckets(string $region): array
    {
        $buckets = [];

        dump('Checking S3 buckets');

        /** @var S3Client $s3Client */
        $s3Client = resolve(S3Client::class, ['region' => $region]);

        $result = $s3Client->listBuckets();

        foreach ($result->get('Buckets') as $bucket) {
            $buckets[] = [
                'id' => $bucket['Name'],
                'name' => $bucket['Name'],
            ];
        }

        return ['global' => $buckets];
    }

    /**
     * Deleting an instance appears to delete the associated main volume at the same time.
     */
    public function deleteInstance(string $id, string $region): bool
    {
        dump('Deleting Ec2 Instance: ' . $id . ' in region: ' . $region);

        /** @var Ec2Client $ec2Client */
        $ec2Client = resolve(Ec2Client::class, ['region' => $region]);

//        $ec2Client->deleteKeyPair();
//        $ec2Client->deleteSecurityGroup();
//        $ec2Client->deleteVolume();
//        $ec2Client->deleteVpc();

        $result = $ec2Client->terminateInstances([
            'InstanceIds' => [$id],
        ]);

        return false;
    }

    /**
     * @throws Exception
     */
    public function validateRegions(array $regions): bool
    {
        foreach ($regions as $region) {
            if (!in_array($region, config('aws.regions'))) {
                throw new Exception('"' . $region . '" is not a valid region.');
            }
        }

        return true;
    }

    public function getNameIfAvailable(array $resource): string
    {
        $tags = [];

        if (isset($resource['Tags'])) {
            $tags = $resource['Tags'];
        }

        foreach ($tags as $tag) {
            if ($tag['Key'] === 'Name') {
                return $tag['Value'];
            }
        }

        return '';
    }
}
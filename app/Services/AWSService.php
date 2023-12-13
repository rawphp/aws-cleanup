<?php

namespace App\Services;

use Aws\AutoScaling\AutoScalingClient;
use Aws\Ec2\Ec2Client;
use Aws\S3\S3Client;
use Exception;

/**
 * Represents an AWS Service for managing resources.
 */
class AWSService
{
    protected array $regions = [];

    public function list(array $regions, bool $excludeDefault = false): array
    {
        $this->validateRegions($regions);

        // security groups
        $securityGroups = $this->getSecurityGroups($regions);

        // ec2 instances
        $instances = $this->getEc2Instances($regions);

        // volumes
        $volumes = $this->getVolumes($regions);

        // auto-scaling groups
        $scalingGroups = $this->getAutoScalingGroups($regions);

        // elastic IPs
        $elasticIps = $this->getElasticIPs($regions);

        // key pairs
        $keyPairs = $this->getKeyPairs($regions);

        // snapshots
        $snapshots = $this->getSnapshots($regions);

        // availability zones
        $availabilityZones = $this->getAvailabilityZones($regions);

        // subnets
        $subnets = $this->getSubnets($regions);

        // vpcs
        $vpcs = $this->getVpcs($regions);

        // S3 buckets
        $buckets = $this->getS3Buckets($regions[0]);

        return [
            'securityGroups' => $securityGroups,
            'ec2Instances' => $instances,
            'volumes' => $volumes,
            'snapshots' => $snapshots,
            'keyPairs' => $keyPairs,
            'elasticIPs' => $elasticIps,
            's3Buckets' => $buckets,
            'vpcs' => $vpcs,
            'subnets' => $subnets,
            'availabilityZones' => $availabilityZones,
        ];
    }

    public function getAvailabilityZones(array $regions): array
    {
        return $this->handleDescribeResource($regions,
            fn(string $region) => resolve(Ec2Client::class, ['region' => $region]),
            'describeAvailabilityZones',
            'AvailabilityZones',
            'ZoneId',
            'ZoneName',
        );
    }

    public function getSubnets(array $regions): array
    {
        return $this->handleDescribeResource($regions,
            fn(string $region) => resolve(Ec2Client::class, ['region' => $region]),
            'describeSubnets',
            'Subnets',
            'SubnetId',
        );
    }

    public function getSnapshots(array $regions): array
    {
        return $this->handleDescribeResource($regions,
            fn(string $region) => resolve(Ec2Client::class, ['region' => $region]),
            'describeSnapshots',
            'Snapshots',
            'SnapshotId',
        );
    }

    public function getSecurityGroups(array $regions): array
    {
        return $this->handleDescribeResource($regions,
            fn(string $region) => resolve(Ec2Client::class, ['region' => $region]),
            'describeSecurityGroups',
            'SecurityGroups',
            'GroupId',
            'GroupName',
        );
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
                        'name' => $this->getNameFromTagsIfAvailable($instance)
                    ];
                }
            }

            $instances[$region] = $instancesByRegion;
        }

        return $instances;
    }

    public function getVolumes(array $regions): array
    {
        return $this->handleDescribeResource($regions,
            fn(string $region) => resolve(Ec2Client::class, ['region' => $region]),
            'describeVolumes',
            'Volumes',
            'VolumeId',
        );
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

    public function getKeyPairs(array $regions): array
    {
        return $this->handleDescribeResource($regions,
            fn(string $region) => resolve(Ec2Client::class, ['region' => $region]),
            'describeKeyPairs',
            'KeyPairs',
            'KeyPairId',
            'KeyName',
        );
    }

    public function getElasticIPs(array $regions): array
    {
        return $this->handleDescribeResource($regions,
            fn(string $region) => resolve(Ec2Client::class, ['region' => $region]),
            'describeAddresses',
            'Addresses',
            'AllocationId',
            'PublicIp',
        );
    }

    public function getAutoScalingGroups(array $regions): array
    {
        return $this->handleDescribeResource($regions,
            fn(string $region) => resolve(AutoScalingClient::class, ['region' => $region]),
            'describeAutoScalingGroups',
            'AutoScalingGroups',
            'AutoScalingGroupARN',
            'AutoScalingGroupName',
        );
    }

    public function getVpcs(array $regions): array
    {
        return $this->handleDescribeResource($regions,
            fn(string $region) => resolve(Ec2Client::class, ['region' => $region]),
            'describeVpcs',
            'Vpcs',
            'VpcId',
        );
    }

    public function handleDescribeResource(
        array       $regions,
        callable    $getClient,
        string      $action,
        string      $resourceKey,
        string      $idName,
        string|null $resourceName = null,
    ): array
    {
        $data = [];

        foreach ($regions as $region) {
            dump("Checking $resourceKey for region: $region");
            $dataByRegion = [];

            $client = $getClient($region);
            $result = $client->$action();

            if ($result->get('@metadata')['statusCode'] !== 200) {
                return [];
            }

            dump(json_encode($result->toArray()));

            foreach ($result->get($resourceKey) as $resource) {
                $dataByRegion[] = [
                    'id' => $resource[$idName],
                    'name' => !is_null($resourceName)
                        ? $resource[$resourceName]
                        : $this->getNameFromTagsIfAvailable($resource)
                ];
            }

            $data[$region] = $dataByRegion;
        }

        return $data;
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

    public function getNameFromTagsIfAvailable(array $resource): string
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

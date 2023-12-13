<?php

use App\Services\AWSService;
use Aws\AutoScaling\AutoScalingClient;
use Aws\Ec2\Ec2Client;
use Aws\Result;
use Aws\S3\S3Client;
use Mockery\MockInterface;

describe('list', function () {
    test('getSecurityGroups', function () {
        $service = new AWSService();

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('security-groups'));

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeSecurityGroups')->andReturn($result);
        });

        $resources = $service->getSecurityGroups(['ap-southeast-2']);

        expect($resources)->toHaveKey('ap-southeast-2');
    });

    test('getEc2Instances', function () {
        $service = new AWSService();

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('ec2-instances'));

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeInstances')->andReturn($result);
        });

        $resources = $service->getEc2Instances(['ap-southeast-2']);

        expect($resources)->toHaveKey('ap-southeast-2');
    });

    test('getSnapshots', function () {
        $service = new AWSService();

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('snapshots'));

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeSnapshots')->andReturn($result);
        });

        $resources = $service->getSnapshots(['ap-southeast-2']);

        expect($resources)->toHaveKey('ap-southeast-2');
    });

    test('getVolumes', function () {
        $service = new AWSService();

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('volumes'));

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeVolumes')->andReturn($result);
        });

        $resources = $service->getVolumes(['ap-southeast-2']);

        expect($resources)->toHaveKey('ap-southeast-2');
    });

    test('getS3Buckets', function () {
        $service = new AWSService();

        mock(S3Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('s3-buckets'));

            app()->bind(S3Client::class, fn() => $mock);

            $mock->expects('listBuckets')->andReturn($result);
        });

        $resources = $service->getS3Buckets('ap-southeast-2');

        expect($resources)->toHaveKey('global');
    });

    test('getKeyPairs', function () {
        $service = new AWSService();

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('key-pairs'));

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeKeyPairs')->andReturn($result);
        });

        $resources = $service->getKeyPairs(['ap-southeast-2']);

        expect($resources)->toHaveKey('ap-southeast-2');
    });

    test('getElasticIPs', function () {
        $service = new AWSService();

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('elastic-ips'));

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeAddresses')->andReturn($result);
        });

        $resources = $service->getElasticIPs(['ap-southeast-2']);

        expect($resources)->toHaveKey('ap-southeast-2');
    });

    test('getAutoScalingGroups', function () {
        $service = new AWSService();

        mock(AutoScalingClient::class, function (MockInterface $mock) {
            $result = new Result(loadJson('auto-scaling-groups'));

            app()->bind(AutoScalingClient::class, fn() => $mock);

            $mock->expects('describeAutoScalingGroups')->andReturn($result);
        });

        $resources = $service->getAutoScalingGroups(['ap-southeast-2']);

        expect($resources)->toHaveKey('ap-southeast-2');

    });

    test('getLoadBalancers', function () {
        $service = new AWSService();

        mock(AutoScalingClient::class, function (MockInterface $mock) {
            $result = new Result(loadJson('load-balancers'));

            app()->bind(AutoScalingClient::class, fn() => $mock);

            $mock->expects('describeLoadBalancers')->andReturn($result);
        });

        $autoScalingGroups = [
            'ap-southeast-2' => [
                'id' => 'arn:aws:autoscaling:ap-southeast-2:450332024000:autoScalingGroup:3997c447-2d28-4be8-a604-368ac4ac384e:autoScalingGroupName/myapp',
                'name' => 'myapp',
            ],
        ];

        $resources = $service->getLoadBalancers($autoScalingGroups);

        expect($resources)->toHaveKey('ap-southeast-2');
    });

    test('getVpcs', function () {
        $service = new AWSService();

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('vpcs'));

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeVpcs')->andReturn($result);
        });

        $resources = $service->getVpcs(['ap-southeast-2']);

        expect($resources['ap-southeast-2'])->toEqual([
            [
                'id' => 'vpc-0769fc0b342a7b69f',
                'name' => '',
            ]
        ]);
    });

    test('getSubnets', function () {
        $service = new AWSService();

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('subnets'));

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeSubnets')->andReturn($result);
        });

        $resources = $service->getSubnets(['ap-southeast-2']);

        expect($resources['ap-southeast-2'])->toEqual([
            [
                'id' => 'subnet-021e48c4afe2ea361',
                'name' => '',
            ],
            [
                'id' => 'subnet-04744dff95a728867',
                'name' => '',
            ]
        ]);
    });

    test('getAvailabilityZones', function () {
        $service = new AWSService();

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(loadJson('availability-zones'));

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeAvailabilityZones')->andReturn($result);
        });

        $resources = $service->getAvailabilityZones(['ap-southeast-2']);

        expect($resources['ap-southeast-2'])->toEqual([
            [
                'id' => 'apse2-az3',
                'name' => 'ap-southeast-2a',
            ],
            [
                'id' => 'apse2-az1',
                'name' => 'ap-southeast-2b',
            ],
            [
                'id' => 'apse2-az2',
                'name' => 'ap-southeast-2c',
            ]
        ]);
    });

    test('validates region list successfully', function () {
        $service = new AWSService();

        expect($service->validateRegions(['ap-southeast-2']))->toBeTrue();
        expect($service->validateRegions(['ap-southeast-2', 'us-west-2']))->toBeTrue();
    });

    describe('validate regions', function () {
        test('invalid region throws exception', function () {
            $service = new AWSService();

            expect($service->validateRegions(['fake']));
        })->throws('"fake" is not a valid region.');

        test('invalid region in list throws exception', function () {
            $service = new AWSService();

            expect($service->validateRegions(['ap-southeast-2', 'us-west-2', 'another-fake']));
        })->throws('"another-fake" is not a valid region.');
    });

    test('it_can_return_name_if_available', function () {
        $service = new AWSService();

        $data = [
            'Tags' => [
                [
                    'Key' => 'Name',
                    'Value' => 'value',
                ]
            ],
        ];

        $result = $service->getNameFromTagsIfAvailable($data);

        $this->assertSame('value', $result);
    });

    test('it_returns_empty_string_when_no_name_is_present', function () {
        $service = new AWSService();

        $data = [
            'Tags' => [
                [
                    'Key' => 'OtherName',
                    'Value' => 'value',
                ]
            ],
        ];

        $result = $service->getNameFromTagsIfAvailable($data);

        $this->assertSame('', $result);
    });

    test('it_returns_empty_string_when_no_name_is_tagged', function () {
        $service = new AWSService();

        $mockWithoutNameTag = ['Tags' => [['Key' => 'NotName', 'Value' => 'Test Value']]];

        $result = $service->getNameFromTagsIfAvailable($mockWithoutNameTag);

        $this->assertSame('', $result);
    });
});

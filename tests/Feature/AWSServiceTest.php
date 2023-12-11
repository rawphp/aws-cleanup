<?php

use App\Services\AWSService;
use Aws\Ec2\Ec2Client;
use Aws\ElasticLoadBalancing\ElasticLoadBalancingClient;
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

    test('getLoadBalancers', function () {
        $service = new AWSService();

        mock(ElasticLoadBalancingClient::class, function (MockInterface $mock) {
            $result = new Result(loadJson('load-balancers'));

            app()->bind(ElasticLoadBalancingClient::class, fn() => $mock);

            $mock->expects('describeLoadBalancers')->andReturn($result);
        });

        $resources = $service->getLoadBalancers(['ap-southeast-2']);

        expect($resources)->toHaveKey('ap-southeast-2');
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

});

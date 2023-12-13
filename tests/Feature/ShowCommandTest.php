<?php

use App\Services\AWSService;

test('show command', function () {
    $mock = Mockery::mock(AWSService::class)->makePartial();

    $mock->expects('getSecurityGroups')->andReturn([]);
    $mock->expects('getEc2Instances')->andReturn([]);
    $mock->expects('getVolumes')->andReturn([]);
    $mock->expects('getAutoScalingGroups')->andReturn([]);
    $mock->expects('getElasticIPs')->andReturn([]);
    $mock->expects('getKeyPairs')->andReturn([]);
    $mock->expects('getSnapshots')->andReturn([]);
    $mock->expects('getAvailabilityZones')->andReturn([]);
    $mock->expects('getSubnets')->andReturn([]);
    $mock->expects('getVpcs')->andReturn([]);
    $mock->expects('getS3Buckets')->andReturn([]);

    app()->instance(AWSService::class, $mock);

    $this->artisan('show --onlyRegions us-west-2')
      // ->expectsOutput('')
         ->assertExitCode(0);
});

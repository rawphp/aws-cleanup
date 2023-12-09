<?php

use App\Services\AWSService;
use Aws\Ec2\Ec2Client;
use Aws\Result;
use Mockery\MockInterface;

describe('list', function () {
    $region = 'ap-southeast-2';

    test('getSecurityGroups', function () use ($region) {
        $service = new AWSService();
        config()->set('aws.regions', [$region]);

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(
                ['SecurityGroups' => [
                    [
                        "Description" => "default VPC security group",
                        "GroupName" => "default",
                        "IpPermissions" => [
                            [
                                "FromPort" => 80,
                                "IpProtocol" => "tcp",
                                "IpRanges" => [
                                    [
                                        "CidrIp" => "0.0.0.0/0",
                                    ],
                                ],
                                "Ipv6Ranges" => [
                                    [
                                        "CidrIpv6" => "::/0",
                                    ],
                                ],
                                "PrefixListIds" => [],
                                "ToPort" => 80,
                                "UserIdGroupPairs" => [],
                            ],
                            [
                                "FromPort" => 5432,
                                "IpProtocol" => "tcp",
                                "IpRanges" => [
                                    [
                                        "CidrIp" => "0.0.0.0/0"
                                    ],
                                ],
                                "Ipv6Ranges" => [
                                    [
                                        "CidrIpv6" => "::/0",
                                    ],
                                ],
                                "PrefixListIds" => [],
                                "ToPort" => 5432,
                                "UserIdGroupPairs" => [],
                            ],
                            [
                                "IpProtocol" => "-1",
                                "IpRanges" => [],
                                "Ipv6Ranges" => [],
                                "PrefixListIds" => [],
                                "UserIdGroupPairs" => [
                                    [
                                        "GroupId" => "sg-0824a72b0d7b354c8",
                                        "UserId" => "450332024000",
                                    ],
                                ],
                            ],
                            [
                                "FromPort" => 22,
                                "IpProtocol" => "tcp",
                                "IpRanges" => [
                                    [
                                        "CidrIp" => "0.0.0.0/0",
                                    ],
                                ],
                                "Ipv6Ranges" => [
                                    [
                                        "CidrIpv6" => "::/0",
                                    ],
                                ],
                                "PrefixListIds" => [],
                                "ToPort" => 22,
                                "UserIdGroupPairs" => [],
                            ],
                            [
                                "FromPort" => 3306,
                                "IpProtocol" => "tcp",
                                "IpRanges" => [
                                    [
                                        "CidrIp" => "0.0.0.0/0",
                                    ],
                                ],
                                "Ipv6Ranges" => [
                                    [
                                        "CidrIpv6" => "::/0",
                                    ],
                                ],
                                "PrefixListIds" => [],
                                "ToPort" => 3306,
                                "UserIdGroupPairs" => [],
                            ],
                            [
                                "FromPort" => 443,
                                "IpProtocol" => "tcp",
                                "IpRanges" => [
                                    [
                                        "CidrIp" => "0.0.0.0/0",
                                    ],
                                ],
                                "Ipv6Ranges" => [
                                    [
                                        "CidrIpv6" => "::/0",
                                    ],
                                ],
                                "PrefixListIds" => [],
                                "ToPort" => 443,
                                "UserIdGroupPairs" => [],
                            ],
                        ],
                        "OwnerId" => "450332024000",
                        "GroupId" => "sg-0824a72b0d7b354c8",
                        "IpPermissionsEgress" => [
                            [
                                "IpProtocol" => "-1",
                                "IpRanges" => [
                                    [
                                        "CidrIp" => "0.0.0.0/0",
                                    ],
                                ],
                                "Ipv6Ranges" => [],
                                "PrefixListIds" => [],
                                "UserIdGroupPairs" => [],
                            ],
                        ],
                        "VpcId" => "vpc-00cb0341a9dcbb02a",
                    ],
                    [
                        "Description" => "default VPC security group",
                        "GroupName" => "default",
                        "IpPermissions" => [
                            [
                                "IpProtocol" => "-1",
                                "IpRanges" => [],
                                "Ipv6Ranges" => [],
                                "PrefixListIds" => [],
                                "UserIdGroupPairs" => [
                                    [
                                        "GroupId" => "sg-0e8ba8b63a82bbd00",
                                        "UserId" => "450332024000",
                                    ],
                                ],
                            ],
                        ],
                        "OwnerId" => "450332024000",
                        "GroupId" => "sg-0e8ba8b63a82bbd00",
                        "IpPermissionsEgress" => [
                            [
                                "IpProtocol" => "-1",
                                "IpRanges" => [
                                    [
                                        "CidrIp" => "0.0.0.0/0",
                                    ],
                                ],
                                "Ipv6Ranges" => [],
                                "PrefixListIds" => [],
                                "UserIdGroupPairs" => [],
                            ],
                        ],
                        "VpcId" => "vpc-0229f25ebb89db810",
                    ],
                ],
                    "@metadata" => [
                        "statusCode" => 200,
                        "effectiveUri" => "https://ec2.us-east-1.amazonaws.com",
                        "headers" => [
                            "x-amzn-requestid" => "a2d28083-816e-4760-bfd1-e6e7fea0d243",
                            "cache-control" => "no-cache, no-store",
                            "strict-transport-security" => "max-age=31536000; includeSubDomains",
                            "vary" => "accept-encoding",
                            "content-type" => "text/xml;charset=UTF-8",
                            "content-length" => "5825",
                            "date" => "Sat, 09 Dec 2023 00:56:10 GMT",
                            "server" => "AmazonEC2",
                        ],
                        "transferStats" => [
                            "http" => [
                                [],
                            ]
                        ]
                    ]]
            );

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeSecurityGroups')->andReturn($result);
        });

        $resources = $service->getSecurityGroups();

        expect($resources)->toHaveKey('ap-southeast-2');
    });

    test('getEc2Instances', function () use ($region) {
        $service = new AWSService();
        config()->set('aws.regions', [$region]);

        mock(Ec2Client::class, function (MockInterface $mock) {
            $result = new Result(
                [
                    "Reservations" => [
                        0 => [
                            "Groups" => [],
                            "Instances" => [
                                0 => [
                                    "AmiLaunchIndex" => 0,
                                    "ImageId" => "ami-0361bbf2b99f46c1d",
                                    "InstanceId" => "i-0213e29fb529a06c6",
                                    "InstanceType" => "t2.micro",
                                    "KeyName" => "keys",
                                    "LaunchTime" => null,
                                    "Monitoring" => [
                                        "State" => "disabled",
                                    ],
                                    "Placement" => [
                                        "AvailabilityZone" => "ap-southeast-2a",
                                        "GroupName" => "",
                                        "Tenancy" => "default",
                                    ],
                                    "PrivateDnsName" => "ip-172-31-0-52.ap-southeast-2.compute.internal",
                                    "PrivateIpAddress" => "172.31.0.52",
                                    "ProductCodes" => [],
                                    "PublicDnsName" => "",
                                    "State" => [
                                        "Code" => 16,
                                        "Name" => "running",
                                    ],
                                    "StateTransitionReason" => "",
                                    "SubnetId" => "subnet-021e48c4afe2ea361",
                                    "VpcId" => "vpc-0769fc0b342a7b69f",
                                    "Architecture" => "x86_64",
                                    "BlockDeviceMappings" => [
                                        0 => [
                                            "DeviceName" => "/dev/xvda",
                                            "Ebs" => [
                                                "AttachTime" => null,
                                                "DeleteOnTermination" => true,
                                                "Status" => "attached",
                                                "VolumeId" => "vol-0519fefff96814753",
                                            ],
                                        ],
                                    ],
                                    "ClientToken" => "f4203c18-f69e-4ede-a5d4-c187afb2495e",
                                    "EbsOptimized" => false,
                                    "EnaSupport" => true,
                                    "Hypervisor" => "xen",
                                    "NetworkInterfaces" => [
                                        0 => [
                                            "Attachment" => [
                                                "AttachTime" => null,
                                                "AttachmentId" => "eni-attach-0a1747e6dd4d3a5fb",
                                                "DeleteOnTermination" => true,
                                                "DeviceIndex" => 0,
                                                "Status" => "attached",
                                                "NetworkCardIndex" => 0,
                                            ],
                                            "Description" => "",
                                            "Groups" => [
                                                0 => [
                                                    "GroupName" => "launch-wizard-1",
                                                    "GroupId" => "sg-04fb529a0b7cd56b4",
                                                ],
                                            ],
                                            "Ipv6Addresses" => [],
                                            "MacAddress" => "06:5d:17:9c:f9:f7",
                                            "NetworkInterfaceId" => "eni-022b42fb19aa308f8",
                                            "OwnerId" => "450332024000",
                                            "PrivateDnsName" => "ip-172-31-0-52.ap-southeast-2.compute.internal",
                                            "PrivateIpAddress" => "172.31.0.52",
                                            "PrivateIpAddresses" => [
                                                0 => [
                                                    "Primary" => true,
                                                    "PrivateDnsName" => "ip-172-31-0-52.ap-southeast-2.compute.internal",
                                                    "PrivateIpAddress" => "172.31.0.52",
                                                ],
                                            ],
                                            "SourceDestCheck" => true,
                                            "Status" => "in-use",
                                            "SubnetId" => "subnet-021e48c4afe2ea361",
                                            "VpcId" => "vpc-0769fc0b342a7b69f",
                                            "InterfaceType" => "interface",
                                        ],
                                    ],
                                    "RootDeviceName" => "/dev/xvda",
                                    "RootDeviceType" => "ebs",
                                    "SecurityGroups" => [
                                        0 => [
                                            "GroupName" => "launch-wizard-1",
                                            "GroupId" => "sg-04fb529a0b7cd56b4",
                                        ],
                                    ],
                                    "SourceDestCheck" => true,
                                    "Tags" => [
                                        0 => [
                                            "Key" => "Name",
                                            "Value" => "saas",
                                        ],
                                    ],
                                    "VirtualizationType" => "hvm",
                                    "CpuOptions" => [
                                        "CoreCount" => 1,
                                        "ThreadsPerCore" => 1,
                                    ],
                                    "CapacityReservationSpecification" => [
                                        "CapacityReservationPreference" => "open",
                                    ],
                                    "HibernationOptions" => [
                                        "Configured" => false,
                                    ],
                                    "MetadataOptions" => [
                                        "State" => "applied",
                                        "HttpTokens" => "required",
                                        "HttpPutResponseHopLimit" => 2,
                                        "HttpEndpoint" => "enabled",
                                        "HttpProtocolIpv6" => "disabled",
                                        "InstanceMetadataTags" => "disabled",
                                    ],
                                    "EnclaveOptions" => [
                                        "Enabled" => false,
                                    ],
                                    "BootMode" => "uefi-preferred",
                                    "PlatformDetails" => "Linux/UNIX",
                                    "UsageOperation" => "RunInstances",
                                    "UsageOperationUpdateTime" => null,
                                    "PrivateDnsNameOptions" => [
                                        "HostnameType" => "ip-name",
                                        "EnableResourceNameDnsARecord" => false,
                                        "EnableResourceNameDnsAAAARecord" => false,
                                    ],
                                    "MaintenanceOptions" => [
                                        "AutoRecovery" => "default",
                                    ],
                                    "CurrentInstanceBootMode" => "legacy-bios",
                                ],
                            ],
                            "OwnerId" => "450332024000",
                            "ReservationId" => "r-0a8502a00d0175105",
                        ],
                    ],
                    "@metadata" => [
                        "statusCode" => 200,
                        "effectiveUri" => "https://ec2.ap-southeast-2.amazonaws.com",
                        "headers" => [
                            "x-amzn-requestid" => "7fe49387-3fc5-4cb1-923d-95b1dd2f45e8",
                            "cache-control" => "no-cache, no-store",
                            "strict-transport-security" => "max-age=31536000; includeSubDomains",
                            "vary" => "accept-encoding",
                            "content-type" => "text/xml;charset=UTF-8",
                            "content-length" => "7628",
                            "date" => "Sat, 09 Dec 2023 02:03:44 GMT",
                            "server" => "AmazonEC2",
                        ],
                        "transferStats" => [
                            "http" => [
                                0 => [],
                            ],
                        ],
                    ],
                ],
            );

            app()->bind(Ec2Client::class, fn() => $mock);

            $mock->expects('describeInstances')->andReturn($result);
        });

        $resources = $service->getEc2Instances();

        expect($resources)->toHaveKey('ap-southeast-2');
    });
});

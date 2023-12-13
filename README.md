# AWS Cleanup

NOTE: IN ACTIVE DEVELOPMENT - NOT READY FOR PRODUCTION USE

This tool can be ran against an AWS account and delete all existing resources to prevent being charged.

You can optionally exclude some resources.

## Commands

- show
- clean --dry

### Show Command

```shell
aws-cleanup show
```

#### AWS Resources Supported

- Security Groups
- Ec2 Instances
- Volumes
- Auto Scaling Groups
- Elastic IPs
- Key Pairs
- Availability Zones
- Subnets
- VPCs
- S3 Buckets

### Clean Command

```shell
aws-cleanup clean


```

#### AWS Resources Supported

- Ec2 Instances

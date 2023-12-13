# AWS Cleanup

This tool can be ran against an AWS account and delete all existing resources to prevent being charged.

You can optionally exclude some resources.

## Commands

- show
- clean --dry

### Show Command

```shell
php artisan show
```

#### AWS Resources Supported

- Security Groups
- Ec2 Instances
- Volumes
- Scaling Groups
- // Load Balancers (?)
- Elastic IPs
- Key Pairs
- S3 Buckets

### Clean Command

```shell
php artisan delete
```

#### AWS Resources Supported

- Ec2 Instances

<?php

namespace Bangpound\Bref\Bridge\Test\Bref;

use Bangpound\Bref\Bridge\Bref\LambdaLayerVersionArn;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for LambdaLayerVersionArn.
 */
#[CoversClass(LambdaLayerVersionArn::class)]
class LambdaLayerVersionArnTest extends TestCase
{
    /**
     * Test that parse method correctly parses a valid Lambda Layer ARN string.
     */
    public function testParseValidArnString()
    {
        $arnString = 'arn:aws:lambda:us-east-1:123456789012:layer:my-layer:1';
        $parsed = LambdaLayerVersionArn::parse($arnString);

        $this->assertIsArray($parsed);
        $this->assertArrayHasKey('partition', $parsed);
        $this->assertArrayHasKey('service', $parsed);
        $this->assertArrayHasKey('region', $parsed);
        $this->assertArrayHasKey('account_id', $parsed);
        $this->assertArrayHasKey('resource', $parsed);

        $this->assertEquals('aws', $parsed['partition']);
        $this->assertEquals('lambda', $parsed['service']);
        $this->assertEquals('us-east-1', $parsed['region']);
        $this->assertEquals('123456789012', $parsed['account_id']);
        $this->assertEquals('layer:my-layer:1', $parsed['resource']);
    }

    /**
     * Test that parse method correctly extracts resource type, ID, and version.
     */
    public function testParseResourceTypeIdAndVersion()
    {
        $arnString = 'arn:aws:lambda:us-east-1:123456789012:layer:my-layer:1';
        $parsed = LambdaLayerVersionArn::parse($arnString);

        $this->assertArrayHasKey('resource_type', $parsed);
        $this->assertArrayHasKey('resource_id', $parsed);
        $this->assertArrayHasKey('resource_version', $parsed);

        $this->assertEquals('layer', $parsed['resource_type']);
        $this->assertEquals('my-layer', $parsed['resource_id']);
        $this->assertEquals('1', $parsed['resource_version']);
    }

    /**
     * Test that __toString correctly formats the Lambda Layer ARN as expected.
     */
    public function testToStringReturnsCorrectArnFormat()
    {
        $arn = new LambdaLayerVersionArn([
            'arn' => 'arn',
            'partition' => 'aws',
            'service' => 'lambda',
            'region' => 'us-east-1',
            'account_id' => '123456789012',
            'resource' => 'layer:my-layer:1',
            'resource_type' => 'layer',
            'resource_id' => 'my-layer',
            'resource_version' => '1',
        ]);

        $expectedString = 'arn:aws:lambda:us-east-1:123456789012:layer:my-layer:1';
        $this->assertEquals($expectedString, (string)$arn);
    }
}

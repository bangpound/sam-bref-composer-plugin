<?php

namespace Bangpound\Bref\Bridge\Test\Bref;

use Bangpound\Bref\Bridge\Bref\ResourceTypeIdAndVersionTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

#[CoversTrait(ResourceTypeIdAndVersionTrait::class)]
class ResourceTypeIdAndVersionTraitTest extends TestCase
{
    private $classWithTrait;

    protected function setUp(): void
    {
        $this->classWithTrait = new class {
            use ResourceTypeIdAndVersionTrait;

            public array $data;

            public function __construct(array $data = [])
            {
                $this->data = $data;
            }
        };
    }

    /**
     * Test getResourceType when resource_type is present in data.
     */
    public function testGetResourceTypeWhenResourceTypeExists(): void
    {
        $instance = new $this->classWithTrait(['resource_type' => 'custom_type']);
        $this->assertSame('custom_type', $instance->getResourceType());
    }

    /**
     * Test getResourceType when resource_type is not present in data.
     */
    public function testGetResourceTypeWhenResourceTypeDoesNotExist(): void
    {
        $instance = new $this->classWithTrait([]);
        $this->assertSame('layer', $instance->getResourceType());
    }

    /**
     * Test getResourceId when resource_id is present.
     */
    public function testGetResourceIdWhenResourceIdExists(): void
    {
        $instance = new $this->classWithTrait(['resource_id' => '12345']);
        $this->assertSame('12345', $instance->getResourceId());
    }

    /**
     * Test getResourceId when resource_id is not present in data.
     */
    public function testGetResourceIdWhenResourceIdDoesNotExist(): void
    {
        $instance = new $this->classWithTrait([]);
        $this->assertNull($instance->getResourceId());
    }

    /**
     * Test getResourceVersion when resource_version is present in data.
     */
    public function testGetResourceVersionWhenResourceVersionExists(): void
    {
        $instance = new $this->classWithTrait(['resource_version' => '1.0.0']);
        $this->assertSame('1.0.0', $instance->getResourceVersion());
    }

    /**
     * Test getResourceVersion when resource_version is not present in data.
     */
    public function testGetResourceVersionWhenResourceVersionDoesNotExist(): void
    {
        $instance = new $this->classWithTrait([]);
        $this->assertNull($instance->getResourceVersion());
    }

    /**
     * Test withResourceVersion method creates a new instance with updated resource_version.
     */
    public function testWithResourceVersionCreatesNewInstance(): void
    {
        $originalInstance = new $this->classWithTrait(['resource_version' => '1.0.0']);
        $newInstance = $originalInstance->withResourceVersion('2.0.0');

        $this->assertSame('2.0.0', $newInstance->getResourceVersion());
        $this->assertSame('1.0.0', $originalInstance->getResourceVersion());
    }

    /**
     * Test withResourceVersion method does not mutate the original instance.
     */
    public function testWithResourceVersionDoesNotMutateOriginalInstance(): void
    {
        $originalInstance = new $this->classWithTrait(['resource_id' => '12345', 'resource_version' => '1.0.0']);
        $newInstance = $originalInstance->withResourceVersion('2.0.0');

        $this->assertSame('1.0.0', $originalInstance->getResourceVersion());
        $this->assertSame('12345', $originalInstance->getResourceId());
        $this->assertSame('2.0.0', $newInstance->getResourceVersion());
        $this->assertSame('12345', $newInstance->getResourceId());
    }
}

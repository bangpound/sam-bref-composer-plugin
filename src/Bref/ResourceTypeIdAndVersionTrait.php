<?php

namespace Bangpound\Bref\Bridge\Bref;

trait ResourceTypeIdAndVersionTrait
{
    public function getResourceType()
    {
        return $this->data['resource_type'] ?? 'layer';
    }

    public function getResourceId()
    {
        return $this->data['resource_id'] ?? null;
    }

    public function getResourceVersion()
    {
        return $this->data['resource_version'] ?? null;
    }

    public function withResourceVersion($version)
    {
        $data = $this->data;
        $data['resource_version'] = $version;
        return new static($data);
    }

    protected static function parseResourceTypeIdAndVersion(array $data)
    {
        $resourceData = preg_split("/[\/:]/", $data['resource'], 3);
        $data['resource_type'] = $resourceData[0] ?? null;
        $data['resource_id'] = $resourceData[1] ?? null;
        $data['resource_version'] = $resourceData[2] ?? null;
        return $data;
    }
}

<?php

namespace Bangpound\Bref\Bridge\Bref;

use Aws\Arn\Arn;
use Aws\Arn\ArnInterface;

class LambdaLayerVersionArn extends Arn implements ArnInterface
{
    use ResourceTypeIdAndVersionTrait;

    public static function parse($string)
    {
        $data = parent::parse($string);
        return self::parseResourceTypeIdAndVersion($data);
    }

    public function __toString()
    {
        if (!isset($this->string)) {
            $this->data['resource'] = implode(':', [
                $this->getResourceType(),
                $this->getResourceId(),
                $this->getResourceVersion(),
            ]);

            $this->string = parent::__toString();
        }

        return $this->string;
    }
}

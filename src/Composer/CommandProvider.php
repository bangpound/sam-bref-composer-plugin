<?php

namespace Bangpound\Bref\Bridge\Composer;

use Aws\Sdk;
use Bangpound\Bref\Bridge\Command\BrefLayersArnCommand;
use Bangpound\Bref\Bridge\Command\BrefLayersCommand;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return [
            new BrefLayersArnCommand(new Sdk()),
        ];
    }
}

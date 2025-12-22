<?php

namespace Barryvdh\Debugbar\DataFormatter;

use DebugBar\DataFormatter\DataFormatter;

/**
 * Simple DataFormatter based on the deprecated Symfony ValueExporter
 *
 * @deprecated use upstream SimpleFormatter
 * @see https://github.com/symfony/symfony/blob/v3.4.4/src/Symfony/Component/HttpKernel/DataCollector/Util/ValueExporter.php
 */
#[\AllowDynamicProperties]
class SimpleFormatter extends \DebugBar\DataFormatter\SimpleFormatter
{
}

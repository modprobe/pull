<?php declare(strict_types=1);

namespace Pull\Domain;

use MyCLabs\Enum\Enum;

/**
 * @method static UpdateStatus SUCCESS()
 * @method static UpdateStatus FAILED()
 *
 * @extends Enum<string>
 */
class UpdateStatus extends Enum
{
    private const SUCCESS = 'success';
    private const FAILED  = 'failed';
}

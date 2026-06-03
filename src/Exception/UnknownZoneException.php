<?php

declare(strict_types=1);

namespace CommerceGuys\Tax\Exception;

/**
 * Thrown when an unknown zone id is passed to the ZoneRepository.
 */
class UnknownZoneException extends \InvalidArgumentException implements ExceptionInterface
{
}

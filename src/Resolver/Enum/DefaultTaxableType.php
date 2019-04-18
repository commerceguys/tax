<?php

namespace CommerceGuys\Tax\Resolver\Enum;

use CommerceGuys\Enum\AbstractEnum;

/**
 * Enumerates available Default Taxable Types.
 *
 * @codeCoverageIgnore
 */
final class DefaultTaxableType extends AbstractEnum
{
  const GOODS = 'goods';
  const SERVICE = 'service';

  /**
   * Gets the default value.
   *
   * @return string The default value.
   */
  public static function getDefault()
  {
    return static::GOODS;
  }
}

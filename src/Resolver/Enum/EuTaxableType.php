<?php

namespace CommerceGuys\Tax\Resolver\Enum;

use CommerceGuys\Enum\AbstractEnum;

/**
 * Enumerates available EU Taxable Types.
 *
 * @codeCoverageIgnore
 */
final class EuTaxableType extends AbstractEnum
{
    const GOODS = 'goods';
    const GOODS_DISTANCE = 'goods_distance';
    const GOODS_INSTALLED = 'goods_installed';
    const GOODS_ONBOARD = 'goods_onboard';
    const GOODS_POWER = 'goods_power';
    const SERVICE = 'service';
    const SERVICE_BTE = 'service_bte';
    const SERVICE_TRANSPORT_HIRE = 'service_transport_hire';
    const SERVICE_LAND = 'service_land';
    const SERVICE_EVENT_ADMISSION = 'service_event_admission';
    const SERVICE_EVENT_SERVICE = 'service_event_service';
    const SERVICE_INTERMEDIARIES = 'service_intermediaries';
    const SERVICE_GOODS = 'service_goods';
    const SERVICE_TRANSPORT_GOODS = 'service_transport';
    const SERVICE_TRANSPORT_PASSENGER = 'service_transport_passenger';
    const SERVICE_TRANSPORT_SERVICE = 'service_transport_service';

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

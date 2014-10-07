<?php

namespace CommerceGuys\Tax;

use CommerceGuys\Zone\ZoneInterface;

interface TaxTypeInterface
{
    // Rounding modes.
    const ROUND_NONE = 0;
    const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

    /**
     * Gets the tax type id.
     *
     * @return string
     */
    public function getId();

    /**
     * Sets the tax type id.
     *
     * @param string $id The tax type id.
     */
    public function setId($id);

    /**
     * Gets the tax type name.
     *
     * For example, "German VAT".
     *
     * @return string The tax type name.
     */
    public function getName();

    /**
     * Sets the tax type name.
     *
     * @param string $name The tax type name.
     */
    public function setName($name);

    /**
     * Gets whether the tax type is compound.
     *
     * Compound tax is calculated on top of a primary tax.
     * For example, Canada's Provincial Sales Tax (PST) is compound, calculated
     * on a price that already includes the Goods and Services Tax (GST).
     *
     * @return boolean True if the tax type is compound, false otherwise.
     */
    public function isCompound();

    /**
     * Sets whether the tax type is compound.
     *
     * @param boolean $compound Whether the tax type is compound.
     */
    public function setCompound($compound);

    /**
     * Gets the tax type rounding mode.
     *
     * @return int The tax type rounding mode, a ROUND_ constant.
     */
    public function getRoundingMode();

    /**
     * Sets the tax type rounding mode.
     *
     * @param int $roundingMode The tax type rounding mode, a ROUND_ constant.
     */
    public function setRoundingMode($roundingMode);

    /**
     * Gets the tax type zone.
     *
     * @return ZoneInterface The tax type zone.
     */
    public function getZone();

    /**
     * Sets the tax type zone.
     *
     * @param ZoneInterface $zone The tax type zone.
     */
    public function setZone(ZoneInterface $zone);

    /**
     * Gets the tax rates.
     *
     * @return TaxRateInterface[] The tax rates.
     */
    public function getRates();

    /**
     * Sets the tax rates.
     *
     * @param TaxRateInterface[] $rates The tax rates.
     */
    public function setRates($rates);
}

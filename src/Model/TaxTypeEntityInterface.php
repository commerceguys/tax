<?php

namespace CommerceGuys\Tax\Model;

use CommerceGuys\Zone\Model\ZoneEntityInterface;

interface TaxTypeEntityInterface extends TaxTypeInterface
{
    /**
     * Sets the tax type id.
     *
     * @param string $id The tax type id.
     */
    public function setId($id);

    /**
     * Sets the tax type name.
     *
     * @param string $name The tax type name.
     */
    public function setName($name);

    /**
     * Sets the tax type generic label.
     *
     * @param string $genericLabel The tax type generic label.
     */
    public function setGenericLabel($genericLabel);

    /**
     * Sets whether the tax type is compound.
     *
     * @param bool $compound Whether the tax type is compound.
     */
    public function setCompound($compound);

    /**
     * Sets whether the tax type is display inclusive.
     *
     * @param bool $displayInclusive Whether the tax type is display inclusive.
     */
    public function setDisplayInclusive($displayInclusive);

    /**
     * Sets the tax type rounding mode.
     *
     * @param int $roundingMode The tax type rounding mode, a ROUND_ constant.
     */
    public function setRoundingMode($roundingMode);

    /**
     * Sets the tax type zone.
     *
     * @param ZoneEntityInterface $zone The tax type zone.
     */
    public function setZone(ZoneEntityInterface $zone);

    /**
     * Sets the tax type tag.
     *
     * @param string $tag The tax type tag.
     */
    public function setTag($tag);

    /**
     * Sets the tax rates.
     *
     * @param TaxRateEntityInterface[] $rates The tax rates.
     */
    public function setRates($rates);

    /**
     * Adds a tax rate.
     *
     * @param TaxRateEntityInterface $rate The tax rate.
     */
    public function addRate(TaxRateEntityInterface $rate);

    /**
     * Removes a tax rate.
     *
     * @param TaxRateEntityInterface $rate The tax rate.
     */
    public function removeRate(TaxRateEntityInterface $rate);

    /**
     * Checks whether the tax type has a tax rate.
     *
     * @param TaxRateEntityInterface $rate The tax rate.
     *
     * @return bool True if the tax rate was found, false otherwise.
     */
    public function hasRate(TaxRateEntityInterface $rate);
}

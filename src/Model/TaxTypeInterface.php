<?php

namespace CommerceGuys\Tax\Model;

use CommerceGuys\Zone\Model\ZoneInterface;

interface TaxTypeInterface
{
    // Rounding modes.
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
     * Gets the tax type generic label.
     *
     * Used to identify the applied tax in cart and order summaries.
     * Represented by one of the GenericLabel values, it is mapped to a
     * translated string by the implementing application.
     *
     * @return string The tax type generic label.
     */
    public function getGenericLabel();

    /**
     * Sets the tax type generic label.
     *
     * @param string $genericLabel The tax type generic label.
     */
    public function setGenericLabel($genericLabel);

    /**
     * Gets whether the tax type is compound.
     *
     * Compound tax is calculated on top of a primary tax.
     * For example, Canada's Provincial Sales Tax (PST) is compound, calculated
     * on a price that already includes the Goods and Services Tax (GST).
     *
     * @return bool True if the tax type is compound, false otherwise.
     */
    public function isCompound();

    /**
     * Sets whether the tax type is compound.
     *
     * @param bool $compound Whether the tax type is compound.
     */
    public function setCompound($compound);

    /**
     * Gets whether the tax type is display inclusive.
     *
     * E.g. US sales tax is not display inclusive, a $5 price is shown as $5
     * even if a $1 tax has been calculated. In France, a 5€ price is shown as
     * 6€ if a 1€ tax was calculated, because French VAT is display inclusive.
     *
     * @return bool True if the tax type is display inclusive, false otherwise.
     */
    public function isDisplayInclusive();

    /**
     * Sets whether the tax type is display inclusive.
     *
     * @param bool $displayInclusive Whether the tax type is display inclusive.
     */
    public function setDisplayInclusive($displayInclusive);

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
     * Gets the tax type tag.
     *
     * Used by the resolvers to analyze only the tax types relevant to them.
     * For example, the EuTaxTypeResolver would analyze only the tax types
     * with the "EU" tag.
     *
     * @return string The tax type tag.
     */
    public function getTag();

    /**
     * Sets the tax type tag.
     *
     * @param string $tag The tax type tag.
     */
    public function setTag($tag);

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

    /**
     * Checks whether the tax type has tax rates.
     *
     * @return bool True if the tax type has tax rates, false otherwise.
     */
    public function hasRates();

    /**
     * Adds a tax rate.
     *
     * @param TaxRateInterface $rate The tax rate.
     */
    public function addRate(TaxRateInterface $rate);

    /**
     * Removes a tax rate.
     *
     * @param TaxRateInterface $rate The tax rate.
     */
    public function removeRate(TaxRateInterface $rate);

    /**
     * Checks whether the tax type has a tax rate.
     *
     * @param TaxRateInterface $rate The tax rate.
     *
     * @return bool True if the tax rate was found, false otherwise.
     */
    public function hasRate(TaxRateInterface $rate);
}

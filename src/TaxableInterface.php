<?php

namespace CommerceGuys\Tax;

use CommerceGuys\Tax\Model\TaxRateInterface;
use CommerceGuys\Addressing\Model\AddressInterface;

interface TaxableInterface
{
    /**
     * Returns the taxable type for the place of supply.
     *
     * Used by tax resolvers to distinguish the place of supply rule to determine the tax type.
     *
     * @return array The rule that applies to each Resolver.
     */
    public function getTaxableTypes();

    /**
     * Returns the taxable type for the tax resolver.
     *
     * Used by tax resolvers when the rate must be specified per taxable, e.g. the EU.
     *
     * @param string
     *
     * @return string The taxable type.
     */
    public function getTaxableType($taxResolver);

    /**
     * Add a tax rule.
     *
     * @param string $taxable_type A tax place of supply rule.
     */
    public function addTaxableType($taxable_type);

    /**
     * Returns the tax rates for the taxable.
     *
     * Used by tax resolvers when the rate must be specified per taxable, e.g. the EU.
     *
     * @return array The rate for each tax type.
     */
    public function getRates();

    /**
     * Returns the tax rates for the taxable.
     *
     * Used by tax resolvers when the rate must be specified per taxable, e.g. the EU.
     *
     * @param string
     *
     * @return array The rate for each tax type.
     */
    public function getRate($taxType);

    /**
     * Add a tax rate.
     *
     * @param TaxRateInterface $rate A tax rate.
     */
    public function addRate(TaxRateInterface $rate);

    /**
     * Gets the address the taxable is located at.
     *
     * Used by resolvers where the tax is based on the location of a service or event.
     *
     * @return AddressInterface The taxable address.
     */
    public function getAddress();

    /**
     * Sets the address the taxable is located at.
     *
     * @param AddressInterface $address The address the taxable is located at.
     */
    public function setAddress($address);

}
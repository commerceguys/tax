<?php

namespace CommerceGuys\Tax;

interface TaxRateInterface
{
    /**
     * Gets the tax type.
     *
     * @return TaxTypeInterface The tax type.
     */
    public function getType();

    /**
     * Sets the tax type.
     *
     * @param TaxTypeInterface $type The tax type.
     */
    public function setType(TaxTypeInterface $type);

    /**
     * Gets the tax rate id.
     *
     * @return string The tax rate id.
     */
    public function getId();

    /**
     * Sets the tax rate id.
     *
     * @param string $id The tax rate id.
     */
    public function setId($id);

    /**
     * Gets the tax rate name.
     *
     * Used to identify the tax rate on administration pages.
     * For example, "Standard (20%)".
     *
     * @return string The tax rate name.
     */
    public function getName();

    /**
     * Sets the tax rate name.
     *
     * @param string $name The tax rate name.
     */
    public function setName($name);

    /**
     * Gets the tax rate display name.
     *
     * Used to identify the tax rate in the cart and other user-facing pages.
     * For example, "20% VAT".
     *
     * @return string The tax rate display name.
     */
    public function getDisplayName();

    /**
     * Sets the tax rate display name.
     *
     * @param string $displayName The tax rate display name.
     */
    public function setDisplayName($displayName);

    /**
     * Gets the tax rate amounts.
     *
     * @return TaxRateAmount[] The tax rate amounts.
     */
    public function getAmounts();

    /**
     * Sets the tax rate amounts.
     *
     * @param TaxRateAmount[] $amounts The tax rate amounts.
     */
    public function setAmounts($amounts);
}

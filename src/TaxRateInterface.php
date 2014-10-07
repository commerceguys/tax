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
     * @return string
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
     * Gets the decimal tax rate amount.
     *
     * For example, 0.2 for a 20% tax rate.
     *
     * @return float The tax rate amount expressed as a decimal.
     */
    public function getAmount();

    /**
     * Sets the decimal tax rate amount.
     *
     * @param float $amount The tax rate amount expressed as a decimal.
     */
    public function setAmount($amount);

    /**
     * Gets the tax rate start date.
     *
     * @return \DateTime|null The start date, if known.
     */
    public function getStartDate();

    /**
     * Sets the tax rate start date.
     *
     * @param \DateTime $startDate The tax rate start date.
     */
    public function setStartDate($startDate);

    /**
     * Gets the tax rate end date.
     *
     * @return \DateTime|null The tax rate end date, if known.
     */
    public function getEndDate();

    /**
     * Sets the tax rate end date.
     *
     * @param \DateTime $endDate The tax rate end date.
     */
    public function setEndDate($endDate);
}

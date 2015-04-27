<?php

namespace CommerceGuys\Tax\Model;

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
     * @param TaxTypeInterface|null $type The tax type.
     */
    public function setType(TaxTypeInterface $type = null);

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
     * For example, "Standard".
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
     * Gets whether the tax rate is the default for its tax type.
     *
     * When resolving the tax rate for a specific tax type, the default tax
     * rate is returned if no other resolver provides a more applicable one.
     *
     * @return bool True if the tax rate is the default, false otherwise.
     */
    public function isDefault();

    /**
     * Sets whether the tax rate is the default for its tax type.
     *
     * @param bool $default Whether the tax rate is the default.
     */
    public function setDefault($default);

    /**
     * Gets the tax rate amounts.
     *
     * @return TaxRateAmountInterface[] The tax rate amounts.
     */
    public function getAmounts();

    /**
     * Sets the tax rate amounts.
     *
     * @param TaxRateAmountInterface[] $amounts The tax rate amounts.
     */
    public function setAmounts($amounts);

    /**
     * Gets the tax rate amount valid for the provided date.
     *
     * @param \DateTime $date The date.
     *
     * @return TaxRateAmountInterface|null The tax rate amount, if matched.
     */
    public function getAmount(\DateTime $date);

    /**
     * Checks whether the tax rate has tax rate amounts.
     *
     * @return bool True if the tax rate has tax rate amounts, false otherwise.
     */
    public function hasAmounts();

    /**
     * Adds a tax rate amount.
     *
     * @param TaxRateAmountInterface $amount The tax rate amount.
     */
    public function addAmount(TaxRateAmountInterface $amount);

    /**
     * Removes a tax rate amount.
     *
     * @param TaxRateAmountInterface $amount The tax rate amount.
     */
    public function removeAmount(TaxRateAmountInterface $amount);

    /**
     * Checks whether the tax rate has a tax rate amount.
     *
     * @param TaxRateAmountInterface $amount The tax rate amount.
     *
     * @return bool True if the tax rate amount was found, false otherwise.
     */
    public function hasAmount(TaxRateAmountInterface $amount);
}

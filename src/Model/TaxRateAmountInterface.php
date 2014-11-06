<?php

namespace CommerceGuys\Tax\Model;

interface TaxRateAmountInterface
{
    /**
     * Gets the tax rate.
     *
     * @return TaxRateInterface The tax rate.
     */
    public function getRate();

    /**
     * Sets the tax rate.
     *
     * @param TaxRateInterface|null $rate The tax rate.
     */
    public function setRate(TaxRateInterface $rate = null);

    /**
     * Gets the tax rate amount id.
     *
     * @return string The tax rate amount id.
     */
    public function getId();

    /**
     * Sets the tax rate amount id.
     *
     * @param string $id The tax rate amount id.
     */
    public function setId($id);

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
     * Gets the tax rate amount start date.
     *
     * @return \DateTime|null The tax rate amount start date, if known.
     */
    public function getStartDate();

    /**
     * Sets the tax rate amount start date.
     *
     * @param \DateTime $startDate The tax rate amount start date.
     */
    public function setStartDate(\DateTime $startDate);

    /**
     * Gets the tax rate amount end date.
     *
     * @return \DateTime|null The tax rate amount end date, if known.
     */
    public function getEndDate();

    /**
     * Sets the tax rate amount end date.
     *
     * @param \DateTime $endDate The tax rate amount end date.
     */
    public function setEndDate(\DateTime $endDate);
}

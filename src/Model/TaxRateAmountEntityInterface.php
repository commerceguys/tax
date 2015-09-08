<?php

namespace CommerceGuys\Tax\Model;

interface TaxRateAmountEntityInterface extends TaxRateAmountInterface
{
    /**
     * Sets the tax rate.
     *
     * @param TaxRateEntityInterface|null $rate The tax rate.
     */
    public function setRate(TaxRateEntityInterface $rate = null);

    /**
     * Sets the tax rate amount id.
     *
     * @param string $id The tax rate amount id.
     */
    public function setId($id);

    /**
     * Sets the decimal tax rate amount.
     *
     * @param float $amount The tax rate amount expressed as a decimal.
     */
    public function setAmount($amount);

    /**
     * Sets the tax rate amount start date.
     *
     * @param \DateTime $startDate The tax rate amount start date.
     */
    public function setStartDate(\DateTime $startDate);

    /**
     * Sets the tax rate amount end date.
     *
     * @param \DateTime $endDate The tax rate amount end date.
     */
    public function setEndDate(\DateTime $endDate);
}

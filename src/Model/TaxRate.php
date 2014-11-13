<?php

namespace CommerceGuys\Tax\Model;

class TaxRate implements TaxRateInterface
{
    /**
     * The tax type.
     *
     * @var TaxTypeInterface
     */
    protected $type;

    /**
     * The tax rate id.
     *
     * @var string
     */
    protected $id;

    /**
     * The tax rate name.
     *
     * @var string
     */
    protected $name;

    /**
     * The tax rate display name.
     *
     * @var string
     */
    protected $displayName;

    /**
     * Whether the tax rate is the default for its tax type.
     *
     * @var bool
     */
    protected $default;

    /**
     * The tax rate amounts.
     *
     * @var TaxRateAmount[]
     */
    protected $amounts = array();

    /**
     * Returns the string representation of the tax rate.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(TaxTypeInterface $type = null)
    {
        $this->type = $type;

        return $this;
    }

     /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefault()
    {
        return !empty($this->default);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmounts()
    {
        return $this->amounts;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmounts($amounts)
    {
        $this->amounts = $amounts;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAmounts()
    {
        return !empty($this->amounts);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount(\DateTime $date)
    {
        // Amount start/end dates don't include the time, so discard the time
        // portion of the provided date to make the matching precise.
        $date->setTime(0, 0);
        foreach ($this->amounts as $amount) {
            $startDate = $amount->getStartDate();
            $endDate = $amount->getEndDate();
            // Match the date against the optional amount start/end dates.
            if ((!$startDate || $startDate <= $date) && (!$endDate || $endDate > $date)) {
                return $amount;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function addAmount(TaxRateAmountInterface $amount)
    {
        if (!$this->hasAmount($amount)) {
            $amount->setRate($this);
            $this->amounts[] = $amount;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAmount(TaxRateAmountInterface $amount)
    {
        if ($this->hasAmount($amount)) {
            $amount->setRate(null);
            // Remove the amount and rekey the array.
            $index = array_search($amount, $this->amounts);
            unset($this->amounts[$index]);
            $this->amounts = array_values($this->amounts);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAmount(TaxRateAmountInterface $amount)
    {
        return in_array($amount, $this->amounts);
    }
}

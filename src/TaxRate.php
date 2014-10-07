<?php

namespace CommerceGuys\Tax;

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
     * The tax rate amount.
     *
     * @var float
     */
    protected $amount;

    /**
     * The tax rate start date.
     *
     * @var \DateTime
     */
    protected $startDate;

    /**
     * The tax rate end date.
     *
     * @var \DateTime
     */
    protected $endDate;

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
    public function setType(TaxTypeInterface $type)
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
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }
}

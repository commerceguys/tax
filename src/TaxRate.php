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
     * The tax rate amounts.
     *
     * @var TaxRateAmount[]
     */
    protected $amounts;

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
}

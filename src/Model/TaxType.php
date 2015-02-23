<?php

namespace CommerceGuys\Tax\Model;

use CommerceGuys\Zone\Model\ZoneInterface;
use CommerceGuys\Tax\Exception\UnexpectedTypeException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class TaxType implements TaxTypeInterface
{
    /**
     * The tax type id.
     *
     * @var string
     */
    protected $id;

    /**
     * The tax type name.
     *
     * @var string
     */
    protected $name;

    /**
     * Whether the tax type is compound.
     *
     * @var bool
     */
    protected $compound;

    /**
     * The tax type rounding mode.
     *
     * @var integer
     */
    protected $roundingMode;

    /**
     * The tax type zone.
     *
     * @var ZoneInterface
     */
    protected $zone;

    /**
     * The tax type tag.
     *
     * @var string
     */
    protected $tag;

    /**
     * The tax rates.
     *
     * @var TaxRateInterface[]
     */
    protected $rates;

    /**
     * Creates a TaxeType instance.
     */
    public function __construct()
    {
        $this->rates = new ArrayCollection();
    }

    /**
     * Returns the string representation of the tax type.
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
    public function isCompound()
    {
        return !empty($this->compound);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompound($compound)
    {
        $this->compound = $compound;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoundingMode()
    {
        return $this->roundingMode;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoundingMode($roundingMode)
    {
        $this->roundingMode = $roundingMode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * {@inheritdoc}
     */
    public function setZone(ZoneInterface $zone)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * {@inheritdoc}
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * {@inheritdoc}
     */
    public function setRates($rates)
    {
        // The interface doesn't typehint $children to allow other
        // implementations to avoid using Doctrine Collections if desired.
        if (!($rates instanceof Collection)) {
           throw new UnexpectedTypeException($rates, 'Collection');
        }
        $this->rates = $rates;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRates()
    {
        return !$this->rates->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addRate(TaxRateInterface $rate)
    {
        if (!$this->hasRate($rate)) {
            $rate->setType($this);
            $this->rates->add($rate);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRate(TaxRateInterface $rate)
    {
        if ($this->hasRate($rate)) {
            $this->rates->removeElement($rate);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRate(TaxRateInterface $rate)
    {
        return $this->rates->contains($rate);
    }
}

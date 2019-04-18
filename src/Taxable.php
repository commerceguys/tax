<?php

namespace CommerceGuys\Tax;

use CommerceGuys\Tax\Model\TaxRateInterface;
use CommerceGuys\Addressing\Model\AddressInterface;

class Taxable implements TaxableInterface {
  /**
   * The tax rules.
   *
   * @var array
   */
  protected $rules;

  /**
   * The tax rates.
   *
   * @var array
   */
  protected $rates;

  /**
   * The taxable address.
   *
   * @var AddressInterface
   */
  protected $address;

  /**
   * {@inheritdoc}
   */
  public function getTaxableTypes()
  {
      return $this->rules;
  }

  /**
   * {@inheritdoc}
   */
  public function addTaxableType($type)
  {
      $this->rules[] = $type;

      return $this->rules;
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
  public function getRate($taxType)
  {
      return $this->rates[$taxType];
  }

  /**
   * {@inheritdoc}
   */
  public function addRate(TaxRateInterface $rate)
  {
      $this->rates[$rate->getType()] = $rate;

      return $this->rates;
  }

  /**
   * {@inheritdoc}
   */
  public function getAddress()
  {
      return $this->address;
  }

  /**
   * {@inheritdoc}
   */
  public function setAddress($address)
  {
      $this->address = $address;

      return $this;
  }
}

<?php

namespace CommerceGuys\Tax\Resolver;

use CommerceGuys\Addressing\Model\AddressInterface;

/**
 * Contains information relevant to tax resolving.
 *
 * Includes customer information, store information, and the calculation date.
 */
class Context
{
    /**
     * The customer address.
     *
     * @var AddressInterface
     */
    protected $customerAddress;

    /**
     * The store address.
     *
     * @var AddressInterface
     */
    protected $storeAddress;

    /**
     * The customer's tax number, if provided.
     *
     * @var string
     */
    protected $customerTaxNumber;

    /**
     * A list of additional country codes where the store is registered to
     * collect taxes.
     *
     * @var array
     */
    protected $storeRegistrations;

    /**
     * The calculation date.
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Creates a Context instance.
     *
     * @param AddressInterface $customerAddress
     * @param AddressInterface $storeAddress
     * @param string           $customerTaxNumber
     * @param array            $storeRegistrations
     * @param DateTime         $date
     */
    public function __construct(AddressInterface $customerAddress, AddressInterface $storeAddress, $customerTaxNumber = '', array $storeRegistrations = array(), \DateTime $date = null)
    {
        $this->customerAddress = $customerAddress;
        $this->storeAddress = $storeAddress;
        $this->customerTaxNumber = $customerTaxNumber;
        $this->storeRegistrations = $storeRegistrations;
        $this->date = $date ?: new \DateTime();
    }

    /**
     * Gets the customer address.
     *
     * @return AddressInterface The customer address.
     */
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    /**
     * Sets the customer address.
     *
     * @param AddressInterface $customerAddress The customer address.
     */
    public function setCustomerAddress($customerAddress)
    {
        $this->customerAddress = $customerAddress;

        return $this;
    }

    /**
     * Gets the store address.
     *
     * @return AddressInterface The store address.
     */
    public function getStoreAddress()
    {
        return $this->storeAddress;
    }

    /**
     * Sets the store address.
     *
     * @param AddressInterface $storeAddress The store address.
     */
    public function setStoreAddress($storeAddress)
    {
        $this->storeAddress = $storeAddress;

        return $this;
    }

    /**
     * Gets the customer tax number.
     *
     * @return string The customer tax number.
     */
    public function getCustomerTaxNumber()
    {
        return $this->customerTaxNumber;
    }

    /**
     * Sets the customer tax number.
     *
     * @param string $customerTaxNumber The customer tax number.
     */
    public function setCustomerTaxNumber($customerTaxNumber)
    {
        $this->customerTaxNumber = $customerTaxNumber;

        return $this;
    }

    /**
     * Gets the additional tax countries.
     *
     * @return array An array of country codes.
     */
    public function getStoreRegistrations()
    {
        return $this->storeRegistrations;
    }

    /**
     * Sets the additional tax countries.
     *
     * @param array $storeRegistrations An array of country codes.
     */
    public function setStoreRegistrations(array $storeRegistrations)
    {
        $this->storeRegistrations = $storeRegistrations;

        return $this;
    }

    /**
     * Gets the calculation date.
     *
     * @return DateTime The calculation date.
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the calculation date.
     *
     * @return DateTime $date The calculation date.
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
}

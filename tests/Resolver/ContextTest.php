<?php

namespace CommerceGuys\Tax\Tests\Resolver;

use CommerceGuys\Tax\Resolver\Context;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Resolver\Context
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxType
     */
    protected $context;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $address = $this
            ->getMockBuilder('CommerceGuys\Addressing\Model\Address')
            ->getMock();
        $this->context = new Context($address, $address);
    }

    /**
     * @covers ::__construct
     * @uses \CommerceGuys\Tax\Resolver\Context::getCustomerAddress
     * @uses \CommerceGuys\Tax\Resolver\Context::getStoreAddress
     * @uses \CommerceGuys\Tax\Resolver\Context::getCustomerTaxNumber
     * @uses \CommerceGuys\Tax\Resolver\Context::getAdditionalTaxCountries
     * @uses \CommerceGuys\Tax\Resolver\Context::getDate
     */
    public function testConstructor()
    {
        $customerAddress = $this
            ->getMockBuilder('CommerceGuys\Addressing\Model\Address')
            ->getMock();
        $storeAddress = $this
            ->getMockBuilder('CommerceGuys\Addressing\Model\Address')
            ->getMock();
        $date = new \DateTime('2014-10-10');
        $context = new Context($customerAddress, $storeAddress, '0123', array('DE'), $date);
        $this->assertSame($customerAddress, $context->getCustomerAddress());
        $this->assertSame($storeAddress, $context->getStoreAddress());
        $this->assertEquals('0123', $context->getCustomerTaxNumber());
        $this->assertEquals(array('DE'), $context->getAdditionalTaxCountries());
        $this->assertSame($date, $context->getDate());
    }

    /**
     * @covers ::getCustomerAddress
     * @covers ::setCustomerAddress
     * @uses \CommerceGuys\Tax\Resolver\Context::__construct
     */
    public function testCustomerAddress()
    {
        $address = $this
            ->getMockBuilder('CommerceGuys\Addressing\Model\Address')
            ->getMock();
        $this->context->setCustomerAddress($address);
        $this->assertSame($address, $this->context->getCustomerAddress());
    }

    /**
     * @covers ::getStoreAddress
     * @covers ::setStoreAddress
     * @uses \CommerceGuys\Tax\Resolver\Context::__construct
     */
    public function testStoreAddress()
    {
        $address = $this
            ->getMockBuilder('CommerceGuys\Addressing\Model\Address')
            ->getMock();
        $this->context->setStoreAddress($address);
        $this->assertSame($address, $this->context->getStoreAddress());
    }

    /**
     * @covers ::getCustomerTaxNumber
     * @covers ::setCustomerTaxNumber
     * @uses \CommerceGuys\Tax\Resolver\Context::__construct
     */
    public function testCustomerTaxNumber()
    {
        $this->context->setCustomerTaxNumber('123456');
        $this->assertEquals('123456', $this->context->getCustomerTaxNumber());
    }

    /**
     * @covers ::getAdditionalTaxCountries
     * @covers ::setAdditionalTaxCountries
     * @uses \CommerceGuys\Tax\Resolver\Context::__construct
     */
    public function testAdditionalTaxCountries()
    {
        $this->context->setAdditionalTaxCountries(array('DE', 'DK'));
        $this->assertEquals(array('DE', 'DK'), $this->context->getAdditionalTaxCountries());
    }

    /**
     * @covers ::getDate
     * @covers ::setDate
     * @uses \CommerceGuys\Tax\Resolver\Context::__construct
     */
    public function testDate()
    {
        $date = new \DateTime('1990-02-24');
        $this->context->setDate($date);
        $this->assertSame($date, $this->context->getDate());
    }
}

<?php

namespace CommerceGuys\Tax\Tests\Model;

use CommerceGuys\Tax\Model\TaxRate;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Model\TaxRate
 */
class TaxRateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxRate
     */
    protected $taxRate;

    public function setUp()
    {
        $this->taxRate = new TaxRate();
    }

    /**
     * @covers ::getType
     * @covers ::setType
     * @uses \CommerceGuys\Tax\Model\TaxRate::__construct
     * @uses \CommerceGuys\Tax\Model\TaxType::__construct
     */
    public function testType()
    {
        $type = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxType')
            ->getMock();

        $this->taxRate->setType($type);
        $this->assertSame($type, $this->taxRate->getType());
    }

    /**
     * @covers ::getId
     * @covers ::setId
     * @uses \CommerceGuys\Tax\Model\TaxRate::__construct
     */
    public function testId()
    {
        $this->taxRate->setId('de_vat_standard');
        $this->assertEquals('de_vat_standard', $this->taxRate->getId());
    }

    /**
     * @covers ::getName
     * @covers ::setName
     * @covers ::__toString
     * @uses \CommerceGuys\Tax\Model\TaxRate::__construct
     */
    public function testName()
    {
        $this->taxRate->setName('Standard');
        $this->assertEquals('Standard', $this->taxRate->getName());
        $this->assertEquals('Standard', (string) $this->taxRate);
    }

    /**
     * @covers ::getDisplayName
     * @covers ::setDisplayName
     * @uses \CommerceGuys\Tax\Model\TaxRate::__construct
     */
    public function testDisplayName()
    {
        $this->taxRate->setDisplayName('% VAT');
        $this->assertEquals('% VAT', $this->taxRate->getDisplayName());
    }

    /**
     * @covers ::isDefault
     * @covers ::setDefault
     * @uses \CommerceGuys\Tax\Model\TaxRate::__construct
     */
    public function testDefault()
    {
        $this->taxRate->setDefault(true);
        $this->assertEquals(true, $this->taxRate->isDefault());
    }

    /**
     * @covers ::getAmounts
     * @covers ::setAmounts
     * @covers ::hasAmounts
     * @covers ::getAmount
     * @covers ::addAmount
     * @covers ::removeAmount
     * @covers ::hasAmount
     * @uses \CommerceGuys\Tax\Model\TaxRate::__construct
     * @uses \CommerceGuys\Tax\Model\TaxRateAmount::setRate
     */
    public function testAmounts()
    {
        $firstAmount = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRateAmount')
            ->getMock();
        $firstAmount
            ->expects($this->any())
            ->method('getStartDate')
            ->will($this->returnValue(new \DateTime('2013/01/01')));
        $firstAmount
            ->expects($this->any())
            ->method('getEndDate')
            ->will($this->returnValue(new \DateTime('2013/12/31')));
        $secondAmount = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRateAmount')
            ->getMock();
        $secondAmount
            ->expects($this->any())
            ->method('getStartDate')
            ->will($this->returnValue(new \DateTime('2014/01/01')));

        $this->assertEquals(false, $this->taxRate->hasAmounts());
        $rates = new ArrayCollection(array($firstAmount, $secondAmount));
        $this->taxRate->setAmounts($rates);
        $this->assertEquals($rates, $this->taxRate->getAmounts());
        $this->assertEquals(true, $this->taxRate->hasAmounts());
        $this->taxRate->removeAmount($secondAmount);
        $this->assertEquals(false, $this->taxRate->hasAmount($secondAmount));
        $this->assertEquals(true, $this->taxRate->hasAmount($firstAmount));
        $this->taxRate->addAmount($secondAmount);
        $this->assertEquals($rates, $this->taxRate->getAmounts());

        $amount = $this->taxRate->getAmount(new \DateTime('2012/02/24'));
        $this->assertNull($amount);
        $amount = $this->taxRate->getAmount(new \DateTime('2013/02/24'));
        $this->assertSame($firstAmount, $amount);
        $amount = $this->taxRate->getAmount(new \DateTime('2014/02/24'));
        $this->assertSame($secondAmount, $amount);
        $amount = $this->taxRate->getAmount();
        $this->assertSame($secondAmount, $amount);
    }
}

<?php

namespace CommerceGuys\Tax\Tests\Model;

use CommerceGuys\Tax\Model\TaxType;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Model\TaxType
 */
class TaxTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxType
     */
    protected $taxType;

    public function setUp()
    {
        $this->taxType = new TaxType();
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testId()
    {
        $this->taxType->setId('de_vat');
        $this->assertEquals('de_vat', $this->taxType->getId());
    }

    /**
     * @covers ::getName
     * @covers ::setName
     * @covers ::__toString
     */
    public function testName()
    {
        $this->taxType->setName('German VAT');
        $this->assertEquals('German VAT', $this->taxType->getName());
        $this->assertEquals('German VAT', (string) $this->taxType);
    }

    /**
     * @covers ::isCompound
     * @covers ::setCompound
     */
    public function testCompound()
    {
        $this->taxType->setCompound(true);
        $this->assertEquals(true, $this->taxType->isCompound());
    }

    /**
     * @covers ::getRoundingMode
     * @covers ::setRoundingMode
     */
    public function testRoundingMode()
    {
        $this->taxType->setRoundingMode(TaxType::ROUND_HALF_UP);
        $this->assertEquals(TaxType::ROUND_HALF_UP, $this->taxType->getRoundingMode());
    }

    /**
     * @covers ::getZone
     * @covers ::setZone
     */
    public function testZone()
    {
        $zone = $this
            ->getMockBuilder('CommerceGuys\Zone\Model\Zone')
            ->getMock();

        $this->taxType->setZone($zone);
        $this->assertEquals($zone, $this->taxType->getZone());
    }

    /**
     * @covers ::getRates
     * @covers ::setRates
     * @covers ::hasRates
     * @covers ::addRate
     * @covers ::removeRate
     * @covers ::hasRate
     * @uses \CommerceGuys\Tax\Model\TaxRate::setType
     */
    public function testRates()
    {
        $firstTaxRate = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRate')
            ->getMock();
        $secondTaxRate = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRate')
            ->getMock();

        $this->assertEquals(false, $this->taxType->hasRates());
        $rates = array($firstTaxRate, $secondTaxRate);
        $this->taxType->setRates($rates);
        $this->assertEquals($rates, $this->taxType->getRates());
        $this->assertEquals(true, $this->taxType->hasRates());
        $this->taxType->removeRate($secondTaxRate);
        $this->assertEquals(array($firstTaxRate), $this->taxType->getRates());
        $this->assertEquals(false, $this->taxType->hasRate($secondTaxRate));
        $this->assertEquals(true, $this->taxType->hasRate($firstTaxRate));
        $this->taxType->addRate($secondTaxRate);
        $this->assertEquals($rates, $this->taxType->getRates());
    }
}

<?php

namespace CommerceGuys\Tax\Tests\Model;

use CommerceGuys\Tax\Model\TaxRateAmount;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Model\TaxRateAmount
 */
class TaxRateAmountTest extends TestCase
{
    /**
     * @var TaxRateAmount
     */
    protected $amount;

    public function setUp(): void
    {
        $this->amount = new TaxRateAmount();
    }

    /**
     * @covers ::getRate
     * @covers ::setRate
     *
     * @uses \CommerceGuys\Tax\Model\TaxRate::__construct
     * @uses \CommerceGuys\Tax\Model\TaxType::__construct
     */
    public function testRate()
    {
        $rate = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRate')
            ->getMock();

        $this->amount->setRate($rate);
        $this->assertSame($rate, $this->amount->getRate());
    }

    /**
     * @covers ::getId
     * @covers ::setId
     */
    public function testId()
    {
        $this->amount->setId('de_vat_standard_19');
        $this->assertEquals('de_vat_standard_19', $this->amount->getId());
    }

    /**
     * @covers ::getAmount
     * @covers ::setAmount
     */
    public function testAmount()
    {
        $this->amount->setAmount('Standard');
        $this->assertEquals('Standard', $this->amount->getAmount());
    }

    /**
     * @covers ::getStartDate
     * @covers ::setStartDate
     */
    public function testStartDate()
    {
        $startDate = new \DateTime('2013/01/01');
        $this->amount->setStartDate($startDate);
        $this->assertSame($startDate, $this->amount->getStartDate());
    }

    /**
     * @covers ::getEndDate
     * @covers ::setEndDate
     */
    public function testEndDate()
    {
        $endDate = new \DateTime('2014/01/01');
        $this->amount->setEndDate($endDate);
        $this->assertSame($endDate, $this->amount->getEndDate());
    }
}

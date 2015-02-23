<?php

namespace CommerceGuys\Tax\Tests\Resolver;

use CommerceGuys\Tax\Resolver\TaxResolver;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Resolver\TaxResolver
 */
class TaxResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $taxTypeResolverEngine = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\Engine\TaxTypeResolverEngine')
            ->getMock();
        $taxRateResolverEngine = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\Engine\TaxRateResolverEngine')
            ->getMock();
        $resolver = new TaxResolver($taxTypeResolverEngine, $taxRateResolverEngine);
        $this->assertSame($taxTypeResolverEngine, $this->getObjectAttribute($resolver, 'taxTypeResolverEngine'));
        $this->assertSame($taxRateResolverEngine, $this->getObjectAttribute($resolver, 'taxRateResolverEngine'));

    }

    /**
     * @covers ::resolveAmounts
     * @covers ::resolveRates
     * @covers ::resolveTypes
     * @uses \CommerceGuys\Tax\Resolver\TaxResolver::__construct
     * @uses \CommerceGuys\Tax\Model\TaxRate::__construct
     * @uses \CommerceGuys\Tax\Model\TaxType::__construct
     */
    public function testResolver()
    {
        $firstTaxRateAmount = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRateAmount')
            ->getMock();
        $secondTaxRateAmount = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRateAmount')
            ->getMock();
        $firstTaxRate = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRate')
            ->getMock();
        $firstTaxRate->expects($this->any())
            ->method('getAmount')
            ->will($this->returnValue($firstTaxRateAmount));
        $secondTaxRate = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRate')
            ->getMock();
        $secondTaxRate->expects($this->any())
            ->method('getAmount')
            ->will($this->returnValue($secondTaxRateAmount));
        $firstTaxType = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxType')
            ->getMock();
        $secondTaxType = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxType')
            ->getMock();

        $taxTypeResolverEngine = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\Engine\TaxTypeResolverEngine')
            ->getMock();
        $taxTypeResolverEngine->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue([$firstTaxType, $secondTaxType]));
        $taxRateResolverEngine = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\Engine\TaxRateResolverEngine')
            ->getMock();
        $taxRateResolverEngine->expects($this->exactly(2))
            ->method('resolve')
            ->will($this->onConsecutiveCalls($firstTaxRate, $secondTaxRate));

        $resolver = new TaxResolver($taxTypeResolverEngine, $taxRateResolverEngine);
        $taxable = $this
            ->getMockBuilder('CommerceGuys\Tax\TaxableInterface')
            ->getMock();
        $context = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\Context')
            ->disableOriginalConstructor()
            ->getMock();
        // Since resolveAmounts calls resolveRates and resolveTypes, there
        // is no need to invoke them separately.
        $result = $resolver->resolveAmounts($taxable, $context);
        $this->assertEquals([$firstTaxRateAmount, $secondTaxRateAmount], $result);
    }
}

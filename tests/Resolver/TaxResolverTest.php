<?php

namespace CommerceGuys\Tax\Tests\Resolver;

use CommerceGuys\Tax\Resolver\TaxResolver;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Resolver\TaxResolver
 */
class TaxResolverTest extends TestCase
{
    /**
     * @covers ::resolveAmounts
     * @covers ::resolveRates
     * @covers ::resolveTypes
     *
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
            ->willReturn($firstTaxRateAmount);
        $secondTaxRate = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRate')
            ->getMock();
        $secondTaxRate->expects($this->any())
            ->method('getAmount')
            ->willReturn($secondTaxRateAmount);
        $firstTaxType = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxType')
            ->getMock();
        $secondTaxType = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxType')
            ->getMock();

        $chainTaxTypeResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxType\ChainTaxTypeResolver')
            ->getMock();
        $chainTaxTypeResolver->expects($this->any())
            ->method('resolve')
            ->willReturn([$firstTaxType, $secondTaxType]);
        $chainTaxRateResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxRate\ChainTaxRateResolver')
            ->getMock();

        $invokedCount = $this->exactly(2);
        $chainTaxRateResolver->expects($invokedCount)
            ->method('resolve')
            ->willReturnCallback(function ($parameters) use ($invokedCount, $firstTaxRate, $secondTaxRate) {
                if ($invokedCount->numberOfInvocations() === 1) {
                    return $firstTaxRate;
                }

                if ($invokedCount->numberOfInvocations() === 2) {
                    return $secondTaxRate;
                }
            });

        $resolver = new TaxResolver($chainTaxTypeResolver, $chainTaxRateResolver);
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

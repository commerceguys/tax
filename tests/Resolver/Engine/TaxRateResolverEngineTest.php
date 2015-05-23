<?php

namespace CommerceGuys\Tax\Tests\Resolver\Engine;

use CommerceGuys\Tax\Resolver\Engine\TaxRateResolverEngine;
use CommerceGuys\Tax\Resolver\TaxRate\TaxRateResolverInterface;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Resolver\Engine\TaxRateResolverEngine
 */
class TaxRateResolverEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxRateResolverEngine
     */
    protected $engine;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->engine = new TaxRateResolverEngine();
    }

    /**
     * @covers ::add
     * @covers ::getAll
     * @covers ::resolve
     * @covers \CommerceGuys\Tax\Resolver\Engine\ResolverSorterTrait::sortResolvers
     *
     * @uses \CommerceGuys\Tax\Model\TaxRate::__construct
     * @uses \CommerceGuys\Tax\Model\TaxType::__construct
     */
    public function testEngine()
    {
        $firstTaxRate = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRate')
            ->getMock();
        $secondTaxRate = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxRate')
            ->getMock();
        $firstResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxRate\TaxRateResolverInterface')
            ->getMock();
        $secondResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxRate\TaxRateResolverInterface')
            ->getMock();
        $secondResolver->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($firstTaxRate));
        $thirdResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxRate\TaxRateResolverInterface')
            ->getMock();
        $thirdResolver->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($secondTaxRate));
        $fourthResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxRate\TaxRateResolverInterface')
            ->getMock();
        $fourthResolver->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue(TaxRateResolverInterface::NO_APPLICABLE_TAX_RATE));

        $this->engine->add($firstResolver, 10);
        $this->engine->add($secondResolver);
        $this->engine->add($thirdResolver, 5);

        // Confirm that the added resolvers have been ordered by priority.
        $expectedResolvers = [$firstResolver, $thirdResolver, $secondResolver];
        $this->assertEquals($expectedResolvers, $this->engine->getAll());

        $taxType = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxType')
            ->getMock();
        $taxable = $this
            ->getMockBuilder('CommerceGuys\Tax\TaxableInterface')
            ->getMock();
        $context = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $result = $this->engine->resolve($taxType, $taxable, $context);
        $this->assertSame($secondTaxRate, $result);

        // The new resolver will run first, and return NO_APPLICABLE_TAX_RATE,
        // which should cause the resolving to stop and null to be returned.
        $this->engine->add($fourthResolver, 10);
        $result = $this->engine->resolve($taxType, $taxable, $context);
        $this->assertNull($result);
    }
}

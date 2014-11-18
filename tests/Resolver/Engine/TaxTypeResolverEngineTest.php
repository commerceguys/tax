<?php

namespace CommerceGuys\Tax\Tests\Resolver\Engine;

use CommerceGuys\Tax\Resolver\Engine\TaxTypeResolverEngine;
use CommerceGuys\Tax\Resolver\TaxType\TaxTypeResolverInterface;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Resolver\Engine\TaxTypeResolverEngine
 */
class TaxTypeResolverEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxTypeResolverEngine
     */
    protected $engine;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->engine = new TaxTypeResolverEngine();
    }

    /**
     * @covers ::add
     * @covers ::getAll
     * @covers ::resolve
     * @covers \CommerceGuys\Tax\Resolver\Engine\ResolverSorterTrait::sortResolvers
     */
    public function testEngine()
    {
        $firstTaxType = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxType')
            ->getMock();
        $secondTaxType = $this
            ->getMockBuilder('CommerceGuys\Tax\Model\TaxType')
            ->getMock();
        $firstResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxType\TaxTypeResolverInterface')
            ->getMock();
        $secondResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxType\TaxTypeResolverInterface')
            ->getMock();
        $secondResolver->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue(array($firstTaxType)));
        $thirdResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxType\TaxTypeResolverInterface')
            ->getMock();
        $thirdResolver->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue(array($secondTaxType)));
        $fourthResolver = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\TaxType\TaxTypeResolverInterface')
            ->getMock();
        $fourthResolver->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue(TaxTypeResolverInterface::NO_APPLICABLE_TAX_TYPE));

        $this->engine->add($firstResolver, 10);
        $this->engine->add($secondResolver);
        $this->engine->add($thirdResolver, 5);

        // Confirm that the added resolvers have been ordered by priority.
        $expectedResolvers = array($firstResolver, $thirdResolver, $secondResolver);
        $this->assertEquals($expectedResolvers, $this->engine->getAll());

        $taxable = $this
            ->getMockBuilder('CommerceGuys\Tax\TaxableInterface')
            ->getMock();
        $context = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $result = $this->engine->resolve($taxable, $context);
        $this->assertSame(array($secondTaxType), $result);

        // The new resolver will run first, and return NO_APPLICABLE_TAX_TYPE,
        // which should cause the resolving to stop and an empty array to be
        // returned.
        $this->engine->add($fourthResolver, 10);
        $result = $this->engine->resolve($taxable, $context);
        $this->assertEmpty($result);
    }
}

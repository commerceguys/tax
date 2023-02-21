<?php

namespace CommerceGuys\Tax\Tests\Resolver;

use CommerceGuys\Addressing\AddressInterface;
use CommerceGuys\Tax\Repository\TaxTypeRepository;
use CommerceGuys\Tax\Resolver\TaxType\DefaultTaxTypeResolver;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Resolver\TaxType\DefaultTaxTypeResolver
 */
class DefaultTaxTypeResolverTest extends TestCase
{
    /**
     * Known tax types.
     *
     * @var array
     */
    protected $taxTypes = [
        'rs_vat' => [
            'name' => 'Serbian VAT',
            'generic_label' => 'vat',
            'zone' => 'rs_vat',
            'rates' => [
                [
                    'id' => 'rs_vat_standard',
                    'name' => 'Standard',
                    'amounts' => [
                        [
                            'id' => 'rs_vat_standard_20',
                            'amount' => 0.2,
                            'start_date' => '2002-10-01',
                        ],
                    ],
                ],
            ],
        ],
        'me_vat' => [
            'name' => 'Montenegrin VAT',
            'generic_label' => 'vat',
            'zone' => 'me_vat',
            'rates' => [
                [
                    'id' => 'me_vat_standard',
                    'name' => 'Standard',
                    'amounts' => [
                        [
                            'id' => 'me_vat_standard_19',
                            'amount' => 0.19,
                            'start_date' => '2003-07-01',
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * Known zones.
     *
     * @var array
     */
    protected $zones = [
        'rs_vat' => [
            'name' => 'Serbia (VAT)',
            'members' => [
                [
                    'type' => 'country',
                    'id' => '1',
                    'name' => 'Serbia',
                    'country_code' => 'RS',
                ],
            ],
        ],
        'me_vat' => [
            'name' => 'Montenegro (VAT)',
            'members' => [
                [
                    'type' => 'country',
                    'id' => '2',
                    'name' => 'Montenegro',
                    'country_code' => 'ME',
                ],
            ],
        ],
    ];

    /**
     * @covers ::__construct
     *
     * @uses \CommerceGuys\Tax\Repository\TaxTypeRepository
     */
    protected function createResolver()
    {
        $root = vfsStream::setup('resources');
        $directory = vfsStream::newDirectory('tax_type')->at($root);
        foreach ($this->taxTypes as $id => $definition) {
            $filename = $id . '.json';
            vfsStream::newFile($filename)->at($directory)->setContent(json_encode($definition));
        }
        $directory = vfsStream::newDirectory('zone')->at($root);
        foreach ($this->zones as $id => $definition) {
            $filename = $id . '.json';
            vfsStream::newFile($filename)->at($directory)->setContent(json_encode($definition));
        }

        $taxTypeRepository = new TaxTypeRepository('vfs://resources/');
        $resolver = new DefaultTaxTypeResolver($taxTypeRepository);

        return $resolver;
    }

    /**
     * @covers ::resolve
     * @covers ::getTaxTypes
     *
     * @uses \CommerceGuys\Tax\Resolver\TaxType\StoreRegistrationCheckerTrait
     * @uses \CommerceGuys\Tax\Repository\TaxTypeRepository
     * @uses \CommerceGuys\Tax\Model\TaxType
     * @uses \CommerceGuys\Tax\Model\TaxRate
     * @uses \CommerceGuys\Tax\Model\TaxRateAmount
     */
    public function testResolver()
    {
        $resolver = $this->createResolver();

        $taxable = $this
            ->getMockBuilder('CommerceGuys\Tax\TaxableInterface')
            ->getMock();
        $serbianAddress = $this->createStub('CommerceGuys\Addressing\Address');
        $serbianAddress
            ->method('getCountryCode')
            ->willReturn('RS')
            ;
        $serbianAddress
            ->method('getPostalCode')
            ->willReturn('');
            ;
        $montenegrinAddress = $this->createStub('CommerceGuys\Addressing\Address');
        $montenegrinAddress
            ->method('getCountryCode')
            ->willReturn('ME')
            ;
        $montenegrinAddress
            ->method('getPostalCode')
            ->willReturn('');

        // Serbian store, Serbian customer.
        $context = $this->getContext($serbianAddress, $serbianAddress);
        $results = $resolver->resolve($taxable, $context);
        $result = reset($results);
        $this->assertInstanceOf('CommerceGuys\Tax\Model\TaxType', $result);
        $this->assertEquals('rs_vat', $result->getId());

        // Serbian store, Montenegrin customer, store registered for VAT in ME.
        $context = $this->getContext($montenegrinAddress, $serbianAddress, ['ME']);
        $results = $resolver->resolve($taxable, $context);
        $result = reset($results);
        $this->assertInstanceOf('CommerceGuys\Tax\Model\TaxType', $result);
        $this->assertEquals('me_vat', $result->getId());

        // Serbian store, Montenegrin customer, store not registered in ME.
        $context = $this->getContext($montenegrinAddress, $serbianAddress);
        $result = $resolver->resolve($taxable, $context);
        $this->assertEquals([], $result);
    }

    /**
     * Returns a mock context based on the provided data.
     *
     * @param AddressInterface $customerAddress    The customer address.
     * @param AddressInterface $storeAddress       The store address.
     * @param array            $storeRegistrations The store registrations.
     *
     * @return \CommerceGuys\Tax\Resolver\Context
     */
    protected function getContext($customerAddress, $storeAddress, $storeRegistrations = [])
    {
        $context = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->any())
            ->method('getCustomerAddress')
            ->will($this->returnValue($customerAddress));
        $context->expects($this->any())
            ->method('getStoreAddress')
            ->will($this->returnValue($storeAddress));
        $context->expects($this->any())
            ->method('getStoreRegistrations')
            ->will($this->returnValue($storeRegistrations));

        return $context;
    }
}

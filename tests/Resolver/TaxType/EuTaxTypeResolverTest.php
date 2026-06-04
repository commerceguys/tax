<?php

namespace CommerceGuys\Tax\Tests\Resolver;

use CommerceGuys\Addressing\AddressInterface;
use CommerceGuys\Tax\Repository\TaxTypeRepository;
use CommerceGuys\Tax\Resolver\TaxType\EuTaxTypeResolver;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Resolver\TaxType\EuTaxTypeResolver
 */
class EuTaxTypeResolverTest extends TestCase
{
    /**
     * Known tax types.
     *
     * @var array
     */
    protected $taxTypes = [
        'fr_vat' => [
            'name' => 'French VAT',
            'generic_label' => 'vat',
            'zone' => 'fr_vat',
            'tag' => 'EU',
            'rates' => [
                [
                    'id' => 'fr_vat_standard',
                    'name' => 'Standard',
                    'default' => true,
                    'amounts' => [
                        [
                            'id' => 'fr_vat_standard_196',
                            'amount' => 0.196,
                            'start_date' => '2004-04-01',
                            'end_date' => '2013-12-31',
                        ],
                        [
                            'id' => 'fr_vat_standard_20',
                            'amount' => 0.2,
                            'start_date' => '2014-01-01',
                        ],
                    ],
                ],
            ],
        ],
        'de_vat' => [
            'name' => 'German VAT',
            'generic_label' => 'vat',
            'zone' => 'de_vat',
            'tag' => 'EU',
            'rates' => [
                [
                    'id' => 'de_vat_standard',
                    'name' => 'Standard',
                    'default' => true,
                    'amounts' => [
                        [
                            'id' => 'de_vat_standard_19',
                            'amount' => 0.19,
                            'start_date' => '2007-01-01',
                        ],
                    ],
                ],
            ],
        ],
        'eu_ic_vat' => [
            'name' => 'Intra-Community Supply',
            'generic_label' => 'vat',
            'zone' => 'eu_vat',
            'tag' => 'EU',
            'rates' => [
                [
                    'id' => 'eu_ic_vat',
                    'name' => 'Intra-Community Supply',
                    'default' => true,
                    'amounts' => [
                        [
                            'id' => 'eu_ic_vat',
                            'amount' => 0,
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * Known zones.
     *
     * Note: The real fr_vat and de_vat zones are more complex, France excludes
     * Corsica, Germany excludes Heligoland and Bussingen, but includes 4
     * Austrian postal codes. Those details were irrelevant for this test.
     *
     * @var array
     */
    protected $zones = [
        'fr_vat' => [
            'name' => 'France (VAT)',
            'members' => [
                [
                    'type' => 'country',
                    'id' => '1',
                    'name' => 'France',
                    'country_code' => 'FR',
                ],
                [
                    'type' => 'country',
                    'id' => '2',
                    'name' => 'Monaco',
                    'country_code' => 'MC',
                ],
            ],
        ],
        'de_vat' => [
            'name' => 'Germany (VAT)',
            'members' => [
                [
                    'type' => 'country',
                    'id' => '2',
                    'name' => 'Germany',
                    'country_code' => 'DE',
                ],
            ],
        ],
        'eu_vat' => [
            'name' => 'European Union (VAT)',
            'members' => [
                [
                    'type' => 'zone',
                    'id' => '3',
                    'name' => 'France (VAT)',
                    'zone' => 'fr_vat',
                ],
                [
                    'type' => 'zone',
                    'id' => '4',
                    'name' => 'Germany (VAT)',
                    'zone' => 'de_vat',
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
        $resolver = new EuTaxTypeResolver($taxTypeRepository);

        return $resolver;
    }

    /**
     * @covers ::resolve
     * @covers ::filterByAddress
     * @covers ::getTaxTypes
     * @covers \CommerceGuys\Tax\Resolver\TaxType\StoreRegistrationCheckerTrait
     *
     * @uses \CommerceGuys\Tax\Repository\TaxTypeRepository
     * @uses \CommerceGuys\Tax\Model\TaxType
     * @uses \CommerceGuys\Tax\Model\TaxRate
     * @uses \CommerceGuys\Tax\Model\TaxRateAmount
     */
    #[DataProvider("resolverProvider")]
    public function testResolver($taxable, $context, $expected): void
    {
        $resolver = $this->createResolver();

        $results = $resolver->resolve($taxable, $context);
        if (empty($expected) || $expected == EuTaxTypeResolver::NO_APPLICABLE_TAX_TYPE) {
            $this->assertEquals($expected, $results);
        } else {
            $result = reset($results);
            $this->assertInstanceOf('CommerceGuys\Tax\Model\TaxType', $result);
            $this->assertEquals($expected, $result->getId());
        }
    }

    /**
     * Provides data for the resolver test.
     */
    public static function resolverProvider(): array
    {
        $self = new self(self::class);

        $mockTaxableBuilder = $self->getMockBuilder('CommerceGuys\Tax\TaxableInterface');
        $physicalTaxable = $mockTaxableBuilder->getMock();

        $physicalTaxable->expects($self->atLeastOnce())
            ->method('isPhysical')
            ->willReturn(true);
        $digitalTaxable = $mockTaxableBuilder->getMock();


        $serbianAddress = $self->createStub('CommerceGuys\Addressing\Address');
        $serbianAddress
            ->method('getCountryCode')
            ->willReturn('RS');
        $serbianAddress
            ->method('getPostalCode')
            ->willReturn('')
            ;
        $frenchAddress = $self->createStub('CommerceGuys\Addressing\Address');
        $frenchAddress
            ->method('getCountryCode')
            ->willReturn('FR');
        $frenchAddress
            ->method('getPostalCode')
            ->willReturn('')
            ;

        $germanAddress = $self->createStub('CommerceGuys\Addressing\Address');
        $germanAddress
            ->method('getCountryCode')
            ->willReturn('DE');
        $germanAddress
            ->method('getPostalCode')
            ->willReturn('')
            ;
        $usAddress = $self->createStub('CommerceGuys\Addressing\Address');
        $usAddress
            ->method('getCountryCode')
            ->willReturn('US');
        $usAddress
            ->method('getPostalCode')
            ->willReturn('')
            ;

        $date1 = new \DateTime('2014-02-24');
        $date2 = new \DateTime('2015-02-24');
        $date3 = new \DateTime('2021-08-24');
        $notApplicable = EuTaxTypeResolver::NO_APPLICABLE_TAX_TYPE;

        return [
            // German customer, French store, VAT number provided.
            [$physicalTaxable, $self->getContext($germanAddress, $frenchAddress, '123'), 'eu_ic_vat'],
            // French customer, French store, VAT number provided.
            [$physicalTaxable, $self->getContext($frenchAddress, $frenchAddress, '123'), 'fr_vat'],
            // German customer, French store, physical product.
            [$physicalTaxable, $self->getContext($germanAddress, $frenchAddress, '', [], $date2), 'fr_vat'],
            // German customer, French store registered for German VAT, physical product.
            [$physicalTaxable, $self->getContext($germanAddress, $frenchAddress, '', ['DE'], $date2), 'de_vat'],
            // German customer, French store, digital product before Jan 1st 2015.
            [$digitalTaxable, $self->getContext($germanAddress, $frenchAddress, '', [], $date1), 'fr_vat'],
            // German customer, French store, digital product.
            [$digitalTaxable, $self->getContext($germanAddress, $frenchAddress, '', [], $date2), 'de_vat'],
            // German customer, US store, digital product
            [$digitalTaxable, $self->getContext($germanAddress, $usAddress, '', [], $date2), []],
            // German customer, US store registered in FR, digital product.
            [$digitalTaxable, $self->getContext($germanAddress, $usAddress, '', ['FR'], $date2), 'de_vat'],
            // German customer with VAT number, US store registered in FR, digital product.
            [$digitalTaxable, $self->getContext($germanAddress, $usAddress, '123', ['FR'], $date2), $notApplicable],
            // Serbian customer, French store, physical product.
            [$physicalTaxable, $self->getContext($serbianAddress, $frenchAddress), []],
            // French customer, Serbian store, physical product.
            [$physicalTaxable, $self->getContext($frenchAddress, $serbianAddress), []],
            // German customer, French store, digital product after July 1st 2021.
            [$digitalTaxable, $self->getContext($germanAddress, $frenchAddress, '', [], $date3), 'de_vat'],
            // German customer, French store, physical product after July 1st 2021.
            [$physicalTaxable, $self->getContext($germanAddress, $frenchAddress, '', [], $date3), 'de_vat'],
            // German customer US store registered in FR, physical product after July 1st 2021
            [$physicalTaxable, $self->getContext($germanAddress, $usAddress, '', ['FR'], $date3), 'de_vat'],
        ];
    }

    /**
     * Returns a mock context based on the provided data.
     *
     * @param AddressInterface $customerAddress    The customer address.
     * @param AddressInterface $storeAddress       The store address.
     * @param string           $customerTaxNumber  The customer tax number.
     * @param array            $storeRegistrations The store registrations.
     * @param \DateTime        $date               The date.
     *
     * @return \CommerceGuys\Tax\Resolver\Context
     */
    protected function getContext($customerAddress, $storeAddress, $customerTaxNumber = '', $storeRegistrations = [], $date = null)
    {
        $context = $this
            ->getMockBuilder('CommerceGuys\Tax\Resolver\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->atLeastOnce())
            ->method('getCustomerAddress')
            ->willReturn($customerAddress);
        $context->expects($this->atLeastOnce())
            ->method('getStoreAddress')
            ->willReturn($storeAddress);
        $context->expects($this->atLeastOnce())
            ->method('getCustomerTaxNumber')
            ->willReturn($customerTaxNumber);
        $context->expects($this->atLeastOnce())
            ->method('getStoreRegistrations')
            ->willReturn($storeRegistrations);
        $date = $date ?: new \DateTime();
        $context->expects($this->atLeastOnce())
            ->method('getDate')
            ->willReturn($date);

        return $context;
    }
}

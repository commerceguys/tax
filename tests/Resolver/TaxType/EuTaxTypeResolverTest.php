<?php

namespace CommerceGuys\Tax\Tests\Resolver;

use CommerceGuys\Addressing\AddressInterface;
use CommerceGuys\Tax\Repository\TaxTypeRepository;
use CommerceGuys\Tax\Resolver\TaxType\EuTaxTypeResolver;
use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Resolver\TaxType\EuTaxTypeResolver
 */
class EuTaxTypeResolverTest extends \PHPUnit_Framework_TestCase
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
        'at_vat' => [
            'name' => 'Austrian VAT',
            'generic_label' => 'vat',
            'zone' => 'at_vat',
            'tag' => 'EU',
            'rates' => [
                [
                    'id' => 'at_vat_standard',
                    'name' => 'Standard',
                    'default' => true,
                    'amounts' => [
                        [
                            'id' => 'at_vat_standard_1995',
                            'amount' => 0.2,
                            'start_date' => '1995-01-01'
                        ]
                    ]
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
                [
                    'type' => 'country',
                    'id' => 'de_vat_1',
                    'name' => 'Austria (Jungholz and Mittelberg)',
                    'country_code' => 'AT',
                    'included_postal_codes' => '6691, 6991:6993'
                ]
            ],
        ],
        'at_vat' => [
            'name' => 'Austria (VAT)',
            'members' => [
                [
                    'type' => 'country',
                    'id' => 'at_vat_0',
                    'name' => 'Austria (ex. Jungholz and Mittelberg)',
                    'country_code' => 'AT',
                    'excluded_postal_codes' => '6691, 6991:6993'
                ]
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
    public function testConstructor()
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
        $this->assertSame($taxTypeRepository, $this->getObjectAttribute($resolver, 'taxTypeRepository'));

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
     * @depends testConstructor
     * @dataProvider dataProvider
     */
    public function testResolver($taxable, $context, $expected, $resolver)
    {
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
    public function dataProvider()
    {
        $mockTaxableBuilder = $this->getMockBuilder('CommerceGuys\Tax\TaxableInterface');
        $physicalTaxable = $mockTaxableBuilder->getMock();
        $physicalTaxable->expects($this->any())
            ->method('isPhysical')
            ->will($this->returnValue(true));
        $digitalTaxable = $mockTaxableBuilder->getMock();

        $mockAddressBuilder = $this->getMockBuilder('CommerceGuys\Addressing\Address');
        $serbianAddress = $mockAddressBuilder->getMock();
        $serbianAddress->expects($this->any())
            ->method('getCountryCode')
            ->will($this->returnValue('RS'));
        $frenchAddress = $mockAddressBuilder->getMock();
        $frenchAddress->expects($this->any())
            ->method('getCountryCode')
            ->will($this->returnValue('FR'));
        $germanAddress = $mockAddressBuilder->getMock();
        $germanAddress->expects($this->any())
            ->method('getCountryCode')
            ->will($this->returnValue('DE'));
        $usAddress = $mockAddressBuilder->getMock();
        $usAddress->expects($this->any())
            ->method('getCountryCode')
            ->will($this->returnValue('US'));

        $date1 = new \DateTime('2014-02-24');
        $date2 = new \DateTime('2015-02-24');
        $notApplicable = EuTaxTypeResolver::NO_APPLICABLE_TAX_TYPE;

        $austrianAddress = $mockAddressBuilder->getMock();
        $austrianAddress->expects($this->any())
            ->method('getCountryCode')
            ->will($this->returnValue('AT'));
        $austrianAddress->method('getLocality')->will($this->returnValue('Wien'));
        $austrianAddress->method('getPostalCode')->will($this->returnValue('1017'));
        $austrianAddress->method('getAddressLine1')->will($this->returnValue('Dr.-Karl-Renner-Ring 3'));

        $austrianAddressUnderGermanVAT = $mockAddressBuilder->getMock();
        $austrianAddressUnderGermanVAT->expects($this->any())
            ->method('getCountryCode')
            ->will($this->returnValue('AT'));
        $austrianAddressUnderGermanVAT->method('getLocality')->will($this->returnValue('Mittelberg'));
        $austrianAddressUnderGermanVAT->method('getPostalCode')->will($this->returnValue('6992'));
        $austrianAddressUnderGermanVAT->method('getAddressLine1')->will($this->returnValue('Walserstraße 226'));


        return [
            // German customer, French store, VAT number provided.
            [$physicalTaxable, $this->getContext($germanAddress, $frenchAddress, '123'), 'eu_ic_vat'],
            // French customer, French store, VAT number provided.
            [$physicalTaxable, $this->getContext($frenchAddress, $frenchAddress, '123'), 'fr_vat'],
            // German customer, French store, physical product.
            [$physicalTaxable, $this->getContext($germanAddress, $frenchAddress), 'fr_vat'],
            // German customer, French store registered for German VAT, physical product.
            [$physicalTaxable, $this->getContext($germanAddress, $frenchAddress, '', ['DE']), 'de_vat'],
            // German customer, French store, digital product before Jan 1st 2015.
            [$digitalTaxable, $this->getContext($germanAddress, $frenchAddress, '', [], $date1), 'fr_vat'],
            // German customer, French store, digital product.
            [$digitalTaxable, $this->getContext($germanAddress, $frenchAddress, '', [], $date2), 'de_vat'],
            // German customer, US store, digital product
            [$digitalTaxable, $this->getContext($germanAddress, $usAddress, '', [], $date2), []],
            // German customer, US store registered in FR, digital product.
            [$digitalTaxable, $this->getContext($germanAddress, $usAddress, '', ['FR'], $date2), 'de_vat'],
            // German customer with VAT number, US store registered in FR, digital product.
            [$digitalTaxable, $this->getContext($germanAddress, $usAddress, '123', ['FR'], $date2), $notApplicable],
            // Serbian customer, French store, physical product.
            [$physicalTaxable, $this->getContext($serbianAddress, $frenchAddress), []],
            // French customer, Serbian store, physical product.
            [$physicalTaxable, $this->getContext($frenchAddress, $serbianAddress), []],
            // German customer, Austrian store under German VAT, VAT number provided.
            [$physicalTaxable, $this->getContext($germanAddress, $austrianAddressUnderGermanVAT, '123'), 'de_vat'],
            // Austrian customer, Austrian store under German VAT, VAT number provided.
            [$physicalTaxable, $this->getContext($austrianAddress, $austrianAddressUnderGermanVAT, '123'), 'eu_ic_vat'],
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
        $context->expects($this->any())
            ->method('getCustomerAddress')
            ->will($this->returnValue($customerAddress));
        $context->expects($this->any())
            ->method('getStoreAddress')
            ->will($this->returnValue($storeAddress));
        $context->expects($this->any())
            ->method('getCustomerTaxNumber')
            ->will($this->returnValue($customerTaxNumber));
        $context->expects($this->any())
            ->method('getStoreRegistrations')
            ->will($this->returnValue($storeRegistrations));
        $date = $date ?: new \DateTime();
        $context->expects($this->any())
            ->method('getDate')
            ->will($this->returnValue($date));

        return $context;
    }
}

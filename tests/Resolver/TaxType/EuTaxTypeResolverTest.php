<?php

namespace CommerceGuys\Tax\Tests\Resolver;

use CommerceGuys\Addressing\Model\AddressInterface;
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
    protected $taxTypes = array(
        'fr_vat' => array(
            'name' => 'French VAT',
            'zone' => 'fr_vat',
            'tag' => 'EU',
            'rates' => array(
                array(
                    'id' => 'fr_vat_standard',
                    'name' => 'Standard',
                    'display_name' => '% VAT',
                    'default' => true,
                    'amounts' => array(
                        array(
                            'id' => 'fr_vat_standard_196',
                            'amount' => 0.196,
                            'start_date' => '2004-04-01',
                            'end_date' => '2013-12-31',
                        ),
                        array(
                            'id' => 'fr_vat_standard_20',
                            'amount' => 0.2,
                            'start_date' => '2014-01-01',
                        ),
                    ),
                ),
            ),
        ),
        'de_vat' => array(
            'name' => 'German VAT',
            'zone' => 'de_vat',
            'tag' => 'EU',
            'rates' => array(
                array(
                    'id' => 'de_vat_standard',
                    'name' => 'Standard',
                    'display_name' => '% VAT',
                    'default' => true,
                    'amounts' => array(
                        array(
                            'id' => 'de_vat_standard_19',
                            'amount' => 0.19,
                            'start_date' => '2007-01-01',
                        ),
                    ),
                ),
            ),
        ),
        'eu_ic_vat' => array(
            'name' => 'Intra-Community Supply',
            'zone' => 'eu_vat',
            'tag' => 'EU',
            'rates' => array(
                array(
                    'id' => 'eu_ic_vat',
                    'name' => 'Intra-Community Supply',
                    'display_name' => '% VAT',
                    'default' => true,
                    'amounts' => array(
                        array(
                            'id' => 'eu_ic_vat',
                            'amount' => 0,
                        ),
                    ),
                ),
            ),
        ),
    );

    /**
     * Known zones.
     *
     * Note: The real fr_vat and de_vat zones are more complex, France excludes
     * Corsica, Germany excludes Heligoland and Bussingen, but includes 4
     * Austrian postal codes. Those details were irrelevant for this test.
     *
     * @var array
     */
    protected $zones = array(
        'fr_vat' => array(
            'name' => 'France (VAT)',
            'members' => array(
                array(
                    'type' => 'country',
                    'id' => '1',
                    'name' => 'France',
                    'country_code' => 'FR',
                ),
                array(
                    'type' => 'country',
                    'id' => '2',
                    'name' => 'Monaco',
                    'country_code' => 'MC',
                ),
            ),
        ),
        'de_vat' => array(
            'name' => 'Germany (VAT)',
            'members' => array(
                array(
                    'type' => 'country',
                    'id' => '2',
                    'name' => 'Germany',
                    'country_code' => 'DE',
                ),
            ),
        ),
        'eu_vat' => array(
            'name' => 'European Union (VAT)',
            'members' => array(
                array(
                    'type' => 'zone',
                    'id' => '3',
                    'name' => 'France (VAT)',
                    'zone' => 'fr_vat',
                ),
                array(
                    'type' => 'zone',
                    'id' => '4',
                    'name' => 'Germany (VAT)',
                    'zone' => 'de_vat',
                ),
            ),
        ),
    );

    /**
     * @covers ::__construct
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
     * @uses \CommerceGuys\Tax\Resolver\TaxType\StoreRegistrationCheckerTrait
     * @uses \CommerceGuys\Tax\Repository\TaxTypeRepository
     * @uses \CommerceGuys\Tax\Model\TaxType
     * @uses \CommerceGuys\Tax\Model\TaxRate
     * @uses \CommerceGuys\Tax\Model\TaxRateAmount
     * @depends testConstructor
     */
    public function testResolver($resolver)
    {
        $physicalTaxable = $this
            ->getMockBuilder('CommerceGuys\Tax\TaxableInterface')
            ->getMock();
        $physicalTaxable->expects($this->any())
            ->method('isPhysical')
            ->will($this->returnValue(true));
        $digitalTaxable = $this
            ->getMockBuilder('CommerceGuys\Tax\TaxableInterface')
            ->getMock();

        $serbianAddress = $this
            ->getMockBuilder('CommerceGuys\Addressing\Model\Address')
            ->getMock();
        $serbianAddress->expects($this->any())
            ->method('getCountryCode')
            ->will($this->returnValue('RS'));
        $frenchAddress = $this
            ->getMockBuilder('CommerceGuys\Addressing\Model\Address')
            ->getMock();
        $frenchAddress->expects($this->any())
            ->method('getCountryCode')
            ->will($this->returnValue('FR'));
        $germanAddress = $this
            ->getMockBuilder('CommerceGuys\Addressing\Model\Address')
            ->getMock();
        $germanAddress->expects($this->any())
            ->method('getCountryCode')
            ->will($this->returnValue('DE'));

        // German customer, French store, VAT number provided.
        $context = $this->getContext($germanAddress, $frenchAddress, '123');
        $results = $resolver->resolve($physicalTaxable, $context);
        $result = reset($results);
        $this->assertInstanceOf('CommerceGuys\Tax\Model\TaxType', $result);
        $this->assertEquals('eu_ic_vat', $result->getId());

        // German customer, French store, physical product.
        $context = $this->getContext($germanAddress, $frenchAddress);
        $results = $resolver->resolve($physicalTaxable, $context);
        $result = reset($results);
        $this->assertInstanceOf('CommerceGuys\Tax\Model\TaxType', $result);
        $this->assertEquals('fr_vat', $result->getId());

        // German customer, French store, physical product, store registered
        // for German VAT.
        $context = $this->getContext($germanAddress, $frenchAddress, '', array('DE'));
        $results = $resolver->resolve($physicalTaxable, $context);
        $result = reset($results);
        $this->assertInstanceOf('CommerceGuys\Tax\Model\TaxType', $result);
        $this->assertEquals('de_vat', $result->getId());

        // German customer, French store, digital product.
        $date = new \DateTime('2014-02-24');
        $context = $this->getContext($germanAddress, $frenchAddress, '', array(), $date);
        $results = $resolver->resolve($digitalTaxable, $context);
        $result = reset($results);
        $this->assertInstanceOf('CommerceGuys\Tax\Model\TaxType', $result);
        $this->assertEquals('fr_vat', $result->getId());

        // German customer, French store, digital product after Jan 1st 2015.
        $date = new \DateTime('2015-02-24');
        $context = $this->getContext($germanAddress, $frenchAddress, '', array(), $date);
        $results = $resolver->resolve($digitalTaxable, $context);
        $result = reset($results);
        $this->assertInstanceOf('CommerceGuys\Tax\Model\TaxType', $result);
        $this->assertEquals('de_vat', $result->getId());

        // Serbian customer, French store, physical product.
        $context = $this->getContext($serbianAddress, $frenchAddress);
        $result = $resolver->resolve($physicalTaxable, $context);
        $this->assertEquals(array(), $result);

        // French customer, Serbian store, physical product.
        $context = $this->getContext($frenchAddress, $serbianAddress);
        $result = $resolver->resolve($physicalTaxable, $context);
        $this->assertEquals(array(), $result);
    }

    /**
     * Returns a mock context based on the provided data.
     *
     * @param AddressInterface $customerAddress        The customer address.
     * @param AddressInterface $storeAddress           The store address.
     * @param string           $customerTaxNumber      The customer tax number.
     * @param array            $additionalTaxCountries Additional tax countries.
     * @param \DateTime        $date                   The date.
     *
     * @return \CommerceGuys\Tax\Resolver\Context
     */
    protected function getContext($customerAddress, $storeAddress, $customerTaxNumber = '', $additionalTaxCountries = array(), $date = null)
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
            ->method('getAdditionalTaxCountries')
            ->will($this->returnValue($additionalTaxCountries));
        $date = $date ?: new \DateTime();
        $context->expects($this->any())
            ->method('getDate')
            ->will($this->returnValue($date));

        return $context;
    }
}

<?php

declare(strict_types=1);

namespace CommerceGuys\Tax\Tests\Repository;

use CommerceGuys\Tax\Repository\ZoneRepository;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CommerceGuys\Tax\Repository\ZoneRepository
 */
final class ZoneRepositoryTest extends TestCase
{
    /**
     * Known zones.
     *
     * @var array
     */
    protected $zones = [
        'de_vat' => [
            'name' => 'Germany (VAT)',
            'members' => [
                [
                    'type' => 'country',
                    'country_code' => 'DE',
                ],
                [
                    'type' => 'country',
                    'country_code' => 'AT',
                    'included_postal_codes' => '6691, 6991:6993',
                    'administrative_area' => 'dummyArea',
                    'locality' => 'dummyLocality',
                    'dependent_locality' => 'dummyDependentLocality',
                    'excluded_postal_codes' => '123456ExPostcodes',
                ],
            ],
        ],
    ];

    /**
     * @covers ::__construct
     */
    public function testConstructor(): ZoneRepository
    {
        // Mock the existence of JSON definitions on the filesystem.
        $root = vfsStream::setup('resources');
        $directory = vfsStream::newDirectory('zone')->at($root);
        foreach ($this->zones as $id => $definition) {
            $filename = $id . '.json';
            vfsStream::newFile($filename)->at($directory)->setContent(json_encode($definition));
        }

        // Instantiate the zone repository and confirm that the
        // definition path was properly set.
        $zoneRepository = new ZoneRepository('vfs://resources/zone/');
        $definitionPath = $this->getObjectAttribute($zoneRepository, 'definitionPath');
        $this->assertEquals('vfs://resources/zone/', $definitionPath);

        return $zoneRepository;
    }

    /**
     * @covers ::get
     * @covers ::loadDefinition
     * @covers ::createZoneFromDefinition
     *
     * @uses \CommerceGuys\Addressing\Zone\Zone
     * @uses \CommerceGuys\Addressing\Zone\ZoneTerritory
     * @uses \CommerceGuys\Addressing\PostalCodeHelper
     * @depends testConstructor
     */
    public function testGet(ZoneRepository $zoneRepository): void
    {
        $zone = $zoneRepository->get('de_vat');
        $this->assertInstanceOf('CommerceGuys\Addressing\Zone\Zone', $zone);
        $this->assertEquals('de_vat', $zone->getId());
        $this->assertEquals('Germany (VAT)', $zone->getLabel());
        $territories = $zone->getTerritories();
        $this->assertCount(2, $territories);

        $germanyTerritory = $territories[0];
        $this->assertInstanceOf('CommerceGuys\Addressing\Zone\ZoneTerritory', $germanyTerritory);
        $this->assertEquals('DE', $germanyTerritory->getCountryCode());

        $austriaTerritory = $territories[1];
        $this->assertInstanceOf('CommerceGuys\Addressing\Zone\ZoneTerritory', $austriaTerritory);
        $this->assertEquals('AT', $austriaTerritory->getCountryCode());
        $this->assertEquals('6691, 6991:6993', $austriaTerritory->getIncludedPostalCodes());
        $this->assertEquals('123456ExPostcodes', $austriaTerritory->getExcludedPostalCodes());
        $this->assertEquals('dummyArea', $austriaTerritory->getAdministrativeArea());
        $this->assertEquals('dummyLocality', $austriaTerritory->getLocality());
        $this->assertEquals('dummyDependentLocality', $austriaTerritory->getDependentLocality());

        // Test the static cache.
        $sameZone = $zoneRepository->get('de_vat');
        $this->assertSame($zone, $sameZone);
    }

    /**
     * @covers ::get
     * @covers ::loadDefinition
     * @covers ::createZoneFromDefinition
     * @depends testConstructor
     */
    public function testGetNonExistingZone(ZoneRepository $zoneRepository): void
    {
        $this->expectException(\CommerceGuys\Tax\Exception\UnknownZoneException::class);
        $zone = $zoneRepository->get('fr_vat');
    }

    /**
     * @covers ::getAll
     * @covers ::loadDefinition
     * @covers ::createZoneFromDefinition
     *
     * @uses \CommerceGuys\Tax\Repository\ZoneRepository::get
     * @uses \CommerceGuys\Addressing\Zone\Zone
     * @uses \CommerceGuys\Addressing\Zone\ZoneTerritory
     * @uses \CommerceGuys\Addressing\PostalCodeHelper
     * @depends testConstructor
     */
    public function testGetAll(ZoneRepository $zoneRepository): void
    {
        $zones = $zoneRepository->getAll();
        $this->assertCount(1, $zones);
        $this->assertArrayHasKey('de_vat', $zones);
        $this->assertEquals($zones['de_vat']->getId(), 'de_vat');
    }
}

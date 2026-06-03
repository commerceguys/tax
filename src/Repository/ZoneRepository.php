<?php

declare(strict_types=1);

namespace CommerceGuys\Tax\Repository;

use CommerceGuys\Addressing\Zone\Zone;
use CommerceGuys\Tax\Exception\UnknownZoneException;

/**
 * Manages zones based on JSON definitions.
 */
class ZoneRepository
{
    /**
     * The path where zone definitions are stored.
     *
     * @var string
     */
    protected $definitionPath;

    /**
     * Zone index.
     *
     * @var array
     */
    protected $zoneIndex = [];

    /**
     * Zones.
     *
     * @var Zone[]
     */
    protected $zones = [];

    /**
     * Creates a ZoneRepository instance.
     *
     * @param string $definitionPath Path to the zone definitions.
     */
    public function __construct(string $definitionPath)
    {
        $this->definitionPath = $definitionPath;
    }

    public function get(string $id): Zone
    {
        if (!isset($this->zones[$id])) {
            $definition = $this->loadDefinition($id);
            $this->zones[$id] = $this->createZoneFromDefinition($definition);
        }

        return $this->zones[$id];
    }

    public function getAll(): array
    {
        // Build the list of all available zones.
        if (empty($this->zoneIndex)) {
            if ($handle = opendir($this->definitionPath)) {
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, 0, 1) != '.') {
                        $id = strtok($entry, '.');
                        $this->zoneIndex[] = $id;
                    }
                }
                closedir($handle);
            }
        }

        $zones = [];
        foreach ($this->zoneIndex as $id) {
            $zones[$id] = $this->get($id);
        }

        return $zones;
    }

    /**
     * Loads the zone definition for the provided id.
     *
     * @param string $id The zone id.
     * @return array The zone definition.
     */
    protected function loadDefinition(string $id): array
    {
        $filename = $this->definitionPath . $id . '.json';
        $definition = @file_get_contents($filename);
        if (empty($definition)) {
            throw new UnknownZoneException($id);
        }
        $definition = json_decode($definition, true);
        $definition['id'] = $id;

        return $definition;
    }

    /**
     * Creates a Zone instance from the provided definition.
     *
     * @param array $definition The zone definition.
     * @return Zone
     */
    protected function createZoneFromDefinition(array $definition): Zone
    {
        $definition['label'] = $definition['name'];

        $territories = [];
        foreach ($definition['members'] as $member) {
            if ($member['type'] === 'zone') {
                foreach ($this->loadDefinition($member['zone'])['members'] as $otherTerritory) {
                    $territories[] = $otherTerritory;
                }
            } else {
                $territories[] = $member;
            }
        }
        $definition['territories'] = $territories;
        return new Zone($definition);
    }
}

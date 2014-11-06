<?php

namespace CommerceGuys\Tax\Repository;

use CommerceGuys\Tax\Exception\UnknownTaxTypeException;
use CommerceGuys\Tax\Model\TaxType;
use CommerceGuys\Tax\Model\TaxRate;
use CommerceGuys\Tax\Model\TaxRateAmount;
use CommerceGuys\Zone\Repository\ZoneRepository;
use CommerceGuys\Zone\Repository\ZoneRepositoryInterface;

/**
 * Manages tax types based on JSON definitions.
 */
class TaxTypeRepository implements TaxTypeRepositoryInterface
{
    /**
     * The path where the tax type and zone definitions are stored.
     *
     * @var string
     */
    protected $definitionPath;

    /**
     * The zone repository.
     *
     * @var ZoneRepositoryInterface
     */
    protected $zoneRepository;

    /**
     * Tax type index.
     *
     * @var array
     */
    protected $taxTypeIndex = array();

    /**
     * Tax types.
     *
     * @var array
     */
    protected $taxTypes = array();

    /**
     * Creates a TaxRepository instance.
     *
     * @param string $definitionPath The path to the tax type and zone
     *                               definitions. Defaults to 'resources/'.
     */
    public function __construct($definitionPath = null, ZoneRepositoryInterface $zoneRepository = null)
    {
        $definitionPath = $definitionPath ?: __DIR__ . '/../../resources/';
        $this->definitionPath = $definitionPath . 'tax_type/';
        $this->zoneRepository = $zoneRepository ?: new ZoneRepository($definitionPath . 'zone/');
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!isset($this->taxTypes[$id])) {
            $definition = $this->loadDefinition($id);
            $this->taxTypes[$id] = $this->createTaxTypeFromDefinition($definition);
        }

        return $this->taxTypes[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        // Build the list of all available tax types.
        if (empty($this->taxTypeIndex)) {
            if ($handle = opendir($this->definitionPath)) {
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, 0, 1) != '.') {
                        $id = strtok($entry, '.');
                        $this->taxTypeIndex[] = $id;
                    }
                }
                closedir($handle);
            }
        }

        // Load each tax type.
        $taxTypes = array();
        foreach ($this->taxTypeIndex as $id) {
            $taxTypes[$id] = $this->get($id);
        }

        return $taxTypes;
    }

    /**
     * Loads the tax type definition for the provided id.
     *
     * @param string $id The zone id.
     *
     * @return array The zone definition.
     */
    protected function loadDefinition($id)
    {
        $filename = $this->definitionPath . $id . '.json';
        $definition = @file_get_contents($filename);
        if (empty($definition)) {
            throw new UnknownTaxTypeException($id);
        }
        $definition = json_decode($definition, true);
        $definition['id'] = $id;

        return $definition;
    }

    /**
     * Creates a tax type object from the provided definition.
     *
     * @param array $definition The tax type definition.
     *
     * @return TaxType
     */
    protected function createTaxTypeFromDefinition(array $definition)
    {
        $zone = $this->zoneRepository->get($definition['zone']);
        // Provide defaults.
        if (!isset($definition['compound'])) {
            $definition['compound'] = false;
        }
        if (!isset($definition['rounding_mode'])) {
            $definition['rounding_mode'] = PHP_ROUND_HALF_UP;
        }

        $type = new TaxType();
        $type->setId($definition['id']);
        $type->setName($definition['name']);
        $type->setCompound($definition['compound']);
        $type->setRoundingMode($definition['rounding_mode']);
        $type->setZone($zone);
        foreach ($definition['rates'] as $rateDefinition) {
            $rate = $this->createTaxRateFromDefinition($rateDefinition);
            $type->addRate($rate);
        }

        return $type;
    }

    /**
     * Creates a tax rate object from the provided definition.
     *
     * @param array $definition The tax rate definition.
     *
     * @return TaxRate
     */
    protected function createTaxRateFromDefinition(array $definition)
    {
        $rate = new TaxRate();
        $rate->setId($definition['id']);
        $rate->setName($definition['name']);
        $rate->setDisplayName($definition['display_name']);
        foreach ($definition['amounts'] as $amountDefinition) {
            $amount = $this->createTaxRateAmountFromDefinition($amountDefinition);
            $rate->addAmount($amount);
        }

        return $rate;
    }

    /**
     * Creates a tax rate amount object from the provided definition.
     *
     * @param array $definition The tax rate amount definition.
     *
     * @return TaxRateAmount
     */
    protected function createTaxRateAmountFromDefinition(array $definition)
    {
        $amount = new TaxRateAmount();
        $amount->setId($definition['id']);
        $amount->setAmount($definition['amount']);
        if (isset($definition['start_date'])) {
            $amount->setStartDate(new \DateTime($definition['start_date']));
        }
        if (isset($definition['end_date'])) {
            $amount->setEndDate(new \DateTime($definition['end_date']));
        }

        return $amount;
    }
}

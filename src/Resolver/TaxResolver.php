<?php

namespace CommerceGuys\Tax\Resolver;

use CommerceGuys\Tax\TaxableInterface;
use CommerceGuys\Tax\Resolver\Engine\TaxRateResolverEngineInterface;
use CommerceGuys\Tax\Resolver\Engine\TaxTypeResolverEngineInterface;

class TaxResolver implements TaxResolverInterface
{
    /**
     * The tax type resolver engine.
     *
     * @var TaxTypeResolverEngineInterface
     */
    protected $taxTypeResolverEngine;

    /**
     * The tax rate resolver engine.
     *
     * @var TaxRateResolverEngineInterface
     */
    protected $taxRateResolverEngine;

    /**
     * Creates a TaxResolver instance.
     *
     * @param TaxTypeResolverEngineInterface $taxTypeResolverEngine
     * @param TaxRateResolverEngineInterface $taxRateResolverEngine
     */
    public function __construct($taxTypeResolverEngine, $taxRateResolverEngine)
    {
        $this->taxTypeResolverEngine = $taxTypeResolverEngine;
        $this->taxRateResolverEngine = $taxRateResolverEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveAmounts(TaxableInterface $taxable, Context $context)
    {
        $date = $context->getDate();
        $rates = $this->resolveRates($taxable, $context);
        $amounts = [];
        foreach ($rates as $rate) {
            $amounts[] = $rate->getAmount($date);
        }

        return $amounts;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRates(TaxableInterface $taxable, Context $context)
    {
        $types = $this->resolveTypes($taxable, $context);
        $rates = [];
        foreach ($types as $type) {
            $rate = $this->taxRateResolverEngine->resolve($type, $taxable, $context);
            if ($rate) {
                $rates[] = $rate;
            }
        }

        return $rates;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTypes(TaxableInterface $taxable, Context $context)
    {
        return $this->taxTypeResolverEngine->resolve($taxable, $context);
    }
}

<?php

namespace CommerceGuys\Tax\Resolver\TaxType;

use CommerceGuys\Tax\TaxableInterface;
use CommerceGuys\Tax\Repository\TaxTypeRepositoryInterface;
use CommerceGuys\Tax\Resolver\Context;

/**
 * Resolver for Canada's tax types (HST, PST, GST).
 */
class CanadaTaxTypeResolver implements TaxTypeResolverInterface
{
    /**
     * The tax type repository
     *
     * @param TaxTypeRepositoryInterface
     */
    protected $taxTypeRepository;

    /**
     * Creates a CanadaTaxTypeResolver instance.
     *
     * @param TaxTypeRepositoryInterface $taxTypeRepository The tax type repository.
     */
    public function __construct(TaxTypeRepositoryInterface $taxTypeRepository)
    {
        $this->taxTypeRepository = $taxTypeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(TaxableInterface $taxable, Context $context)
    {
        $customerAddress = $context->getCustomerAddress();
        if ($customerAddress->getCountryCode() != 'CA') {
            return array();
        }

        // Canadian tax types are matched by the customer address.
        // If the customer is from Ontario, the tax types are for Ontario.
        $taxTypes = $this->taxTypeRepository->getAll();
        $results = array();
        foreach ($taxTypes as $taxType) {
            if ($taxType->getTag() == 'CA') {
                $zone = $taxType->getZone();
                if ($zone->match($customerAddress)) {
                    $results[] = $taxType;
                }
            }
        }

        return $results;
    }
}

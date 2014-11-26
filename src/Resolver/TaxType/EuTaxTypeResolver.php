<?php

namespace CommerceGuys\Tax\Resolver\TaxType;

use CommerceGuys\Tax\TaxableInterface;
use CommerceGuys\Tax\Repository\TaxTypeRepositoryInterface;
use CommerceGuys\Tax\Resolver\Context;

/**
 * Resolver for EU VAT.
 */
class EuTaxTypeResolver implements TaxTypeResolverInterface
{
    use StoreRegistrationCheckerTrait;

    /**
     * The tax type repository
     *
     * @param TaxTypeRepositoryInterface
     */
    protected $taxTypeRepository;

    /**
     * Creates a EuTaxTypeResolver instance.
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
        $taxTypes = $this->taxTypeRepository->getAll();
        $customerAddress = $context->getCustomerAddress();
        $storeAddress = $context->getStoreAddress();
        // Match the customer and store zones, gather the relevant tax types.
        $customerZone = null;
        $storeZone = null;
        $customerTaxTypes = array();
        $storeTaxTypes = array();
        foreach ($taxTypes as $taxType) {
            if ($taxType->getTag() == 'EU') {
                $zone = $taxType->getZone();
                if ($zone->match($customerAddress)) {
                    $customerTaxTypes[] = $taxType;
                    $customerZone = $zone;
                }
                if ($zone->match($storeAddress)) {
                    $storeTaxTypes[] = $taxType;
                    $storeZone = $zone;
                }
            }
        }

        if (is_null($customerZone) || is_null($storeZone)) {
            // The customer or the store is not in the EU.
            return array();
        }

        $resolvedTaxTypes = array();
        $taxNumber = $context->getCustomerTaxNumber();
        $date = $context->getDate();
        if (!empty($taxNumber)) {
            // Intra-community supply.
            $icTaxType = $this->taxTypeRepository->get('eu_ic_vat');
            $resolvedTaxTypes = array($icTaxType);
        } elseif ($date->format('Y') >= '2015' && !$taxable->isPhysical()) {
            // Since january 1st 2015 all digital products inside the EU use
            // the destination tax type(s). For example, an ebook sold from
            // France to Germany needs to have German VAT applied.
            $resolvedTaxTypes = $customerTaxTypes;
        } else {
            // Physical products use the origin tax types, unless the store is
            // registered to pay taxes in the destination zone. This is required
            // when the total yearly transactions breach the defined threshold.
            // See http://www.vatlive.com/eu-vat-rules/vat-registration-threshold/
            $resolvedTaxTypes = $storeTaxTypes;
            if ($this->checkStoreRegistration($customerZone, $context)) {
                $resolvedTaxTypes = $customerTaxTypes;
            }
        }

        return $resolvedTaxTypes;
    }
}

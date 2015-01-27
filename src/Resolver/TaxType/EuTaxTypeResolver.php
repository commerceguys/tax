<?php

namespace CommerceGuys\Tax\Resolver\TaxType;

use CommerceGuys\Addressing\Model\AddressInterface;
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
        $taxTypes = $this->getTaxTypes();
        $customerAddress = $context->getCustomerAddress();
        $customerTaxTypes = $this->filterByAddress($taxTypes, $customerAddress);
        if (empty($customerTaxTypes)) {
            // The customer is not in the EU.
            return array();
        }
        $storeAddress = $context->getStoreAddress();
        $storeTaxTypes = $this->filterByAddress($taxTypes, $storeAddress);
        if (empty($storeTaxTypes)) {
            // The store is not in the EU.
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
            $customerTaxType = reset($customerTaxTypes);
            if ($this->checkStoreRegistration($customerTaxType->getZone(), $context)) {
                $resolvedTaxTypes = $customerTaxTypes;
            }
        }

        return $resolvedTaxTypes;
    }

    /**
     * Filters out tax types not matching the provided address.
     *
     * @param TaxTypeInterface[] $taxTypes The tax types to filter.
     * @param AddressInterface   $address  The address to filter by.
     *
     * @return TaxTypeInterface[] An array of tax types whose zones match the
     *                            provided address.
     */
    protected function filterByAddress(array $taxTypes, AddressInterface $address)
    {
        $taxTypes = array_filter($taxTypes, function ($taxType) use ($address) {
            $zone = $taxType->getZone();

            return $zone->match($address);
        });

        return $taxTypes;
    }

    /**
     * Returns the EU tax types.
     *
     * @return TaxTypeInterface[] An array of EU tax types.
     */
    protected function getTaxTypes()
    {
        $taxTypes = $this->taxTypeRepository->getAll();
        $taxTypes = array_filter($taxTypes, function ($taxType) {
            return $taxType->getTag() == 'EU';
        });

        return $taxTypes;
    }
}

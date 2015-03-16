<?php

namespace CommerceGuys\Tax\Resolver\TaxType;

use CommerceGuys\Addressing\Model\AddressInterface;
use CommerceGuys\Tax\TaxableInterface;
use CommerceGuys\Tax\Model\TaxTypeInterface;
use CommerceGuys\Tax\Repository\TaxTypeRepositoryInterface;
use CommerceGuys\Tax\Resolver\Context;
use CommerceGuys\Tax\Resolver\Enum\EuTaxableType;

/**
 * Resolver for EU VAT.
 *
 * Based on the "Where to tax?" from the European Commission
 * http://ec.europa.eu/taxation_customs/taxation/vat/how_vat_works/vat_on_services/index_en.htm
 *
 */
class EuTaxTypeResolver implements TaxTypeResolverInterface
{
    use StoreRegistrationCheckerTrait;

    /**
     * The tax type repository.
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

        $storeAddress = $context->getStoreAddress();
        $storeCountry = $storeAddress->getCountryCode();
        $storeTaxTypes = $this->filterByAddress($taxTypes, $storeAddress);
        $storeTaxType = reset($storeTaxTypes);
        $storeRegistrationTaxTypes = $this->filterByStoreRegistration($taxTypes, $context);

        // The store is not in the EU nor registered to collect EU VAT.
        if (empty($storeTaxTypes) && empty($storeRegistrationTaxTypes)) {
            return [];
        }

        $customerAddress = $context->getCustomerAddress();
        $customerTaxTypes = $this->filterByAddress($taxTypes, $customerAddress);
        $customerTaxType = reset($customerTaxTypes);

        $taxableAddress = $taxable->getAddress();

        // Work out where the place of supply is.
        $taxableType = $taxable->getTaxableType('EuTaxableType');
        switch ($taxableType) {
            case EuTaxableType::GOODS:
                // Article 31 & 32, Goods sold in person to the customer, or collected by the customer from the supplier.
                $place = $storeAddress;
                break;
            case EuTaxableType::GOODS_DISTANCE:
                // Article 33 & 34, Distance Selling
                if ($this->checkStoreRegistration($customerTaxType->getZone(), $context)) {
                    // The supplier has gone over the distance selling threshold for the customers country.
                    $place = $customerAddress;
                } else {
                    $place = $storeAddress;
                }
                break;
            case EuTaxableType::GOODS_INSTALLED:
                // Article 36, where the goods are being installed or assembled.
                if ($taxableAddress) {
                    // If an address is supplied on the Taxable we will use this as the location where it is being installed.
                    $place = $taxableAddress;
                } else {
                    // Else we assume it is where the customer is located.
                    $place = $customerAddress;
                }
                break;
            case EuTaxableType::GOODS_ONBOARD:
                // Article 37, goods onboard forms of transport.
                if ($taxableAddress) {
                    // If an address is supplied on the Taxable we will use this as the location the transport started.
                    $place = $taxableAddress;
                } else {
                    $place = $storeAddress;
                }
                break;
            case EuTaxableType::GOODS_POWER:
                // Article 38 & 39, electricity or gas supplied through the natural gas distribution system are taxed
                // there the dealer or customer is located.
                $place = $customerAddress;
                break;
            case EuTaxableType::SERVICE:
                // Article 44 & 45, The supply of services between businesses (B2B services) is in principle taxed at
                // the customer's place of establishment, while services supplied to private individuals (B2C services)
                // are taxed at the supplier's place of establishment.
                if ($context->getCustomerTaxNumber()) {
                    $place = $customerAddress;
                } else {
                    $place = $storeAddress;
                }
                break;
            case EuTaxableType::SERVICE_BTE:
                // Article 58, Electronically supplied services, provided by suppliers established in a third country
                // to non-taxable persons (B2C) established in the EU, must be taxed at the place where the customer
                // resides or has a permanent address, and from 1 January 2015 B2C telecommunications, broadcasting and
                // electronically supplied services will be taxed at the place where the private customer is
                // established, has his permanent address or usually resides.
                // Article 59, B2C services like advertising services, services of consultants and lawyers,
                // financial services, telecommunications services, broadcasting services and electronically supplied
                // services are taxed at the place where the customer is established provided the customer is
                // established in a non-EU country.
                // Article 59b, Radio and television broadcasting services and telecommunications services, supplied by
                // suppliers established in a third country to non-taxable customers (B2C) in the EU, are taxable at
                // the place where the private customer effectively uses and enjoys the service.
                if ($taxableAddress) {
                    $place = $taxableAddress;
                } else {
                    $place = $customerAddress;
                }
                break;
            case EuTaxableType::SERVICE_EVENT_ADMISSION:
                // Article 53, B2B services in respect of admission to cultural, artistic, sporting, scientific,
                // educational, entertainment and similar events will be taxed at the place where those events
                // actually take place.
                if ($taxableAddress) {
                    // If an address is supplied on the Taxable we use this as the location of the event.
                    $place = $taxableAddress;
                } else {
                    // Else the event is taking place at the location of the supplier.
                    $place = $storeAddress;
                }
                break;
            case EuTaxableType::SERVICE_EVENT_SERVICE:
                // Article 54, B2C services relating to cultural, artistic, sporting, scientific, educational,
                // entertainment and similar activities will be taxed at the place where those activities actually take place.
                if ($taxableAddress && !$context->getCustomerTaxNumber()) {
                    // If there is a taxable address and the Customer is not a business.
                    $place = $taxableAddress;
                } else {
                    $place = $storeAddress;
                }
                break;
            case EuTaxableType::SERVICE_LAND:
                // Article 47, B2B and B2C services connected with immovable property are taxed where the immovable
                // property is located.
                if ($taxableAddress) {
                    $place = $taxableAddress;
                } else {
                    $place = $customerAddress;
                }
                break;

            case EuTaxableType::SERVICE_GOODS:
                // Article 54, B2C services consisting of valuations of or works on movable tangible property are taxed
                // at the place where the services are physically delivered.
                if ($taxableAddress) {
                    // If there is a Taxable address we will use this as the location of the goods.
                    $place = $taxableAddress;
                } else {
                    // If not we will use the customers location as the place of the goods.
                    $place = $customerAddress;
                }
                break;
            case EuTaxableType::SERVICE_INTERMEDIARIES:
                // Article 46, B2C services provided by an intermediary are taxed at the location where the main
                // transaction, in which the intermediary intervenes, is taxable.
                if ($taxableAddress && !$context->getCustomerTaxNumber()) {
                    // If there is a taxable address and the Customer is not a business.
                    $place = $taxableAddress;
                } else {
                    $place = $customerAddress;
                }
                break;
            case EuTaxableType::SERVICE_TRANSPORT_GOODS:
                // Article 49, B2C transport of goods, other than intra-Community transport, is taxed according to the
                // distances covered.
                // Article 50, B2C intra-Community transport of goods (goods departing from one Member State and
                // arriving in another) is taxed at the place of departure
                if ($taxableAddress) {
                    $place = $taxableAddress;
                } elseif ($context->getCustomerTaxNumber()) {
                    $place = $customerAddress;
                } else {
                    $place = $storeAddress;
                }
                break;
            case EuTaxableType::SERVICE_TRANSPORT_HIRE:
                // Article 56, B2B and B2C short-term hiring of means of transport is taxed at the place where the
                // means of transport is actually put at the disposal of the customer.
                if ($taxableAddress) {
                    $place = $taxableAddress;
                } elseif ($context->getCustomerTaxNumber()) {
                    $place = $customerAddress;
                } else {
                    $place = $storeAddress;
                }
                break;
            case EuTaxableType::SERVICE_TRANSPORT_PASSENGER:
                // Article 48, B2B and B2C passenger transport is taxed according to the distances covered.
                // @todo Handle multiple places of supply for one taxable.
                break;
        }

        $placeTaxTypes = $this->filterByAddress($taxTypes, $place);
        $placeTaxType = reset($placeTaxTypes);

        if (empty($placeTaxTypes)) {
            // The place of supply is not in the EU.
            return [];
        }

        if ($context->getCustomerTaxNumber() && ($storeTaxType != $placeTaxType || in_array($placeTaxType, $storeRegistrationTaxTypes))) {
            // Intra-community reverse charge can be used.
            $icTaxType = $this->taxTypeRepository->get('eu_ic_vat');
            return [$icTaxType];
        }

        return $placeTaxTypes;

    }

    /**
     * Filters out tax types not matching the provided address.
     *
     * @param TaxTypeInterface[] $taxTypes The tax types to filter.
     * @param AddressInterface $address The address to filter by.
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
            // "eu_ic_vat" is not resolved via its zone, so it isn't needed.
            return $taxType->getId() != 'eu_ic_vat' && $taxType->getTag() == 'EU';
        });

        return $taxTypes;
    }
}

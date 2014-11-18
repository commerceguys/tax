<?php

namespace CommerceGuys\Tax\Resolver\TaxType;

use CommerceGuys\Addressing\Model\Address;
use CommerceGuys\Tax\Resolver\Context;
use CommerceGuys\Zone\Model\ZoneInterface;

trait StoreRegistrationCheckerTrait
{
    /**
     * Checks whether the store is registered to collect taxes in the given zone.
     *
     * @param ZoneInterface $zone    The zone.
     * @param Context       $context The context containing store information.
     *
     * @return bool True if the store is registered to collect taxes in the
     *              given zone, false otherwise.
     */
    protected function checkStoreRegistration(ZoneInterface $zone, Context $context)
    {
        $additionalTaxCountries = $context->getAdditionalTaxCountries();
        foreach ($additionalTaxCountries as $additionalTaxCountry) {
            $address = new Address();
            $address->setCountryCode($additionalTaxCountry);
            if ($zone->match($address)) {
                return true;
            }
        }

        return false;
    }
}

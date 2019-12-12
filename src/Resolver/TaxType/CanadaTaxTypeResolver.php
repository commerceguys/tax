<?php

namespace CommerceGuys\Tax\Resolver\TaxType;

use CommerceGuys\Tax\TaxableInterface;
use CommerceGuys\Tax\Repository\TaxTypeRepositoryInterface;
use CommerceGuys\Tax\Resolver\Context;

/**
 * Resolver for Canada's tax types (HST, PST, GST).
 * GST  Goods and Services Tax
 * HST  Harmonized Sales Tax
 * PST  Provincial Sales Tax (generic provincial label)
 * QST  Quebec Sales Tax (local label for PST)
 * RST  Retail Sales Tax (local label for PST)
 */
class CanadaTaxTypeResolver implements TaxTypeResolverInterface
{
	use StoreRegistrationCheckerTrait;

	/**
	 * The tax type repository.
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
		$storeAddress = $context->getStoreAddress();
		if ($customerAddress->getCountryCode() != 'CA' || $storeAddress->getCountryCode() != 'CA') {
			// The customer or the store is not in Canada.
			return [];
		}
		// Canadian tax types are divided in two seperate levels:
		// FEDERAL - GST and HST always apply between canadian stores and clients
		// PROVINCIAL - PST, QST and RST only apply when the store is registered in the client's province
		$taxTypes = $this->getTaxTypes();
		$storeProvince = $storeAddress->getAdministrativeArea();
		$results = [];
		foreach ($taxTypes as $taxType) {
			$zone = $taxType->getZone();
			if ($zone->match($customerAddress)) {

				// Federal taxes (GST or HST)
				if ($taxType->getGenericLabel() == 'GST' || $taxType->getGenericLabel() == 'HST') {
					$results[] = $taxType;
				}
				// Provincial taxes (PST, QST, RST), where the store is registered
				$storeRegistrationTaxProvinces = $context->getStoreRegistrations();
				// Add store's province of origin
				$storeRegistrationTaxProvinces[] = !in_array($storeProvince, $storeRegistrationTaxProvinces) ? $storeProvince : '';
				if (!empty($storeRegistrationTaxProvinces)) {
					foreach ($storeRegistrationTaxProvinces as $province) {
						if ($zone->getMembers()->first()->getAdministrativeArea() == $province) {
							$results[] = $taxType;
						}
					}
				}
			}
		}

		return $results;
	}

	/**
	 * Returns the Canadian tax types.
	 *
	 * @return TaxTypeInterface[] An array of Canadian tax types.
	 */
	protected function getTaxTypes()
	{
		$taxTypes = $this->taxTypeRepository->getAll();
		$taxTypes = array_filter($taxTypes, function ($taxType) {
			return $taxType->getTag() == 'CA';
		});

		return $taxTypes;
	}
}

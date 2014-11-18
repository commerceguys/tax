tax
===

[![Build Status](https://travis-ci.org/commerceguys/tax.svg?branch=master)](https://travis-ci.org/commerceguys/tax)

A PHP 5.4+ tax management library.

Features:
- Smart data model designed for fluctuating tax rate amounts ("19% -> 21% on January 1st")
- Predefined tax rates for EU countries and Switzerland. More to come.
- Tax resolvers with logic for all major use cases.

Requires [commerceguys/zone](https://github.com/commerceguys/zone).

The backstory behind the library design can be found in [this blog post](https://drupalcommerce.org/blog/31036/commerce-2x-stories-taxes).

Don't see your country's tax types and rates in the dataset? Send us a PR!

# Data model

[Zone](https://github.com/commerceguys/zone/blob/master/src/Model/ZoneInterface.php) 1-1 [TaxType](https://github.com/commerceguys/tax/blob/master/src/Model/TaxTypeInterface.php) 1-n [TaxRate](https://github.com/commerceguys/tax/blob/master/src/Model/TaxRateInterface.php) 1-n [TaxRateAmount](https://github.com/commerceguys/tax/blob/master/src/Model/TaxRateAmountInterface.php)

Each tax type has a zone and one or more tax rates.
Each tax rate has one or more tax rate amounts.

Example:
- Tax type: French VAT
- Zone: "France (VAT)" (covers "France without Corsica" and "Monaco")
- Tax rates: Standard, Intermediate, Reduced, Super Reduced
- Tax rate amounts for Standard: 19.6% (until January 1st 2014), 20% (from January 1st 2014)


<?php

namespace CommerceGuys\Tax\Resolver\TaxType;

use CommerceGuys\Tax\TaxableInterface;
use CommerceGuys\Tax\Resolver\Context;
use CommerceGuys\Tax\Resolver\TaxType\TaxTypeResolverInterface;

/**
 * Chain tax type resolver interface.
 *
 * Sorts the provided tax type resolvers by priority and invokes them
 * individually until one of them returns a result.
 */
interface ChainTaxTypeResolverInterface
{
    /**
     * Adds a resolver.
     *
     * @param TaxTypeResolverInterface $resolver The resolver.
     * @param int                      $priority The priority of the resolver.
     */
    public function add(TaxTypeResolverInterface $resolver, $priority = 0);

    /**
     * Gets all added resolvers, sorted by priority.
     *
     * @return TaxTypeResolverInterface[] An array of tax type resolvers.
     */
    public function getAll();

    /**
     * Resolves the tax type by invoking the individual resolvers.
     *
     * @param TaxableInteface The taxable object.
     * @param Context $context The context.
     *
     * @return TaxTypeInterface[] An array of resolved tax types, if any.
     */
    public function resolve(TaxableInterface $taxable, Context $context);
}

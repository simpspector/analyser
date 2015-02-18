<?php

namespace SimpSpector\Analyser\Gadget;

use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author David Badura <d.a.badura@gmail.com>
 * @author Tobias Olry <tobias.olry@gmail.com>
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
abstract class AbstractGadget implements GadgetInterface
{
    /**
     * @param array $options
     * @param string[] $defaults
     * @param string[] $fieldsToNormalize
     * @return array
     */
    protected function prepareOptions(array $options, array $defaults, array $fieldsToNormalize = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults($defaults);

        $normalizers = [];
        foreach ($fieldsToNormalize as $field) {
            $normalizers[$field] = function (Options $options, $value) {
                return is_array($value) ? $value : [$value];
            };
        }
        $resolver->setNormalizers($normalizers);

        return $resolver->resolve($options);
    }
}

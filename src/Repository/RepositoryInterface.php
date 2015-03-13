<?php

namespace SimpSpector\Analyser\Repository;

use SimpSpector\Analyser\Gadget\GadgetInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface RepositoryInterface
{
    /**
     * @return GadgetInterface[]
     */
    public function all();

    /**
     * @param GadgetInterface $gadget
     * @throws \Exception
     */
    public function add(GadgetInterface $gadget);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @param string $name
     * @return GadgetInterface
     * @throws \Exception
     */
    public function get($name);
} 
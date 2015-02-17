<?php

namespace SimpSpector\Analyser\Repository;

use SimpSpector\Analyser\Gadget\GadgetInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Repository implements RepositoryInterface
{
    /**
     * @var GadgetInterface[]
     */
    private $gadgets = [];

    /**
     * @param GadgetInterface $gadget
     * @throws \Exception
     */
    public function add(GadgetInterface $gadget)
    {
        if ($this->has($gadget->getName())) {
            throw new \Exception(sprintf('gadget with the name "%s" exists already', $gadget->getName()));
        }

        $this->gadgets[$gadget->getName()] = $gadget;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if (isset($this->gadgets[$name])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return GadgetInterface
     * @throws \Exception
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \Exception(sprintf('gadget with the name "%s" not exists', $name));
        }

        return $this->gadgets[$name];
    }
}
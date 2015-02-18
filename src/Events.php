<?php

namespace SimpSpector\Analyser;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
final class Events
{
    /**
     * @var string
     */
    const PRE_EXECUTE = 'simpspector.analyser.pre_execute';

    /**
     * @var string
     */
    const POST_EXECUTE = 'simpspector.analyser.post_execute';

    /**
     * @var string
     */
    const PRE_GADGET = 'simpspector.analyser.pre_gadget';

    /**
     * @var string
     */
    const POST_GADGET = 'simpspector.analyser.post_gadget';
}
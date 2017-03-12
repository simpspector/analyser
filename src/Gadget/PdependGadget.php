<?php

namespace SimpSpector\Analyser\Gadget;

use SimpSpector\Analyser\Logger\AbstractLogger;
use SimpSpector\Analyser\Metric;
use SimpSpector\Analyser\Result;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class PdependGadget extends AbstractGadget
{
    /**
     * @var string
     */
    private $bin;

    /**
     * @param string $bin
     */
    public function __construct($bin = 'pdepend')
    {
        $this->bin = $bin;
    }

    /**
     * @param string $path
     * @param array $arguments
     * @param AbstractLogger $logger
     * @return Result
     */
    public function run($path, array $arguments, AbstractLogger $logger)
    {
        $file = tempnam('/tmp', 'pdepend_');

        if (!$arguments) {
            $arguments[] = './src';
        }

        $this->execute($path, array_merge([$this->bin, '--summary-xml=' . $file], $arguments), $logger);
        $data = $this->convertFromXmlToArray(file_get_contents($file));

        (new Filesystem())->remove($file);

        $result = new Result();

        $add = function ($code, $title, $description = null) use ($result, $data) {
            $metric = new Metric($title, strtolower('pdepend.' . $code), $data['@' . $code]);
            $metric->setDescription($description);
            $result->addMetric($metric);
        };

        $add('ahh', 'Average Hierarchy Height',
            'The average of the maximum lenght from a root class to ist deepest subclass subclass');
        $add('andc', 'Average Number of Derived Classes', 'The average of direct subclasses of a class');
        $add('calls', 'Number of Method or Function Calls');
        $add('ccn', 'Cyclomatic Complexity Number');
        $add('ccn2', 'Extended Cyclomatic Complexity Number');
        $add('cloc', 'Comment Lines fo Code');
        $add('clsa', 'Number of Abstract Classes');
        $add('clsc', 'Number of Concrete Classes');
        $add('fanout', 'Number of Fanouts', 'Referenced Classes');
        $add('leafs', 'Number of Leaf Classes', '(final) classes');
        $add('lloc', 'Logical Lines Of Code');
        $add('loc', 'Lines Of Code');
        $add('maxDIT', 'Max Depth of Inheritance Tree', 'Maximum depth of inheritance');
        $add('ncloc', 'Non Comment Lines Of Code');
        $add('noc', 'Number Of Classes');
        $add('nof', 'Number Of Functions');
        $add('noi', 'Number Of Interfaces');
        $add('nom', 'Number Of Methods');
        $add('nop', 'Number of Packages');
        $add('roots', 'Number of Root Classes');

        return $result;
    }

    /**
     * @see GadgetInterface::getName()
     */
    public function getName()
    {
        return 'pdepend';
    }

    /**
     * @see GadgetInterface::getDescription()
     */
    public function getDescription()
    {
        return 'PHP Depend';
    }

    /**
     * @see GadgetInterface::getDefaultConfigurationFile()
     */
    public function getDefaultConfigurationFile()
    {
        return null;
    }

    /**
     * @param string $xml
     * @return array
     */
    private function convertFromXmlToArray($xml)
    {
        $encoder = new XmlEncoder('pmd');

        return $encoder->decode($xml, 'xml');
    }
}

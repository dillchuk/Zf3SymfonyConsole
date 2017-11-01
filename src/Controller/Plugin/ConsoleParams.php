<?php

namespace Zf3SymfonyConsole\Controller\Plugin;

use Symfony\Component\Console\Input\InputInterface;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Stdlib\Parameters;

/**
 * Takes InputInterface and wraps it.  This needs
 * to be injected into the ControllerPluginManager.
 *
 * $serviceManager->get('ControllerPluginManager')
 * ->setService('params', new ConsoleParams($input));
 */
class ConsoleParams extends Params {

    /**
     * @var InputInterace
     */
    protected $input;

    public function __construct(InputInterface $input = null) {
        $this->setInput($input);
    }

    /**
     * @return InputInterface
     */
    public function getInput() {
        return $this->input;
    }

    public function setInput(InputInterface $input = null) {
        $this->input = $input;
    }

    public function __invoke($param = null, $default = null) {
        if ($param === null) {
            return $this;
        }
        return $this->fromConsole($param, $default);
    }

    public function fromRoute($param = null, $default = null) {
        return $this->fromConsole($param, $default);
    }

    public function fromConsole($param = null, $default = null) {
        if (!$this->input) {
            return $default;
        }
        $arguments = new Parameters($this->input->getArguments());
        if ($param === null) {
            return $arguments->toArray();
        }
        return $arguments->get($param, $default);
    }

    public function fromFiles($name = null, $default = null) {
        return $default;
    }

    public function fromHeader($header = null, $default = null) {
        return $default;
    }

    public function fromPost($param = null, $default = null) {
        return $default;
    }

    public function fromQuery($param = null, $default = null) {
        return $default;
    }

}

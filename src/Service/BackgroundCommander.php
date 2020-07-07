<?php

namespace App\Service;

use App\Command\AddSportCommand;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class BackgroundCommander
{
    private $defaultCommand;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * BackgroundCommander constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->defaultCommand = 'bin/console ' . AddSportCommand::getDefaultName();
    }

    /**
     * Run background process
     *
     * @param array $params
     */
    public function runProcess(array $params)
    {
        $serializer = base64_encode(serialize($params));
        $process = Process::fromShellCommandline($this->defaultCommand . ' "' . $serializer . '" > /dev/null 2>&1 &');
        $process->setWorkingDirectory($this->kernel->getProjectDir());
        $process->run();
    }
}
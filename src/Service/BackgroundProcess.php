<?php

namespace App\Service;

use App\Command\AddSportCommand;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class BackgroundProcess
{
    private $defaultName;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * BackgroundProcess constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->defaultName = 'bin/console ' . AddSportCommand::getDefaultName();
    }

    /**
     * Run background process
     *
     * @param array $params
     */
    public function runProcess(array $params)
    {
        $serializer = base64_encode(serialize($params));
        $process = Process::fromShellCommandline($this->defaultName . ' "' . $serializer . '" > /dev/null 2>&1 &');
        $process->setWorkingDirectory($this->kernel->getProjectDir());
        $process->run();
    }
}
<?php

namespace App\Service;

use App\Command\AddSportCommand;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Serializer\SerializerInterface;

class BackgroundCommander
{
    private $defaultCommand;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * BackgroundCommander constructor.
     *
     * @param KernelInterface $kernel
     * @param SerializerInterface $serializer
     */
    public function __construct(KernelInterface $kernel, SerializerInterface $serializer)
    {
        $this->kernel = $kernel;
        $this->serializer = $serializer;
        $this->defaultCommand = 'bin/console ' . AddSportCommand::getDefaultName();
    }

    /**
     * Run background process
     *
     * @param array $params
     */
    public function runProcess(array $params)
    {
        $data = $this->serializer->normalize($params, null, ['groups' => 'id_game']);
        $newData = array_reduce($data, function ($carry, $item) {
            return array_merge($carry, array_values($item));
        }, []);
        $serializedData = $this->serializer->serialize($newData, 'json');

        $process = Process::fromShellCommandline($this->defaultCommand . ' "' . $serializedData . '" > /dev/null 2>&1 &');
        $process->setWorkingDirectory($this->kernel->getProjectDir());
        $process->run();
    }
}
<?php

namespace App\Serializer;

use App\Entity\GameInterface;
use Symfony\Component\Serializer\Normalizer\{NormalizerAwareTrait, NormalizerInterface};
use Symfony\Component\Serializer\{SerializerAwareInterface, SerializerAwareTrait};

class GameNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use NormalizerAwareTrait;
    use SerializerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        $data = [
            "lang" => $object->getLanguage()->getName(),
            "sport" => $object->getLeague()->getSport()->getName(),
            "league" => $object->getLeague()->getName(),
            "team1" => $object->getTeam1()->getName(),
            "team2" => $object->getTeam2()->getName(),
            "date" => $object->getDate(),
        ];
        if (method_exists($object, 'getSource')) {
            $data["source"] = $object->getSource()->getName();
        }
        return $this->serializer->normalize($data, $format, $context);
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof GameInterface && !isset($context['groups']);
    }
}
<?php

namespace App\Controller;

use App\Service\ApiV1;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ApiV1Controller
 *
 * @Route("/api/v1", name="api_v1_")
 */
class ApiV1Controller extends AbstractController
{
    /**
     * @Route("/add", name="add_post", methods={"POST"})
     */
    public function add(Request $request, ApiV1 $api): JsonResponse
    {
        $result = $api->addGameByJson($request->getContent());

        $response = new JsonResponse($result);
        return $this->modifiedResponse($response);
    }

    /**
     * @Route("/random", name="random", methods={"GET"})
     * @throws NonUniqueResultException
     */
    public function random(Request $request, ApiV1 $api, SerializerInterface $serializer): JsonResponse
    {
        $result = $api->random($request->query->all());
        $data = $serializer->serialize($result, JsonEncoder::FORMAT, [
            'datetime_format' => 'Y-m-d G:i:s',
        ]);

        $response = new JsonResponse($data, 200, [], true);
        return $this->modifiedResponse($response);
    }

    private function modifiedResponse(JsonResponse $response): JsonResponse
    {
        $response->setEncodingOptions(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        $response->headers->set('Version', 1);
        return $response;
    }
}
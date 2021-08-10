<?php

namespace App\Controller;

use App\Service\ApiV1;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiV1Controller extends AbstractController
{
    /**
     * @Route("/v1/api/add", name="api_add_post_v1", methods={"POST"})
     *
     * @param Request $request
     * @param ApiV1 $api
     *
     * @return JsonResponse
     */
    public function add(Request $request, ApiV1 $api)
    {
        $result = $api->addGame($request);

        $response = new JsonResponse($result);
        $response->setEncodingOptions(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        return $response;
    }

    /**
     * @Route("/v1/api/random", name="api_random_get_v1", methods={"GET"})
     *
     * @param Request $request
     * @param ApiV1 $api
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function random(Request $request, ApiV1 $api, SerializerInterface $serializer)
    {
        $result = $api->random($request);
        $data = $serializer->serialize($result, 'json', [
            'datetime_format' => 'Y-m-d G:i:s',
        ]);

        $response = new JsonResponse($data, 200, [], true);
        $response->setEncodingOptions(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        return $response;
    }
}
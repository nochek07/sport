<?php

namespace App\Controller;

use App\DependencyInjection\ApiV1;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiV1Controller extends AbstractController
{
    /**
     * @Route("/v1/api/add", name="api_add_post_v1", methods={"POST"})
     * @param ApiV1 $api
     * @return JsonResponse
     */
    public function add(ApiV1 $api)
    {
        $result = $api->add();

        $response = new JsonResponse($result);
        $response->setEncodingOptions(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        return $response;
    }

    /**
     * @Route("/v1/api/random", name="api_random_get_v1", methods={"GET"})
     * @param ApiV1 $api
     * @return JsonResponse
     */
    public function random(ApiV1 $api)
    {
        $result = $api->random();

        $response = new JsonResponse($result);
        $response->setEncodingOptions(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        return $response;
    }
}
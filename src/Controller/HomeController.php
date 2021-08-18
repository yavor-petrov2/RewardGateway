<?php

namespace App\Controller;

use App\Services\RewardGatewayAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * Homepage index
     *
     * @param RewardGatewayAPI $api
     * @return Response
     */
    public function index(RewardGatewayAPI $api): Response
    {
        return $this->render(
            'home/index.html.twig',
            $api->getData()
        );
    }

    /**
     * Fetch RewardGatewayAPI data and return proper JsonResponse
     * Used for AJAX requests
     *
     * @param RewardGatewayAPI $api
     * @return JsonResponse
     */
    /*public function fetchAPIData(RewardGatewayAPI $api): JsonResponse
    {
        return $api->getJsonData();
    }*/
}

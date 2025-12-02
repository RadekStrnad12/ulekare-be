<?php

namespace App\Controller;

use App\Enum\Color;
use App\Http\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/colors', name: 'colors_')]
final class ColorApiController extends AbstractController
{
    public function __construct(private readonly ApiResponseFactory $api)
    {
    }

    #[Route(path: '', name: 'get_all', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $data = array_map(static function (Color $c): array {
            return [
                'code' => $c->value,
                'label' => $c->label(),
            ];
        }, Color::cases());

        return $this->api->ok($data);
    }
}

<?php

namespace App\Controller;

use App\Enum\Priority;
use App\Http\ApiResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/priorities', name: 'priorities_')]
final class PriorityApiController extends AbstractController
{
    public function __construct(private readonly ApiResponseFactory $api)
    {
    }

    #[Route(path: '', name: 'get_all', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $data = array_map(static function (Priority $p): array {
            return [
                'code' => $p->value,
                'label' => $p->label(),
            ];
        }, Priority::cases());

        return $this->api->ok($data);
    }
}

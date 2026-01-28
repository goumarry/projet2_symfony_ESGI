<?php

namespace App\Controller;

use App\DTO\PaymentIntentDTO;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/payments')]
class PaymentController extends AbstractController
{
    #[Route('/intents', name: 'api_payment_intent', methods: ['POST'])]
    public function createIntent(
        #[MapRequestPayload] PaymentIntentDTO $dto,
        PaymentService $paymentService
    ): JsonResponse
    {
        $result = $paymentService->createPaymentIntent($dto);

        return $this->json($result, 201);
    }

    #[Route('/{id}/confirm', name: 'api_payment_confirm', methods: ['POST'])]
    public function confirm(int $id, PaymentService $paymentService): JsonResponse
    {
        $result = $paymentService->confirmReception($id);

        return $this->json($result);
    }
}

<?php

namespace App\Controller;

use App\DTO\SellerOnboardingDTO;
use App\Service\SellerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/sellers')]
class SellerController extends AbstractController
{
    #[Route('/onboard', name: 'api_seller_onboard', methods: ['POST'])]
    public function onboard(
        #[MapRequestPayload] SellerOnboardingDTO $dto,
        SellerService $sellerService
    ): JsonResponse
    {
        $onboardingUrl = $sellerService->onboardSeller($dto);

        return $this->json([
            'message' => 'Lien d\'onboarding généré avec succès',
            'url' => $onboardingUrl
        ], 201);
    }
}

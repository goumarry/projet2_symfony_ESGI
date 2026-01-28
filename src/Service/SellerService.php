<?php

namespace App\Service;

use App\DTO\SellerOnboardingDTO;
use App\Entity\SellerAccount;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SellerService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Autowire(env: 'STRIPE_SECRET_KEY')] private string $stripeSecretKey
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function onboardSeller(SellerOnboardingDTO $dto): string
    {
        $stripeAccount = Account::create([
            'type' => 'express',
            'email' => $dto->email,
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
        ]);

        $seller = new SellerAccount();
        $seller->setEmail($dto->email);
        $seller->setStripeAccountId($stripeAccount->id);
        $seller->setStatus('CREATED');
        $seller->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($seller);
        $this->entityManager->flush();


        $accountLink = AccountLink::create([
            'account' => $stripeAccount->id,
            'refresh_url' => 'http://localhost:8000/reauth',
            'return_url' => 'http://localhost:8000/success',
            'type' => 'account_onboarding',
        ]);

        return $accountLink->url;
    }
}

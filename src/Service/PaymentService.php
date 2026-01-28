<?php

namespace App\Service;

use App\DTO\PaymentIntentDTO;
use App\Entity\Payment;
use App\Repository\SellerAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Stripe\Transfer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentService
{
    private const COMMISSION_PERCENT = 0.05;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SellerAccountRepository $sellerRepository,
        #[Autowire(env: 'STRIPE_SECRET_KEY')] private string $stripeSecretKey
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createPaymentIntent(PaymentIntentDTO $dto): array
    {
        $seller = $this->sellerRepository->find($dto->sellerId);
        if (!$seller) {
            throw new NotFoundHttpException("Vendeur introuvable.");
        }

        $commissionAmount = (int) round($dto->amount * self::COMMISSION_PERCENT);
        $sellerTransferAmount = $dto->amount - $commissionAmount;


        $intent = PaymentIntent::create([
            'amount' => $dto->amount,
            'currency' => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => ['seller_id' => $seller->getId(), 'commission' => $commissionAmount],
            'capture_method' => 'automatic',
        ]);

        $payment = new Payment();
        $payment->setAmount($dto->amount);
        $payment->setCurrency('EUR');
        $payment->setStatus('PENDING');
        $payment->setCommissionAmount($commissionAmount);
        $payment->setSellerTransferAmount($sellerTransferAmount);
        $payment->setStripePaymentIntentId($intent->id);
        $payment->setClientSecret($intent->client_secret);
        $payment->setSeller($seller);

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return [
            'clientSecret' => $intent->client_secret,
            'paymentId' => $payment->getId(),
            'amount' => $dto->amount
        ];
    }

    public function confirmReception(int $paymentId): array
    {
        $payment = $this->entityManager->getRepository(Payment::class)->find($paymentId);

        if (!$payment) {
            throw new NotFoundHttpException("Paiement introuvable.");
        }

        if ($payment->getStatus() !== 'PAID') {
            throw new BadRequestHttpException("Ce paiement ne peut pas être validé (Statut actuel : " . $payment->getStatus() . ")");
        }

        $transfer = Transfer::create([
            'amount' => $payment->getSellerTransferAmount(),
            'currency' => 'eur',
            'destination' => $payment->getSeller()->getStripeAccountId(),
            'transfer_group' => $payment->getStripePaymentIntentId(),
        ]);

        $payment->setStatus('COMPLETED');
        $this->entityManager->flush();

        return [
            'status' => 'COMPLETED',
            'transferId' => $transfer->id,
            'amountTransferred' => $payment->getSellerTransferAmount()
        ];
    }
}

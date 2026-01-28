<?php

namespace App\Service;

use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Event;
use Psr\Log\LoggerInterface;

class StripeWebhookService
{
    public function __construct(
        private PaymentRepository $paymentRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    public function handleEvent(Event $event): void
    {
        $this->logger->info('Stripe Event reçu : ' . $event->type);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->logger->warning('Paiement échoué pour : ' . $event->data->object->id);
                break;

            default:
                break;
        }
    }

    private function handlePaymentSucceeded(object $stripePaymentIntent): void
    {
        $payment = $this->paymentRepository->findOneBy([
            'stripePaymentIntentId' => $stripePaymentIntent->id
        ]);

        if (!$payment) {
            $this->logger->error('Paiement introuvable en BDD : ' . $stripePaymentIntent->id);
            return;
        }

        $payment->setStatus('PAID');

        $this->entityManager->flush();

        $this->logger->info('Paiement validé (PAID) pour le paiement #' . $payment->getId());
    }
}

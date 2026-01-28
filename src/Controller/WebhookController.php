<?php

namespace App\Controller;

use App\Service\StripeWebhookService;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/webhooks')]
class WebhookController extends AbstractController
{
    #[Route('/stripe', name: 'api_stripe_webhook', methods: ['POST'])]
    public function handleStripeWebhook(
        Request $request,
        StripeWebhookService $webhookService,
        #[Autowire(env: 'STRIPE_WEBHOOK_SECRET')] string $webhookSecret
    ): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Payload invalide
            return new Response('Invalid Payload', 400);
        } catch (SignatureVerificationException $e) {
            return new Response('Invalid Signature', 400);
        }

        $webhookService->handleEvent($event);

        return new Response('Event Handled', 200);
    }
}

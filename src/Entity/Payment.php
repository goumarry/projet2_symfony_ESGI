<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripePaymentIntentId = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(length: 3)]
    private ?string $currency = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $commissionAmount = null;

    #[ORM\Column]
    private ?int $sellerTransferAmount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientSecret = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SellerAccount $seller = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }

    public function setStripePaymentIntentId(?string $stripePaymentIntentId): static
    {
        $this->stripePaymentIntentId = $stripePaymentIntentId;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCommissionAmount(): ?int
    {
        return $this->commissionAmount;
    }

    public function setCommissionAmount(int $commissionAmount): static
    {
        $this->commissionAmount = $commissionAmount;

        return $this;
    }

    public function getSellerTransferAmount(): ?int
    {
        return $this->sellerTransferAmount;
    }

    public function setSellerTransferAmount(int $sellerTransferAmount): static
    {
        $this->sellerTransferAmount = $sellerTransferAmount;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(?string $clientSecret): static
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function getSeller(): ?SellerAccount
    {
        return $this->seller;
    }

    public function setSeller(?SellerAccount $seller): static
    {
        $this->seller = $seller;

        return $this;
    }
}

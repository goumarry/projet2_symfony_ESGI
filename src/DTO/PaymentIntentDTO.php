<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentIntentDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive(message: "Le montant doit être positif")]
    public int $amount; // En centimes ! (1000 = 10.00€)

    #[Assert\NotBlank]
    public int $sellerId; // L'ID de ton entité SellerAccount (ex: 1)
}

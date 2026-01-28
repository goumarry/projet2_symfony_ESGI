<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SellerOnboardingDTO
{
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "Le format de l'email est invalide")]
    public string $email;
}

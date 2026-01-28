# 💳 Micro-service de Paiement Marketplace (Symfony 7.4 & Stripe Connect)

Ce projet est une implémentation d'un **micro-service de paiement isolé** pour une marketplace de mise en relation entre particuliers (type Vinted, Leboncoin ou Airbnb).

Il gère l'intégralité des flux financiers complexes : enrôlement des vendeurs, encaissement, séquestre (escrow), commissionnement et reversement.

## 📋 Contexte & Architecture

L'application suit une logique de **"Separate Charges and Transfers"** pour garantir la sécurité des fonds et le séquestre :

1.  **L'acheteur paie la plateforme** : L'argent arrive sur le compte Stripe de la plateforme et est "bloqué" (Escrow).
2.  **La plateforme valide la transaction** : Une fois le service/colis reçu (simulation).
3.  **Calcul de la commission** : La plateforme garde sa marge (5%).
4.  **Virement Vendeur** : Le reste est transféré automatiquement vers le compte connecté du vendeur (Payout).

### Stack Technique
* **Framework** : Symfony 7.4 (API Platform / Controllers)
* **Langage** : PHP 8.2+
* **Paiement** : Stripe API (SDK PHP officiel)
* **Comptes Vendeurs** : Stripe Connect (Express Accounts)
* **Base de données** : MySQL (via Doctrine)
* **Outils Locaux** : Stripe CLI (pour l'écoute des webhooks)

---

## 🚀 Fonctionnalités Implémentées

### 1. Onboarding Vendeur (KYC)
* Création de comptes **Stripe Express** via l'API.
* Génération de liens d'onboarding sécurisés (`account_onboarding_link`).
* Gestion des URLs de retour (Success / Refresh).
* *Sécurité* : Le vendeur doit compléter son profil (KYC) pour recevoir des fonds.

### 2. Paiement & Séquestre
* Création de `PaymentIntent` côté serveur.
* Calcul automatique de la commission (5%) et du montant net vendeur.
* Stockage en base avec statut initial `PENDING`.
* Métadonnées pour lier le paiement au vendeur.

### 3. Webhooks & Sécurité
* Endpoint sécurisé `/api/webhooks/stripe`.
* **Vérification cryptographique** de la signature Stripe (`whsec_...`).
* Écoute de l'événement `payment_intent.succeeded`.
* Mise à jour automatique du statut local : `PENDING` ➔ `PAID`.

### 4. Virement Vendeur (Payout)
* Endpoint de validation manuelle (simulation de réception colis).
* Vérification stricte des statuts (Interdiction de virer si le paiement n'est pas `PAID`).
* Déclenchement du `Transfer` Stripe vers le compte connecté (`acct_...`).
* Mise à jour finale : `COMPLETED`.

---

## 🛠 Installation & Configuration

### 1. Pré-requis
* PHP 8.2+ & Composer
* Compte Stripe (Mode Test)
* Stripe CLI installé localement

### 2. Installation
```bash
git clone git@github.com:goumarry/projet2_symfony_ESGI.git
cd micro_paiement
composer install

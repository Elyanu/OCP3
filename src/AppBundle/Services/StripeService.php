<?php


namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StripeService extends Controller
{

    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function createCharge($montant, $token, $email)
    {
        \Stripe\Stripe::setApiKey($this->key);

        \Stripe\Charge::create(array(
            'amount' => $montant * 100,
            'currency' => 'EUR',
            'source' => $token, // obtained with Stripe.js
            'description' => 'Charge for '.$email
        ));
    }
}

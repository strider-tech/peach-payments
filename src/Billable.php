<?php

namespace StriderTech\PeachPayments;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait Billable
{
    public function charge($amount, array $options = [])
    {

    }

    public function refund($charge, array $options = [])
    {

    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cards()
    {
        return $this->hasMany(PaymentCard::class, $this->getForeignKey())->orderBy('created_at', 'desc');
    }

    public function invoice()
    {

    }

    /**
     * Find an invoice by ID.
     *
     * @param  string $id
     */
    public function findInvoice($id)
    {

    }

    /**
     * Get a collection of the entity's invoices.
     */
    public function invoices($includePending = false, $parameters = [])
    {

    }

    /**
     * Get the default card for the entity.
     */
    public function defaultCard()
    {

    }

    /**
     * Update customer's credit card.
     *
     * @param  string $token
     * @return void
     */
    public function updateCard($token)
    {

    }

    protected function fillCardDetails($card)
    {

    }

    public function deleteCards()
    {

    }

    /**
     * Determine if the entity has a Stripe customer ID.
     *
     * @return bool
     */
    public function hasRemoteId()
    {
        return !is_null($this->remote_id);
    }
}

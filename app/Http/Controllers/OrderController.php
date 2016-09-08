<?php

namespace App\Http\Controllers;

use Auth;
use App\Order;
use App\Http\Requests;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Get all orders.
     *
     * @var    App\Order            $orders
     * @return Illuminate\View\View
     */
    public function getAllOrders()
    {
        $orders = Order::all();
        return view('admin', compact('orders'));
    }

	/**
	 * Make a Stripe payment.
	 *
     * @param  Illuminate\Http\Request $request
	 * @param  App\Product             $product
	 * @return chargeCustomer()
	 */
    public function postPayWithStripe(Request $request, \App\Product $product)  
    {
        return $this->chargeCustomer($product->id, $product->price, $product->name, $request->input('stripeToken'));
    }

    /**
     * Charge a Stripe customer.
     *
     * @var    Stripe\Customer      $customer
     * @param  integer              $product_id
     * @param  integer              $product_price
     * @param  string               $product_name
     * @param  string               $token
     * @return createStripeCharge()
     */
    public function chargeCustomer($product_id, $product_price, $product_name, $token)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        if (!$this->isStripeCustomer())
        {
            $customer = $this->createStripeCustomer($token);
        }
        else
        {
            $customer = \Stripe\Customer::retrieve(Auth::user()->stripe_id);
        }

        return $this->createStripeCharge($product_id, $product_price, $product_name, $customer);
    }

    /**
     * Create a Stripe charge.
     *
     * @var    Stripe\Charge        $charge
     * @var    Stripe\Error\Card    $e
     * @param  integer              $product_id
     * @param  integer              $product_price
     * @param  string               $product_name
     * @param  Stripe\Customer      $customer
     * @return postStoreOrder()
     */
    public function createStripeCharge($product_id, $product_price, $product_name, $customer)
    {
        try {
          $charge = \Stripe\Charge::create(array(
            "amount" => $product_price,
            "currency" => "brl",
            "customer" => $customer->id,
            "description" => $product_name
            ));
        } catch(\Stripe\Error\Card $e) {
          return redirect()
            ->route('index')
            ->with('error', 'Your credit card was been declined. Please try again or contact us.');
        }

        return $this->postStoreOrder($product_name);
    }

    /**
     * Create a new Stripe customer for a given user.
     *
     * @var    Stripe\Customer $customer
     * @param  string          $token
     * @return Stripe\Customer $customer
     */
    public function createStripeCustomer($token)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $customer = \Stripe\Customer::create(array(
            "description" => Auth::user()->email,
            "source" => $token
        ));

        Auth::user()->stripe_id = $customer->id;
        Auth::user()->save();

        return $customer;
    }

    /**
     * Check if the Stripe customer exists.
     *
     * @return boolean
     */
    public function isStripeCustomer()
    {
        return Auth::user() && \App\User::where('id', Auth::user()->id)->whereNotNull('stripe_id')->first();
    }

    /**
     * Store a order.
     *
     * @param  string     $product_name
     * @return redirect()
     */
    public function postStoreOrder($product_name)
    {
        Order::create([
            'email' => Auth::user()->email,
            'product' => $product_name
        ]);

        return redirect()
            ->route('index')
            ->with('msg', 'Thanks for your purchase!');
    }
}

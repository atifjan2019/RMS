<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Laravel\Cashier\Exceptions\IncompletePayment;

class BillingController extends Controller
{
    /**
     * Show the pricing page.
     */
    public function pricing(): View
    {
        return view('pages.pricing');
    }

    /**
     * Show the billing plan page (for logged-in users).
     */
    public function plan(): View
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription('default');

        return view('billing.plan', [
            'tenant' => $tenant,
            'subscription' => $subscription,
            'isSubscribed' => $tenant->hasActiveSubscription(),
        ]);
    }

    /**
     * Start Stripe Checkout session.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        // Stripe Price ID for $5/month subscription
        // You need to create this in Stripe Dashboard
        $priceId = config('services.stripe.price_id', 'price_XXXXXXXX');

        try {
            return $tenant->newSubscription('default', $priceId)
                ->checkout([
                    'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('billing.cancel'),
                    'customer_email' => auth()->user()->email,
                ])
                ->redirect();
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to start checkout: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful checkout.
     */
    public function success(Request $request): View
    {
        return view('billing.success');
    }

    /**
     * Handle cancelled checkout.
     */
    public function cancel(): View
    {
        return view('billing.cancel');
    }

    /**
     * Show subscription management page.
     */
    public function manage(): View
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription('default');

        $invoices = [];
        if ($tenant->hasStripeId()) {
            try {
                $invoices = $tenant->invoices();
            } catch (\Exception $e) {
                // Ignore if no invoices
            }
        }

        return view('billing.manage', [
            'tenant' => $tenant,
            'subscription' => $subscription,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Redirect to Stripe Customer Portal for payment method updates.
     */
    public function portal(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        return $tenant->redirectToBillingPortal(route('billing.manage'));
    }

    /**
     * Cancel subscription.
     */
    public function cancel_subscription(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription('default');

        if ($subscription && $subscription->active()) {
            $subscription->cancel();
            return back()->with('success', 'Subscription cancelled. You will retain access until the end of your billing period.');
        }

        return back()->with('error', 'No active subscription to cancel.');
    }

    /**
     * Resume cancelled subscription.
     */
    public function resume(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();
            return back()->with('success', 'Subscription resumed successfully.');
        }

        return back()->with('error', 'Unable to resume subscription.');
    }
}

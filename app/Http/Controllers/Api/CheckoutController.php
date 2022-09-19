<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Invoice;
use Midtrans\Snap;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    protected $request;

    /**
     * __construct
     *
     * @param  mixed $request
     * @return void
     */
    public function __construct(Request $request)
    {
        // SET MIDDLEWARE EXCEPT 'notificationHandler'
        $this->middleware('auth:api')->except('notificationHandler');

        $this->request = $request;

        // SET MIDTRANS CONFIGURATION
        \Midtrans\Config::$serverKey    = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized  = config('services.midtrans.isSanitized');
        \Midtrans\Config::$is3ds        = config('services.midtrans.is3ds');
    }

    /**
     * store
     *
     * @return void
     */
    public function store()
    {
        DB::transaction(function () {
            // ALGORITHM TO CREATE INVOICE NUMBER
            $length = 10;
            $random = '';
            for ($i = 0; $i < $length; $i++) {
                $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
            }
            $no_invoice = 'INV-' . Str::upper($random);

            // CREATE INVOICE FROM REQUEST
            $invoice = Invoice::create([
                'invoice'       => $no_invoice,
                'customer_id'   => auth()->guard('api')->user()->id,
                'courier'       => $this->request->courier,
                'service'       => $this->request->service,
                'cost_courier'  => $this->request->cost,
                'weight'        => $this->request->weight,
                'name'          => $this->request->name,
                'phone'         => $this->request->phone,
                'province'      => $this->request->province,
                'city'          => $this->request->city,
                'address'       => $this->request->address,
                'grand_total'   => $this->request->grand_total,
                'status'        => 'pending',
                'note'          => $this->request->note
            ]);

            // LOOP CART CUSTOMER
            foreach (Cart::where('customer_id', auth()->guard('api')->user()->id)->get() as $cart) {
                // INSERT PRODUCT TO TABLE ORDER
                $invoice->orders()->create([
                    'invoice_id'    => $invoice->id,
                    'invoice'       => $no_invoice,
                    'product_id'    => $cart->product_id,
                    'product_name'  => $cart->product->title,
                    'image'         => $cart->product->image,
                    'qty'           => $cart->quantity,
                    'price'         => $cart->price,
                ]);
            }

            // CREATE PAYLOAD DATA MIDTRANS
            $payload = [
                'transaction_details' => [
                    'order_id'      => $invoice->invoice,
                    'gross_amount'  => $invoice->grand_total,
                ],
                'customer_details' => [
                    'first_name'       => $invoice->name,
                    'email'            => auth()->guard('api')->user()->email,
                    'phone'            => $invoice->phone,
                    'shipping_address' => $invoice->address
                ]
            ];

            // GENERATE SNAP TOKEN
            $snapToken = Snap::getSnapToken($payload);
            // UPDATE SNAP TOKEN
            $invoice->snap_token = $snapToken;
            // SAVE CHANGES
            $invoice->save();

            $this->response['snap_token'] = $snapToken;
        });

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'Order Successfully',
            $this->response
        ]);
    }

    /**
     * notificationHandler
     *
     * @param  mixed $request
     * @return void
     */
    public function notificationHandler(Request $request)
    {
        // GET CONTENT JSON FROM MIDTRANS
        $payload      = $request->getContent();
        $notification = json_decode($payload);

        // CREATE SIGNATURE KEY
        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));

        // IF SIGNATURE !SAME(?)
        if ($notification->signature_key != $validSignatureKey) {
            // RETURN RESPONSE FAILED
            return response(['message' => 'Invalid signature'], 403);
        }

        $transaction  = $notification->transaction_status;
        $type         = $notification->payment_type;
        $orderId      = $notification->order_id;
        $fraud        = $notification->fraud_status;

        // DATA TRANSACTION
        $data_transaction = Invoice::where('invoice', $orderId)->first();

        // CHECKING PAYMENT FROM MIDTRANS
        if ($transaction == 'capture') {

            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {

                if ($fraud == 'challenge') {

                    /**
                     *   update invoice to pending
                     */
                    $data_transaction->update([
                        'status' => 'pending'
                    ]);
                } else {

                    /**
                     *   update invoice to success
                     */
                    $data_transaction->update([
                        'status' => 'success'
                    ]);
                }
            }
        } elseif ($transaction == 'settlement') {

            /**
             *   update invoice to success
             */
            $data_transaction->update([
                'status' => 'success'
            ]);

            // UPDATE STOCK WHEN TRANSACTIONS SUCCESS
            foreach ($data_transaction->orders()->get() as $order) {
                // GET PRODUCT
                $product = \App\Models\Product::whereId($order->product_id)->first();

                // UPDATE STOCK
                $product->update([
                    'stock' => $product->stock - $order->qty
                ]);
            }
        } elseif ($transaction == 'pending') {


            /**
             *   update invoice to pending
             */
            $data_transaction->update([
                'status' => 'pending'
            ]);
        } elseif ($transaction == 'deny') {


            /**
             *   update invoice to failed
             */
            $data_transaction->update([
                'status' => 'failed'
            ]);
        } elseif ($transaction == 'expire') {


            /**
             *   update invoice to expired
             */
            $data_transaction->update([
                'status' => 'expired'
            ]);
        } elseif ($transaction == 'cancel') {

            /**
             *   update invoice to failed
             */
            $data_transaction->update([
                'status' => 'failed'
            ]);
        }
    }
}

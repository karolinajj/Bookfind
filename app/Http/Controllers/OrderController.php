<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Order;
use App\Models\Cart;
use App\Models\BookInCart;
use App\Models\Book;
use App\Models\BookOrder;
use App\Models\ShippingAddress;
use App\Models\Country;

class OrderController extends Controller
{
    public function processPayment(Request $request)
    {
        //checking if there is cart and items in cart, to avoid orders with no items
        $cart = Cart::where('userid', auth()->id())->first();

        if (!$cart) {
            return redirect()->route('books.search')->with('error', 'Your cart is empty. Add items to proceed.');
        }

        $cartItemsCount = BookInCart::where('cartid', $cart->id)->count();

        if ($cartItemsCount === 0)
        {
            return redirect()->route('books.search')->with('error', 'Your cart is empty. Add items to proceed.');
        }
        
        // assuming payment is successful at this point
        $shippingAddress = session('shipping_address');
    
        if (!$shippingAddress) {
            return redirect()->route('address')->withErrors('Please fill in the shipping address before proceeding.');
        }

        // Save the shipping address to the database
        $address = new ShippingAddress();
        $address->name = $shippingAddress['name'];
        $address->surname = $shippingAddress['surname'];
        $address->email = $shippingAddress['email'];
        $address->street = $shippingAddress['street'];
        $address->number = $shippingAddress['number'];
        $address->code = $shippingAddress['code'];
        $address->city = $shippingAddress['city'];
        $address->countryid = $shippingAddress['country'];
        $address->save();
    
        // creating the order
        $order = new Order();
        $order->userid = Auth::id();
        $order->status = 'PENDING'; // initial status
        $order->purchasedate = now();
        $order->totalprice = 0; 
        $order->addressid = $address->id; // associating the saved address
        $order->save();
    
        $totalPrice = 0;
    
        // Retrieve the cart for the authenticated user
        $cart = Cart::where('userid', auth()->id())->first();
    
        if (!$cart) {
            return redirect()->route('books.search')->with('error', 'Your cart is empty.');
        }
    
        // retrieving the cart items from the BookInCart table
        $cartItems = BookInCart::where('cartid', $cart->id)->get();
    
        foreach ($cartItems as $cartItem) {
            $book = Book::find($cartItem->bookid); // finding the book associated with the cart item
            if ($book) {
                // creating a new BookOrder for the order
                $bookOrder = new BookOrder();
                $bookOrder->orderid = $order->id;
                $bookOrder->bookid = $book->id;
                $bookOrder->quantity = $cartItem->quantity;
                $bookOrder->price = $book->price; // saving the price of the book at the time of purchase
                $bookOrder->save();
    
                // calculating the total price
                $totalPrice += $book->price * $cartItem->quantity;
            }
        }
    
        // updating the order with the total price
        $order->totalprice = $totalPrice;
        $order->save();
        Session::put('order_placed', true);
    
        // emptying all books from the user's cart
        BookInCart::where('cartid', $cart->id)->delete();
    
        // displaying the order on the confimration page
        $orderShow = Order::with('books')->find($order->id);

        if (!$orderShow) {
            return redirect()->route('home')->with('error', 'Order not found');
        }
        session()->forget('shipping_address');

        return view('confirmation', ['order' => $orderShow]);
    }

    public function startNewOrder()
    {
        session()->forget('shipping_address');
        if(Session::get('order_placed', true))
        {
            session(['order_placed' => false]);
            return view('confirmation');
        }
        else return redirect()->route('home');
    }

    public function viewUsersOrders()
    {
        $userId = Auth::id();

        $orders = Order::where('userid', $userId)
        ->with(['books' => function ($query) {
            $query->withoutGlobalScope('active'); //we want to fetch books even if their were deleted after the order
        }])
        ->orderBy('id', 'desc')
        ->get();
        return view('orders', ['orders' => $orders]);
    }

}
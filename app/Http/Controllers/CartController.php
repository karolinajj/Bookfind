<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\Cart;
use App\Models\BookInCart;

class CartController extends Controller
{
    public function addToCart(Request $request, $bookid)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $quantity = $validated['quantity'];
        $book = Book::findOrFail($bookid);

        $cart = Cart::firstOrCreate(
            ['userid' => auth()->id()],
            ['price' => 0]
        );

        $bookincart = BookInCart::where('cartid', $cart->id)
                                ->where('bookid', $bookid)
                                ->first();


        if ($bookincart) {
            $bookincart->quantity += $quantity;
            $bookincart->price = $book->price * $bookincart->quantity;
            $bookincart->save();
        } else {
            BookInCart::create([
                'cartid' => $cart->id,
                'bookid' => $bookid,
                'quantity' => $quantity,
                'price' => $book->price * $quantity
            ]);
        }

        $cart->price = BookInCart::where('cartid', $cart->id)->sum('price');
        $cart->save();

        return redirect()->route('books.search')->with('success', 'Book added to cart!');
    }

    public function viewCart()
    {
        $cart = Cart::where('userid', auth()->id())->first();

        if ($cart) {
            $booksincart = BookInCart::where('cartid', $cart->id)
                                     ->with('book')
                                     ->get();
        } else {
            $booksincart = collect();  // empties collection if no cart exists
        }

        return view('cart', compact('booksincart', 'cart'));
    }

    public function updateCartTotal($cartid)
    {
        $cart = Cart::findOrFail($cartid);
    
        $booksInCart = $cart->booksincart;
    
        if ($booksInCart->isNotEmpty()) {
            $cart->price = $booksInCart->sum(function ($bookincart) {

                $book = Book::find($bookincart->bookid);
                if ($book) {
                    return $book->price * $bookincart->quantity;
                }
                return 0;
            });
        } else {
            $cart->price = 0;
        }

        $cart->save();
    }
    

    

    public function deleteFromCart(Request $request, $id)
    {
        $bookincart = BookInCart::findOrFail($id);
        $cartid = $bookincart->cartid;

    
        DB::table('bookincart')->where('id', $bookincart->id)->delete(); //if a book is deleted it is also deleted from all the shopping carts
    
        $bookincart->delete();
        $this->updateCartTotal($cartid);
    
        return redirect()->route('cart.view')->with('success', 'Book deleted successfully!');
    }

    public function updateQuantity(Request $request, $id)
    {
        $bookInCart = BookInCart::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $bookInCart->quantity = $request->quantity;
        $bookInCart->save();

        $this->updateCartTotal($bookInCart->cartid);

        return redirect()->route('cart.view')->with('success', 'Quantity updated successfully!');
    }


}
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
use App\Models\Review;

class ReviewController extends Controller
{
    public function postReview(Request $request, $bookid) {
        $request->validate([
            'score' => 'required|integer|min:0|max:5',
            'content' => 'required|string|max:1000',
        ]);
    
        // checking if the user purchased the book
        $userPurchased = DB::table('order')
        ->where('order.userid', auth()->id())
        ->whereExists(function ($query) use ($bookid) {
            $query->select(DB::raw(1))
                ->from('book')
                ->join('book_order', 'book.id', '=', 'book_order.bookid')
                ->whereColumn('order.id', 'book_order.orderid')
                ->where('book.id', $bookid); // Explicitly specify table for "id"
        })
        ->exists();

        if (!$userPurchased) {
            return back()->withErrors('You can only review books you purchased.');
        }
    
        Review::create([
            'bookid' => $bookid,
            'userid' => auth()->id(),
            'score' => $request->score,
            'content' => $request->content,
        ]);
    
        return back()->with('success', 'Review posted successfully.');
    }

    public function deleteReview($bookid, $reviewid)
    {
        $review = Review::findOrFail($reviewid);
        
        // checking if the logged-in user is the owner of the review
        if ($review->userid !== auth()->id()) {
            return redirect()->back()->with('error', 'You do not have permission to delete this review.');
        }
    
        $review->delete();
        return redirect()->route('book', $bookid)->with('success', 'Review deleted successfully.');
    }
    
    
    

    


}
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\Http\Controllers\CartController;


use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;


class BookController extends Controller
{
    public function viewAll(Request $request){
        $books = Book::active()->get();
        return view('books', compact('books'));
    }

    public function viewAllManager(Request $request){
        $books = Book::active()->with('category')->get();
        return view('booksmanager', compact('books'));        
    }

    public function viewBookDetails_Book($id)
    {
        $book = Book::active()->with(['authors', 'category'])->findOrFail($id);
        return view('book', compact('book'));
    }
    
    public function search(Request $request){
        $searchQuery = $request->input('search');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $categoryIds = $request->input('category', []);  // Array of selected categories
        $formats = $request->input('format', []);

        $booksQuery = Book::active()->with('authors')
        ->where(function ($query) use ($searchQuery) {
            $query->where('title', 'ILIKE', '%' . $searchQuery . '%')
                  ->orWhereHas('authors', function ($query) use ($searchQuery) {
                      $query->where('name', 'ILIKE', '%' . $searchQuery . '%');
                  });
        });

        if ($priceMin) {
            $booksQuery->where('price', '>=', $priceMin);
        }

        if ($priceMax) {
            $booksQuery->where('price', '<=', $priceMax);
        }
        if (!empty($categoryIds)) {
            $booksQuery->whereIn('categoryid', $categoryIds);
        }
        if (!empty($formats)) {
            $booksQuery->whereIn('format', $formats);
        }
    
        
        $books = $booksQuery->get();
    
        $categories = Category::all();
        $formats = ['HARDCOVER', 'PAPERBACK', 'EBOOK'];

        return view('books', compact('books','categories', 'formats'));
    }

    public function create(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'author' => 'required|string|max:255',
            'category_id' => 'required',
            'format' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $book = new Book();
        $book->title = $request->title;
        $book->price = $request->price;
        $book->format = $request->format;
        $book->categoryid = $request->category_id;
        $book->status = TRUE;
        $book->save();

        $author = Author::firstOrCreate(['name' => $request->author]);
        $book->authors()->attach($author->id);

        if ($request->hasFile('image')) {
            $imageName = 'book' . $book->id . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images/books'), $imageName);
            $book->update(['image' => 'images/books/' . $imageName]);
        }

        return redirect()->route('booksmanager.index')->with('success', 'Book added successfully!');

    }

    public function delete($id){
        $book = Book::active()->findOrFail($id);
    
        //DB::table('book_author')->where('id_book', $book->id)->delete();

        // get all carts that contained this book
        $cartids = DB::table('bookincart')
        ->where('bookid', $book->id)
        ->pluck('cartid');
        
        //delete the book from all carts
        DB::table('bookincart')->where('bookid', $book->id)->delete();
        
        //DB::table('book_order')->where('bookid', $book->id)->update(['bookid' => null]);

        $cartController = new CartController();
        foreach ($cartids as $cartid) {
            $cartController->updateCartTotal($cartid);
        }
    
        $book->update(['status' => FALSE]);
    
        return redirect()->route('booksmanager.index')->with('success', 'Book deleted successfully!');
    }

    public function viewBookDetails($id){
        $book = Book::active()->findOrFail($id);

        $categories = Category::all();
        
        $authors = Author::all();

        return view('editbook', compact('book', 'categories','authors'));
    }
    
    public function update(Request $request, $id){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'author_id' => 'required|string|max:255',
            'category_id' => 'required',
            'format' => 'required|string|in:EBOOK,PAPERBACK,HARDCOVER',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
    
        $book = Book::active()->findOrFail($id);
    
        $book->update([
            'title' => $validated['title'],
            'price' => $validated['price'],
            'id_author' => $validated['author_id'],
            'categoryid' => $validated['category_id'],
            'format' => $validated['format'],
        ]);

        $book->authors()->sync([$validated['author_id']]);
        $book->category()->associate($validated['category_id']);

        if ($request->hasFile('image')) {
            if ($book->image && file_exists(public_path($book->image))) {
                unlink(public_path($book->image));
            }
            $imageName = 'book' . $book->id . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images/books'), $imageName);
            $book->image = 'images/books/' . $imageName;
        }

        $book->save();

        //updating price of carts that the book is in
        $cartids = DB::table('bookincart')
        ->where('bookid', $book->id)
        ->pluck('cartid');

        $cartController = new CartController();
        foreach ($cartids as $cartid) {
            $cartController->updateCartTotal($cartid);
        }
        
        return redirect()->route('booksmanager.index')->with('success', 'Book updated successfully!');
    }

    public function search_AJAX(Request $request){
        $query = $request->get('query', '');

        $books = Book::active()->where('title', 'ILIKE', "%{$query}%")
            ->with('authors', 'category')
            ->get();

        $formattedBooks = $books->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'price' => $book->price,
                'authors' => $book->authors->pluck('name')->implode(', '), //displaying many authors separated by comma
                'category' => $book->category->name ?? 'N/A',
                'format' => ucfirst(strtolower($book->format)),
            ];
        });

        return response()->json($formattedBooks);
    }

}
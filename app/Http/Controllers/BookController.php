<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Display the search page and results.
     */
    public function index(Request $request)
    {
        $q = $request->query('q', '');

        $results = null;

        if (!empty($q)) {
            $results = Book::query()
                ->where('title', 'like', "%{$q}%")
                ->orWhere('isbn', 'like', "%{$q}%")
                ->orWhereHas('authors', function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%");
                })
                ->with(['authors', 'loans' => function ($q) {
                    // Only load ACTIVE loans (still checked out)
                    $q->where(function ($subQ) {
                        $subQ->whereNull('Date_in');
                    })->with('borrower');
                }])
                ->orderBy('title')
                ->paginate(10);
        }

        return view('books.search', [
            'results' => $results,
            'q' => $q,
        ]);
    }
}

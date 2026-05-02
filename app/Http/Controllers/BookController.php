<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepo
    ) {}

    /**
     * Mostrar el catálogo de libros.
     */
    public function index(Request $request): View
    {
        $books = $this->bookRepo->paginate(12, $request->all());

        // Géneros para el sidebar con ícono si el modelo lo tiene
        $genres = Genre::orderBy('name')
            ->get()
            ->map(fn($g) => [
                'slug' => $g->slug,
                'name' => $g->name,
                'icon' => $g->icon ?? '',
            ]);

        $sortOptions = [
            'relevance'  => 'Relevancia',
            'bestseller' => 'Más vendidos',
            'newest'     => 'Más recientes',
            'price_low'  => 'Precio: menor a mayor',
            'price_high' => 'Precio: mayor a menor',
        ];

        return view('books.index', compact('books', 'genres', 'sortOptions'));
    }

    /**
     * Mostrar el detalle de un libro.
     */
    public function show(string $slug): View
    {
        $book = $this->bookRepo->findBySlug($slug);

        if (!$book) {
            abort(404);
        }

        // ¿El usuario autenticado ya compró este libro?
        $hasPurchased = false;
        if (Auth::check() && class_exists(\App\Models\Order::class)) {
            try {
                $hasPurchased = Auth::user()
                    ->orders()
                    ->whereHas('items', fn($q) => $q->where('book_id', $book->id))
                    ->exists();
            } catch (\Exception $e) {
                $hasPurchased = false;
            }
        }

        // Libros similares (mismo género, excluyendo el actual)
        $genreIds = $book->genres->pluck('id');
        $similar = $genreIds->isNotEmpty()
            ? Book::whereHas('genres', fn($q) => $q->whereIn('genres.id', $genreIds))
                ->where('id', '!=', $book->id)
                ->with('authors')
                ->inRandomOrder()
                ->limit(4)
                ->get()
            : collect();

        return view('books.show', compact('book', 'hasPurchased', 'similar'));
    }

    /**
     * Búsqueda AJAX — devuelve los campos que el JS del layout espera:
     * url, cover, title, author, price
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen(trim($query)) < 2) {
            return response()->json([]);
        }

        $books = $this->bookRepo->search($query);

        $results = $books->map(function (Book $book) {
            return [
                'url'    => route('books.show', $book->slug),
                'cover'  => $book->cover_image ? asset('storage/' . $book->cover_image) : null,
                'title'  => $book->title,
                'author' => $book->authors->first()?->name ?? '',
                'price'  => '$' . number_format($book->price_cents / 100, 2),
            ];
        });

        return response()->json($results);
    }
}

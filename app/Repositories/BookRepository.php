<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Support\Collection;

class BookRepository implements BookRepositoryInterface
{
    public function getFeatured(int $limit = 10): Collection
    {
        return Book::where('is_featured', true)
            ->with(['authors'])
            ->limit($limit)
            ->get();
    }

    public function getBestsellers(int $limit = 4): Collection
    {
        return Book::where('is_bestseller', true)
            ->with(['authors'])
            ->limit($limit)
            ->get();
    }

    public function getNewArrivals(int $limit = 4): Collection
    {
        return Book::where('is_new_arrival', true)
            ->with(['authors'])
            ->limit($limit)
            ->get();
    }

    public function paginate(int $perPage = 12, array $filters = [])
    {
        $query = Book::query()->with(['authors', 'genres']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('authors', fn($qa) =>
                      $qa->where('name', 'like', '%' . $search . '%')
                  );
            });
        }

        if (!empty($filters['genre'])) {
            $query->whereHas('genres', fn($q) =>
                $q->where('slug', $filters['genre'])
            );
        }

        // Filtro por formato
        if (!empty($filters['format'])) {
            match ($filters['format']) {
                'pdf'      => $query->where('has_pdf', true),
                'epub'     => $query->where('has_epub', true),
                'physical' => $query->where('has_physical', true),
                default    => null,
            };
        }

        // Filtro por precio (en dólares → convertir a cents)
        if (!empty($filters['min_price'])) {
            $query->where('price_cents', '>=', (int)($filters['min_price'] * 100));
        }
        if (!empty($filters['max_price'])) {
            $query->where('price_cents', '<=', (int)($filters['max_price'] * 100));
        }

        if (!empty($filters['sort'])) {
            match ($filters['sort']) {
                'bestseller' => $query->orderBy('is_bestseller', 'desc'),
                'newest'     => $query->orderBy('created_at', 'desc'),
                'price_low'  => $query->orderBy('price_cents', 'asc'),
                'price_high' => $query->orderBy('price_cents', 'desc'),
                default      => null,
            };
        }

        return $query->paginate($perPage);
    }

    public function findBySlug(string $slug)
    {
        return Book::where('slug', $slug)
            ->with(['authors', 'genres', 'reviews.user'])
            ->first();
    }

    public function search(string $query): Collection
    {
        return Book::where('title', 'like', '%' . $query . '%')
            ->orWhereHas('authors', function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->with(['authors'])
            ->limit(8)
            ->get();
    }
}

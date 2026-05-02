<?php

namespace App\Repositories\Interfaces;

interface BookRepositoryInterface
{
    public function getFeatured(int $limit = 10);
    
    public function getBestsellers(int $limit = 4);
    
    public function getNewArrivals(int $limit = 4);

    public function paginate(int $perPage = 12, array $filters = []);

    public function findBySlug(string $slug);

    public function search(string $query);
}

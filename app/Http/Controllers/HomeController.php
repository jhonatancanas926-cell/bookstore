<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepo,
    ) {}

    public function index(): View
    {
        return view('home', [
            'featured'    => $this->bookRepo->getFeatured(10),
            'bestsellers' => $this->bookRepo->getBestsellers(4),
            'newArrivals' => $this->bookRepo->getNewArrivals(4),
        ]);
    }
}

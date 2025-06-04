<?php

namespace App\Http\Controllers\Social;

use App\Providers\Social\PostService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};

class SearchController extends Controller
{
    public function __construct(private PostService $post_provider)
    {
    }

    #[Get("/search/control", "search.control")]
    public function index(): string
    {
        return $this->render("search/modal.html.twig", []);
    }

    #[Post("/search", "search.query")]
    public function query(): string
    {
        $valid = $this->validate([
            "term" => ["required"],
        ]);

        if ($valid) {
            session()->set("search_term", $valid->term);
            return $this->render("search/load.html.twig", [
                "posts" => $this->post_provider->searchPosts($valid->term),
                "term" => $valid->term,
            ]);
        }
        session()->delete("search_term");
        header("HX-Redirect: /");
        exit;
    }

    #[Get("/search/page/{page}", "search.more")]
    public function more(int $page)
    {
        $term = session()->get("search_term");
        if ($term) {
            return $this->render("search/more.html.twig", [
                "term" => $term,
                "posts" => $this->post_provider->searchPosts($term, $page),
                "next_page" => $page + 1,
            ]);
        }
    }
}

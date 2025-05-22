<?php

require_once __DIR__.'/../vendor/autoload.php';

use App\Models\Post;
use App\Models\User;
use PHPUnit\Framework\Exception;
use jcobhams\NewsApi\NewsApi;

$config = config("news");

if ($config['api_key']) {
    $newsapi = new NewsApi($config["api_key"]);
    $bot = getBot();

    foreach (range(1, 10) as $page) {
        try {
            $top_headlines = $newsapi->getTopHeadlines(country: $config['country'], page_size: $config['page_size'], page: $page);

            foreach ($top_headlines->articles as $article) {
                $created = new DateTime($article->publishedAt);
                $post = Post::where("url", $article->url)->get();
                if (!$post && strlen($article->content) > 0) {
                    Post::create([
                        "user_id" => $bot->id,
                        "comment" => twig()->render("post/news.html.twig", [
                            "article" => $article,
                        ]),
                        "url" => $article->url,
                        "image" => $article->urlToImage,
                        "created_at" => $created->format("Y-m-d H:i:s"),
                    ]);
                }
            }
        } catch (Exception $ex) {
            error_log("Failed to fetch newsapi");
        }
    }
}

function getBot()
{
    // Fetch news bot
    $bot = User::where("username", "news_bot")
        ->andWhere("bot", 1)->get();

    if (!$bot) {
        // Create news bot
        $bot = User::create([
            "bot" => 1,
            "first_name" => "News",
            "surname" => "Bot",
            "username" => "news_bot",
            "email" => "news_bot@echo",
            "password" => password_hash(time(), PASSWORD_ARGON2I),
        ]);
    }

    return $bot;
}

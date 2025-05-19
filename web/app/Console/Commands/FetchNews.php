<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\News;
use Illuminate\Support\Str;

class FetchNews extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Thu thập tin tức tự động về gốm sứ từ RSS feed và lưu vào bảng news';

    public function handle()
    {
        $this->info('Bắt đầu thu thập tin tức...');

        try {
            $news_articles = [];

            // Nguồn 1: RSS Feed từ VnExpress (danh mục Công nghệ - giữ nguyên)
            $rss_url = 'https://vnexpress.net/rss/cong-nghe.rss';
            $response = Http::get($rss_url);

            if ($response->failed()) {
                $this->error('Không thể lấy dữ liệu từ RSS feed: ' . $rss_url);
                return;
            }

            // Phân tích XML
            $xml = new \SimpleXMLElement($response->body());
            $article_count = 0;
            foreach ($xml->channel->item as $item) {
                if ($article_count >= 3) {
                    break; // Giới hạn tối đa 3 bài từ nguồn này
                }

                $title = (string)$item->title;
                $description = (string)$item->description;
                $link = (string)$item->link;
                $pubDate = $item->pubDate ? new \DateTime((string)$item->pubDate) : now();

                // Trích xuất hình ảnh từ description (nếu có)
                $image = null;
                if ($description) {
                    $doc = new \DOMDocument();
                    @$doc->loadHTML($description);
                    $images = $doc->getElementsByTagName('img');
                    if ($images->length > 0) {
                        $image = $images->item(0)->getAttribute('src');
                    }
                }

                // Lấy nội dung chi tiết từ trang web
                $content = strip_tags($description); // Nội dung mặc định nếu không lấy được từ trang web
                try {
                    $article_response = Http::get($link);
                    if ($article_response->successful()) {
                        $doc = new \DOMDocument();
                        @$doc->loadHTML($article_response->body());
                        // Tìm thẻ chứa nội dung chính (thường là <p> trong bài viết)
                        $paragraphs = $doc->getElementsByTagName('p');
                        $full_content = '';
                        foreach ($paragraphs as $paragraph) {
                            $full_content .= $paragraph->textContent . ' ';
                        }
                        $content = trim($full_content);
                    }
                } catch (\Exception $e) {
                    $this->warn('Không thể lấy nội dung chi tiết từ: ' . $link . '. Lỗi: ' . $e->getMessage());
                }

                $news_articles[] = [
                    'title' => $title,
                    'excerpt' => Str::limit(strip_tags($description), 200),
                    'content' => $content, // Đã loại bỏ thẻ HTML
                    'image' => $image,
                    'source_url' => $link,
                    'created_at' => $pubDate,
                ];

                $article_count++;
            }

            // Nguồn 2: RSS Feed từ TuoiTre (danh mục Công nghệ - giữ nguyên)
            $rss_url_2 = 'https://tuoitre.vn/rss/cong-nghe.rss';
            $response_2 = Http::get($rss_url_2);

            if (!$response_2->failed()) {
                $xml_2 = new \SimpleXMLElement($response_2->body());
                $article_count = 0;
                foreach ($xml_2->channel->item as $item) {
                    if ($article_count >= 3) {
                        break; // Giới hạn tối đa 3 bài từ nguồn này
                    }

                    $title = (string)$item->title;
                    $description = (string)$item->description;
                    $link = (string)$item->link;
                    $pubDate = $item->pubDate ? new \DateTime((string)$item->pubDate) : now();

                    $image = null;
                    if ($description) {
                        $doc = new \DOMDocument();
                        @$doc->loadHTML($description);
                        $images = $doc->getElementsByTagName('img');
                        if ($images->length > 0) {
                            $image = $images->item(0)->getAttribute('src');
                        }
                    }

                    // Lấy nội dung chi tiết từ trang web
                    $content = strip_tags($description);
                    try {
                        $article_response = Http::get($link);
                        if ($article_response->successful()) {
                            $doc = new \DOMDocument();
                            @$doc->loadHTML($article_response->body());
                            $paragraphs = $doc->getElementsByTagName('p');
                            $full_content = '';
                            foreach ($paragraphs as $paragraph) {
                                $full_content .= $paragraph->textContent . ' ';
                            }
                            $content = trim($full_content);
                        }
                    } catch (\Exception $e) {
                        $this->warn('Không thể lấy nội dung chi tiết từ: ' . $link . '. Lỗi: ' . $e->getMessage());
                    }

                    $news_articles[] = [
                        'title' => $title,
                        'excerpt' => Str::limit(strip_tags($description), 200),
                        'content' => $content, // Đã loại bỏ thẻ HTML
                        'image' => $image,
                        'source_url' => $link,
                        'created_at' => $pubDate,
                    ];

                    $article_count++;
                }
            }

            // Sắp xếp theo thời gian và lấy 6 bài mới nhất
            $news_articles = collect($news_articles)
                ->sortByDesc('created_at')
                ->take(6)
                ->toArray();

            // Xóa các bài viết cũ
            News::truncate();

            // Lưu các bài viết mới
            foreach ($news_articles as $article) {
                News::create([
                    'title' => $article['title'],
                    'excerpt' => $article['excerpt'],
                    'content' => $article['content'],
                    'image' => $article['image'],
                    'source_url' => $article['source_url'],
                    'created_at' => $article['created_at'],
                    'updated_at' => now(),
                ]);
            }

            $this->info('Đã thu thập và lưu ' . count($news_articles) . ' bài viết.');
        } catch (\Exception $e) {
            $this->error('Lỗi khi thu thập tin tức: ' . $e->getMessage());
        }
    }
}
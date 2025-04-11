<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $feedUrl = 'https://feeds.simplecast.com/54nAGcIl';
        $response = Http::get($feedUrl);

        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());

            if ($xml) {
                foreach ($xml->channel->item as $item) {
                    $this->createBlogPost($item);
                }
                $this->command->info('Successfully imported podcast feed data.');
            } else {
                $this->command->error('Failed to parse the RSS feed.');
            }
        } else {
            $this->command->error('Failed to fetch the RSS feed.');
        }
    }

    protected function createBlogPost($item)
    {
        $title = (string)$item->title;
        
        // Skip if post already exists
        if (Blog::where('title', $title)->exists()) {
            $this->command->warn("Post '{$title}' already exists. Skipping...");
            return;
        }

        // Get content (prefer encoded content if available)
        $content = isset($item->children('content', true)->encoded) 
            ? (string)$item->children('content', true)->encoded 
            : (string)$item->description;

        // Get image URL
        $imageUrl = $this->getImageUrl($item);
        $imagePath = null;

        // Download and store image if URL exists
        if ($imageUrl) {
            try {
                $imageContents = file_get_contents($imageUrl);
                $imageName = 'blog/' . Str::slug($title) . '-' . time() . '.jpg';
                Storage::disk('public')->put($imageName, $imageContents);
                $imagePath = $imageName;
            } catch (\Exception $e) {
                $this->command->error("Failed to download image for '{$title}': " . $e->getMessage());
            }
        }

        $slug = $this->getSlug($title);

        if (Blog::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . uniqid();
        }

        // Create the blog post
        Blog::create([
            'title' => $title,
            'content' => $this->cleanContent($content),
            'status' => 'published',
            'slug' => $slug,
            'created_by' => 1, // or use a specific user ID
            'image' => $imagePath,
            'created_at' => date('Y-m-d H:i:s', strtotime((string)$item->pubDate)),
            'updated_at' => date('Y-m-d H:i:s', strtotime((string)$item->pubDate)),
        ]);

        $this->command->info("Created post: {$title}");
    }

    protected function getSlug($title)
    {
        //remove all special characters
        $slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
        //replace spaces with hyphens
        $slug = str_replace(' ', '-', $slug);
        //convert to lowercase
        $slug = strtolower($slug);
        //remove trailing hyphens
        $slug = rtrim($slug, '-');
        return $slug;
    }

    protected function getImageUrl($item)
    {
        // Check media:thumbnail first
        if (isset($item->children('media', true)->thumbnail)) {
            $media = $item->children('media', true)->thumbnail;
            $attributes = $media->attributes();
            if (isset($attributes['url'])) {
                return (string)$attributes['url'];
            }
        }

        // Then check itunes:image
        if (isset($item->children('itunes', true)->image)) {
            $itunesImage = $item->children('itunes', true)->image;
            $attributes = $itunesImage->attributes();
            if (isset($attributes['href'])) {
                return (string)$attributes['href'];
            }
        }

        return null;
    }

    protected function cleanContent($content)
    {
        // Remove any unnecessary HTML tags or attributes
        $content = strip_tags($content, '<p><a><ul><ol><li><strong><em><br>');

        // Clean up any CDATA tags if present
        $content = str_replace('<![CDATA[', '', $content);
        $content = str_replace(']]>', '', $content);

        return trim($content);
    }
}
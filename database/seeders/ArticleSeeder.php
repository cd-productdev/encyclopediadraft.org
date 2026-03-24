<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleAttribute;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        $statuses = ['draft', 'pending', 'published', 'rejected'];
        $topics = [
            'History', 'Science', 'Technology', 'Geography', 'Biography',
            'Philosophy', 'Literature', 'Art', 'Music', 'Sports',
            'Politics', 'Economics', 'Medicine', 'Engineering', 'Mathematics',
            'Physics', 'Chemistry', 'Biology', 'Psychology', 'Sociology',
            'Anthropology', 'Archaeology', 'Architecture', 'Astronomy', 'Education'
        ];

        // Increased to 200
        $totalArticles = 200;
        $this->command->info("Creating {$totalArticles} articles...");
        $progressBar = $this->command->getOutput()->createProgressBar($totalArticles);

        for ($i = 1; $i <= $totalArticles; $i++) {
            $topic = $topics[array_rand($topics)];
            $title = $this->generateTitle($topic, $i);
            $status = $statuses[array_rand($statuses)];
            $user = $users->random();
            $content = $this->generateContent(500);
            $summary = Str::limit(strip_tags($content), 200);

            $articleData = [
                'title' => $title,
                'slug' => $this->generateUniqueSlug($title),
                'content' => $content,
                'summary' => $summary,
                'status' => $status,
                'created_by' => $user->id,
                'created_at' => now()->subDays(rand(0, 90)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];

            // Timestamps and Reviewer logic
            if (in_array($status, ['published', 'rejected'])) {
                $articleData['submitted_at'] = now()->subDays(rand(10, 30));
                $articleData['reviewed_by'] = $users->where('role', 'admin')->first()->id ?? $users->first()->id;

                if ($status === 'published') {
                    $articleData['published_at'] = now()->subDays(rand(1, 10));
                } else {
                    $articleData['rejection_reason'] = 'Needs more references and better sources.';
                }
            } elseif ($status === 'pending') {
                $articleData['submitted_at'] = now()->subDays(rand(1, 10));
            }

            // References
            $references = [];
            for ($j = 0; $j < rand(2, 5); $j++) {
                $references[] = [
                    'title' => 'Reference ' . ($j + 1) . ': ' . $this->generateReferenceTitle(),
                    'url' => 'https://example.com/reference/' . Str::random(10),
                ];
            }
            $articleData['references'] = json_encode($references);

            // Create Article
            $article = Article::create($articleData);

            // Attributes
            $attributeKeys = ['Type', 'Category', 'Field', 'Origin', 'Period', 'Location', 'Founded', 'Author', 'Date', 'Source'];
            $attributesCount = rand(3, 6);

            for ($k = 0; $k < $attributesCount; $k++) {
                $key = $attributeKeys[array_rand($attributeKeys)];
                ArticleAttribute::create([
                    'article_id' => $article->id,
                    'key' => $key,
                    'value' => $this->generateAttributeValue($key),
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info("Successfully created {$totalArticles} articles!");
    }

    /**
     * Improved Slug generation to prevent collisions
     */
    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title) . '-' . Str::random(5);

        // Check if exists, if so, re-generate
        while (Article::where('slug', $slug)->exists()) {
            $slug = Str::slug($title) . '-' . Str::random(8);
        }

        return $slug;
    }

    /**
     * Generate article title
     */
    private function generateTitle(string $topic, int $number): string
    {
        $templates = [
            'The Complete Guide to {topic}',
            'Understanding {topic}: A Comprehensive Overview',
            'Introduction to {topic}',
            'The History of {topic}',
            'Modern {topic} and Its Impact',
            '{topic} in the 21st Century',
            'Advanced Concepts in {topic}',
            'The Evolution of {topic}',
            'Key Principles of {topic}',
            '{topic}: Past, Present, and Future',
        ];

        $template = $templates[array_rand($templates)];
        return str_replace('{topic}', $topic, $template);
    }

    /**
     * Generate unique slug for article
     */

    /**
     * Generate article content with approximately the specified word count
     */
    private function generateContent(int $wordCount): string
    {
        $paragraphs = [];
        $wordsRemaining = $wordCount;

        // Generate introduction
        $introParagraph = $this->generateParagraph(rand(40, 60));
        $paragraphs[] = "<h2>Introduction</h2>\n<p>" . $introParagraph . "</p>";
        $wordsRemaining -= str_word_count($introParagraph);

        // Generate 3-5 main sections
        $sectionsCount = rand(3, 5);
        $wordsPerSection = intval($wordsRemaining / $sectionsCount);

        $sections = ['Overview', 'Historical Background', 'Key Features', 'Significance', 'Impact', 'Development', 'Analysis', 'Applications'];

        for ($i = 0; $i < $sectionsCount; $i++) {
            $sectionTitle = $sections[$i] ?? "Section " . ($i + 1);
            $sectionContent = $this->generateParagraph($wordsPerSection);
            $paragraphs[] = "<h2>{$sectionTitle}</h2>\n<p>" . $sectionContent . "</p>";
        }

        return implode("\n\n", $paragraphs);
    }

    /**
     * Generate a paragraph with approximately the specified word count
     */
    private function generateParagraph(int $wordCount): string
    {
        $sentences = [];
        $wordsGenerated = 0;

        while ($wordsGenerated < $wordCount) {
            $sentenceLength = rand(10, 20);
            $sentence = $this->generateSentence($sentenceLength);
            $sentences[] = $sentence;
            $wordsGenerated += $sentenceLength;
        }

        return implode(' ', $sentences);
    }

    /**
     * Generate a sentence with the specified number of words
     */
    private function generateSentence(int $wordCount): string
    {
        $words = [
            'the', 'of', 'and', 'to', 'in', 'is', 'that', 'it', 'was', 'for',
            'on', 'are', 'as', 'with', 'his', 'they', 'at', 'be', 'this', 'from',
            'by', 'not', 'or', 'have', 'an', 'which', 'one', 'were', 'all', 'their',
            'there', 'been', 'has', 'when', 'who', 'will', 'more', 'if', 'no', 'out',
            'important', 'significant', 'major', 'primary', 'essential', 'fundamental',
            'notable', 'remarkable', 'substantial', 'considerable', 'extensive',
            'comprehensive', 'complex', 'diverse', 'various', 'numerous', 'multiple',
            'different', 'specific', 'particular', 'general', 'common', 'traditional',
            'modern', 'contemporary', 'historical', 'ancient', 'recent', 'current',
            'development', 'evolution', 'progress', 'advancement', 'innovation',
            'research', 'study', 'analysis', 'investigation', 'examination',
            'understanding', 'knowledge', 'information', 'evidence', 'data',
            'theory', 'concept', 'principle', 'methodology', 'approach', 'framework',
            'process', 'system', 'structure', 'organization', 'foundation',
            'influence', 'impact', 'effect', 'contribution', 'significance',
            'represents', 'demonstrates', 'indicates', 'suggests', 'reveals',
            'shows', 'illustrates', 'reflects', 'emphasizes', 'highlights',
        ];

        $sentence = [];
        for ($i = 0; $i < $wordCount; $i++) {
            $sentence[] = $words[array_rand($words)];
        }

        $sentenceStr = implode(' ', $sentence);
        return ucfirst($sentenceStr) . '.';
    }

    /**
     * Generate reference title
     */
    private function generateReferenceTitle(): string
    {
        $titles = [
            'Journal of Advanced Studies',
            'Encyclopedia of Knowledge',
            'Historical Archives',
            'Scientific Research Papers',
            'Academic Publications',
            'International Review',
            'Scholarly Articles',
            'Research Documentation',
            'Academic Journal',
            'Reference Database',
        ];

        return $titles[array_rand($titles)] . ' (' . rand(1990, 2025) . ')';
    }

    /**
     * Generate attribute value based on key
     */
    private function generateAttributeValue(string $key): string
    {
        $values = [
            'Type' => ['Academic', 'Professional', 'Historical', 'Scientific', 'Cultural'],
            'Category' => ['Research', 'Documentation', 'Analysis', 'Study', 'Report'],
            'Field' => ['Science', 'Arts', 'Technology', 'Humanities', 'Social Sciences'],
            'Origin' => ['Europe', 'Asia', 'Americas', 'Africa', 'Oceania'],
            'Period' => ['Ancient', 'Medieval', 'Modern', 'Contemporary', 'Renaissance'],
            'Location' => ['Global', 'Regional', 'International', 'Local', 'Continental'],
            'Founded' => [rand(1800, 2020)],
            'Author' => ['Various Authors', 'Multiple Contributors', 'Research Team', 'Academic Committee'],
            'Date' => [rand(1990, 2025)],
            'Source' => ['Academic Press', 'University Publishing', 'Research Institute', 'International Organization'],
        ];

        $options = $values[$key] ?? ['Unknown'];
        return is_array($options[0]) ? (string)$options[0] : $options[array_rand($options)];
    }
}

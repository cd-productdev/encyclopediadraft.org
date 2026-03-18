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
        
        // Article topics for more realistic content
        $topics = [
            'History', 'Science', 'Technology', 'Geography', 'Biography',
            'Philosophy', 'Literature', 'Art', 'Music', 'Sports',
            'Politics', 'Economics', 'Medicine', 'Engineering', 'Mathematics',
            'Physics', 'Chemistry', 'Biology', 'Psychology', 'Sociology',
            'Anthropology', 'Archaeology', 'Architecture', 'Astronomy', 'Education'
        ];

        $this->command->info('Creating 100 articles...');
        $progressBar = $this->command->getOutput()->createProgressBar(100);

        for ($i = 1; $i <= 100; $i++) {
            $topic = $topics[array_rand($topics)];
            $title = $this->generateTitle($topic, $i);
            $status = $statuses[array_rand($statuses)];
            $user = $users->random();

            // Generate 500-word content
            $content = $this->generateContent(500);
            
            // Generate summary (first 100-150 words)
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

            // Add status-specific timestamps
            if ($status === 'pending') {
                $articleData['submitted_at'] = now()->subDays(rand(1, 10));
            } elseif ($status === 'published') {
                $articleData['submitted_at'] = now()->subDays(rand(10, 30));
                $articleData['published_at'] = now()->subDays(rand(1, 10));
                $articleData['reviewed_by'] = $users->where('role', 'admin')->first()->id ?? $users->first()->id;
            } elseif ($status === 'rejected') {
                $articleData['submitted_at'] = now()->subDays(rand(5, 20));
                $articleData['reviewed_by'] = $users->where('role', 'admin')->first()->id ?? $users->first()->id;
                $articleData['rejection_reason'] = 'Needs more references and better sources. Please revise and resubmit.';
            }

            // Add references (2-5 references per article)
            $referencesCount = rand(2, 5);
            $references = [];
            for ($j = 0; $j < $referencesCount; $j++) {
                $references[] = [
                    'title' => 'Reference ' . ($j + 1) . ': ' . $this->generateReferenceTitle(),
                    'url' => 'https://example.com/reference/' . Str::random(10),
                ];
            }
            $articleData['references'] = json_encode($references);

            $article = Article::create($articleData);

            // Add article attributes (infobox fields) - 3-6 attributes per article
            $attributesCount = rand(3, 6);
            $attributeKeys = ['Type', 'Category', 'Field', 'Origin', 'Period', 'Location', 'Founded', 'Author', 'Date', 'Source'];
            
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
        $this->command->info('Successfully created 100 articles!');
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
    private function generateUniqueSlug(string $title): string
    {
        $randomString = uniqid($title, true);
        return rtrim(base64_encode($randomString), '=');
    }

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

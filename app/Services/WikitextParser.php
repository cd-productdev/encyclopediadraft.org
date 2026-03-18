<?php

namespace App\Services;

class WikitextParser
{
    /**
     * Parse Wikitext to HTML
     */
    public function parse(string $wikitext): string
    {
        if (empty($wikitext)) {
            return '';
        }

        $html = $wikitext;

        // Headings (== Heading ==, === Subheading ===, etc.)
        $html = preg_replace('/^=====(.+?)=====\s*$/m', '<h5>$1</h5>', $html);
        $html = preg_replace('/^====(.+?)====\s*$/m', '<h4>$1</h4>', $html);
        $html = preg_replace('/^===(.+?)===\s*$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^==(.+?)==\s*$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^=(.+?)=\s*$/m', '<h1>$1</h1>', $html);

        // Bold ('''text''')
        $html = preg_replace("/'''(.+?)'''/", '<strong>$1</strong>', $html);

        // Italic (''text'')
        $html = preg_replace("/''(.+?)''/", '<em>$1</em>', $html);

        // Internal links [[Page Name]] or [[Page Name|Display Text]]
        $html = preg_replace_callback(
            '/\[\[([^\]]+)\]\]/',
            function ($matches) {
                $link = $matches[1];
                if (strpos($link, '|') !== false) {
                    [$page, $text] = explode('|', $link, 2);
                    $slug = $this->slugify($page);

                    return '<a href="/'.$slug.'">'.htmlspecialchars($text).'</a>';
                } else {
                    $slug = $this->slugify($link);

                    return '<a href="/'.$slug.'">'.htmlspecialchars($link).'</a>';
                }
            },
            $html
        );

        // External links [URL Text] or [URL]
        $html = preg_replace_callback(
            '/\[(https?:\/\/[^\s\]]+)(?:\s+([^\]]+))?\]/',
            function ($matches) {
                $url = $matches[1];
                $text = $matches[2] ?? $url;

                return '<a href="'.htmlspecialchars($url).'" target="_blank" rel="noopener">'.htmlspecialchars($text).'</a>';
            },
            $html
        );

        // Lists - Simple approach: convert to HTML lists
        // Unordered lists (* item)
        $html = preg_replace_callback('/^(\*+)\s*(.+)$/m', function ($matches) {
            $stars = $matches[1];
            $content = trim($matches[2]);
            $depth = strlen($stars);
            $indent = str_repeat('  ', $depth - 1);

            return $indent.'<ul><li>'.$content.'</li></ul>';
        }, $html);

        // Ordered lists (# item)
        $html = preg_replace_callback('/^(#+)\s*(.+)$/m', function ($matches) {
            $hashes = $matches[1];
            $content = trim($matches[2]);
            $depth = strlen($hashes);
            $indent = str_repeat('  ', $depth - 1);

            return $indent.'<ol><li>'.$content.'</li></ol>';
        }, $html);

        // Horizontal rule (----)
        $html = preg_replace('/^----+$/m', '<hr>', $html);

        // Line breaks (double newline = paragraph)
        $html = preg_replace('/\n\n+/', '</p><p>', $html);
        $html = '<p>'.$html.'</p>';

        // Single line breaks
        $html = preg_replace('/\n/', '<br>', $html);

        // Categories [[Category:Name]]
        $html = preg_replace('/\[\[Category:([^\]]+)\]\]/', '', $html);

        // Templates {{Template Name|param1=value1|param2=value2}}
        $html = preg_replace_callback(
            '/\{\{([^}]+)\}\}/',
            function ($matches) {
                return $this->renderTemplate($matches[1]);
            },
            $html
        );

        // Clean up empty paragraphs
        $html = preg_replace('/<p>\s*<\/p>/', '', $html);
        $html = preg_replace('/<p>(<[^>]+>)<\/p>/', '$1', $html);

        return $html;
    }

    /**
     * Convert text to slug
     */
    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');

        return $text;
    }

    /**
     * Render template
     */
    private function renderTemplate(string $templateString): string
    {
        // Parse template name and parameters
        // Format: TemplateName|param1=value1|param2=value2
        $parts = explode('|', $templateString);
        $templateName = trim($parts[0]);

        // Get parameters
        $params = [];
        for ($i = 1; $i < count($parts); $i++) {
            $part = trim($parts[$i]);
            if (strpos($part, '=') !== false) {
                [$key, $value] = explode('=', $part, 2);
                $params[trim($key)] = trim($value);
            } else {
                // Positional parameter
                $params[$i - 1] = $part;
            }
        }

        // Load template from database
        try {
            $template = \App\Models\Template::where('name', $templateName)
                ->orWhere('slug', $templateName)
                ->where('is_active', true)
                ->first();

            if ($template) {
                // Increment usage count
                $template->increment('usage_count');

                // Render template with parameters
                return $template->render($params);
            }
        } catch (\Exception $e) {
            // Template not found or error, return placeholder
        }

        // Return template placeholder if not found
        return '<div class="template" data-name="'.htmlspecialchars($templateName).'">'.
               htmlspecialchars($templateString).'</div>';
    }

    /**
     * Parse specific Wikitext elements
     */
    public function parseInline(string $wikitext): string
    {
        $html = $wikitext;

        // Bold
        $html = preg_replace("/'''(.+?)'''/", '<strong>$1</strong>', $html);

        // Italic
        $html = preg_replace("/''(.+?)''/", '<em>$1</em>', $html);

        // Links
        $html = preg_replace_callback(
            '/\[\[([^\]]+)\]\]/',
            function ($matches) {
                $link = $matches[1];
                if (strpos($link, '|') !== false) {
                    [$page, $text] = explode('|', $link, 2);
                    $slug = $this->slugify($page);

                    return '<a href="/'.$slug.'">'.htmlspecialchars($text).'</a>';
                } else {
                    $slug = $this->slugify($link);

                    return '<a href="/'.$slug.'">'.htmlspecialchars($link).'</a>';
                }
            },
            $html
        );

        return $html;
    }
}

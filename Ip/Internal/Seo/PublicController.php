<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Seo;


/**
 * Serves sitemap.xml, robots.txt, llms.txt and llms-full.txt, built from the published pages.
 */
class PublicController extends \Ip\Controller
{
    public function sitemap()
    {
        if (!ipGetOption('Config.sitemapEnabled', 1)) {
            return new \Ip\Response\PageNotFound();
        }

        $urls = [];
        foreach (Model::getSitePages() as $section) {
            foreach ($section['pages'] as $page) {
                $urls[] = array('loc' => $page['url'], 'lastmod' => $page['updatedAt']);
            }
        }
        $urls = ipFilter('ipSitemapUrls', $urls);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= '  <url><loc>' . esc($url['loc']) . '</loc>';
            if (!empty($url['lastmod'])) {
                $xml .= '<lastmod>' . esc($url['lastmod']) . '</lastmod>';
            }
            $xml .= '</url>' . "\n";
        }
        $xml .= '</urlset>' . "\n";

        return new \Ip\Response($xml, 'Content-type: application/xml; charset=utf-8');
    }

    public function robots()
    {
        $lines = array(
            'User-agent: *',
            'Disallow: /update/',
            'Disallow: /install/',
            'Disallow: /admin.php',
            'Disallow: /admin/',
        );
        foreach (Model::aiCrawlers() as $userAgent => $operator) {
            if (!ipGetOption('Config.' . Model::aiCrawlerOption($userAgent), 1)) {
                $lines[] = '';
                $lines[] = 'User-agent: ' . $userAgent;
                $lines[] = 'Disallow: /';
            }
        }
        if (ipGetOption('Config.sitemapEnabled', 1)) {
            $lines[] = '';
            $lines[] = 'Sitemap: ' . ipConfig()->baseUrl() . 'sitemap.xml';
        }
        $text = ipFilter('ipRobotsTxt', implode("\n", $lines) . "\n");

        return new \Ip\Response($text, 'Content-type: text/plain; charset=utf-8');
    }

    /**
     * Plain-text site summary for AI assistants, following https://llmstxt.org
     */
    public function llms()
    {
        if (!ipGetOption('Config.llmsTxtEnabled', 1)) {
            return new \Ip\Response\PageNotFound();
        }

        // A language without public pages (e.g. one just added) would only add an empty heading.
        $sections = array_values(array_filter(Model::getSitePages(), function ($section) {
            return !empty($section['pages']);
        }));
        $multilingual = count($sections) > 1;
        $firstLanguage = $sections ? $sections[0]['language']->getCode() : null;

        $title = trim((string)ipGetOptionLang('Config.websiteTitle', $firstLanguage, ''));
        $summary = trim((string)ipGetOptionLang('Config.websiteDescription', $firstLanguage, ''));
        if ($summary === '' && !empty($sections[0]['pages'][0]['isHome'])) {
            $summary = $sections[0]['pages'][0]['description'];
        }

        $text = '# ' . ($title !== '' ? $title : ipConfig()->baseUrl()) . "\n\n";
        if ($summary !== '') {
            $text .= '> ' . preg_replace('/\s+/', ' ', $summary) . "\n\n";
        }

        foreach ($sections as $section) {
            $text .= '## ' . ($multilingual ? sprintf('Pages (%s)', $section['language']->getTitle()) : 'Pages') . "\n\n";
            foreach ($section['pages'] as $page) {
                $text .= '- [' . $page['title'] . '](' . $page['url'] . ')';
                if ($page['description'] !== '') {
                    $text .= ': ' . preg_replace('/\s+/', ' ', $page['description']);
                }
                $text .= "\n";
            }
            $text .= "\n";
        }

        if (ipGetOption('Config.llmsFullTxtEnabled', 0)) {
            $text .= "## Optional\n\n- [Full text of every page](" . ipConfig()->baseUrl() . "llms-full.txt)\n\n";
        }

        $text = ipFilter('ipLlmsTxt', rtrim($text) . "\n", array('sections' => $sections));

        return new \Ip\Response($text, 'Content-type: text/plain; charset=utf-8');
    }

    /**
     * The text of every public page in one file, for AI assistants (the llms-full.txt companion of llms.txt).
     */
    public function llmsFull()
    {
        if (!ipGetOption('Config.llmsFullTxtEnabled', 0)) {
            return new \Ip\Response\PageNotFound();
        }

        $sections = array_values(array_filter(Model::getSitePages(), function ($section) {
            return !empty($section['pages']);
        }));
        $multilingual = count($sections) > 1;
        $firstLanguage = $sections ? $sections[0]['language']->getCode() : null;

        $title = trim((string)ipGetOptionLang('Config.websiteTitle', $firstLanguage, ''));
        $summary = trim((string)ipGetOptionLang('Config.websiteDescription', $firstLanguage, ''));

        $text = '# ' . ($title !== '' ? $title : ipConfig()->baseUrl()) . "\n\n";
        if ($summary !== '') {
            $text .= '> ' . preg_replace('/\s+/', ' ', $summary) . "\n\n";
        }

        foreach ($sections as $section) {
            foreach ($section['pages'] as $page) {
                $text .= '## ' . $page['title'] . "\n\n";
                $text .= 'URL: ' . $page['url'] . "\n";
                if ($multilingual) {
                    $text .= 'Language: ' . $section['language']->getTitle() . "\n";
                }
                if ($page['description'] !== '') {
                    $text .= 'Summary: ' . preg_replace('/\s+/', ' ', $page['description']) . "\n";
                }
                $body = Model::pageText($page['id']);
                $text .= "\n" . ($body !== '' ? $body . "\n\n" : '');
            }
        }

        $text = ipFilter('ipLlmsFullTxt', rtrim($text) . "\n", array('sections' => $sections));

        return new \Ip\Response($text, 'Content-type: text/plain; charset=utf-8');
    }
}

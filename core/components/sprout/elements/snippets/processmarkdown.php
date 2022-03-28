<?php
/**
 * sproutProcessMarkdown
 *
 * Convert Markdown to HTML using the PHP League commonmark parser:
 * https://commonmark.thephpleague.com/
 *
 * Note that the front matter extension is loaded, but not used. This hides any
 * front matter present in the markdown file. In the future, it could be
 * rendered through a placeholder. For details, see:
 * https://commonmark.thephpleague.com/2.2/extensions/front-matter/
 */

use FractalFarming\Sprout\Sprout;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * @var modX $modx
 * @var array $scriptProperties
 * @var string $input
 * @var string $options
 */

$sprout = new Sprout($modx);
$input = $modx->getOption('markdown', $scriptProperties, $input);
$assetsPath = $modx->getOption('sprout.assets_path_static', $scriptProperties, 'uploads/notes/assets/attachments/');

// Get file extension
$query = $modx->newQuery('modContentType');
$query->where(['id' => $modx->resource->get('content_type')]);
$query->select('file_extensions');
$ext = $modx->getValue($query->prepare());

// Define your CommonMark configuration, if needed
$config = [
    'external_link' => [
        'internal_hosts' => $modx->getOption('site_url', $scriptProperties),
        'open_in_new_window' => true,
        'html_class' => 'external',
        'nofollow' => '',
        'noopener' => 'external',
        'noreferrer' => 'external',
    ],
];

// Configure the Environment with all the CommonMark and GFM parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new GithubFlavoredMarkdownExtension());
$environment->addExtension(new ExternalLinkExtension());
$environment->addExtension(new FrontMatterExtension());

// Parse content
$converter = new MarkdownConverter($environment);
$html = $converter->convert($input);

// Parse front matter (for future reference)
//if ($html instanceof RenderedContentWithFrontMatter) {
//    $modx->toPlaceholder('front_matter', $html->getFrontMatter());
//}

// Escape MODX tags
$html = $sprout->escapeTags($html);

// Post-processing with HtmlPageDom
$dom = new HtmlPageCrawler($html);

// Transform Obsidian links to HTML
$dom->filter('h1, h2, h3, h4, h5, h6, p, li, blockquote')
    ->each(function (HtmlPageCrawler $element) use ($ext, $assetsPath) {
        $content = $element->getInnerHtml();

        $matchEmbed = '/\!\[\[([^\]]+)\]\]/'; // ![[some-image.jpg]]
        $matchPipedRef = '/\[\[([^\]]+)\|([^\]]+)\]\]/'; // [[some-article|See here]]
        $matchRef = '/\[\[([^(\]|&|+)]+)\]\]/'; // [[Regular reference]]

        // Look for matches and bundle them with PREG_SET_ORDER
        preg_match_all($matchEmbed, $content,$embeds, PREG_SET_ORDER);
        preg_match_all($matchPipedRef, $content,$pipedRefs, PREG_SET_ORDER);
        preg_match_all($matchRef, $content,$refs, PREG_SET_ORDER);

        $dirty = 0;
        foreach ($embeds as $embed) {
            if (!$embed) continue;
            $content = str_replace($embed[0], '<img src="'.$assetsPath.$embed[1].'" />', $content);
            $dirty = 1;
        }
        foreach ($pipedRefs as $ref) {
            if (!$ref) continue;
            $content = str_replace($ref[0], '<a href="'.$ref[1].$ext.'">'.$ref[2].'</a>', $content);
            $dirty = 1;
        }
        foreach ($refs as $ref) {
            if (!$ref) continue;
            $content = str_replace($ref[0], '<a href="'.$ref[1].$ext.'">'.$ref[1].'</a>', $content);
            $dirty = 1;
        }

        // Only replace HTML in altered elements
        if ($dirty) {
            $element->makeEmpty();
            $element->setInnerHtml($content);
        }
    })
;

// Modify links
$dom->filter('a')
    ->each(function (HtmlPageCrawler $link) use ($modx, $ext) {
        $href = $link->getAttribute('href');
        $href = str_replace('.md', $ext, $href);

        // Replace .md extension
        $link->setAttribute('href', $href);

        // Internal link
        if ($link->hasClass('external') === false) {

            // Prepend anchor link with URI
            if (strpos($href, '#') === 0) {
                $link
                    ->addClass('contrast')
                    ->setAttribute('href', $modx->resource->get('uri') . $href)
                ;
                return;
            }

            // Mute non-existing link
            $query = $modx->newQuery('modResource');
            $query->where(['uri' => $href]);
            $query->select('id');
            if (!$modx->getValue($query->prepare())) {
                $link->addClass('secondary');
            }
        }
    })
;

// Update HTML
$html = $sprout->escapeTags($dom->saveHTML());

return $html;
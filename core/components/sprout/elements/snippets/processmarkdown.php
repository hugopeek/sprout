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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter;

/**
 * @var modX $modx
 * @var array $scriptProperties
 * @var string $input
 * @var string $options
 */

$input = $modx->getOption('markdown', $scriptProperties, $input);

// Define your configuration, if needed
$config = [];

// Configure the Environment with all the CommonMark and GFM parsers/renderers
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new GithubFlavoredMarkdownExtension());
$environment->addExtension(new FrontMatterExtension());

// Parse content
$converter = new MarkdownConverter($environment);
$html = $converter->convert($input);

// Parse front matter (for future reference)
//if ($html instanceof RenderedContentWithFrontMatter) {
//    $modx->toPlaceholder('frontmatter', $html->getFrontMatter());
//}

// Escape MODX tags
$html = str_replace(
    ['[', ']', '&amp;#96;', '{', '}'],
    ['&#91;', '&#93;', '&#96;', '&#123;', '&#125;'],
    $html
);

return $html;
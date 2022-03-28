<?php
/**
 * sproutImportArticles
 *
 * Scan static markdown folder and create a resource for each new article.
 *
 * For an article to be imported, it needs to contain a configuration object for
 * MODX in YAML format (inside the front matter section). For example:
 *
 * modx:
 *     id: 0
 *     pagetitle: 'Markdown content'
 *     longtitle: ''
 *     description: ''
 *     introtext: ''
 *     parent: 2
 *     template: 2
 *     published: 1
 *
 * Make sure the ID is either 0, empty or absent. After generating the resource,
 * the ID will be updated in or added to the front matter.
 *
 * If a resource already exists, it will update the resource with any changes
 * made in the front matter data. That means this data takes precedence over
 * changes made in MODX. So if you add a description in MODX while it's still
 * empty in the front matter, your changes will be lost on the next import.
 * If you want to avoid this behaviour, simply remove the line from the config.
 *
 * This snippet depends on the DirWalker extra from Bob Ray.
 */

use FractalFarming\Sprout\Sprout;
use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;
use Symfony\Component\Yaml\Yaml;

require_once MODX_CORE_PATH . 'components/dirwalker/model/dirwalker/dirwalker.class.php';

/**
 * @var modX $modx
 * @var array $scriptProperties
 */

if (!class_exists(DirWalker::class)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'DirWalker class not found.');
}

$searchStart = MODX_BASE_PATH . 'uploads/notes/';
$dw = new DirWalker();
$dw->setIncludes('.md');
$dw->setExcludes('.gitignore');
$dw->setExcludeDirs('.git,assets');
$dw->dirWalk($searchStart, true);

$fileArray = $dw->getFiles();

foreach($fileArray as $path => $filename) {
    $content = file_get_contents($path);
    $relativePath = str_replace(MODX_BASE_PATH, '', $path);
    $config = [];

    // Get front matter
    $frontMatterParser = new FrontMatterParser(new SymfonyYamlFrontMatterParser());
    $result = $frontMatterParser->parse($content);
    $frontMatter = $result->getFrontMatter();

    // Skip file if it doesn't contain a MODX config
    if (!$frontMatter['modx']) {
        continue;
    }

    // Update existing resource or create new one
    if ($frontMatter['modx']['id']) {
        $resource = $modx->getObject('modResource', $frontMatter['modx']['id']);

        if (is_object($resource)) {
            $resource->fromArray($frontMatter['modx']);
            $resource->save();
        } else {
            echo 'Resource not found!';
        }
    }
    else {
        $resource = $modx->newObject('modResource');
        $resource->fromArray($frontMatter['modx']);
        $resource->set('content_type', 1);
        $resource->set('class_key', 'MODX\Revolution\modStaticResource');
        $resource->set('content', $relativePath);
        $resource->set('richtext', 0);
        $resource->set('show_in_tree', 1);
        $resource->save();

        // Write ID back to the markdown file
        if ($resource->get('id')) {
            $frontMatter['modx']['id'] = $resource->get('id');

            // Split original content to isolate front matter
            $split = preg_split('/[\n]*[-]{3}[\n]/', $content, 3);

            // Overwrite front matter with updated ID
            file_put_contents($path, str_replace($split[1], yaml::dump($frontMatter), $content));
        } else {
            echo 'Massive failure!';
        }
    }
}

return;
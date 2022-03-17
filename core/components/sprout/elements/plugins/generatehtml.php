<?php
/**
 * sproutGenerateHTML
 *
 * Generate static HTML files in designated folder.
 *
 * Inspired by StatCache, but with the intention of creating a fully independent
 * static site. So with all assets included, and without backend installed.
 *
 * Please note that the OnBeforeSaveWebPageCache event fires only once, whereas
 * OnWebPagePrerender is triggered on every page visit.
 */

namespace FractalFarming\Sprout\GenerateHTML;

use modX;
use FractalFarming\Sprout\Sprout;
//use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * @var modX $modx
 * @var array $scriptProperties
 */
switch ($modx->event->name) {
    case 'OnSiteRefresh':

        // Regenerate all, or maybe just use a crawler?

        break;

    case 'OnBeforeSaveWebPageCache':
        $sprout = new Sprout($modx);

        // Get processed output of resource
        $output = &$modx->resource->_output;

        // Generate static file
        $staticPath = $sprout->getStaticPath($modx->resource->get('uri'));
        if (!$modx->cacheManager->writeFile($staticPath, $output)) {
            $modx->log(modX::LOG_LEVEL_ERROR, "Error caching output from Resource {$modx->resource->get('id')} to static file {$staticPath}", '', __FUNCTION__, __FILE__, __LINE__);
        }

        break;

    case 'OnDocFormSave':
        /**
         * @var modResource $resource
         * @var int $id
         */

        // Crawl page to generate static file (borrowed from StatCache)
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, $modx->getOption('regenerate_useragent', $scriptProperties, 'MODX RegenCache'));
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        $url = $modx->makeUrl($id, $resource->get('context_key'), '', 'full');
        if ($url) {
            $modx->log(modX::LOG_LEVEL_INFO, "Requesting Resource at {$url}");
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_exec($curl);
            $modx->log(modX::LOG_LEVEL_INFO, "Updated cache for resource at {$url}");
        }
        curl_close($curl);

        break;

    case 'OnDocUnPublished':
    case 'OnResourceDelete':
        /**
         * @var modResource $resource
         * @var int $id
        */

        $sprout = new Sprout($modx);
        $staticPath = $sprout->getStaticPath($resource->get('uri'));

        // Remove file
        if (is_readable($staticPath)) {
            @unlink($staticPath);
        }

        break;
}
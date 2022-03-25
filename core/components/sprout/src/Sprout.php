<?php
namespace FractalFarming\Sprout;

use modX;

class Sprout
{
    /** @var modX $modx */
    public $modx;

    public $namespace = 'sprout';

    /** @var array $config */
    public $config = [];

    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/sprout/');
        $assetsUrl = $this->getOption('assets_url', $config, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/sprout/');

        $this->config = array_merge(
            [
                'corePath'  => $corePath,
                'srcPath'   => $corePath . 'src/',
                'modelPath' => $corePath . 'src/Model/',
                'assetsUrl' => $assetsUrl,
                'cssUrl'    => $assetsUrl . 'css/',
                'jsUrl'     => $assetsUrl . 'js/',

                'templatesPath' => $corePath . 'templates/',
                'processorsPath' => $corePath . 'src/Processors',
            ],
            $config
        );
        $this->modx->lexicon->load('sprout:default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     *
     * @return mixed The option value or the default value specified.
     */
    public function getOption(string $key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    /**
     * Generate path for static file, based on resource alias.
     *
     * @param string $uri The resource URI.
     * @param array $properties An array of options that override local options.
     *
     * @return string
     */
    public function getStaticPath(string $uri, $properties = [])
    {
        $staticPath = $this->modx->getOption('static_path', $properties, MODX_BASE_PATH . 'static/') . $uri;

        // Add .html extension if needed and create index for category pages
        if (substr($staticPath, -5) == '.html') {
            return $staticPath;
        }
        elseif (substr($staticPath, -1) == '/') {
            return $staticPath . 'index.html';
        }
        else {
            return $staticPath . '.html';
        }
    }
}

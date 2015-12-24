<?php namespace Barryvdh\Debugbar;

use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;
use Illuminate\Routing\UrlGenerator;

/**
 * {@inheritdoc}
 */
class JavascriptRenderer extends BaseJavascriptRenderer
{
    // Use XHR handler by default, instead of jQuery
    protected $ajaxHandlerBindToJquery = false;
    protected $ajaxHandlerBindToXHR = true;

    // Format the javascript output so it is compatible with requirejs
    protected $requirejsCompatible = false;

    /** @var \Illuminate\Routing\UrlGenerator */
    protected $url;

    public function __construct(DebugBar $debugBar, $baseUrl = null, $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);

        $this->cssFiles['laravel'] = __DIR__ . '/Resources/laravel-debugbar.css';
        $this->cssVendors['fontawesome'] = __DIR__ . '/Resources/vendor/font-awesome/style.css';
    }

    /**
     * Set the URL Generator
     *
     * @param \Illuminate\Routing\UrlGenerator $url
     */
    public function setUrlGenerator($url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function renderHead()
    {
        if (!$this->url) {
            return parent::renderHead();
        }

        $jsModified = $this->getModifiedTime('js');
        $cssModified = $this->getModifiedTime('css');

        $html = '';
        $html .= sprintf(
            '<link rel="stylesheet" type="text/css" href="%s?%s">' . "\n",
            $this->url->route('debugbar.assets.css'),
            $cssModified
        );
        if (!$this->requirejsCompatible) {
            $html .= sprintf(
                '<script type="text/javascript" src="%s?%s"></script>' . "\n",
                $this->url->route('debugbar.assets.js'),
                $jsModified
            );
        }

        if ($this->isJqueryNoConflictEnabled() && !$this->requirejsCompatible) {
            $html .= '<script type="text/javascript">jQuery.noConflict(true);</script>' . "\n";
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function render($initialize = true, $renderStackedData = true)
    {
        $js = parent::render($initialize, $renderStackedData);
        if ($this->requirejsCompatible) {
            $js = str_replace('<script type="text/javascript">', "<script type=\"text/javascript\">\nrequire(['debugbar-all'], function(PhpDebugBar){", $js);
            $js = str_replace('</script>', "});\n</script>", $js);
        }
        return $js;
    }

    /**
     * Get the last modified time of any assets.
     *
     * @param string $type 'js' or 'css'
     * @return int
     */
    protected function getModifiedTime($type)
    {
        $files = $this->getAssets($type);

        $latest = 0;
        foreach ($files as $file) {
            $mtime = filemtime($file);
            if ($mtime > $latest) {
                $latest = $mtime;
            }
        }
        return $latest;
    }

    /**
     * Return assets as a string
     *
     * @param string $type 'js' or 'css'
     * @return string
     */
    public function dumpAssetsToString($type)
    {
        $files = $this->getAssets($type);

        $content = '';
        foreach ($files as $file) {
            $content .= file_get_contents($file) . "\n";
        }

        return $content;
    }

    /**
     * Makes a URI relative to another
     *
     * @param string|array $uri
     * @param string $root
     * @return string
     */
    protected function makeUriRelativeTo($uri, $root)
    {
        if (!$root) {
            return $uri;
        }

        if (is_array($uri)) {
            $uris = array();
            foreach ($uri as $u) {
                $uris[] = $this->makeUriRelativeTo($u, $root);
            }
            return $uris;
        }

        if (substr($uri, 0, 1) === '/' || preg_match('/^([a-zA-Z]+:\/\/|[a-zA-Z]:\/|[a-zA-Z]:\\\)/', $uri)) {
            return $uri;
        }
        return rtrim($root, '/') . "/$uri";
    }

    /**
     * Sets the requirejsCompatible property
     *
     * @param $value
     */
    public function setRequirejsCompatible($value)
    {
        $this->requirejsCompatible = $value;
    }
}

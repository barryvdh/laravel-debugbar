<?php

namespace Barryvdh\Debugbar;

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

    public function __construct(DebugBar $debugBar, $baseUrl = null, $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);

        $this->cssFiles['laravel'] = __DIR__ . '/Resources/laravel-debugbar.css';
        $this->jsFiles['laravel-cache'] = __DIR__ . '/Resources/cache/widget.js';
        $this->jsFiles['laravel-queries'] = __DIR__ . '/Resources/queries/widget.js';

        $this->setTheme(config('debugbar.theme', 'auto'));
    }

    /**
     * Set the URL Generator
     *
     * @param \Illuminate\Routing\UrlGenerator $url
     * @deprecated
     */
    public function setUrlGenerator($url)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function renderHead()
    {
        $cssRoute = preg_replace('/\Ahttps?:\/\/[^\/]+/', '', route('debugbar.assets.css', [
            'v' => $this->getModifiedTime('css'),
        ]));

        $jsRoute = preg_replace('/\Ahttps?:\/\/[^\/]+/', '', route('debugbar.assets.js', [
            'v' => $this->getModifiedTime('js')
        ]));

        $nonce = $this->getNonceAttribute();

        $html  = "<link rel='stylesheet' type='text/css' property='stylesheet' href='{$cssRoute}' data-turbolinks-eval='false' data-turbo-eval='false'>";
        $html .= "<script{$nonce} src='{$jsRoute}' data-turbolinks-eval='false' data-turbo-eval='false'></script>";

        if ($this->isJqueryNoConflictEnabled()) {
            $html .= "<script{$nonce} data-turbo-eval='false'>jQuery.noConflict(true);</script>" . "\n";
        }

        $inlineHtml = $this->getInlineHtml();
        if ($nonce != '') {
            $inlineHtml = preg_replace("/<(script|style)>/", "<$1{$nonce}>", $inlineHtml);
        }
        $html .= $inlineHtml;


        return $html;
    }

    protected function getInlineHtml()
    {
        $html = '';

        foreach (['head', 'css', 'js'] as $asset) {
            foreach ($this->getAssets('inline_' . $asset) as $item) {
                $html .= $item . "\n";
            }
        }

        return $html;
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
            $uris = [];
            foreach ($uri as $u) {
                $uris[] = $this->makeUriRelativeTo($u, $root);
            }
            return $uris;
        }

        if (substr($uri ?? '', 0, 1) === '/' || preg_match('/^([a-zA-Z]+:\/\/|[a-zA-Z]:\/|[a-zA-Z]:\\\)/', $uri ?? '')) {
            return $uri;
        }
        return rtrim($root, '/') . "/$uri";
    }
}

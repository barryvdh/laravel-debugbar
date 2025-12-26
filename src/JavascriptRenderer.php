<?php

namespace Barryvdh\Debugbar;

use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;

/**
 * {@inheritdoc}
 */
class JavascriptRenderer extends BaseJavascriptRenderer
{
    // Use XHR handler by default, instead of jQuery
    protected $ajaxHandlerBindToJquery = false;
    protected $ajaxHandlerBindToXHR = true;

    /**
     * Position configuration for floating debugbar
     *
     * @var array
     */
    protected $positionConfig = [];

    public function __construct(DebugBar $debugBar, $baseUrl = null, $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);

        $this->cssFiles['laravel'] = __DIR__ . '/Resources/laravel-debugbar.css';
        $this->jsFiles['laravel-cache'] = __DIR__ . '/Resources/cache/widget.js';
        $this->jsFiles['laravel-queries'] = __DIR__ . '/Resources/queries/widget.js';
        $this->jsFiles['laravel-draggable'] = __DIR__ . '/Resources/draggable.js';

        $this->setTheme(config('debugbar.theme', 'auto'));
    }

    /**
     * Set the position configuration for the debugbar
     *
     * @param string $position Position setting: 'bottom' or 'floating'
     * @param array $floatingOptions Options for floating mode
     * @return void
     */
    public function setPositionOptions(string $position = 'bottom', array $floatingOptions = []): void
    {
        $this->positionConfig = [
            'position' => $position,
            'floating' => array_merge([
                'initial_x' => null,
                'initial_y' => null,
                'remember_position' => true,
            ], $floatingOptions),
        ];
    }

    /**
     * Get the position configuration
     *
     * @return array
     */
    public function getPositionConfig(): array
    {
        return $this->positionConfig;
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
        $html .= $this->getPreloadInlineHtml($nonce);

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

    protected function getPreloadInlineHtml(string $nonce): string
    {
        $html = '';

        if (!empty($this->positionConfig)) {
            $configJson = json_encode($this->positionConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            $html .= "<script{$nonce}>window.phpdebugbar_position_config = {$configJson};</script>";
        }

        $html .= "<script{$nonce}>(function(){try{var s=JSON.parse(localStorage.getItem('phpdebugbar-settings')||'{}');var c=window.phpdebugbar_position_config||{};if((s.positionMode||c.position)==='floating'){var st=document.createElement('style');st.id='phpdebugbar-fouc-fix';st.textContent='div.phpdebugbar:not(.phpdebugbar-ready){opacity:0!important}';(document.head||document.documentElement).appendChild(st);}}catch(e){}})();</script>";

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

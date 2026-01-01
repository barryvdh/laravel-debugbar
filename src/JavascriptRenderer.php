<?php

namespace Barryvdh\Debugbar;

use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;

/**
 * {@inheritdoc}
 */
class JavascriptRenderer extends BaseJavascriptRenderer
{
    public function __construct(DebugBar $debugBar, $baseUrl = null, $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);

        $resourceDir = __DIR__ . '/../resources';

        $this->additionalAssets[] = [
            'base_path' => $resourceDir,
            'css' => ['laravel-debugbar.css', 'laravel-icons.css'],
            'js' => ['cache/widget.js', 'queries/widget.js'],
        ];

        $this->setTheme(config('debugbar.theme', 'auto'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderHead(): string
    {
        $cssRoute = preg_replace('/\Ahttps?:\/\/[^\/]+/', '', route('debugbar.assets.css', [
            'v' => $this->getModifiedTime('css'),
        ]));

        $jsRoute = preg_replace('/\Ahttps?:\/\/[^\/]+/', '', route('debugbar.assets.js', [
            'v' => $this->getModifiedTime('js'),
        ]));

        $nonce = $this->getNonceAttribute();

        $html  = "<link rel='stylesheet' type='text/css' property='stylesheet' href='{$cssRoute}' data-turbolinks-eval='false' data-turbo-eval='false'>";
        $html .= "<script{$nonce} src='{$jsRoute}' data-turbolinks-eval='false' data-turbo-eval='false'></script>";

        $inlineHtml = $this->getInlineHtml();
        if ($nonce != '') {
            $inlineHtml = preg_replace("/<(script|style)>/", "<$1{$nonce}>", $inlineHtml);
        }
        $html .= $inlineHtml;


        return $html;
    }

    protected function getInlineHtml(): string
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
     */
    protected function getModifiedTime(string $type): int
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
     */
    public function dumpAssetsToString(string $type): string
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
     */
    protected function makeUriRelativeTo(string|array|null $uri, string $root): string|array
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

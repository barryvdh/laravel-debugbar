<?php

declare(strict_types=1);

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
        ];

        $this->setTheme(config('debugbar.theme', 'auto'));
    }

    /**
     * {@inheritdoc}
     */
    public function renderHead(): string
    {
        $asset = $this->getAssets();
        $cssRoute = preg_replace('/\Ahttps?:\/\/[^\/]+/', '', route('debugbar.assets.css', [
            'v' => $this->getModifiedTimes($asset['css']),
        ]));

        $jsRoute = preg_replace('/\Ahttps?:\/\/[^\/]+/', '', route('debugbar.assets.js', [
            'v' => $this->getModifiedTimes($asset['css']),
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

        $assets = $this->getAssets();
        foreach (['head', 'css', 'js'] as $asset) {
            foreach ($assets['inline_' . $asset] as $item) {
                $html .= $item . "\n";
            }
        }

        return $html;
    }

    /**
     * Return assets as a string
     *
     * @param 'js'|'css' $type
     */
    public function dumpAssetsToString(string $type): string
    {
        $files = $this->getAssets()[$type];

        return $this->dumpAssets($files, null, null, false);
    }
}

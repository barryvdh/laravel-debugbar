<template><div><h1 id="installation" tabindex="-1"><a class="header-anchor" href="#installation"><span>Installation</span></a></h1>
<h2 id="using-composer" tabindex="-1"><a class="header-anchor" href="#using-composer"><span>Using Composer</span></a></h2>
<div class="language-bash line-numbers-mode" data-highlighter="prismjs" data-ext="sh" data-title="sh"><pre v-pre><code><span class="line"><span class="token function">composer</span> require barryvdh/laravel-debugbar <span class="token parameter variable">--dev</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><h2 id="laravel-setup" tabindex="-1"><a class="header-anchor" href="#laravel-setup"><span>Laravel Setup</span></a></h2>
<p>The Debugbar ServiceProvider will be discovered and registered automatically.</p>
<p>If you want to manually register the service provider, add the following to your <code v-pre>config/app.php</code>:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token class-name class-name-fully-qualified static-context">Barryvdh<span class="token punctuation">\</span>Debugbar<span class="token punctuation">\</span>ServiceProvider</span><span class="token operator">::</span><span class="token keyword">class</span><span class="token punctuation">,</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><h3 id="facade-optional" tabindex="-1"><a class="header-anchor" href="#facade-optional"><span>Facade (Optional)</span></a></h3>
<p>You can register the facade in your <code v-pre>config/app.php</code>:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token string single-quoted-string">'Debugbar'</span> <span class="token operator">=></span> <span class="token class-name class-name-fully-qualified static-context">Barryvdh<span class="token punctuation">\</span>Debugbar<span class="token punctuation">\</span>Facades<span class="token punctuation">\</span>Debugbar</span><span class="token operator">::</span><span class="token keyword">class</span><span class="token punctuation">,</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><h2 id="configuration" tabindex="-1"><a class="header-anchor" href="#configuration"><span>Configuration</span></a></h2>
<p>To publish the config file to <code v-pre>config/debugbar.php</code>, run:</p>
<div class="language-bash line-numbers-mode" data-highlighter="prismjs" data-ext="sh" data-title="sh"><pre v-pre><code><span class="line">php artisan vendor:publish <span class="token parameter variable">--provider</span><span class="token operator">=</span><span class="token string">"Barryvdh\Debugbar\ServiceProvider"</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><div class="hint-container warning">
<p class="hint-container-title">Warning</p>
<p>Make sure you configure your environment properly. The debugbar adds overhead, so it should be disabled in production!</p>
</div>
</div></template>



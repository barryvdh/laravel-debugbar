<template><div><h1 id="frequently-asked-questions" tabindex="-1"><a class="header-anchor" href="#frequently-asked-questions"><span>Frequently Asked Questions</span></a></h1>
<h2 id="common-issues" tabindex="-1"><a class="header-anchor" href="#common-issues"><span>Common Issues</span></a></h2>
<h3 id="the-debugbar-is-not-showing-up" tabindex="-1"><a class="header-anchor" href="#the-debugbar-is-not-showing-up"><span>The debugbar is not showing up</span></a></h3>
<ol>
<li>Check if it's enabled in your environment:</li>
</ol>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token string single-quoted-string">'enabled'</span> <span class="token operator">=></span> <span class="token function">env</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'DEBUGBAR_ENABLED'</span><span class="token punctuation">,</span> <span class="token constant">null</span><span class="token punctuation">)</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><ol start="2">
<li>Verify your environment settings</li>
<li>Check if you're in production mode</li>
</ol>
<h3 id="performance-impact" tabindex="-1"><a class="header-anchor" href="#performance-impact"><span>Performance Impact</span></a></h3>
<p><strong>Q: Will the debugbar slow down my application?</strong></p>
<p>A: The debugbar does add some overhead. That's why it's recommended to:</p>
<ul>
<li>Disable it in production</li>
<li>Only enable needed collectors</li>
<li>Use the file storage driver for larger datasets</li>
</ul>
<h3 id="storage-issues" tabindex="-1"><a class="header-anchor" href="#storage-issues"><span>Storage Issues</span></a></h3>
<p><strong>Q: Where is the data stored?</strong></p>
<p>A: By default, debugbar data is stored in:</p>
<div class="language-text line-numbers-mode" data-highlighter="prismjs" data-ext="text" data-title="text"><pre v-pre><code><span class="line">storage/debugbar</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><p>You can change this in the configuration file.</p>
<h2 id="best-practices" tabindex="-1"><a class="header-anchor" href="#best-practices"><span>Best Practices</span></a></h2>
<ol>
<li>Always disable in production</li>
<li>Clear storage regularly</li>
<li>Only enable needed collectors</li>
<li>Use middleware to control access</li>
</ol>
<h2 id="getting-help" tabindex="-1"><a class="header-anchor" href="#getting-help"><span>Getting Help</span></a></h2>
<ul>
<li>Check the <a href="https://github.com/barryvdh/laravel-debugbar/issues" target="_blank" rel="noopener noreferrer">GitHub Issues</a></li>
<li>Join Laravel community channels</li>
<li>Review the source code</li>
</ul>
</div></template>



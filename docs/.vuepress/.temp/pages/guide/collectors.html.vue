<template><div><h1 id="collectors" tabindex="-1"><a class="header-anchor" href="#collectors"><span>Collectors</span></a></h1>
<h2 id="available-collectors" tabindex="-1"><a class="header-anchor" href="#available-collectors"><span>Available Collectors</span></a></h2>
<p>Laravel Debugbar comes with several collectors that gather different types of information:</p>
<h3 id="querycollector" tabindex="-1"><a class="header-anchor" href="#querycollector"><span>QueryCollector</span></a></h3>
<p>Logs database queries:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token variable">$debugbar</span><span class="token operator">-></span><span class="token function">addCollector</span><span class="token punctuation">(</span><span class="token keyword">new</span> <span class="token class-name">QueryCollector</span><span class="token punctuation">(</span><span class="token variable">$queries</span><span class="token punctuation">)</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><h3 id="timedatacollector" tabindex="-1"><a class="header-anchor" href="#timedatacollector"><span>TimeDataCollector</span></a></h3>
<p>Tracks time between points:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token variable">$debugbar</span><span class="token operator">-></span><span class="token function">addCollector</span><span class="token punctuation">(</span><span class="token keyword">new</span> <span class="token class-name">TimeDataCollector</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token variable">$debugbar</span><span class="token operator">-></span><span class="token function">startMeasure</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'render'</span><span class="token punctuation">,</span><span class="token string single-quoted-string">'Time for rendering'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token variable">$debugbar</span><span class="token operator">-></span><span class="token function">stopMeasure</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'render'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="exceptioncollector" tabindex="-1"><a class="header-anchor" href="#exceptioncollector"><span>ExceptionCollector</span></a></h3>
<p>Shows exception data:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token variable">$debugbar</span><span class="token operator">-></span><span class="token function">addCollector</span><span class="token punctuation">(</span><span class="token keyword">new</span> <span class="token class-name">ExceptionCollector</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><h2 id="custom-collectors" tabindex="-1"><a class="header-anchor" href="#custom-collectors"><span>Custom Collectors</span></a></h2>
<p>You can create your own collectors by implementing the <code v-pre>DebugBar\DataCollector\DataCollectorInterface</code>:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token keyword">use</span> <span class="token package">DebugBar<span class="token punctuation">\</span>DataCollector<span class="token punctuation">\</span>DataCollector</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token keyword">use</span> <span class="token package">DebugBar<span class="token punctuation">\</span>DataCollector<span class="token punctuation">\</span>Renderable</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line"><span class="token keyword">class</span> <span class="token class-name-definition class-name">MyCollector</span> <span class="token keyword">extends</span> <span class="token class-name">DataCollector</span> <span class="token keyword">implements</span> <span class="token class-name">Renderable</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">collect</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span>
<span class="line">    <span class="token punctuation">{</span></span>
<span class="line">        <span class="token keyword">return</span> <span class="token punctuation">[</span></span>
<span class="line">            <span class="token string single-quoted-string">'message'</span> <span class="token operator">=></span> <span class="token string single-quoted-string">'Custom data here'</span></span>
<span class="line">        <span class="token punctuation">]</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token punctuation">}</span></span>
<span class="line"></span>
<span class="line">    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">getName</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span>
<span class="line">    <span class="token punctuation">{</span></span>
<span class="line">        <span class="token keyword">return</span> <span class="token string single-quoted-string">'custom'</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token punctuation">}</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>Register your collector:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token variable">$debugbar</span><span class="token operator">-></span><span class="token function">addCollector</span><span class="token punctuation">(</span><span class="token keyword">new</span> <span class="token class-name">MyCollector</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div></div></template>



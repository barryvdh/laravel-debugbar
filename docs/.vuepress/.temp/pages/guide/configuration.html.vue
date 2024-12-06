<template><div><h1 id="configuration" tabindex="-1"><a class="header-anchor" href="#configuration"><span>Configuration</span></a></h1>
<h2 id="basic-configuration" tabindex="-1"><a class="header-anchor" href="#basic-configuration"><span>Basic Configuration</span></a></h2>
<p>The configuration file is located at <code v-pre>config/debugbar.php</code>. Here are the main options:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token keyword">return</span> <span class="token punctuation">[</span></span>
<span class="line">    <span class="token string single-quoted-string">'enabled'</span> <span class="token operator">=></span> <span class="token function">env</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'DEBUGBAR_ENABLED'</span><span class="token punctuation">,</span> <span class="token constant">null</span><span class="token punctuation">)</span><span class="token punctuation">,</span></span>
<span class="line">    <span class="token string single-quoted-string">'storage'</span> <span class="token operator">=></span> <span class="token punctuation">[</span></span>
<span class="line">        <span class="token string single-quoted-string">'enabled'</span>    <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span></span>
<span class="line">        <span class="token string single-quoted-string">'driver'</span>     <span class="token operator">=></span> <span class="token string single-quoted-string">'file'</span><span class="token punctuation">,</span></span>
<span class="line">        <span class="token string single-quoted-string">'path'</span>       <span class="token operator">=></span> <span class="token function">storage_path</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'debugbar'</span><span class="token punctuation">)</span><span class="token punctuation">,</span></span>
<span class="line">        <span class="token string single-quoted-string">'connection'</span> <span class="token operator">=></span> <span class="token constant">null</span><span class="token punctuation">,</span></span>
<span class="line">        <span class="token string single-quoted-string">'provider'</span>   <span class="token operator">=></span> <span class="token string single-quoted-string">''</span><span class="token punctuation">,</span></span>
<span class="line">    <span class="token punctuation">]</span><span class="token punctuation">,</span></span>
<span class="line"><span class="token punctuation">]</span><span class="token punctuation">;</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h2 id="environment-settings" tabindex="-1"><a class="header-anchor" href="#environment-settings"><span>Environment Settings</span></a></h2>
<h3 id="production-environment" tabindex="-1"><a class="header-anchor" href="#production-environment"><span>Production Environment</span></a></h3>
<p>In your production environment, you should set:</p>
<div class="language-env line-numbers-mode" data-highlighter="prismjs" data-ext="env" data-title="env"><pre v-pre><code><span class="line">DEBUGBAR_ENABLED=false</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><h3 id="development-environment" tabindex="-1"><a class="header-anchor" href="#development-environment"><span>Development Environment</span></a></h3>
<p>For local development:</p>
<div class="language-env line-numbers-mode" data-highlighter="prismjs" data-ext="env" data-title="env"><pre v-pre><code><span class="line">DEBUGBAR_ENABLED=true</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><h2 id="collectors-configuration" tabindex="-1"><a class="header-anchor" href="#collectors-configuration"><span>Collectors Configuration</span></a></h2>
<p>You can enable/disable specific collectors:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php" data-title="php"><pre v-pre><code><span class="line"><span class="token string single-quoted-string">'collectors'</span> <span class="token operator">=></span> <span class="token punctuation">[</span></span>
<span class="line">    <span class="token string single-quoted-string">'phpinfo'</span>         <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// PHP info</span></span>
<span class="line">    <span class="token string single-quoted-string">'messages'</span>        <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Messages</span></span>
<span class="line">    <span class="token string single-quoted-string">'time'</span>           <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Time Datalogger</span></span>
<span class="line">    <span class="token string single-quoted-string">'memory'</span>         <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Memory usage</span></span>
<span class="line">    <span class="token string single-quoted-string">'exceptions'</span>     <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Exception displayer</span></span>
<span class="line">    <span class="token string single-quoted-string">'log'</span>            <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Logs from Monolog</span></span>
<span class="line">    <span class="token string single-quoted-string">'db'</span>             <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Database operations</span></span>
<span class="line">    <span class="token string single-quoted-string">'views'</span>          <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Views with their data</span></span>
<span class="line">    <span class="token string single-quoted-string">'route'</span>          <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Current route information</span></span>
<span class="line">    <span class="token string single-quoted-string">'auth'</span>           <span class="token operator">=></span> <span class="token constant boolean">false</span><span class="token punctuation">,</span> <span class="token comment">// Display Laravel authentication status</span></span>
<span class="line">    <span class="token string single-quoted-string">'gate'</span>           <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Display Laravel Gate checks</span></span>
<span class="line">    <span class="token string single-quoted-string">'session'</span>        <span class="token operator">=></span> <span class="token constant boolean">true</span><span class="token punctuation">,</span>  <span class="token comment">// Display session data</span></span>
<span class="line"><span class="token punctuation">]</span><span class="token punctuation">,</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><div class="hint-container tip">
<p class="hint-container-title">Tips</p>
<p>You can customize the appearance and behavior of each collector through additional configuration options.</p>
</div>
</div></template>



export const redirects = JSON.parse("{}")

export const routes = Object.fromEntries([
  ["/", { loader: () => import(/* webpackChunkName: "index.html" */"/Users/barry/Sites/laravel-debugbar/docs/.vuepress/.temp/pages/index.html.js"), meta: {"title":"Laravel Debugbar"} }],
  ["/guide/", { loader: () => import(/* webpackChunkName: "guide_index.html" */"/Users/barry/Sites/laravel-debugbar/docs/.vuepress/.temp/pages/guide/index.html.js"), meta: {"title":"Introduction"} }],
  ["/guide/collectors.html", { loader: () => import(/* webpackChunkName: "guide_collectors.html" */"/Users/barry/Sites/laravel-debugbar/docs/.vuepress/.temp/pages/guide/collectors.html.js"), meta: {"title":"Collectors"} }],
  ["/guide/configuration.html", { loader: () => import(/* webpackChunkName: "guide_configuration.html" */"/Users/barry/Sites/laravel-debugbar/docs/.vuepress/.temp/pages/guide/configuration.html.js"), meta: {"title":"Configuration"} }],
  ["/guide/faq.html", { loader: () => import(/* webpackChunkName: "guide_faq.html" */"/Users/barry/Sites/laravel-debugbar/docs/.vuepress/.temp/pages/guide/faq.html.js"), meta: {"title":"Frequently Asked Questions"} }],
  ["/guide/installation.html", { loader: () => import(/* webpackChunkName: "guide_installation.html" */"/Users/barry/Sites/laravel-debugbar/docs/.vuepress/.temp/pages/guide/installation.html.js"), meta: {"title":"Installation"} }],
  ["/404.html", { loader: () => import(/* webpackChunkName: "404.html" */"/Users/barry/Sites/laravel-debugbar/docs/.vuepress/.temp/pages/404.html.js"), meta: {"title":""} }],
]);

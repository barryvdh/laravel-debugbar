/*! jQuery v3.7.1 | (c) OpenJS Foundation and other contributors | jquery.org/license */
!function(e,t){"use strict";"object"==typeof module&&"object"==typeof module.exports?module.exports=e.document?t(e,!0):function(e){if(!e.document)throw new Error("jQuery requires a window with a document");return t(e)}:t(e)}("undefined"!=typeof window?window:this,function(ie,e){"use strict";var oe=[],r=Object.getPrototypeOf,ae=oe.slice,g=oe.flat?function(e){return oe.flat.call(e)}:function(e){return oe.concat.apply([],e)},s=oe.push,se=oe.indexOf,n={},i=n.toString,ue=n.hasOwnProperty,o=ue.toString,a=o.call(Object),le={},v=function(e){return"function"==typeof e&&"number"!=typeof e.nodeType&&"function"!=typeof e.item},y=function(e){return null!=e&&e===e.window},C=ie.document,u={type:!0,src:!0,nonce:!0,noModule:!0};function m(e,t,n){var r,i,o=(n=n||C).createElement("script");if(o.text=e,t)for(r in u)(i=t[r]||t.getAttribute&&t.getAttribute(r))&&o.setAttribute(r,i);n.head.appendChild(o).parentNode.removeChild(o)}function x(e){return null==e?e+"":"object"==typeof e||"function"==typeof e?n[i.call(e)]||"object":typeof e}var t="3.7.1-pre",l=/HTML$/i,ce=function(e,t){return new ce.fn.init(e,t)};function c(e){var t=!!e&&"length"in e&&e.length,n=x(e);return!v(e)&&!y(e)&&("array"===n||0===t||"number"==typeof t&&0<t&&t-1 in e)}function fe(e,t){return e.nodeName&&e.nodeName.toLowerCase()===t.toLowerCase()}ce.fn=ce.prototype={jquery:t,constructor:ce,length:0,toArray:function(){return ae.call(this)},get:function(e){return null==e?ae.call(this):e<0?this[e+this.length]:this[e]},pushStack:function(e){var t=ce.merge(this.constructor(),e);return t.prevObject=this,t},each:function(e){return ce.each(this,e)},map:function(n){return this.pushStack(ce.map(this,function(e,t){return n.call(e,t,e)}))},slice:function(){return this.pushStack(ae.apply(this,arguments))},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},even:function(){return this.pushStack(ce.grep(this,function(e,t){return(t+1)%2}))},odd:function(){return this.pushStack(ce.grep(this,function(e,t){return t%2}))},eq:function(e){var t=this.length,n=+e+(e<0?t:0);return this.pushStack(0<=n&&n<t?[this[n]]:[])},end:function(){return this.prevObject||this.constructor()},push:s,sort:oe.sort,splice:oe.splice},ce.extend=ce.fn.extend=function(){var e,t,n,r,i,o,a=arguments[0]||{},s=1,u=arguments.length,l=!1;for("boolean"==typeof a&&(l=a,a=arguments[s]||{},s++),"object"==typeof a||v(a)||(a={}),s===u&&(a=this,s--);s<u;s++)if(null!=(e=arguments[s]))for(t in e)r=e[t],"__proto__"!==t&&a!==r&&(l&&r&&(ce.isPlainObject(r)||(i=Array.isArray(r)))?(n=a[t],o=i&&!Array.isArray(n)?[]:i||ce.isPlainObject(n)?n:{},i=!1,a[t]=ce.extend(l,o,r)):void 0!==r&&(a[t]=r));return a},ce.extend({expando:"jQuery"+(t+Math.random()).replace(/\D/g,""),isReady:!0,error:function(e){throw new Error(e)},noop:function(){},isPlainObject:function(e){var t,n;return!(!e||"[object Object]"!==i.call(e))&&(!(t=r(e))||"function"==typeof(n=ue.call(t,"constructor")&&t.constructor)&&o.call(n)===a)},isEmptyObject:function(e){var t;for(t in e)return!1;return!0},globalEval:function(e,t,n){m(e,{nonce:t&&t.nonce},n)},each:function(e,t){var n,r=0;if(c(e)){for(n=e.length;r<n;r++)if(!1===t.call(e[r],r,e[r]))break}else for(r in e)if(!1===t.call(e[r],r,e[r]))break;return e},text:function(e){var t,n="",r=0,i=e.nodeType;if(!i)while(t=e[r++])n+=ce.text(t);return 1===i||11===i?e.textContent:9===i?e.documentElement.textContent:3===i||4===i?e.nodeValue:n},makeArray:function(e,t){var n=t||[];return null!=e&&(c(Object(e))?ce.merge(n,"string"==typeof e?[e]:e):s.call(n,e)),n},inArray:function(e,t,n){return null==t?-1:se.call(t,e,n)},isXMLDoc:function(e){var t=e&&e.namespaceURI,n=e&&(e.ownerDocument||e).documentElement;return!l.test(t||n&&n.nodeName||"HTML")},merge:function(e,t){for(var n=+t.length,r=0,i=e.length;r<n;r++)e[i++]=t[r];return e.length=i,e},grep:function(e,t,n){for(var r=[],i=0,o=e.length,a=!n;i<o;i++)!t(e[i],i)!==a&&r.push(e[i]);return r},map:function(e,t,n){var r,i,o=0,a=[];if(c(e))for(r=e.length;o<r;o++)null!=(i=t(e[o],o,n))&&a.push(i);else for(o in e)null!=(i=t(e[o],o,n))&&a.push(i);return g(a)},guid:1,support:le}),"function"==typeof Symbol&&(ce.fn[Symbol.iterator]=oe[Symbol.iterator]),ce.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "),function(e,t){n["[object "+t+"]"]=t.toLowerCase()});var pe=oe.pop,de=oe.sort,he=oe.splice,ge="[\\x20\\t\\r\\n\\f]",ve=new RegExp("^"+ge+"+|((?:^|[^\\\\])(?:\\\\.)*)"+ge+"+$","g");ce.contains=function(e,t){var n=t&&t.parentNode;return e===n||!(!n||1!==n.nodeType||!(e.contains?e.contains(n):e.compareDocumentPosition&&16&e.compareDocumentPosition(n)))};var f=/([\0-\x1f\x7f]|^-?\d)|^-$|[^\x80-\uFFFF\w-]/g;function p(e,t){return t?"\0"===e?"\ufffd":e.slice(0,-1)+"\\"+e.charCodeAt(e.length-1).toString(16)+" ":"\\"+e}ce.escapeSelector=function(e){return(e+"").replace(f,p)};var ye=C,me=s;!function(){var e,b,w,o,a,T,r,C,d,i,k=me,S=ce.expando,E=0,n=0,s=W(),c=W(),u=W(),h=W(),l=function(e,t){return e===t&&(a=!0),0},f="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",t="(?:\\\\[\\da-fA-F]{1,6}"+ge+"?|\\\\[^\\r\\n\\f]|[\\w-]|[^\0-\\x7f])+",p="\\["+ge+"*("+t+")(?:"+ge+"*([*^$|!~]?=)"+ge+"*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|("+t+"))|)"+ge+"*\\]",g=":("+t+")(?:\\((('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|((?:\\\\.|[^\\\\()[\\]]|"+p+")*)|.*)\\)|)",v=new RegExp(ge+"+","g"),y=new RegExp("^"+ge+"*,"+ge+"*"),m=new RegExp("^"+ge+"*([>+~]|"+ge+")"+ge+"*"),x=new RegExp(ge+"|>"),j=new RegExp(g),A=new RegExp("^"+t+"$"),D={ID:new RegExp("^#("+t+")"),CLASS:new RegExp("^\\.("+t+")"),TAG:new RegExp("^("+t+"|[*])"),ATTR:new RegExp("^"+p),PSEUDO:new RegExp("^"+g),CHILD:new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+ge+"*(even|odd|(([+-]|)(\\d*)n|)"+ge+"*(?:([+-]|)"+ge+"*(\\d+)|))"+ge+"*\\)|)","i"),bool:new RegExp("^(?:"+f+")$","i"),needsContext:new RegExp("^"+ge+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+ge+"*((?:-\\d)?\\d*)"+ge+"*\\)|)(?=[^-]|$)","i")},N=/^(?:input|select|textarea|button)$/i,q=/^h\d$/i,L=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,H=/[+~]/,O=new RegExp("\\\\[\\da-fA-F]{1,6}"+ge+"?|\\\\([^\\r\\n\\f])","g"),P=function(e,t){var n="0x"+e.slice(1)-65536;return t||(n<0?String.fromCharCode(n+65536):String.fromCharCode(n>>10|55296,1023&n|56320))},M=function(){V()},R=J(function(e){return!0===e.disabled&&fe(e,"fieldset")},{dir:"parentNode",next:"legend"});try{k.apply(oe=ae.call(ye.childNodes),ye.childNodes),oe[ye.childNodes.length].nodeType}catch(e){k={apply:function(e,t){me.apply(e,ae.call(t))},call:function(e){me.apply(e,ae.call(arguments,1))}}}function I(t,e,n,r){var i,o,a,s,u,l,c,f=e&&e.ownerDocument,p=e?e.nodeType:9;if(n=n||[],"string"!=typeof t||!t||1!==p&&9!==p&&11!==p)return n;if(!r&&(V(e),e=e||T,C)){if(11!==p&&(u=L.exec(t)))if(i=u[1]){if(9===p){if(!(a=e.getElementById(i)))return n;if(a.id===i)return k.call(n,a),n}else if(f&&(a=f.getElementById(i))&&I.contains(e,a)&&a.id===i)return k.call(n,a),n}else{if(u[2])return k.apply(n,e.getElementsByTagName(t)),n;if((i=u[3])&&e.getElementsByClassName)return k.apply(n,e.getElementsByClassName(i)),n}if(!(h[t+" "]||d&&d.test(t))){if(c=t,f=e,1===p&&(x.test(t)||m.test(t))){(f=H.test(t)&&U(e.parentNode)||e)==e&&le.scope||((s=e.getAttribute("id"))?s=ce.escapeSelector(s):e.setAttribute("id",s=S)),o=(l=Y(t)).length;while(o--)l[o]=(s?"#"+s:":scope")+" "+Q(l[o]);c=l.join(",")}try{return k.apply(n,f.querySelectorAll(c)),n}catch(e){h(t,!0)}finally{s===S&&e.removeAttribute("id")}}}return re(t.replace(ve,"$1"),e,n,r)}function W(){var r=[];return function e(t,n){return r.push(t+" ")>b.cacheLength&&delete e[r.shift()],e[t+" "]=n}}function F(e){return e[S]=!0,e}function $(e){var t=T.createElement("fieldset");try{return!!e(t)}catch(e){return!1}finally{t.parentNode&&t.parentNode.removeChild(t),t=null}}function B(t){return function(e){return fe(e,"input")&&e.type===t}}function _(t){return function(e){return(fe(e,"input")||fe(e,"button"))&&e.type===t}}function z(t){return function(e){return"form"in e?e.parentNode&&!1===e.disabled?"label"in e?"label"in e.parentNode?e.parentNode.disabled===t:e.disabled===t:e.isDisabled===t||e.isDisabled!==!t&&R(e)===t:e.disabled===t:"label"in e&&e.disabled===t}}function X(a){return F(function(o){return o=+o,F(function(e,t){var n,r=a([],e.length,o),i=r.length;while(i--)e[n=r[i]]&&(e[n]=!(t[n]=e[n]))})})}function U(e){return e&&"undefined"!=typeof e.getElementsByTagName&&e}function V(e){var t,n=e?e.ownerDocument||e:ye;return n!=T&&9===n.nodeType&&n.documentElement&&(r=(T=n).documentElement,C=!ce.isXMLDoc(T),i=r.matches||r.webkitMatchesSelector||r.msMatchesSelector,r.msMatchesSelector&&ye!=T&&(t=T.defaultView)&&t.top!==t&&t.addEventListener("unload",M),le.getById=$(function(e){return r.appendChild(e).id=ce.expando,!T.getElementsByName||!T.getElementsByName(ce.expando).length}),le.disconnectedMatch=$(function(e){return i.call(e,"*")}),le.scope=$(function(){return T.querySelectorAll(":scope")}),le.cssHas=$(function(){try{return T.querySelector(":has(*,:jqfake)"),!1}catch(e){return!0}}),le.getById?(b.filter.ID=function(e){var t=e.replace(O,P);return function(e){return e.getAttribute("id")===t}},b.find.ID=function(e,t){if("undefined"!=typeof t.getElementById&&C){var n=t.getElementById(e);return n?[n]:[]}}):(b.filter.ID=function(e){var n=e.replace(O,P);return function(e){var t="undefined"!=typeof e.getAttributeNode&&e.getAttributeNode("id");return t&&t.value===n}},b.find.ID=function(e,t){if("undefined"!=typeof t.getElementById&&C){var n,r,i,o=t.getElementById(e);if(o){if((n=o.getAttributeNode("id"))&&n.value===e)return[o];i=t.getElementsByName(e),r=0;while(o=i[r++])if((n=o.getAttributeNode("id"))&&n.value===e)return[o]}return[]}}),b.find.TAG=function(e,t){return"undefined"!=typeof t.getElementsByTagName?t.getElementsByTagName(e):t.querySelectorAll(e)},b.find.CLASS=function(e,t){if("undefined"!=typeof t.getElementsByClassName&&C)return t.getElementsByClassName(e)},d=[],$(function(e){var t;r.appendChild(e).innerHTML="<a id='"+S+"' href='' disabled='disabled'></a><select id='"+S+"-\r\\' disabled='disabled'><option selected=''></option></select>",e.querySelectorAll("[selected]").length||d.push("\\["+ge+"*(?:value|"+f+")"),e.querySelectorAll("[id~="+S+"-]").length||d.push("~="),e.querySelectorAll("a#"+S+"+*").length||d.push(".#.+[+~]"),e.querySelectorAll(":checked").length||d.push(":checked"),(t=T.createElement("input")).setAttribute("type","hidden"),e.appendChild(t).setAttribute("name","D"),r.appendChild(e).disabled=!0,2!==e.querySelectorAll(":disabled").length&&d.push(":enabled",":disabled"),(t=T.createElement("input")).setAttribute("name",""),e.appendChild(t),e.querySelectorAll("[name='']").length||d.push("\\["+ge+"*name"+ge+"*="+ge+"*(?:''|\"\")")}),le.cssHas||d.push(":has"),d=d.length&&new RegExp(d.join("|")),l=function(e,t){if(e===t)return a=!0,0;var n=!e.compareDocumentPosition-!t.compareDocumentPosition;return n||(1&(n=(e.ownerDocument||e)==(t.ownerDocument||t)?e.compareDocumentPosition(t):1)||!le.sortDetached&&t.compareDocumentPosition(e)===n?e===T||e.ownerDocument==ye&&I.contains(ye,e)?-1:t===T||t.ownerDocument==ye&&I.contains(ye,t)?1:o?se.call(o,e)-se.call(o,t):0:4&n?-1:1)}),T}for(e in I.matches=function(e,t){return I(e,null,null,t)},I.matchesSelector=function(e,t){if(V(e),C&&!h[t+" "]&&(!d||!d.test(t)))try{var n=i.call(e,t);if(n||le.disconnectedMatch||e.document&&11!==e.document.nodeType)return n}catch(e){h(t,!0)}return 0<I(t,T,null,[e]).length},I.contains=function(e,t){return(e.ownerDocument||e)!=T&&V(e),ce.contains(e,t)},I.attr=function(e,t){(e.ownerDocument||e)!=T&&V(e);var n=b.attrHandle[t.toLowerCase()],r=n&&ue.call(b.attrHandle,t.toLowerCase())?n(e,t,!C):void 0;return void 0!==r?r:e.getAttribute(t)},I.error=function(e){throw new Error("Syntax error, unrecognized expression: "+e)},ce.uniqueSort=function(e){var t,n=[],r=0,i=0;if(a=!le.sortStable,o=!le.sortStable&&ae.call(e,0),de.call(e,l),a){while(t=e[i++])t===e[i]&&(r=n.push(i));while(r--)he.call(e,n[r],1)}return o=null,e},ce.fn.uniqueSort=function(){return this.pushStack(ce.uniqueSort(ae.apply(this)))},(b=ce.expr={cacheLength:50,createPseudo:F,match:D,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:!0}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:!0},"~":{dir:"previousSibling"}},preFilter:{ATTR:function(e){return e[1]=e[1].replace(O,P),e[3]=(e[3]||e[4]||e[5]||"").replace(O,P),"~="===e[2]&&(e[3]=" "+e[3]+" "),e.slice(0,4)},CHILD:function(e){return e[1]=e[1].toLowerCase(),"nth"===e[1].slice(0,3)?(e[3]||I.error(e[0]),e[4]=+(e[4]?e[5]+(e[6]||1):2*("even"===e[3]||"odd"===e[3])),e[5]=+(e[7]+e[8]||"odd"===e[3])):e[3]&&I.error(e[0]),e},PSEUDO:function(e){var t,n=!e[6]&&e[2];return D.CHILD.test(e[0])?null:(e[3]?e[2]=e[4]||e[5]||"":n&&j.test(n)&&(t=Y(n,!0))&&(t=n.indexOf(")",n.length-t)-n.length)&&(e[0]=e[0].slice(0,t),e[2]=n.slice(0,t)),e.slice(0,3))}},filter:{TAG:function(e){var t=e.replace(O,P).toLowerCase();return"*"===e?function(){return!0}:function(e){return fe(e,t)}},CLASS:function(e){var t=s[e+" "];return t||(t=new RegExp("(^|"+ge+")"+e+"("+ge+"|$)"))&&s(e,function(e){return t.test("string"==typeof e.className&&e.className||"undefined"!=typeof e.getAttribute&&e.getAttribute("class")||"")})},ATTR:function(n,r,i){return function(e){var t=I.attr(e,n);return null==t?"!="===r:!r||(t+="","="===r?t===i:"!="===r?t!==i:"^="===r?i&&0===t.indexOf(i):"*="===r?i&&-1<t.indexOf(i):"$="===r?i&&t.slice(-i.length)===i:"~="===r?-1<(" "+t.replace(v," ")+" ").indexOf(i):"|="===r&&(t===i||t.slice(0,i.length+1)===i+"-"))}},CHILD:function(d,e,t,h,g){var v="nth"!==d.slice(0,3),y="last"!==d.slice(-4),m="of-type"===e;return 1===h&&0===g?function(e){return!!e.parentNode}:function(e,t,n){var r,i,o,a,s,u=v!==y?"nextSibling":"previousSibling",l=e.parentNode,c=m&&e.nodeName.toLowerCase(),f=!n&&!m,p=!1;if(l){if(v){while(u){o=e;while(o=o[u])if(m?fe(o,c):1===o.nodeType)return!1;s=u="only"===d&&!s&&"nextSibling"}return!0}if(s=[y?l.firstChild:l.lastChild],y&&f){p=(a=(r=(i=l[S]||(l[S]={}))[d]||[])[0]===E&&r[1])&&r[2],o=a&&l.childNodes[a];while(o=++a&&o&&o[u]||(p=a=0)||s.pop())if(1===o.nodeType&&++p&&o===e){i[d]=[E,a,p];break}}else if(f&&(p=a=(r=(i=e[S]||(e[S]={}))[d]||[])[0]===E&&r[1]),!1===p)while(o=++a&&o&&o[u]||(p=a=0)||s.pop())if((m?fe(o,c):1===o.nodeType)&&++p&&(f&&((i=o[S]||(o[S]={}))[d]=[E,p]),o===e))break;return(p-=g)===h||p%h==0&&0<=p/h}}},PSEUDO:function(e,o){var t,a=b.pseudos[e]||b.setFilters[e.toLowerCase()]||I.error("unsupported pseudo: "+e);return a[S]?a(o):1<a.length?(t=[e,e,"",o],b.setFilters.hasOwnProperty(e.toLowerCase())?F(function(e,t){var n,r=a(e,o),i=r.length;while(i--)e[n=se.call(e,r[i])]=!(t[n]=r[i])}):function(e){return a(e,0,t)}):a}},pseudos:{not:F(function(e){var r=[],i=[],s=ne(e.replace(ve,"$1"));return s[S]?F(function(e,t,n,r){var i,o=s(e,null,r,[]),a=e.length;while(a--)(i=o[a])&&(e[a]=!(t[a]=i))}):function(e,t,n){return r[0]=e,s(r,null,n,i),r[0]=null,!i.pop()}}),has:F(function(t){return function(e){return 0<I(t,e).length}}),contains:F(function(t){return t=t.replace(O,P),function(e){return-1<(e.textContent||ce.text(e)).indexOf(t)}}),lang:F(function(n){return A.test(n||"")||I.error("unsupported lang: "+n),n=n.replace(O,P).toLowerCase(),function(e){var t;do{if(t=C?e.lang:e.getAttribute("xml:lang")||e.getAttribute("lang"))return(t=t.toLowerCase())===n||0===t.indexOf(n+"-")}while((e=e.parentNode)&&1===e.nodeType);return!1}}),target:function(e){var t=ie.location&&ie.location.hash;return t&&t.slice(1)===e.id},root:function(e){return e===r},focus:function(e){return e===function(){try{return T.activeElement}catch(e){}}()&&T.hasFocus()&&!!(e.type||e.href||~e.tabIndex)},enabled:z(!1),disabled:z(!0),checked:function(e){return fe(e,"input")&&!!e.checked||fe(e,"option")&&!!e.selected},selected:function(e){return e.parentNode&&e.parentNode.selectedIndex,!0===e.selected},empty:function(e){for(e=e.firstChild;e;e=e.nextSibling)if(e.nodeType<6)return!1;return!0},parent:function(e){return!b.pseudos.empty(e)},header:function(e){return q.test(e.nodeName)},input:function(e){return N.test(e.nodeName)},button:function(e){return fe(e,"input")&&"button"===e.type||fe(e,"button")},text:function(e){var t;return fe(e,"input")&&"text"===e.type&&(null==(t=e.getAttribute("type"))||"text"===t.toLowerCase())},first:X(function(){return[0]}),last:X(function(e,t){return[t-1]}),eq:X(function(e,t,n){return[n<0?n+t:n]}),even:X(function(e,t){for(var n=0;n<t;n+=2)e.push(n);return e}),odd:X(function(e,t){for(var n=1;n<t;n+=2)e.push(n);return e}),lt:X(function(e,t,n){var r;for(r=n<0?n+t:t<n?t:n;0<=--r;)e.push(r);return e}),gt:X(function(e,t,n){for(var r=n<0?n+t:n;++r<t;)e.push(r);return e})}}).pseudos.nth=b.pseudos.eq,{radio:!0,checkbox:!0,file:!0,password:!0,image:!0})b.pseudos[e]=B(e);for(e in{submit:!0,reset:!0})b.pseudos[e]=_(e);function G(){}function Y(e,t){var n,r,i,o,a,s,u,l=c[e+" "];if(l)return t?0:l.slice(0);a=e,s=[],u=b.preFilter;while(a){for(o in n&&!(r=y.exec(a))||(r&&(a=a.slice(r[0].length)||a),s.push(i=[])),n=!1,(r=m.exec(a))&&(n=r.shift(),i.push({value:n,type:r[0].replace(ve," ")}),a=a.slice(n.length)),b.filter)!(r=D[o].exec(a))||u[o]&&!(r=u[o](r))||(n=r.shift(),i.push({value:n,type:o,matches:r}),a=a.slice(n.length));if(!n)break}return t?a.length:a?I.error(e):c(e,s).slice(0)}function Q(e){for(var t=0,n=e.length,r="";t<n;t++)r+=e[t].value;return r}function J(a,e,t){var s=e.dir,u=e.next,l=u||s,c=t&&"parentNode"===l,f=n++;return e.first?function(e,t,n){while(e=e[s])if(1===e.nodeType||c)return a(e,t,n);return!1}:function(e,t,n){var r,i,o=[E,f];if(n){while(e=e[s])if((1===e.nodeType||c)&&a(e,t,n))return!0}else while(e=e[s])if(1===e.nodeType||c)if(i=e[S]||(e[S]={}),u&&fe(e,u))e=e[s]||e;else{if((r=i[l])&&r[0]===E&&r[1]===f)return o[2]=r[2];if((i[l]=o)[2]=a(e,t,n))return!0}return!1}}function K(i){return 1<i.length?function(e,t,n){var r=i.length;while(r--)if(!i[r](e,t,n))return!1;return!0}:i[0]}function Z(e,t,n,r,i){for(var o,a=[],s=0,u=e.length,l=null!=t;s<u;s++)(o=e[s])&&(n&&!n(o,r,i)||(a.push(o),l&&t.push(s)));return a}function ee(d,h,g,v,y,e){return v&&!v[S]&&(v=ee(v)),y&&!y[S]&&(y=ee(y,e)),F(function(e,t,n,r){var i,o,a,s,u=[],l=[],c=t.length,f=e||function(e,t,n){for(var r=0,i=t.length;r<i;r++)I(e,t[r],n);return n}(h||"*",n.nodeType?[n]:n,[]),p=!d||!e&&h?f:Z(f,u,d,n,r);if(g?g(p,s=y||(e?d:c||v)?[]:t,n,r):s=p,v){i=Z(s,l),v(i,[],n,r),o=i.length;while(o--)(a=i[o])&&(s[l[o]]=!(p[l[o]]=a))}if(e){if(y||d){if(y){i=[],o=s.length;while(o--)(a=s[o])&&i.push(p[o]=a);y(null,s=[],i,r)}o=s.length;while(o--)(a=s[o])&&-1<(i=y?se.call(e,a):u[o])&&(e[i]=!(t[i]=a))}}else s=Z(s===t?s.splice(c,s.length):s),y?y(null,t,s,r):k.apply(t,s)})}function te(e){for(var i,t,n,r=e.length,o=b.relative[e[0].type],a=o||b.relative[" "],s=o?1:0,u=J(function(e){return e===i},a,!0),l=J(function(e){return-1<se.call(i,e)},a,!0),c=[function(e,t,n){var r=!o&&(n||t!=w)||((i=t).nodeType?u(e,t,n):l(e,t,n));return i=null,r}];s<r;s++)if(t=b.relative[e[s].type])c=[J(K(c),t)];else{if((t=b.filter[e[s].type].apply(null,e[s].matches))[S]){for(n=++s;n<r;n++)if(b.relative[e[n].type])break;return ee(1<s&&K(c),1<s&&Q(e.slice(0,s-1).concat({value:" "===e[s-2].type?"*":""})).replace(ve,"$1"),t,s<n&&te(e.slice(s,n)),n<r&&te(e=e.slice(n)),n<r&&Q(e))}c.push(t)}return K(c)}function ne(e,t){var n,v,y,m,x,r,i=[],o=[],a=u[e+" "];if(!a){t||(t=Y(e)),n=t.length;while(n--)(a=te(t[n]))[S]?i.push(a):o.push(a);(a=u(e,(v=o,m=0<(y=i).length,x=0<v.length,r=function(e,t,n,r,i){var o,a,s,u=0,l="0",c=e&&[],f=[],p=w,d=e||x&&b.find.TAG("*",i),h=E+=null==p?1:Math.random()||.1,g=d.length;for(i&&(w=t==T||t||i);l!==g&&null!=(o=d[l]);l++){if(x&&o){a=0,t||o.ownerDocument==T||(V(o),n=!C);while(s=v[a++])if(s(o,t||T,n)){k.call(r,o);break}i&&(E=h)}m&&((o=!s&&o)&&u--,e&&c.push(o))}if(u+=l,m&&l!==u){a=0;while(s=y[a++])s(c,f,t,n);if(e){if(0<u)while(l--)c[l]||f[l]||(f[l]=pe.call(r));f=Z(f)}k.apply(r,f),i&&!e&&0<f.length&&1<u+y.length&&ce.uniqueSort(r)}return i&&(E=h,w=p),c},m?F(r):r))).selector=e}return a}function re(e,t,n,r){var i,o,a,s,u,l="function"==typeof e&&e,c=!r&&Y(e=l.selector||e);if(n=n||[],1===c.length){if(2<(o=c[0]=c[0].slice(0)).length&&"ID"===(a=o[0]).type&&9===t.nodeType&&C&&b.relative[o[1].type]){if(!(t=(b.find.ID(a.matches[0].replace(O,P),t)||[])[0]))return n;l&&(t=t.parentNode),e=e.slice(o.shift().value.length)}i=D.needsContext.test(e)?0:o.length;while(i--){if(a=o[i],b.relative[s=a.type])break;if((u=b.find[s])&&(r=u(a.matches[0].replace(O,P),H.test(o[0].type)&&U(t.parentNode)||t))){if(o.splice(i,1),!(e=r.length&&Q(o)))return k.apply(n,r),n;break}}}return(l||ne(e,c))(r,t,!C,n,!t||H.test(e)&&U(t.parentNode)||t),n}G.prototype=b.filters=b.pseudos,b.setFilters=new G,le.sortStable=S.split("").sort(l).join("")===S,V(),le.sortDetached=$(function(e){return 1&e.compareDocumentPosition(T.createElement("fieldset"))}),ce.find=I,ce.expr[":"]=ce.expr.pseudos,ce.unique=ce.uniqueSort,I.compile=ne,I.select=re,I.setDocument=V,I.tokenize=Y,I.escape=ce.escapeSelector,I.getText=ce.text,I.isXML=ce.isXMLDoc,I.selectors=ce.expr,I.support=ce.support,I.uniqueSort=ce.uniqueSort}();var d=function(e,t,n){var r=[],i=void 0!==n;while((e=e[t])&&9!==e.nodeType)if(1===e.nodeType){if(i&&ce(e).is(n))break;r.push(e)}return r},h=function(e,t){for(var n=[];e;e=e.nextSibling)1===e.nodeType&&e!==t&&n.push(e);return n},b=ce.expr.match.needsContext,w=/^<([a-z][^\/\0>:\x20\t\r\n\f]*)[\x20\t\r\n\f]*\/?>(?:<\/\1>|)$/i;function T(e,n,r){return v(n)?ce.grep(e,function(e,t){return!!n.call(e,t,e)!==r}):n.nodeType?ce.grep(e,function(e){return e===n!==r}):"string"!=typeof n?ce.grep(e,function(e){return-1<se.call(n,e)!==r}):ce.filter(n,e,r)}ce.filter=function(e,t,n){var r=t[0];return n&&(e=":not("+e+")"),1===t.length&&1===r.nodeType?ce.find.matchesSelector(r,e)?[r]:[]:ce.find.matches(e,ce.grep(t,function(e){return 1===e.nodeType}))},ce.fn.extend({find:function(e){var t,n,r=this.length,i=this;if("string"!=typeof e)return this.pushStack(ce(e).filter(function(){for(t=0;t<r;t++)if(ce.contains(i[t],this))return!0}));for(n=this.pushStack([]),t=0;t<r;t++)ce.find(e,i[t],n);return 1<r?ce.uniqueSort(n):n},filter:function(e){return this.pushStack(T(this,e||[],!1))},not:function(e){return this.pushStack(T(this,e||[],!0))},is:function(e){return!!T(this,"string"==typeof e&&b.test(e)?ce(e):e||[],!1).length}});var k,S=/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]+))$/;(ce.fn.init=function(e,t,n){var r,i;if(!e)return this;if(n=n||k,"string"==typeof e){if(!(r="<"===e[0]&&">"===e[e.length-1]&&3<=e.length?[null,e,null]:S.exec(e))||!r[1]&&t)return!t||t.jquery?(t||n).find(e):this.constructor(t).find(e);if(r[1]){if(t=t instanceof ce?t[0]:t,ce.merge(this,ce.parseHTML(r[1],t&&t.nodeType?t.ownerDocument||t:C,!0)),w.test(r[1])&&ce.isPlainObject(t))for(r in t)v(this[r])?this[r](t[r]):this.attr(r,t[r]);return this}return(i=C.getElementById(r[2]))&&(this[0]=i,this.length=1),this}return e.nodeType?(this[0]=e,this.length=1,this):v(e)?void 0!==n.ready?n.ready(e):e(ce):ce.makeArray(e,this)}).prototype=ce.fn,k=ce(C);var E=/^(?:parents|prev(?:Until|All))/,j={children:!0,contents:!0,next:!0,prev:!0};function A(e,t){while((e=e[t])&&1!==e.nodeType);return e}ce.fn.extend({has:function(e){var t=ce(e,this),n=t.length;return this.filter(function(){for(var e=0;e<n;e++)if(ce.contains(this,t[e]))return!0})},closest:function(e,t){var n,r=0,i=this.length,o=[],a="string"!=typeof e&&ce(e);if(!b.test(e))for(;r<i;r++)for(n=this[r];n&&n!==t;n=n.parentNode)if(n.nodeType<11&&(a?-1<a.index(n):1===n.nodeType&&ce.find.matchesSelector(n,e))){o.push(n);break}return this.pushStack(1<o.length?ce.uniqueSort(o):o)},index:function(e){return e?"string"==typeof e?se.call(ce(e),this[0]):se.call(this,e.jquery?e[0]:e):this[0]&&this[0].parentNode?this.first().prevAll().length:-1},add:function(e,t){return this.pushStack(ce.uniqueSort(ce.merge(this.get(),ce(e,t))))},addBack:function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}}),ce.each({parent:function(e){var t=e.parentNode;return t&&11!==t.nodeType?t:null},parents:function(e){return d(e,"parentNode")},parentsUntil:function(e,t,n){return d(e,"parentNode",n)},next:function(e){return A(e,"nextSibling")},prev:function(e){return A(e,"previousSibling")},nextAll:function(e){return d(e,"nextSibling")},prevAll:function(e){return d(e,"previousSibling")},nextUntil:function(e,t,n){return d(e,"nextSibling",n)},prevUntil:function(e,t,n){return d(e,"previousSibling",n)},siblings:function(e){return h((e.parentNode||{}).firstChild,e)},children:function(e){return h(e.firstChild)},contents:function(e){return null!=e.contentDocument&&r(e.contentDocument)?e.contentDocument:(fe(e,"template")&&(e=e.content||e),ce.merge([],e.childNodes))}},function(r,i){ce.fn[r]=function(e,t){var n=ce.map(this,i,e);return"Until"!==r.slice(-5)&&(t=e),t&&"string"==typeof t&&(n=ce.filter(t,n)),1<this.length&&(j[r]||ce.uniqueSort(n),E.test(r)&&n.reverse()),this.pushStack(n)}});var D=/[^\x20\t\r\n\f]+/g;function N(e){return e}function q(e){throw e}function L(e,t,n,r){var i;try{e&&v(i=e.promise)?i.call(e).done(t).fail(n):e&&v(i=e.then)?i.call(e,t,n):t.apply(void 0,[e].slice(r))}catch(e){n.apply(void 0,[e])}}ce.Callbacks=function(r){var e,n;r="string"==typeof r?(e=r,n={},ce.each(e.match(D)||[],function(e,t){n[t]=!0}),n):ce.extend({},r);var i,t,o,a,s=[],u=[],l=-1,c=function(){for(a=a||r.once,o=i=!0;u.length;l=-1){t=u.shift();while(++l<s.length)!1===s[l].apply(t[0],t[1])&&r.stopOnFalse&&(l=s.length,t=!1)}r.memory||(t=!1),i=!1,a&&(s=t?[]:"")},f={add:function(){return s&&(t&&!i&&(l=s.length-1,u.push(t)),function n(e){ce.each(e,function(e,t){v(t)?r.unique&&f.has(t)||s.push(t):t&&t.length&&"string"!==x(t)&&n(t)})}(arguments),t&&!i&&c()),this},remove:function(){return ce.each(arguments,function(e,t){var n;while(-1<(n=ce.inArray(t,s,n)))s.splice(n,1),n<=l&&l--}),this},has:function(e){return e?-1<ce.inArray(e,s):0<s.length},empty:function(){return s&&(s=[]),this},disable:function(){return a=u=[],s=t="",this},disabled:function(){return!s},lock:function(){return a=u=[],t||i||(s=t=""),this},locked:function(){return!!a},fireWith:function(e,t){return a||(t=[e,(t=t||[]).slice?t.slice():t],u.push(t),i||c()),this},fire:function(){return f.fireWith(this,arguments),this},fired:function(){return!!o}};return f},ce.extend({Deferred:function(e){var o=[["notify","progress",ce.Callbacks("memory"),ce.Callbacks("memory"),2],["resolve","done",ce.Callbacks("once memory"),ce.Callbacks("once memory"),0,"resolved"],["reject","fail",ce.Callbacks("once memory"),ce.Callbacks("once memory"),1,"rejected"]],i="pending",a={state:function(){return i},always:function(){return s.done(arguments).fail(arguments),this},"catch":function(e){return a.then(null,e)},pipe:function(){var i=arguments;return ce.Deferred(function(r){ce.each(o,function(e,t){var n=v(i[t[4]])&&i[t[4]];s[t[1]](function(){var e=n&&n.apply(this,arguments);e&&v(e.promise)?e.promise().progress(r.notify).done(r.resolve).fail(r.reject):r[t[0]+"With"](this,n?[e]:arguments)})}),i=null}).promise()},then:function(t,n,r){var u=0;function l(i,o,a,s){return function(){var n=this,r=arguments,e=function(){var e,t;if(!(i<u)){if((e=a.apply(n,r))===o.promise())throw new TypeError("Thenable self-resolution");t=e&&("object"==typeof e||"function"==typeof e)&&e.then,v(t)?s?t.call(e,l(u,o,N,s),l(u,o,q,s)):(u++,t.call(e,l(u,o,N,s),l(u,o,q,s),l(u,o,N,o.notifyWith))):(a!==N&&(n=void 0,r=[e]),(s||o.resolveWith)(n,r))}},t=s?e:function(){try{e()}catch(e){ce.Deferred.exceptionHook&&ce.Deferred.exceptionHook(e,t.error),u<=i+1&&(a!==q&&(n=void 0,r=[e]),o.rejectWith(n,r))}};i?t():(ce.Deferred.getErrorHook?t.error=ce.Deferred.getErrorHook():ce.Deferred.getStackHook&&(t.error=ce.Deferred.getStackHook()),ie.setTimeout(t))}}return ce.Deferred(function(e){o[0][3].add(l(0,e,v(r)?r:N,e.notifyWith)),o[1][3].add(l(0,e,v(t)?t:N)),o[2][3].add(l(0,e,v(n)?n:q))}).promise()},promise:function(e){return null!=e?ce.extend(e,a):a}},s={};return ce.each(o,function(e,t){var n=t[2],r=t[5];a[t[1]]=n.add,r&&n.add(function(){i=r},o[3-e][2].disable,o[3-e][3].disable,o[0][2].lock,o[0][3].lock),n.add(t[3].fire),s[t[0]]=function(){return s[t[0]+"With"](this===s?void 0:this,arguments),this},s[t[0]+"With"]=n.fireWith}),a.promise(s),e&&e.call(s,s),s},when:function(e){var n=arguments.length,t=n,r=Array(t),i=ae.call(arguments),o=ce.Deferred(),a=function(t){return function(e){r[t]=this,i[t]=1<arguments.length?ae.call(arguments):e,--n||o.resolveWith(r,i)}};if(n<=1&&(L(e,o.done(a(t)).resolve,o.reject,!n),"pending"===o.state()||v(i[t]&&i[t].then)))return o.then();while(t--)L(i[t],a(t),o.reject);return o.promise()}});var H=/^(Eval|Internal|Range|Reference|Syntax|Type|URI)Error$/;ce.Deferred.exceptionHook=function(e,t){ie.console&&ie.console.warn&&e&&H.test(e.name)&&ie.console.warn("jQuery.Deferred exception: "+e.message,e.stack,t)},ce.readyException=function(e){ie.setTimeout(function(){throw e})};var O=ce.Deferred();function P(){C.removeEventListener("DOMContentLoaded",P),ie.removeEventListener("load",P),ce.ready()}ce.fn.ready=function(e){return O.then(e)["catch"](function(e){ce.readyException(e)}),this},ce.extend({isReady:!1,readyWait:1,ready:function(e){(!0===e?--ce.readyWait:ce.isReady)||(ce.isReady=!0)!==e&&0<--ce.readyWait||O.resolveWith(C,[ce])}}),ce.ready.then=O.then,"complete"===C.readyState||"loading"!==C.readyState&&!C.documentElement.doScroll?ie.setTimeout(ce.ready):(C.addEventListener("DOMContentLoaded",P),ie.addEventListener("load",P));var M=function(e,t,n,r,i,o,a){var s=0,u=e.length,l=null==n;if("object"===x(n))for(s in i=!0,n)M(e,t,s,n[s],!0,o,a);else if(void 0!==r&&(i=!0,v(r)||(a=!0),l&&(a?(t.call(e,r),t=null):(l=t,t=function(e,t,n){return l.call(ce(e),n)})),t))for(;s<u;s++)t(e[s],n,a?r:r.call(e[s],s,t(e[s],n)));return i?e:l?t.call(e):u?t(e[0],n):o},R=/^-ms-/,I=/-([a-z])/g;function W(e,t){return t.toUpperCase()}function F(e){return e.replace(R,"ms-").replace(I,W)}var $=function(e){return 1===e.nodeType||9===e.nodeType||!+e.nodeType};function B(){this.expando=ce.expando+B.uid++}B.uid=1,B.prototype={cache:function(e){var t=e[this.expando];return t||(t={},$(e)&&(e.nodeType?e[this.expando]=t:Object.defineProperty(e,this.expando,{value:t,configurable:!0}))),t},set:function(e,t,n){var r,i=this.cache(e);if("string"==typeof t)i[F(t)]=n;else for(r in t)i[F(r)]=t[r];return i},get:function(e,t){return void 0===t?this.cache(e):e[this.expando]&&e[this.expando][F(t)]},access:function(e,t,n){return void 0===t||t&&"string"==typeof t&&void 0===n?this.get(e,t):(this.set(e,t,n),void 0!==n?n:t)},remove:function(e,t){var n,r=e[this.expando];if(void 0!==r){if(void 0!==t){n=(t=Array.isArray(t)?t.map(F):(t=F(t))in r?[t]:t.match(D)||[]).length;while(n--)delete r[t[n]]}(void 0===t||ce.isEmptyObject(r))&&(e.nodeType?e[this.expando]=void 0:delete e[this.expando])}},hasData:function(e){var t=e[this.expando];return void 0!==t&&!ce.isEmptyObject(t)}};var _=new B,z=new B,X=/^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,U=/[A-Z]/g;function V(e,t,n){var r,i;if(void 0===n&&1===e.nodeType)if(r="data-"+t.replace(U,"-$&").toLowerCase(),"string"==typeof(n=e.getAttribute(r))){try{n="true"===(i=n)||"false"!==i&&("null"===i?null:i===+i+""?+i:X.test(i)?JSON.parse(i):i)}catch(e){}z.set(e,t,n)}else n=void 0;return n}ce.extend({hasData:function(e){return z.hasData(e)||_.hasData(e)},data:function(e,t,n){return z.access(e,t,n)},removeData:function(e,t){z.remove(e,t)},_data:function(e,t,n){return _.access(e,t,n)},_removeData:function(e,t){_.remove(e,t)}}),ce.fn.extend({data:function(n,e){var t,r,i,o=this[0],a=o&&o.attributes;if(void 0===n){if(this.length&&(i=z.get(o),1===o.nodeType&&!_.get(o,"hasDataAttrs"))){t=a.length;while(t--)a[t]&&0===(r=a[t].name).indexOf("data-")&&(r=F(r.slice(5)),V(o,r,i[r]));_.set(o,"hasDataAttrs",!0)}return i}return"object"==typeof n?this.each(function(){z.set(this,n)}):M(this,function(e){var t;if(o&&void 0===e)return void 0!==(t=z.get(o,n))?t:void 0!==(t=V(o,n))?t:void 0;this.each(function(){z.set(this,n,e)})},null,e,1<arguments.length,null,!0)},removeData:function(e){return this.each(function(){z.remove(this,e)})}}),ce.extend({queue:function(e,t,n){var r;if(e)return t=(t||"fx")+"queue",r=_.get(e,t),n&&(!r||Array.isArray(n)?r=_.access(e,t,ce.makeArray(n)):r.push(n)),r||[]},dequeue:function(e,t){t=t||"fx";var n=ce.queue(e,t),r=n.length,i=n.shift(),o=ce._queueHooks(e,t);"inprogress"===i&&(i=n.shift(),r--),i&&("fx"===t&&n.unshift("inprogress"),delete o.stop,i.call(e,function(){ce.dequeue(e,t)},o)),!r&&o&&o.empty.fire()},_queueHooks:function(e,t){var n=t+"queueHooks";return _.get(e,n)||_.access(e,n,{empty:ce.Callbacks("once memory").add(function(){_.remove(e,[t+"queue",n])})})}}),ce.fn.extend({queue:function(t,n){var e=2;return"string"!=typeof t&&(n=t,t="fx",e--),arguments.length<e?ce.queue(this[0],t):void 0===n?this:this.each(function(){var e=ce.queue(this,t,n);ce._queueHooks(this,t),"fx"===t&&"inprogress"!==e[0]&&ce.dequeue(this,t)})},dequeue:function(e){return this.each(function(){ce.dequeue(this,e)})},clearQueue:function(e){return this.queue(e||"fx",[])},promise:function(e,t){var n,r=1,i=ce.Deferred(),o=this,a=this.length,s=function(){--r||i.resolveWith(o,[o])};"string"!=typeof e&&(t=e,e=void 0),e=e||"fx";while(a--)(n=_.get(o[a],e+"queueHooks"))&&n.empty&&(r++,n.empty.add(s));return s(),i.promise(t)}});var G=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,Y=new RegExp("^(?:([+-])=|)("+G+")([a-z%]*)$","i"),Q=["Top","Right","Bottom","Left"],J=C.documentElement,K=function(e){return ce.contains(e.ownerDocument,e)},Z={composed:!0};J.getRootNode&&(K=function(e){return ce.contains(e.ownerDocument,e)||e.getRootNode(Z)===e.ownerDocument});var ee=function(e,t){return"none"===(e=t||e).style.display||""===e.style.display&&K(e)&&"none"===ce.css(e,"display")};function te(e,t,n,r){var i,o,a=20,s=r?function(){return r.cur()}:function(){return ce.css(e,t,"")},u=s(),l=n&&n[3]||(ce.cssNumber[t]?"":"px"),c=e.nodeType&&(ce.cssNumber[t]||"px"!==l&&+u)&&Y.exec(ce.css(e,t));if(c&&c[3]!==l){u/=2,l=l||c[3],c=+u||1;while(a--)ce.style(e,t,c+l),(1-o)*(1-(o=s()/u||.5))<=0&&(a=0),c/=o;c*=2,ce.style(e,t,c+l),n=n||[]}return n&&(c=+c||+u||0,i=n[1]?c+(n[1]+1)*n[2]:+n[2],r&&(r.unit=l,r.start=c,r.end=i)),i}var ne={};function re(e,t){for(var n,r,i,o,a,s,u,l=[],c=0,f=e.length;c<f;c++)(r=e[c]).style&&(n=r.style.display,t?("none"===n&&(l[c]=_.get(r,"display")||null,l[c]||(r.style.display="")),""===r.style.display&&ee(r)&&(l[c]=(u=a=o=void 0,a=(i=r).ownerDocument,s=i.nodeName,(u=ne[s])||(o=a.body.appendChild(a.createElement(s)),u=ce.css(o,"display"),o.parentNode.removeChild(o),"none"===u&&(u="block"),ne[s]=u)))):"none"!==n&&(l[c]="none",_.set(r,"display",n)));for(c=0;c<f;c++)null!=l[c]&&(e[c].style.display=l[c]);return e}ce.fn.extend({show:function(){return re(this,!0)},hide:function(){return re(this)},toggle:function(e){return"boolean"==typeof e?e?this.show():this.hide():this.each(function(){ee(this)?ce(this).show():ce(this).hide()})}});var xe,be,we=/^(?:checkbox|radio)$/i,Te=/<([a-z][^\/\0>\x20\t\r\n\f]*)/i,Ce=/^$|^module$|\/(?:java|ecma)script/i;xe=C.createDocumentFragment().appendChild(C.createElement("div")),(be=C.createElement("input")).setAttribute("type","radio"),be.setAttribute("checked","checked"),be.setAttribute("name","t"),xe.appendChild(be),le.checkClone=xe.cloneNode(!0).cloneNode(!0).lastChild.checked,xe.innerHTML="<textarea>x</textarea>",le.noCloneChecked=!!xe.cloneNode(!0).lastChild.defaultValue,xe.innerHTML="<option></option>",le.option=!!xe.lastChild;var ke={thead:[1,"<table>","</table>"],col:[2,"<table><colgroup>","</colgroup></table>"],tr:[2,"<table><tbody>","</tbody></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:[0,"",""]};function Se(e,t){var n;return n="undefined"!=typeof e.getElementsByTagName?e.getElementsByTagName(t||"*"):"undefined"!=typeof e.querySelectorAll?e.querySelectorAll(t||"*"):[],void 0===t||t&&fe(e,t)?ce.merge([e],n):n}function Ee(e,t){for(var n=0,r=e.length;n<r;n++)_.set(e[n],"globalEval",!t||_.get(t[n],"globalEval"))}ke.tbody=ke.tfoot=ke.colgroup=ke.caption=ke.thead,ke.th=ke.td,le.option||(ke.optgroup=ke.option=[1,"<select multiple='multiple'>","</select>"]);var je=/<|&#?\w+;/;function Ae(e,t,n,r,i){for(var o,a,s,u,l,c,f=t.createDocumentFragment(),p=[],d=0,h=e.length;d<h;d++)if((o=e[d])||0===o)if("object"===x(o))ce.merge(p,o.nodeType?[o]:o);else if(je.test(o)){a=a||f.appendChild(t.createElement("div")),s=(Te.exec(o)||["",""])[1].toLowerCase(),u=ke[s]||ke._default,a.innerHTML=u[1]+ce.htmlPrefilter(o)+u[2],c=u[0];while(c--)a=a.lastChild;ce.merge(p,a.childNodes),(a=f.firstChild).textContent=""}else p.push(t.createTextNode(o));f.textContent="",d=0;while(o=p[d++])if(r&&-1<ce.inArray(o,r))i&&i.push(o);else if(l=K(o),a=Se(f.appendChild(o),"script"),l&&Ee(a),n){c=0;while(o=a[c++])Ce.test(o.type||"")&&n.push(o)}return f}var De=/^([^.]*)(?:\.(.+)|)/;function Ne(){return!0}function qe(){return!1}function Le(e,t,n,r,i,o){var a,s;if("object"==typeof t){for(s in"string"!=typeof n&&(r=r||n,n=void 0),t)Le(e,s,n,r,t[s],o);return e}if(null==r&&null==i?(i=n,r=n=void 0):null==i&&("string"==typeof n?(i=r,r=void 0):(i=r,r=n,n=void 0)),!1===i)i=qe;else if(!i)return e;return 1===o&&(a=i,(i=function(e){return ce().off(e),a.apply(this,arguments)}).guid=a.guid||(a.guid=ce.guid++)),e.each(function(){ce.event.add(this,t,i,r,n)})}function He(e,r,t){t?(_.set(e,r,!1),ce.event.add(e,r,{namespace:!1,handler:function(e){var t,n=_.get(this,r);if(1&e.isTrigger&&this[r]){if(n)(ce.event.special[r]||{}).delegateType&&e.stopPropagation();else if(n=ae.call(arguments),_.set(this,r,n),this[r](),t=_.get(this,r),_.set(this,r,!1),n!==t)return e.stopImmediatePropagation(),e.preventDefault(),t}else n&&(_.set(this,r,ce.event.trigger(n[0],n.slice(1),this)),e.stopPropagation(),e.isImmediatePropagationStopped=Ne)}})):void 0===_.get(e,r)&&ce.event.add(e,r,Ne)}ce.event={global:{},add:function(t,e,n,r,i){var o,a,s,u,l,c,f,p,d,h,g,v=_.get(t);if($(t)){n.handler&&(n=(o=n).handler,i=o.selector),i&&ce.find.matchesSelector(J,i),n.guid||(n.guid=ce.guid++),(u=v.events)||(u=v.events=Object.create(null)),(a=v.handle)||(a=v.handle=function(e){return"undefined"!=typeof ce&&ce.event.triggered!==e.type?ce.event.dispatch.apply(t,arguments):void 0}),l=(e=(e||"").match(D)||[""]).length;while(l--)d=g=(s=De.exec(e[l])||[])[1],h=(s[2]||"").split(".").sort(),d&&(f=ce.event.special[d]||{},d=(i?f.delegateType:f.bindType)||d,f=ce.event.special[d]||{},c=ce.extend({type:d,origType:g,data:r,handler:n,guid:n.guid,selector:i,needsContext:i&&ce.expr.match.needsContext.test(i),namespace:h.join(".")},o),(p=u[d])||((p=u[d]=[]).delegateCount=0,f.setup&&!1!==f.setup.call(t,r,h,a)||t.addEventListener&&t.addEventListener(d,a)),f.add&&(f.add.call(t,c),c.handler.guid||(c.handler.guid=n.guid)),i?p.splice(p.delegateCount++,0,c):p.push(c),ce.event.global[d]=!0)}},remove:function(e,t,n,r,i){var o,a,s,u,l,c,f,p,d,h,g,v=_.hasData(e)&&_.get(e);if(v&&(u=v.events)){l=(t=(t||"").match(D)||[""]).length;while(l--)if(d=g=(s=De.exec(t[l])||[])[1],h=(s[2]||"").split(".").sort(),d){f=ce.event.special[d]||{},p=u[d=(r?f.delegateType:f.bindType)||d]||[],s=s[2]&&new RegExp("(^|\\.)"+h.join("\\.(?:.*\\.|)")+"(\\.|$)"),a=o=p.length;while(o--)c=p[o],!i&&g!==c.origType||n&&n.guid!==c.guid||s&&!s.test(c.namespace)||r&&r!==c.selector&&("**"!==r||!c.selector)||(p.splice(o,1),c.selector&&p.delegateCount--,f.remove&&f.remove.call(e,c));a&&!p.length&&(f.teardown&&!1!==f.teardown.call(e,h,v.handle)||ce.removeEvent(e,d,v.handle),delete u[d])}else for(d in u)ce.event.remove(e,d+t[l],n,r,!0);ce.isEmptyObject(u)&&_.remove(e,"handle events")}},dispatch:function(e){var t,n,r,i,o,a,s=new Array(arguments.length),u=ce.event.fix(e),l=(_.get(this,"events")||Object.create(null))[u.type]||[],c=ce.event.special[u.type]||{};for(s[0]=u,t=1;t<arguments.length;t++)s[t]=arguments[t];if(u.delegateTarget=this,!c.preDispatch||!1!==c.preDispatch.call(this,u)){a=ce.event.handlers.call(this,u,l),t=0;while((i=a[t++])&&!u.isPropagationStopped()){u.currentTarget=i.elem,n=0;while((o=i.handlers[n++])&&!u.isImmediatePropagationStopped())u.rnamespace&&!1!==o.namespace&&!u.rnamespace.test(o.namespace)||(u.handleObj=o,u.data=o.data,void 0!==(r=((ce.event.special[o.origType]||{}).handle||o.handler).apply(i.elem,s))&&!1===(u.result=r)&&(u.preventDefault(),u.stopPropagation()))}return c.postDispatch&&c.postDispatch.call(this,u),u.result}},handlers:function(e,t){var n,r,i,o,a,s=[],u=t.delegateCount,l=e.target;if(u&&l.nodeType&&!("click"===e.type&&1<=e.button))for(;l!==this;l=l.parentNode||this)if(1===l.nodeType&&("click"!==e.type||!0!==l.disabled)){for(o=[],a={},n=0;n<u;n++)void 0===a[i=(r=t[n]).selector+" "]&&(a[i]=r.needsContext?-1<ce(i,this).index(l):ce.find(i,this,null,[l]).length),a[i]&&o.push(r);o.length&&s.push({elem:l,handlers:o})}return l=this,u<t.length&&s.push({elem:l,handlers:t.slice(u)}),s},addProp:function(t,e){Object.defineProperty(ce.Event.prototype,t,{enumerable:!0,configurable:!0,get:v(e)?function(){if(this.originalEvent)return e(this.originalEvent)}:function(){if(this.originalEvent)return this.originalEvent[t]},set:function(e){Object.defineProperty(this,t,{enumerable:!0,configurable:!0,writable:!0,value:e})}})},fix:function(e){return e[ce.expando]?e:new ce.Event(e)},special:{load:{noBubble:!0},click:{setup:function(e){var t=this||e;return we.test(t.type)&&t.click&&fe(t,"input")&&He(t,"click",!0),!1},trigger:function(e){var t=this||e;return we.test(t.type)&&t.click&&fe(t,"input")&&He(t,"click"),!0},_default:function(e){var t=e.target;return we.test(t.type)&&t.click&&fe(t,"input")&&_.get(t,"click")||fe(t,"a")}},beforeunload:{postDispatch:function(e){void 0!==e.result&&e.originalEvent&&(e.originalEvent.returnValue=e.result)}}}},ce.removeEvent=function(e,t,n){e.removeEventListener&&e.removeEventListener(t,n)},ce.Event=function(e,t){if(!(this instanceof ce.Event))return new ce.Event(e,t);e&&e.type?(this.originalEvent=e,this.type=e.type,this.isDefaultPrevented=e.defaultPrevented||void 0===e.defaultPrevented&&!1===e.returnValue?Ne:qe,this.target=e.target&&3===e.target.nodeType?e.target.parentNode:e.target,this.currentTarget=e.currentTarget,this.relatedTarget=e.relatedTarget):this.type=e,t&&ce.extend(this,t),this.timeStamp=e&&e.timeStamp||Date.now(),this[ce.expando]=!0},ce.Event.prototype={constructor:ce.Event,isDefaultPrevented:qe,isPropagationStopped:qe,isImmediatePropagationStopped:qe,isSimulated:!1,preventDefault:function(){var e=this.originalEvent;this.isDefaultPrevented=Ne,e&&!this.isSimulated&&e.preventDefault()},stopPropagation:function(){var e=this.originalEvent;this.isPropagationStopped=Ne,e&&!this.isSimulated&&e.stopPropagation()},stopImmediatePropagation:function(){var e=this.originalEvent;this.isImmediatePropagationStopped=Ne,e&&!this.isSimulated&&e.stopImmediatePropagation(),this.stopPropagation()}},ce.each({altKey:!0,bubbles:!0,cancelable:!0,changedTouches:!0,ctrlKey:!0,detail:!0,eventPhase:!0,metaKey:!0,pageX:!0,pageY:!0,shiftKey:!0,view:!0,"char":!0,code:!0,charCode:!0,key:!0,keyCode:!0,button:!0,buttons:!0,clientX:!0,clientY:!0,offsetX:!0,offsetY:!0,pointerId:!0,pointerType:!0,screenX:!0,screenY:!0,targetTouches:!0,toElement:!0,touches:!0,which:!0},ce.event.addProp),ce.each({focus:"focusin",blur:"focusout"},function(r,i){function o(e){if(C.documentMode){var t=_.get(this,"handle"),n=ce.event.fix(e);n.type="focusin"===e.type?"focus":"blur",n.isSimulated=!0,t(e),n.target===n.currentTarget&&t(n)}else ce.event.simulate(i,e.target,ce.event.fix(e))}ce.event.special[r]={setup:function(){var e;if(He(this,r,!0),!C.documentMode)return!1;(e=_.get(this,i))||this.addEventListener(i,o),_.set(this,i,(e||0)+1)},trigger:function(){return He(this,r),!0},teardown:function(){var e;if(!C.documentMode)return!1;(e=_.get(this,i)-1)?_.set(this,i,e):(this.removeEventListener(i,o),_.remove(this,i))},_default:function(e){return _.get(e.target,r)},delegateType:i},ce.event.special[i]={setup:function(){var e=this.ownerDocument||this.document||this,t=C.documentMode?this:e,n=_.get(t,i);n||(C.documentMode?this.addEventListener(i,o):e.addEventListener(r,o,!0)),_.set(t,i,(n||0)+1)},teardown:function(){var e=this.ownerDocument||this.document||this,t=C.documentMode?this:e,n=_.get(t,i)-1;n?_.set(t,i,n):(C.documentMode?this.removeEventListener(i,o):e.removeEventListener(r,o,!0),_.remove(t,i))}}}),ce.each({mouseenter:"mouseover",mouseleave:"mouseout",pointerenter:"pointerover",pointerleave:"pointerout"},function(e,i){ce.event.special[e]={delegateType:i,bindType:i,handle:function(e){var t,n=e.relatedTarget,r=e.handleObj;return n&&(n===this||ce.contains(this,n))||(e.type=r.origType,t=r.handler.apply(this,arguments),e.type=i),t}}}),ce.fn.extend({on:function(e,t,n,r){return Le(this,e,t,n,r)},one:function(e,t,n,r){return Le(this,e,t,n,r,1)},off:function(e,t,n){var r,i;if(e&&e.preventDefault&&e.handleObj)return r=e.handleObj,ce(e.delegateTarget).off(r.namespace?r.origType+"."+r.namespace:r.origType,r.selector,r.handler),this;if("object"==typeof e){for(i in e)this.off(i,t,e[i]);return this}return!1!==t&&"function"!=typeof t||(n=t,t=void 0),!1===n&&(n=qe),this.each(function(){ce.event.remove(this,e,n,t)})}});var Oe=/<script|<style|<link/i,Pe=/checked\s*(?:[^=]|=\s*.checked.)/i,Me=/^\s*<!\[CDATA\[|\]\]>\s*$/g;function Re(e,t){return fe(e,"table")&&fe(11!==t.nodeType?t:t.firstChild,"tr")&&ce(e).children("tbody")[0]||e}function Ie(e){return e.type=(null!==e.getAttribute("type"))+"/"+e.type,e}function We(e){return"true/"===(e.type||"").slice(0,5)?e.type=e.type.slice(5):e.removeAttribute("type"),e}function Fe(e,t){var n,r,i,o,a,s;if(1===t.nodeType){if(_.hasData(e)&&(s=_.get(e).events))for(i in _.remove(t,"handle events"),s)for(n=0,r=s[i].length;n<r;n++)ce.event.add(t,i,s[i][n]);z.hasData(e)&&(o=z.access(e),a=ce.extend({},o),z.set(t,a))}}function $e(n,r,i,o){r=g(r);var e,t,a,s,u,l,c=0,f=n.length,p=f-1,d=r[0],h=v(d);if(h||1<f&&"string"==typeof d&&!le.checkClone&&Pe.test(d))return n.each(function(e){var t=n.eq(e);h&&(r[0]=d.call(this,e,t.html())),$e(t,r,i,o)});if(f&&(t=(e=Ae(r,n[0].ownerDocument,!1,n,o)).firstChild,1===e.childNodes.length&&(e=t),t||o)){for(s=(a=ce.map(Se(e,"script"),Ie)).length;c<f;c++)u=e,c!==p&&(u=ce.clone(u,!0,!0),s&&ce.merge(a,Se(u,"script"))),i.call(n[c],u,c);if(s)for(l=a[a.length-1].ownerDocument,ce.map(a,We),c=0;c<s;c++)u=a[c],Ce.test(u.type||"")&&!_.access(u,"globalEval")&&ce.contains(l,u)&&(u.src&&"module"!==(u.type||"").toLowerCase()?ce._evalUrl&&!u.noModule&&ce._evalUrl(u.src,{nonce:u.nonce||u.getAttribute("nonce")},l):m(u.textContent.replace(Me,""),u,l))}return n}function Be(e,t,n){for(var r,i=t?ce.filter(t,e):e,o=0;null!=(r=i[o]);o++)n||1!==r.nodeType||ce.cleanData(Se(r)),r.parentNode&&(n&&K(r)&&Ee(Se(r,"script")),r.parentNode.removeChild(r));return e}ce.extend({htmlPrefilter:function(e){return e},clone:function(e,t,n){var r,i,o,a,s,u,l,c=e.cloneNode(!0),f=K(e);if(!(le.noCloneChecked||1!==e.nodeType&&11!==e.nodeType||ce.isXMLDoc(e)))for(a=Se(c),r=0,i=(o=Se(e)).length;r<i;r++)s=o[r],u=a[r],void 0,"input"===(l=u.nodeName.toLowerCase())&&we.test(s.type)?u.checked=s.checked:"input"!==l&&"textarea"!==l||(u.defaultValue=s.defaultValue);if(t)if(n)for(o=o||Se(e),a=a||Se(c),r=0,i=o.length;r<i;r++)Fe(o[r],a[r]);else Fe(e,c);return 0<(a=Se(c,"script")).length&&Ee(a,!f&&Se(e,"script")),c},cleanData:function(e){for(var t,n,r,i=ce.event.special,o=0;void 0!==(n=e[o]);o++)if($(n)){if(t=n[_.expando]){if(t.events)for(r in t.events)i[r]?ce.event.remove(n,r):ce.removeEvent(n,r,t.handle);n[_.expando]=void 0}n[z.expando]&&(n[z.expando]=void 0)}}}),ce.fn.extend({detach:function(e){return Be(this,e,!0)},remove:function(e){return Be(this,e)},text:function(e){return M(this,function(e){return void 0===e?ce.text(this):this.empty().each(function(){1!==this.nodeType&&11!==this.nodeType&&9!==this.nodeType||(this.textContent=e)})},null,e,arguments.length)},append:function(){return $e(this,arguments,function(e){1!==this.nodeType&&11!==this.nodeType&&9!==this.nodeType||Re(this,e).appendChild(e)})},prepend:function(){return $e(this,arguments,function(e){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var t=Re(this,e);t.insertBefore(e,t.firstChild)}})},before:function(){return $e(this,arguments,function(e){this.parentNode&&this.parentNode.insertBefore(e,this)})},after:function(){return $e(this,arguments,function(e){this.parentNode&&this.parentNode.insertBefore(e,this.nextSibling)})},empty:function(){for(var e,t=0;null!=(e=this[t]);t++)1===e.nodeType&&(ce.cleanData(Se(e,!1)),e.textContent="");return this},clone:function(e,t){return e=null!=e&&e,t=null==t?e:t,this.map(function(){return ce.clone(this,e,t)})},html:function(e){return M(this,function(e){var t=this[0]||{},n=0,r=this.length;if(void 0===e&&1===t.nodeType)return t.innerHTML;if("string"==typeof e&&!Oe.test(e)&&!ke[(Te.exec(e)||["",""])[1].toLowerCase()]){e=ce.htmlPrefilter(e);try{for(;n<r;n++)1===(t=this[n]||{}).nodeType&&(ce.cleanData(Se(t,!1)),t.innerHTML=e);t=0}catch(e){}}t&&this.empty().append(e)},null,e,arguments.length)},replaceWith:function(){var n=[];return $e(this,arguments,function(e){var t=this.parentNode;ce.inArray(this,n)<0&&(ce.cleanData(Se(this)),t&&t.replaceChild(e,this))},n)}}),ce.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(e,a){ce.fn[e]=function(e){for(var t,n=[],r=ce(e),i=r.length-1,o=0;o<=i;o++)t=o===i?this:this.clone(!0),ce(r[o])[a](t),s.apply(n,t.get());return this.pushStack(n)}});var _e=new RegExp("^("+G+")(?!px)[a-z%]+$","i"),ze=/^--/,Xe=function(e){var t=e.ownerDocument.defaultView;return t&&t.opener||(t=ie),t.getComputedStyle(e)},Ue=function(e,t,n){var r,i,o={};for(i in t)o[i]=e.style[i],e.style[i]=t[i];for(i in r=n.call(e),t)e.style[i]=o[i];return r},Ve=new RegExp(Q.join("|"),"i");function Ge(e,t,n){var r,i,o,a,s=ze.test(t),u=e.style;return(n=n||Xe(e))&&(a=n.getPropertyValue(t)||n[t],s&&a&&(a=a.replace(ve,"$1")||void 0),""!==a||K(e)||(a=ce.style(e,t)),!le.pixelBoxStyles()&&_e.test(a)&&Ve.test(t)&&(r=u.width,i=u.minWidth,o=u.maxWidth,u.minWidth=u.maxWidth=u.width=a,a=n.width,u.width=r,u.minWidth=i,u.maxWidth=o)),void 0!==a?a+"":a}function Ye(e,t){return{get:function(){if(!e())return(this.get=t).apply(this,arguments);delete this.get}}}!function(){function e(){if(l){u.style.cssText="position:absolute;left:-11111px;width:60px;margin-top:1px;padding:0;border:0",l.style.cssText="position:relative;display:block;box-sizing:border-box;overflow:scroll;margin:auto;border:1px;padding:1px;width:60%;top:1%",J.appendChild(u).appendChild(l);var e=ie.getComputedStyle(l);n="1%"!==e.top,s=12===t(e.marginLeft),l.style.right="60%",o=36===t(e.right),r=36===t(e.width),l.style.position="absolute",i=12===t(l.offsetWidth/3),J.removeChild(u),l=null}}function t(e){return Math.round(parseFloat(e))}var n,r,i,o,a,s,u=C.createElement("div"),l=C.createElement("div");l.style&&(l.style.backgroundClip="content-box",l.cloneNode(!0).style.backgroundClip="",le.clearCloneStyle="content-box"===l.style.backgroundClip,ce.extend(le,{boxSizingReliable:function(){return e(),r},pixelBoxStyles:function(){return e(),o},pixelPosition:function(){return e(),n},reliableMarginLeft:function(){return e(),s},scrollboxSize:function(){return e(),i},reliableTrDimensions:function(){var e,t,n,r;return null==a&&(e=C.createElement("table"),t=C.createElement("tr"),n=C.createElement("div"),e.style.cssText="position:absolute;left:-11111px;border-collapse:separate",t.style.cssText="box-sizing:content-box;border:1px solid",t.style.height="1px",n.style.height="9px",n.style.display="block",J.appendChild(e).appendChild(t).appendChild(n),r=ie.getComputedStyle(t),a=parseInt(r.height,10)+parseInt(r.borderTopWidth,10)+parseInt(r.borderBottomWidth,10)===t.offsetHeight,J.removeChild(e)),a}}))}();var Qe=["Webkit","Moz","ms"],Je=C.createElement("div").style,Ke={};function Ze(e){var t=ce.cssProps[e]||Ke[e];return t||(e in Je?e:Ke[e]=function(e){var t=e[0].toUpperCase()+e.slice(1),n=Qe.length;while(n--)if((e=Qe[n]+t)in Je)return e}(e)||e)}var et=/^(none|table(?!-c[ea]).+)/,tt={position:"absolute",visibility:"hidden",display:"block"},nt={letterSpacing:"0",fontWeight:"400"};function rt(e,t,n){var r=Y.exec(t);return r?Math.max(0,r[2]-(n||0))+(r[3]||"px"):t}function it(e,t,n,r,i,o){var a="width"===t?1:0,s=0,u=0,l=0;if(n===(r?"border":"content"))return 0;for(;a<4;a+=2)"margin"===n&&(l+=ce.css(e,n+Q[a],!0,i)),r?("content"===n&&(u-=ce.css(e,"padding"+Q[a],!0,i)),"margin"!==n&&(u-=ce.css(e,"border"+Q[a]+"Width",!0,i))):(u+=ce.css(e,"padding"+Q[a],!0,i),"padding"!==n?u+=ce.css(e,"border"+Q[a]+"Width",!0,i):s+=ce.css(e,"border"+Q[a]+"Width",!0,i));return!r&&0<=o&&(u+=Math.max(0,Math.ceil(e["offset"+t[0].toUpperCase()+t.slice(1)]-o-u-s-.5))||0),u+l}function ot(e,t,n){var r=Xe(e),i=(!le.boxSizingReliable()||n)&&"border-box"===ce.css(e,"boxSizing",!1,r),o=i,a=Ge(e,t,r),s="offset"+t[0].toUpperCase()+t.slice(1);if(_e.test(a)){if(!n)return a;a="auto"}return(!le.boxSizingReliable()&&i||!le.reliableTrDimensions()&&fe(e,"tr")||"auto"===a||!parseFloat(a)&&"inline"===ce.css(e,"display",!1,r))&&e.getClientRects().length&&(i="border-box"===ce.css(e,"boxSizing",!1,r),(o=s in e)&&(a=e[s])),(a=parseFloat(a)||0)+it(e,t,n||(i?"border":"content"),o,r,a)+"px"}function at(e,t,n,r,i){return new at.prototype.init(e,t,n,r,i)}ce.extend({cssHooks:{opacity:{get:function(e,t){if(t){var n=Ge(e,"opacity");return""===n?"1":n}}}},cssNumber:{animationIterationCount:!0,aspectRatio:!0,borderImageSlice:!0,columnCount:!0,flexGrow:!0,flexShrink:!0,fontWeight:!0,gridArea:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnStart:!0,gridRow:!0,gridRowEnd:!0,gridRowStart:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,scale:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeMiterlimit:!0,strokeOpacity:!0},cssProps:{},style:function(e,t,n,r){if(e&&3!==e.nodeType&&8!==e.nodeType&&e.style){var i,o,a,s=F(t),u=ze.test(t),l=e.style;if(u||(t=Ze(s)),a=ce.cssHooks[t]||ce.cssHooks[s],void 0===n)return a&&"get"in a&&void 0!==(i=a.get(e,!1,r))?i:l[t];"string"===(o=typeof n)&&(i=Y.exec(n))&&i[1]&&(n=te(e,t,i),o="number"),null!=n&&n==n&&("number"!==o||u||(n+=i&&i[3]||(ce.cssNumber[s]?"":"px")),le.clearCloneStyle||""!==n||0!==t.indexOf("background")||(l[t]="inherit"),a&&"set"in a&&void 0===(n=a.set(e,n,r))||(u?l.setProperty(t,n):l[t]=n))}},css:function(e,t,n,r){var i,o,a,s=F(t);return ze.test(t)||(t=Ze(s)),(a=ce.cssHooks[t]||ce.cssHooks[s])&&"get"in a&&(i=a.get(e,!0,n)),void 0===i&&(i=Ge(e,t,r)),"normal"===i&&t in nt&&(i=nt[t]),""===n||n?(o=parseFloat(i),!0===n||isFinite(o)?o||0:i):i}}),ce.each(["height","width"],function(e,u){ce.cssHooks[u]={get:function(e,t,n){if(t)return!et.test(ce.css(e,"display"))||e.getClientRects().length&&e.getBoundingClientRect().width?ot(e,u,n):Ue(e,tt,function(){return ot(e,u,n)})},set:function(e,t,n){var r,i=Xe(e),o=!le.scrollboxSize()&&"absolute"===i.position,a=(o||n)&&"border-box"===ce.css(e,"boxSizing",!1,i),s=n?it(e,u,n,a,i):0;return a&&o&&(s-=Math.ceil(e["offset"+u[0].toUpperCase()+u.slice(1)]-parseFloat(i[u])-it(e,u,"border",!1,i)-.5)),s&&(r=Y.exec(t))&&"px"!==(r[3]||"px")&&(e.style[u]=t,t=ce.css(e,u)),rt(0,t,s)}}}),ce.cssHooks.marginLeft=Ye(le.reliableMarginLeft,function(e,t){if(t)return(parseFloat(Ge(e,"marginLeft"))||e.getBoundingClientRect().left-Ue(e,{marginLeft:0},function(){return e.getBoundingClientRect().left}))+"px"}),ce.each({margin:"",padding:"",border:"Width"},function(i,o){ce.cssHooks[i+o]={expand:function(e){for(var t=0,n={},r="string"==typeof e?e.split(" "):[e];t<4;t++)n[i+Q[t]+o]=r[t]||r[t-2]||r[0];return n}},"margin"!==i&&(ce.cssHooks[i+o].set=rt)}),ce.fn.extend({css:function(e,t){return M(this,function(e,t,n){var r,i,o={},a=0;if(Array.isArray(t)){for(r=Xe(e),i=t.length;a<i;a++)o[t[a]]=ce.css(e,t[a],!1,r);return o}return void 0!==n?ce.style(e,t,n):ce.css(e,t)},e,t,1<arguments.length)}}),((ce.Tween=at).prototype={constructor:at,init:function(e,t,n,r,i,o){this.elem=e,this.prop=n,this.easing=i||ce.easing._default,this.options=t,this.start=this.now=this.cur(),this.end=r,this.unit=o||(ce.cssNumber[n]?"":"px")},cur:function(){var e=at.propHooks[this.prop];return e&&e.get?e.get(this):at.propHooks._default.get(this)},run:function(e){var t,n=at.propHooks[this.prop];return this.options.duration?this.pos=t=ce.easing[this.easing](e,this.options.duration*e,0,1,this.options.duration):this.pos=t=e,this.now=(this.end-this.start)*t+this.start,this.options.step&&this.options.step.call(this.elem,this.now,this),n&&n.set?n.set(this):at.propHooks._default.set(this),this}}).init.prototype=at.prototype,(at.propHooks={_default:{get:function(e){var t;return 1!==e.elem.nodeType||null!=e.elem[e.prop]&&null==e.elem.style[e.prop]?e.elem[e.prop]:(t=ce.css(e.elem,e.prop,""))&&"auto"!==t?t:0},set:function(e){ce.fx.step[e.prop]?ce.fx.step[e.prop](e):1!==e.elem.nodeType||!ce.cssHooks[e.prop]&&null==e.elem.style[Ze(e.prop)]?e.elem[e.prop]=e.now:ce.style(e.elem,e.prop,e.now+e.unit)}}}).scrollTop=at.propHooks.scrollLeft={set:function(e){e.elem.nodeType&&e.elem.parentNode&&(e.elem[e.prop]=e.now)}},ce.easing={linear:function(e){return e},swing:function(e){return.5-Math.cos(e*Math.PI)/2},_default:"swing"},ce.fx=at.prototype.init,ce.fx.step={};var st,ut,lt,ct,ft=/^(?:toggle|show|hide)$/,pt=/queueHooks$/;function dt(){ut&&(!1===C.hidden&&ie.requestAnimationFrame?ie.requestAnimationFrame(dt):ie.setTimeout(dt,ce.fx.interval),ce.fx.tick())}function ht(){return ie.setTimeout(function(){st=void 0}),st=Date.now()}function gt(e,t){var n,r=0,i={height:e};for(t=t?1:0;r<4;r+=2-t)i["margin"+(n=Q[r])]=i["padding"+n]=e;return t&&(i.opacity=i.width=e),i}function vt(e,t,n){for(var r,i=(yt.tweeners[t]||[]).concat(yt.tweeners["*"]),o=0,a=i.length;o<a;o++)if(r=i[o].call(n,t,e))return r}function yt(o,e,t){var n,a,r=0,i=yt.prefilters.length,s=ce.Deferred().always(function(){delete u.elem}),u=function(){if(a)return!1;for(var e=st||ht(),t=Math.max(0,l.startTime+l.duration-e),n=1-(t/l.duration||0),r=0,i=l.tweens.length;r<i;r++)l.tweens[r].run(n);return s.notifyWith(o,[l,n,t]),n<1&&i?t:(i||s.notifyWith(o,[l,1,0]),s.resolveWith(o,[l]),!1)},l=s.promise({elem:o,props:ce.extend({},e),opts:ce.extend(!0,{specialEasing:{},easing:ce.easing._default},t),originalProperties:e,originalOptions:t,startTime:st||ht(),duration:t.duration,tweens:[],createTween:function(e,t){var n=ce.Tween(o,l.opts,e,t,l.opts.specialEasing[e]||l.opts.easing);return l.tweens.push(n),n},stop:function(e){var t=0,n=e?l.tweens.length:0;if(a)return this;for(a=!0;t<n;t++)l.tweens[t].run(1);return e?(s.notifyWith(o,[l,1,0]),s.resolveWith(o,[l,e])):s.rejectWith(o,[l,e]),this}}),c=l.props;for(!function(e,t){var n,r,i,o,a;for(n in e)if(i=t[r=F(n)],o=e[n],Array.isArray(o)&&(i=o[1],o=e[n]=o[0]),n!==r&&(e[r]=o,delete e[n]),(a=ce.cssHooks[r])&&"expand"in a)for(n in o=a.expand(o),delete e[r],o)n in e||(e[n]=o[n],t[n]=i);else t[r]=i}(c,l.opts.specialEasing);r<i;r++)if(n=yt.prefilters[r].call(l,o,c,l.opts))return v(n.stop)&&(ce._queueHooks(l.elem,l.opts.queue).stop=n.stop.bind(n)),n;return ce.map(c,vt,l),v(l.opts.start)&&l.opts.start.call(o,l),l.progress(l.opts.progress).done(l.opts.done,l.opts.complete).fail(l.opts.fail).always(l.opts.always),ce.fx.timer(ce.extend(u,{elem:o,anim:l,queue:l.opts.queue})),l}ce.Animation=ce.extend(yt,{tweeners:{"*":[function(e,t){var n=this.createTween(e,t);return te(n.elem,e,Y.exec(t),n),n}]},tweener:function(e,t){v(e)?(t=e,e=["*"]):e=e.match(D);for(var n,r=0,i=e.length;r<i;r++)n=e[r],yt.tweeners[n]=yt.tweeners[n]||[],yt.tweeners[n].unshift(t)},prefilters:[function(e,t,n){var r,i,o,a,s,u,l,c,f="width"in t||"height"in t,p=this,d={},h=e.style,g=e.nodeType&&ee(e),v=_.get(e,"fxshow");for(r in n.queue||(null==(a=ce._queueHooks(e,"fx")).unqueued&&(a.unqueued=0,s=a.empty.fire,a.empty.fire=function(){a.unqueued||s()}),a.unqueued++,p.always(function(){p.always(function(){a.unqueued--,ce.queue(e,"fx").length||a.empty.fire()})})),t)if(i=t[r],ft.test(i)){if(delete t[r],o=o||"toggle"===i,i===(g?"hide":"show")){if("show"!==i||!v||void 0===v[r])continue;g=!0}d[r]=v&&v[r]||ce.style(e,r)}if((u=!ce.isEmptyObject(t))||!ce.isEmptyObject(d))for(r in f&&1===e.nodeType&&(n.overflow=[h.overflow,h.overflowX,h.overflowY],null==(l=v&&v.display)&&(l=_.get(e,"display")),"none"===(c=ce.css(e,"display"))&&(l?c=l:(re([e],!0),l=e.style.display||l,c=ce.css(e,"display"),re([e]))),("inline"===c||"inline-block"===c&&null!=l)&&"none"===ce.css(e,"float")&&(u||(p.done(function(){h.display=l}),null==l&&(c=h.display,l="none"===c?"":c)),h.display="inline-block")),n.overflow&&(h.overflow="hidden",p.always(function(){h.overflow=n.overflow[0],h.overflowX=n.overflow[1],h.overflowY=n.overflow[2]})),u=!1,d)u||(v?"hidden"in v&&(g=v.hidden):v=_.access(e,"fxshow",{display:l}),o&&(v.hidden=!g),g&&re([e],!0),p.done(function(){for(r in g||re([e]),_.remove(e,"fxshow"),d)ce.style(e,r,d[r])})),u=vt(g?v[r]:0,r,p),r in v||(v[r]=u.start,g&&(u.end=u.start,u.start=0))}],prefilter:function(e,t){t?yt.prefilters.unshift(e):yt.prefilters.push(e)}}),ce.speed=function(e,t,n){var r=e&&"object"==typeof e?ce.extend({},e):{complete:n||!n&&t||v(e)&&e,duration:e,easing:n&&t||t&&!v(t)&&t};return ce.fx.off?r.duration=0:"number"!=typeof r.duration&&(r.duration in ce.fx.speeds?r.duration=ce.fx.speeds[r.duration]:r.duration=ce.fx.speeds._default),null!=r.queue&&!0!==r.queue||(r.queue="fx"),r.old=r.complete,r.complete=function(){v(r.old)&&r.old.call(this),r.queue&&ce.dequeue(this,r.queue)},r},ce.fn.extend({fadeTo:function(e,t,n,r){return this.filter(ee).css("opacity",0).show().end().animate({opacity:t},e,n,r)},animate:function(t,e,n,r){var i=ce.isEmptyObject(t),o=ce.speed(e,n,r),a=function(){var e=yt(this,ce.extend({},t),o);(i||_.get(this,"finish"))&&e.stop(!0)};return a.finish=a,i||!1===o.queue?this.each(a):this.queue(o.queue,a)},stop:function(i,e,o){var a=function(e){var t=e.stop;delete e.stop,t(o)};return"string"!=typeof i&&(o=e,e=i,i=void 0),e&&this.queue(i||"fx",[]),this.each(function(){var e=!0,t=null!=i&&i+"queueHooks",n=ce.timers,r=_.get(this);if(t)r[t]&&r[t].stop&&a(r[t]);else for(t in r)r[t]&&r[t].stop&&pt.test(t)&&a(r[t]);for(t=n.length;t--;)n[t].elem!==this||null!=i&&n[t].queue!==i||(n[t].anim.stop(o),e=!1,n.splice(t,1));!e&&o||ce.dequeue(this,i)})},finish:function(a){return!1!==a&&(a=a||"fx"),this.each(function(){var e,t=_.get(this),n=t[a+"queue"],r=t[a+"queueHooks"],i=ce.timers,o=n?n.length:0;for(t.finish=!0,ce.queue(this,a,[]),r&&r.stop&&r.stop.call(this,!0),e=i.length;e--;)i[e].elem===this&&i[e].queue===a&&(i[e].anim.stop(!0),i.splice(e,1));for(e=0;e<o;e++)n[e]&&n[e].finish&&n[e].finish.call(this);delete t.finish})}}),ce.each(["toggle","show","hide"],function(e,r){var i=ce.fn[r];ce.fn[r]=function(e,t,n){return null==e||"boolean"==typeof e?i.apply(this,arguments):this.animate(gt(r,!0),e,t,n)}}),ce.each({slideDown:gt("show"),slideUp:gt("hide"),slideToggle:gt("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(e,r){ce.fn[e]=function(e,t,n){return this.animate(r,e,t,n)}}),ce.timers=[],ce.fx.tick=function(){var e,t=0,n=ce.timers;for(st=Date.now();t<n.length;t++)(e=n[t])()||n[t]!==e||n.splice(t--,1);n.length||ce.fx.stop(),st=void 0},ce.fx.timer=function(e){ce.timers.push(e),ce.fx.start()},ce.fx.interval=13,ce.fx.start=function(){ut||(ut=!0,dt())},ce.fx.stop=function(){ut=null},ce.fx.speeds={slow:600,fast:200,_default:400},ce.fn.delay=function(r,e){return r=ce.fx&&ce.fx.speeds[r]||r,e=e||"fx",this.queue(e,function(e,t){var n=ie.setTimeout(e,r);t.stop=function(){ie.clearTimeout(n)}})},lt=C.createElement("input"),ct=C.createElement("select").appendChild(C.createElement("option")),lt.type="checkbox",le.checkOn=""!==lt.value,le.optSelected=ct.selected,(lt=C.createElement("input")).value="t",lt.type="radio",le.radioValue="t"===lt.value;var mt,xt=ce.expr.attrHandle;ce.fn.extend({attr:function(e,t){return M(this,ce.attr,e,t,1<arguments.length)},removeAttr:function(e){return this.each(function(){ce.removeAttr(this,e)})}}),ce.extend({attr:function(e,t,n){var r,i,o=e.nodeType;if(3!==o&&8!==o&&2!==o)return"undefined"==typeof e.getAttribute?ce.prop(e,t,n):(1===o&&ce.isXMLDoc(e)||(i=ce.attrHooks[t.toLowerCase()]||(ce.expr.match.bool.test(t)?mt:void 0)),void 0!==n?null===n?void ce.removeAttr(e,t):i&&"set"in i&&void 0!==(r=i.set(e,n,t))?r:(e.setAttribute(t,n+""),n):i&&"get"in i&&null!==(r=i.get(e,t))?r:null==(r=ce.find.attr(e,t))?void 0:r)},attrHooks:{type:{set:function(e,t){if(!le.radioValue&&"radio"===t&&fe(e,"input")){var n=e.value;return e.setAttribute("type",t),n&&(e.value=n),t}}}},removeAttr:function(e,t){var n,r=0,i=t&&t.match(D);if(i&&1===e.nodeType)while(n=i[r++])e.removeAttribute(n)}}),mt={set:function(e,t,n){return!1===t?ce.removeAttr(e,n):e.setAttribute(n,n),n}},ce.each(ce.expr.match.bool.source.match(/\w+/g),function(e,t){var a=xt[t]||ce.find.attr;xt[t]=function(e,t,n){var r,i,o=t.toLowerCase();return n||(i=xt[o],xt[o]=r,r=null!=a(e,t,n)?o:null,xt[o]=i),r}});var bt=/^(?:input|select|textarea|button)$/i,wt=/^(?:a|area)$/i;function Tt(e){return(e.match(D)||[]).join(" ")}function Ct(e){return e.getAttribute&&e.getAttribute("class")||""}function kt(e){return Array.isArray(e)?e:"string"==typeof e&&e.match(D)||[]}ce.fn.extend({prop:function(e,t){return M(this,ce.prop,e,t,1<arguments.length)},removeProp:function(e){return this.each(function(){delete this[ce.propFix[e]||e]})}}),ce.extend({prop:function(e,t,n){var r,i,o=e.nodeType;if(3!==o&&8!==o&&2!==o)return 1===o&&ce.isXMLDoc(e)||(t=ce.propFix[t]||t,i=ce.propHooks[t]),void 0!==n?i&&"set"in i&&void 0!==(r=i.set(e,n,t))?r:e[t]=n:i&&"get"in i&&null!==(r=i.get(e,t))?r:e[t]},propHooks:{tabIndex:{get:function(e){var t=ce.find.attr(e,"tabindex");return t?parseInt(t,10):bt.test(e.nodeName)||wt.test(e.nodeName)&&e.href?0:-1}}},propFix:{"for":"htmlFor","class":"className"}}),le.optSelected||(ce.propHooks.selected={get:function(e){var t=e.parentNode;return t&&t.parentNode&&t.parentNode.selectedIndex,null},set:function(e){var t=e.parentNode;t&&(t.selectedIndex,t.parentNode&&t.parentNode.selectedIndex)}}),ce.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){ce.propFix[this.toLowerCase()]=this}),ce.fn.extend({addClass:function(t){var e,n,r,i,o,a;return v(t)?this.each(function(e){ce(this).addClass(t.call(this,e,Ct(this)))}):(e=kt(t)).length?this.each(function(){if(r=Ct(this),n=1===this.nodeType&&" "+Tt(r)+" "){for(o=0;o<e.length;o++)i=e[o],n.indexOf(" "+i+" ")<0&&(n+=i+" ");a=Tt(n),r!==a&&this.setAttribute("class",a)}}):this},removeClass:function(t){var e,n,r,i,o,a;return v(t)?this.each(function(e){ce(this).removeClass(t.call(this,e,Ct(this)))}):arguments.length?(e=kt(t)).length?this.each(function(){if(r=Ct(this),n=1===this.nodeType&&" "+Tt(r)+" "){for(o=0;o<e.length;o++){i=e[o];while(-1<n.indexOf(" "+i+" "))n=n.replace(" "+i+" "," ")}a=Tt(n),r!==a&&this.setAttribute("class",a)}}):this:this.attr("class","")},toggleClass:function(t,n){var e,r,i,o,a=typeof t,s="string"===a||Array.isArray(t);return v(t)?this.each(function(e){ce(this).toggleClass(t.call(this,e,Ct(this),n),n)}):"boolean"==typeof n&&s?n?this.addClass(t):this.removeClass(t):(e=kt(t),this.each(function(){if(s)for(o=ce(this),i=0;i<e.length;i++)r=e[i],o.hasClass(r)?o.removeClass(r):o.addClass(r);else void 0!==t&&"boolean"!==a||((r=Ct(this))&&_.set(this,"__className__",r),this.setAttribute&&this.setAttribute("class",r||!1===t?"":_.get(this,"__className__")||""))}))},hasClass:function(e){var t,n,r=0;t=" "+e+" ";while(n=this[r++])if(1===n.nodeType&&-1<(" "+Tt(Ct(n))+" ").indexOf(t))return!0;return!1}});var St=/\r/g;ce.fn.extend({val:function(n){var r,e,i,t=this[0];return arguments.length?(i=v(n),this.each(function(e){var t;1===this.nodeType&&(null==(t=i?n.call(this,e,ce(this).val()):n)?t="":"number"==typeof t?t+="":Array.isArray(t)&&(t=ce.map(t,function(e){return null==e?"":e+""})),(r=ce.valHooks[this.type]||ce.valHooks[this.nodeName.toLowerCase()])&&"set"in r&&void 0!==r.set(this,t,"value")||(this.value=t))})):t?(r=ce.valHooks[t.type]||ce.valHooks[t.nodeName.toLowerCase()])&&"get"in r&&void 0!==(e=r.get(t,"value"))?e:"string"==typeof(e=t.value)?e.replace(St,""):null==e?"":e:void 0}}),ce.extend({valHooks:{option:{get:function(e){var t=ce.find.attr(e,"value");return null!=t?t:Tt(ce.text(e))}},select:{get:function(e){var t,n,r,i=e.options,o=e.selectedIndex,a="select-one"===e.type,s=a?null:[],u=a?o+1:i.length;for(r=o<0?u:a?o:0;r<u;r++)if(((n=i[r]).selected||r===o)&&!n.disabled&&(!n.parentNode.disabled||!fe(n.parentNode,"optgroup"))){if(t=ce(n).val(),a)return t;s.push(t)}return s},set:function(e,t){var n,r,i=e.options,o=ce.makeArray(t),a=i.length;while(a--)((r=i[a]).selected=-1<ce.inArray(ce.valHooks.option.get(r),o))&&(n=!0);return n||(e.selectedIndex=-1),o}}}}),ce.each(["radio","checkbox"],function(){ce.valHooks[this]={set:function(e,t){if(Array.isArray(t))return e.checked=-1<ce.inArray(ce(e).val(),t)}},le.checkOn||(ce.valHooks[this].get=function(e){return null===e.getAttribute("value")?"on":e.value})});var Et=ie.location,jt={guid:Date.now()},At=/\?/;ce.parseXML=function(e){var t,n;if(!e||"string"!=typeof e)return null;try{t=(new ie.DOMParser).parseFromString(e,"text/xml")}catch(e){}return n=t&&t.getElementsByTagName("parsererror")[0],t&&!n||ce.error("Invalid XML: "+(n?ce.map(n.childNodes,function(e){return e.textContent}).join("\n"):e)),t};var Dt=/^(?:focusinfocus|focusoutblur)$/,Nt=function(e){e.stopPropagation()};ce.extend(ce.event,{trigger:function(e,t,n,r){var i,o,a,s,u,l,c,f,p=[n||C],d=ue.call(e,"type")?e.type:e,h=ue.call(e,"namespace")?e.namespace.split("."):[];if(o=f=a=n=n||C,3!==n.nodeType&&8!==n.nodeType&&!Dt.test(d+ce.event.triggered)&&(-1<d.indexOf(".")&&(d=(h=d.split(".")).shift(),h.sort()),u=d.indexOf(":")<0&&"on"+d,(e=e[ce.expando]?e:new ce.Event(d,"object"==typeof e&&e)).isTrigger=r?2:3,e.namespace=h.join("."),e.rnamespace=e.namespace?new RegExp("(^|\\.)"+h.join("\\.(?:.*\\.|)")+"(\\.|$)"):null,e.result=void 0,e.target||(e.target=n),t=null==t?[e]:ce.makeArray(t,[e]),c=ce.event.special[d]||{},r||!c.trigger||!1!==c.trigger.apply(n,t))){if(!r&&!c.noBubble&&!y(n)){for(s=c.delegateType||d,Dt.test(s+d)||(o=o.parentNode);o;o=o.parentNode)p.push(o),a=o;a===(n.ownerDocument||C)&&p.push(a.defaultView||a.parentWindow||ie)}i=0;while((o=p[i++])&&!e.isPropagationStopped())f=o,e.type=1<i?s:c.bindType||d,(l=(_.get(o,"events")||Object.create(null))[e.type]&&_.get(o,"handle"))&&l.apply(o,t),(l=u&&o[u])&&l.apply&&$(o)&&(e.result=l.apply(o,t),!1===e.result&&e.preventDefault());return e.type=d,r||e.isDefaultPrevented()||c._default&&!1!==c._default.apply(p.pop(),t)||!$(n)||u&&v(n[d])&&!y(n)&&((a=n[u])&&(n[u]=null),ce.event.triggered=d,e.isPropagationStopped()&&f.addEventListener(d,Nt),n[d](),e.isPropagationStopped()&&f.removeEventListener(d,Nt),ce.event.triggered=void 0,a&&(n[u]=a)),e.result}},simulate:function(e,t,n){var r=ce.extend(new ce.Event,n,{type:e,isSimulated:!0});ce.event.trigger(r,null,t)}}),ce.fn.extend({trigger:function(e,t){return this.each(function(){ce.event.trigger(e,t,this)})},triggerHandler:function(e,t){var n=this[0];if(n)return ce.event.trigger(e,t,n,!0)}});var qt=/\[\]$/,Lt=/\r?\n/g,Ht=/^(?:submit|button|image|reset|file)$/i,Ot=/^(?:input|select|textarea|keygen)/i;function Pt(n,e,r,i){var t;if(Array.isArray(e))ce.each(e,function(e,t){r||qt.test(n)?i(n,t):Pt(n+"["+("object"==typeof t&&null!=t?e:"")+"]",t,r,i)});else if(r||"object"!==x(e))i(n,e);else for(t in e)Pt(n+"["+t+"]",e[t],r,i)}ce.param=function(e,t){var n,r=[],i=function(e,t){var n=v(t)?t():t;r[r.length]=encodeURIComponent(e)+"="+encodeURIComponent(null==n?"":n)};if(null==e)return"";if(Array.isArray(e)||e.jquery&&!ce.isPlainObject(e))ce.each(e,function(){i(this.name,this.value)});else for(n in e)Pt(n,e[n],t,i);return r.join("&")},ce.fn.extend({serialize:function(){return ce.param(this.serializeArray())},serializeArray:function(){return this.map(function(){var e=ce.prop(this,"elements");return e?ce.makeArray(e):this}).filter(function(){var e=this.type;return this.name&&!ce(this).is(":disabled")&&Ot.test(this.nodeName)&&!Ht.test(e)&&(this.checked||!we.test(e))}).map(function(e,t){var n=ce(this).val();return null==n?null:Array.isArray(n)?ce.map(n,function(e){return{name:t.name,value:e.replace(Lt,"\r\n")}}):{name:t.name,value:n.replace(Lt,"\r\n")}}).get()}});var Mt=/%20/g,Rt=/#.*$/,It=/([?&])_=[^&]*/,Wt=/^(.*?):[ \t]*([^\r\n]*)$/gm,Ft=/^(?:GET|HEAD)$/,$t=/^\/\//,Bt={},_t={},zt="*/".concat("*"),Xt=C.createElement("a");function Ut(o){return function(e,t){"string"!=typeof e&&(t=e,e="*");var n,r=0,i=e.toLowerCase().match(D)||[];if(v(t))while(n=i[r++])"+"===n[0]?(n=n.slice(1)||"*",(o[n]=o[n]||[]).unshift(t)):(o[n]=o[n]||[]).push(t)}}function Vt(t,i,o,a){var s={},u=t===_t;function l(e){var r;return s[e]=!0,ce.each(t[e]||[],function(e,t){var n=t(i,o,a);return"string"!=typeof n||u||s[n]?u?!(r=n):void 0:(i.dataTypes.unshift(n),l(n),!1)}),r}return l(i.dataTypes[0])||!s["*"]&&l("*")}function Gt(e,t){var n,r,i=ce.ajaxSettings.flatOptions||{};for(n in t)void 0!==t[n]&&((i[n]?e:r||(r={}))[n]=t[n]);return r&&ce.extend(!0,e,r),e}Xt.href=Et.href,ce.extend({active:0,lastModified:{},etag:{},ajaxSettings:{url:Et.href,type:"GET",isLocal:/^(?:about|app|app-storage|.+-extension|file|res|widget):$/.test(Et.protocol),global:!0,processData:!0,async:!0,contentType:"application/x-www-form-urlencoded; charset=UTF-8",accepts:{"*":zt,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/\bxml\b/,html:/\bhtml/,json:/\bjson\b/},responseFields:{xml:"responseXML",text:"responseText",json:"responseJSON"},converters:{"* text":String,"text html":!0,"text json":JSON.parse,"text xml":ce.parseXML},flatOptions:{url:!0,context:!0}},ajaxSetup:function(e,t){return t?Gt(Gt(e,ce.ajaxSettings),t):Gt(ce.ajaxSettings,e)},ajaxPrefilter:Ut(Bt),ajaxTransport:Ut(_t),ajax:function(e,t){"object"==typeof e&&(t=e,e=void 0),t=t||{};var c,f,p,n,d,r,h,g,i,o,v=ce.ajaxSetup({},t),y=v.context||v,m=v.context&&(y.nodeType||y.jquery)?ce(y):ce.event,x=ce.Deferred(),b=ce.Callbacks("once memory"),w=v.statusCode||{},a={},s={},u="canceled",T={readyState:0,getResponseHeader:function(e){var t;if(h){if(!n){n={};while(t=Wt.exec(p))n[t[1].toLowerCase()+" "]=(n[t[1].toLowerCase()+" "]||[]).concat(t[2])}t=n[e.toLowerCase()+" "]}return null==t?null:t.join(", ")},getAllResponseHeaders:function(){return h?p:null},setRequestHeader:function(e,t){return null==h&&(e=s[e.toLowerCase()]=s[e.toLowerCase()]||e,a[e]=t),this},overrideMimeType:function(e){return null==h&&(v.mimeType=e),this},statusCode:function(e){var t;if(e)if(h)T.always(e[T.status]);else for(t in e)w[t]=[w[t],e[t]];return this},abort:function(e){var t=e||u;return c&&c.abort(t),l(0,t),this}};if(x.promise(T),v.url=((e||v.url||Et.href)+"").replace($t,Et.protocol+"//"),v.type=t.method||t.type||v.method||v.type,v.dataTypes=(v.dataType||"*").toLowerCase().match(D)||[""],null==v.crossDomain){r=C.createElement("a");try{r.href=v.url,r.href=r.href,v.crossDomain=Xt.protocol+"//"+Xt.host!=r.protocol+"//"+r.host}catch(e){v.crossDomain=!0}}if(v.data&&v.processData&&"string"!=typeof v.data&&(v.data=ce.param(v.data,v.traditional)),Vt(Bt,v,t,T),h)return T;for(i in(g=ce.event&&v.global)&&0==ce.active++&&ce.event.trigger("ajaxStart"),v.type=v.type.toUpperCase(),v.hasContent=!Ft.test(v.type),f=v.url.replace(Rt,""),v.hasContent?v.data&&v.processData&&0===(v.contentType||"").indexOf("application/x-www-form-urlencoded")&&(v.data=v.data.replace(Mt,"+")):(o=v.url.slice(f.length),v.data&&(v.processData||"string"==typeof v.data)&&(f+=(At.test(f)?"&":"?")+v.data,delete v.data),!1===v.cache&&(f=f.replace(It,"$1"),o=(At.test(f)?"&":"?")+"_="+jt.guid+++o),v.url=f+o),v.ifModified&&(ce.lastModified[f]&&T.setRequestHeader("If-Modified-Since",ce.lastModified[f]),ce.etag[f]&&T.setRequestHeader("If-None-Match",ce.etag[f])),(v.data&&v.hasContent&&!1!==v.contentType||t.contentType)&&T.setRequestHeader("Content-Type",v.contentType),T.setRequestHeader("Accept",v.dataTypes[0]&&v.accepts[v.dataTypes[0]]?v.accepts[v.dataTypes[0]]+("*"!==v.dataTypes[0]?", "+zt+"; q=0.01":""):v.accepts["*"]),v.headers)T.setRequestHeader(i,v.headers[i]);if(v.beforeSend&&(!1===v.beforeSend.call(y,T,v)||h))return T.abort();if(u="abort",b.add(v.complete),T.done(v.success),T.fail(v.error),c=Vt(_t,v,t,T)){if(T.readyState=1,g&&m.trigger("ajaxSend",[T,v]),h)return T;v.async&&0<v.timeout&&(d=ie.setTimeout(function(){T.abort("timeout")},v.timeout));try{h=!1,c.send(a,l)}catch(e){if(h)throw e;l(-1,e)}}else l(-1,"No Transport");function l(e,t,n,r){var i,o,a,s,u,l=t;h||(h=!0,d&&ie.clearTimeout(d),c=void 0,p=r||"",T.readyState=0<e?4:0,i=200<=e&&e<300||304===e,n&&(s=function(e,t,n){var r,i,o,a,s=e.contents,u=e.dataTypes;while("*"===u[0])u.shift(),void 0===r&&(r=e.mimeType||t.getResponseHeader("Content-Type"));if(r)for(i in s)if(s[i]&&s[i].test(r)){u.unshift(i);break}if(u[0]in n)o=u[0];else{for(i in n){if(!u[0]||e.converters[i+" "+u[0]]){o=i;break}a||(a=i)}o=o||a}if(o)return o!==u[0]&&u.unshift(o),n[o]}(v,T,n)),!i&&-1<ce.inArray("script",v.dataTypes)&&ce.inArray("json",v.dataTypes)<0&&(v.converters["text script"]=function(){}),s=function(e,t,n,r){var i,o,a,s,u,l={},c=e.dataTypes.slice();if(c[1])for(a in e.converters)l[a.toLowerCase()]=e.converters[a];o=c.shift();while(o)if(e.responseFields[o]&&(n[e.responseFields[o]]=t),!u&&r&&e.dataFilter&&(t=e.dataFilter(t,e.dataType)),u=o,o=c.shift())if("*"===o)o=u;else if("*"!==u&&u!==o){if(!(a=l[u+" "+o]||l["* "+o]))for(i in l)if((s=i.split(" "))[1]===o&&(a=l[u+" "+s[0]]||l["* "+s[0]])){!0===a?a=l[i]:!0!==l[i]&&(o=s[0],c.unshift(s[1]));break}if(!0!==a)if(a&&e["throws"])t=a(t);else try{t=a(t)}catch(e){return{state:"parsererror",error:a?e:"No conversion from "+u+" to "+o}}}return{state:"success",data:t}}(v,s,T,i),i?(v.ifModified&&((u=T.getResponseHeader("Last-Modified"))&&(ce.lastModified[f]=u),(u=T.getResponseHeader("etag"))&&(ce.etag[f]=u)),204===e||"HEAD"===v.type?l="nocontent":304===e?l="notmodified":(l=s.state,o=s.data,i=!(a=s.error))):(a=l,!e&&l||(l="error",e<0&&(e=0))),T.status=e,T.statusText=(t||l)+"",i?x.resolveWith(y,[o,l,T]):x.rejectWith(y,[T,l,a]),T.statusCode(w),w=void 0,g&&m.trigger(i?"ajaxSuccess":"ajaxError",[T,v,i?o:a]),b.fireWith(y,[T,l]),g&&(m.trigger("ajaxComplete",[T,v]),--ce.active||ce.event.trigger("ajaxStop")))}return T},getJSON:function(e,t,n){return ce.get(e,t,n,"json")},getScript:function(e,t){return ce.get(e,void 0,t,"script")}}),ce.each(["get","post"],function(e,i){ce[i]=function(e,t,n,r){return v(t)&&(r=r||n,n=t,t=void 0),ce.ajax(ce.extend({url:e,type:i,dataType:r,data:t,success:n},ce.isPlainObject(e)&&e))}}),ce.ajaxPrefilter(function(e){var t;for(t in e.headers)"content-type"===t.toLowerCase()&&(e.contentType=e.headers[t]||"")}),ce._evalUrl=function(e,t,n){return ce.ajax({url:e,type:"GET",dataType:"script",cache:!0,async:!1,global:!1,converters:{"text script":function(){}},dataFilter:function(e){ce.globalEval(e,t,n)}})},ce.fn.extend({wrapAll:function(e){var t;return this[0]&&(v(e)&&(e=e.call(this[0])),t=ce(e,this[0].ownerDocument).eq(0).clone(!0),this[0].parentNode&&t.insertBefore(this[0]),t.map(function(){var e=this;while(e.firstElementChild)e=e.firstElementChild;return e}).append(this)),this},wrapInner:function(n){return v(n)?this.each(function(e){ce(this).wrapInner(n.call(this,e))}):this.each(function(){var e=ce(this),t=e.contents();t.length?t.wrapAll(n):e.append(n)})},wrap:function(t){var n=v(t);return this.each(function(e){ce(this).wrapAll(n?t.call(this,e):t)})},unwrap:function(e){return this.parent(e).not("body").each(function(){ce(this).replaceWith(this.childNodes)}),this}}),ce.expr.pseudos.hidden=function(e){return!ce.expr.pseudos.visible(e)},ce.expr.pseudos.visible=function(e){return!!(e.offsetWidth||e.offsetHeight||e.getClientRects().length)},ce.ajaxSettings.xhr=function(){try{return new ie.XMLHttpRequest}catch(e){}};var Yt={0:200,1223:204},Qt=ce.ajaxSettings.xhr();le.cors=!!Qt&&"withCredentials"in Qt,le.ajax=Qt=!!Qt,ce.ajaxTransport(function(i){var o,a;if(le.cors||Qt&&!i.crossDomain)return{send:function(e,t){var n,r=i.xhr();if(r.open(i.type,i.url,i.async,i.username,i.password),i.xhrFields)for(n in i.xhrFields)r[n]=i.xhrFields[n];for(n in i.mimeType&&r.overrideMimeType&&r.overrideMimeType(i.mimeType),i.crossDomain||e["X-Requested-With"]||(e["X-Requested-With"]="XMLHttpRequest"),e)r.setRequestHeader(n,e[n]);o=function(e){return function(){o&&(o=a=r.onload=r.onerror=r.onabort=r.ontimeout=r.onreadystatechange=null,"abort"===e?r.abort():"error"===e?"number"!=typeof r.status?t(0,"error"):t(r.status,r.statusText):t(Yt[r.status]||r.status,r.statusText,"text"!==(r.responseType||"text")||"string"!=typeof r.responseText?{binary:r.response}:{text:r.responseText},r.getAllResponseHeaders()))}},r.onload=o(),a=r.onerror=r.ontimeout=o("error"),void 0!==r.onabort?r.onabort=a:r.onreadystatechange=function(){4===r.readyState&&ie.setTimeout(function(){o&&a()})},o=o("abort");try{r.send(i.hasContent&&i.data||null)}catch(e){if(o)throw e}},abort:function(){o&&o()}}}),ce.ajaxPrefilter(function(e){e.crossDomain&&(e.contents.script=!1)}),ce.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/\b(?:java|ecma)script\b/},converters:{"text script":function(e){return ce.globalEval(e),e}}}),ce.ajaxPrefilter("script",function(e){void 0===e.cache&&(e.cache=!1),e.crossDomain&&(e.type="GET")}),ce.ajaxTransport("script",function(n){var r,i;if(n.crossDomain||n.scriptAttrs)return{send:function(e,t){r=ce("<script>").attr(n.scriptAttrs||{}).prop({charset:n.scriptCharset,src:n.url}).on("load error",i=function(e){r.remove(),i=null,e&&t("error"===e.type?404:200,e.type)}),C.head.appendChild(r[0])},abort:function(){i&&i()}}});var Jt,Kt=[],Zt=/(=)\?(?=&|$)|\?\?/;ce.ajaxSetup({jsonp:"callback",jsonpCallback:function(){var e=Kt.pop()||ce.expando+"_"+jt.guid++;return this[e]=!0,e}}),ce.ajaxPrefilter("json jsonp",function(e,t,n){var r,i,o,a=!1!==e.jsonp&&(Zt.test(e.url)?"url":"string"==typeof e.data&&0===(e.contentType||"").indexOf("application/x-www-form-urlencoded")&&Zt.test(e.data)&&"data");if(a||"jsonp"===e.dataTypes[0])return r=e.jsonpCallback=v(e.jsonpCallback)?e.jsonpCallback():e.jsonpCallback,a?e[a]=e[a].replace(Zt,"$1"+r):!1!==e.jsonp&&(e.url+=(At.test(e.url)?"&":"?")+e.jsonp+"="+r),e.converters["script json"]=function(){return o||ce.error(r+" was not called"),o[0]},e.dataTypes[0]="json",i=ie[r],ie[r]=function(){o=arguments},n.always(function(){void 0===i?ce(ie).removeProp(r):ie[r]=i,e[r]&&(e.jsonpCallback=t.jsonpCallback,Kt.push(r)),o&&v(i)&&i(o[0]),o=i=void 0}),"script"}),le.createHTMLDocument=((Jt=C.implementation.createHTMLDocument("").body).innerHTML="<form></form><form></form>",2===Jt.childNodes.length),ce.parseHTML=function(e,t,n){return"string"!=typeof e?[]:("boolean"==typeof t&&(n=t,t=!1),t||(le.createHTMLDocument?((r=(t=C.implementation.createHTMLDocument("")).createElement("base")).href=C.location.href,t.head.appendChild(r)):t=C),o=!n&&[],(i=w.exec(e))?[t.createElement(i[1])]:(i=Ae([e],t,o),o&&o.length&&ce(o).remove(),ce.merge([],i.childNodes)));var r,i,o},ce.fn.load=function(e,t,n){var r,i,o,a=this,s=e.indexOf(" ");return-1<s&&(r=Tt(e.slice(s)),e=e.slice(0,s)),v(t)?(n=t,t=void 0):t&&"object"==typeof t&&(i="POST"),0<a.length&&ce.ajax({url:e,type:i||"GET",dataType:"html",data:t}).done(function(e){o=arguments,a.html(r?ce("<div>").append(ce.parseHTML(e)).find(r):e)}).always(n&&function(e,t){a.each(function(){n.apply(this,o||[e.responseText,t,e])})}),this},ce.expr.pseudos.animated=function(t){return ce.grep(ce.timers,function(e){return t===e.elem}).length},ce.offset={setOffset:function(e,t,n){var r,i,o,a,s,u,l=ce.css(e,"position"),c=ce(e),f={};"static"===l&&(e.style.position="relative"),s=c.offset(),o=ce.css(e,"top"),u=ce.css(e,"left"),("absolute"===l||"fixed"===l)&&-1<(o+u).indexOf("auto")?(a=(r=c.position()).top,i=r.left):(a=parseFloat(o)||0,i=parseFloat(u)||0),v(t)&&(t=t.call(e,n,ce.extend({},s))),null!=t.top&&(f.top=t.top-s.top+a),null!=t.left&&(f.left=t.left-s.left+i),"using"in t?t.using.call(e,f):c.css(f)}},ce.fn.extend({offset:function(t){if(arguments.length)return void 0===t?this:this.each(function(e){ce.offset.setOffset(this,t,e)});var e,n,r=this[0];return r?r.getClientRects().length?(e=r.getBoundingClientRect(),n=r.ownerDocument.defaultView,{top:e.top+n.pageYOffset,left:e.left+n.pageXOffset}):{top:0,left:0}:void 0},position:function(){if(this[0]){var e,t,n,r=this[0],i={top:0,left:0};if("fixed"===ce.css(r,"position"))t=r.getBoundingClientRect();else{t=this.offset(),n=r.ownerDocument,e=r.offsetParent||n.documentElement;while(e&&(e===n.body||e===n.documentElement)&&"static"===ce.css(e,"position"))e=e.parentNode;e&&e!==r&&1===e.nodeType&&((i=ce(e).offset()).top+=ce.css(e,"borderTopWidth",!0),i.left+=ce.css(e,"borderLeftWidth",!0))}return{top:t.top-i.top-ce.css(r,"marginTop",!0),left:t.left-i.left-ce.css(r,"marginLeft",!0)}}},offsetParent:function(){return this.map(function(){var e=this.offsetParent;while(e&&"static"===ce.css(e,"position"))e=e.offsetParent;return e||J})}}),ce.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(t,i){var o="pageYOffset"===i;ce.fn[t]=function(e){return M(this,function(e,t,n){var r;if(y(e)?r=e:9===e.nodeType&&(r=e.defaultView),void 0===n)return r?r[i]:e[t];r?r.scrollTo(o?r.pageXOffset:n,o?n:r.pageYOffset):e[t]=n},t,e,arguments.length)}}),ce.each(["top","left"],function(e,n){ce.cssHooks[n]=Ye(le.pixelPosition,function(e,t){if(t)return t=Ge(e,n),_e.test(t)?ce(e).position()[n]+"px":t})}),ce.each({Height:"height",Width:"width"},function(a,s){ce.each({padding:"inner"+a,content:s,"":"outer"+a},function(r,o){ce.fn[o]=function(e,t){var n=arguments.length&&(r||"boolean"!=typeof e),i=r||(!0===e||!0===t?"margin":"border");return M(this,function(e,t,n){var r;return y(e)?0===o.indexOf("outer")?e["inner"+a]:e.document.documentElement["client"+a]:9===e.nodeType?(r=e.documentElement,Math.max(e.body["scroll"+a],r["scroll"+a],e.body["offset"+a],r["offset"+a],r["client"+a])):void 0===n?ce.css(e,t,i):ce.style(e,t,n,i)},s,n?e:void 0,n)}})}),ce.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(e,t){ce.fn[t]=function(e){return this.on(t,e)}}),ce.fn.extend({bind:function(e,t,n){return this.on(e,null,t,n)},unbind:function(e,t){return this.off(e,null,t)},delegate:function(e,t,n,r){return this.on(t,e,n,r)},undelegate:function(e,t,n){return 1===arguments.length?this.off(e,"**"):this.off(t,e||"**",n)},hover:function(e,t){return this.on("mouseenter",e).on("mouseleave",t||e)}}),ce.each("blur focus focusin focusout resize scroll click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup contextmenu".split(" "),function(e,n){ce.fn[n]=function(e,t){return 0<arguments.length?this.on(n,null,e,t):this.trigger(n)}});var en=/^[\s\uFEFF\xA0]+|([^\s\uFEFF\xA0])[\s\uFEFF\xA0]+$/g;ce.proxy=function(e,t){var n,r,i;if("string"==typeof t&&(n=e[t],t=e,e=n),v(e))return r=ae.call(arguments,2),(i=function(){return e.apply(t||this,r.concat(ae.call(arguments)))}).guid=e.guid=e.guid||ce.guid++,i},ce.holdReady=function(e){e?ce.readyWait++:ce.ready(!0)},ce.isArray=Array.isArray,ce.parseJSON=JSON.parse,ce.nodeName=fe,ce.isFunction=v,ce.isWindow=y,ce.camelCase=F,ce.type=x,ce.now=Date.now,ce.isNumeric=function(e){var t=ce.type(e);return("number"===t||"string"===t)&&!isNaN(e-parseFloat(e))},ce.trim=function(e){return null==e?"":(e+"").replace(en,"$1")},"function"==typeof define&&define.amd&&define("jquery",[],function(){return ce});var tn=ie.jQuery,nn=ie.$;return ce.noConflict=function(e){return ie.$===ce&&(ie.$=nn),e&&ie.jQuery===ce&&(ie.jQuery=tn),ce},"undefined"==typeof e&&(ie.jQuery=ie.$=ce),ce});

/*!
  Highlight.js v11.10.0 (git: 366a8bd012)
  (c) 2006-2024 Josh Goebel <hello@joshgoebel.com> and other contributors
  License: BSD-3-Clause
  php-template, javascript, sql, shell, css, plaintext, xml, yaml
 */
var hljs=function(){"use strict";function e(t){
        return t instanceof Map?t.clear=t.delete=t.set=()=>{
            throw Error("map is read-only")}:t instanceof Set&&(t.add=t.clear=t.delete=()=>{
            throw Error("set is read-only")
        }),Object.freeze(t),Object.getOwnPropertyNames(t).forEach((n=>{
            const i=t[n],s=typeof i;"object"!==s&&"function"!==s||Object.isFrozen(i)||e(i)
        })),t}class t{constructor(e){
        void 0===e.data&&(e.data={}),this.data=e.data,this.isMatchIgnored=!1}
        ignoreMatch(){this.isMatchIgnored=!0}}function n(e){
        return e.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#x27;")
    }function i(e,...t){const n=Object.create(null);for(const t in e)n[t]=e[t]
    ;return t.forEach((e=>{for(const t in e)n[t]=e[t]})),n}const s=e=>!!e.scope
    ;class o{constructor(e,t){
        this.buffer="",this.classPrefix=t.classPrefix,e.walk(this)}addText(e){
        this.buffer+=n(e)}openNode(e){if(!s(e))return;const t=((e,{prefix:t})=>{
        if(e.startsWith("language:"))return e.replace("language:","language-")
            ;if(e.includes(".")){const n=e.split(".")
        ;return[`${t}${n.shift()}`,...n.map(((e,t)=>`${e}${"_".repeat(t+1)}`))].join(" ")
        }return`${t}${e}`})(e.scope,{prefix:this.classPrefix});this.span(t)}
        closeNode(e){s(e)&&(this.buffer+="</span>")}value(){return this.buffer}span(e){
            this.buffer+=`<span class="${e}">`}}const r=(e={})=>{const t={children:[]}
    ;return Object.assign(t,e),t};class a{constructor(){
        this.rootNode=r(),this.stack=[this.rootNode]}get top(){
        return this.stack[this.stack.length-1]}get root(){return this.rootNode}add(e){
        this.top.children.push(e)}openNode(e){const t=r({scope:e})
    ;this.add(t),this.stack.push(t)}closeNode(){
        if(this.stack.length>1)return this.stack.pop()}closeAllNodes(){
        for(;this.closeNode(););}toJSON(){return JSON.stringify(this.rootNode,null,4)}
        walk(e){return this.constructor._walk(e,this.rootNode)}static _walk(e,t){
            return"string"==typeof t?e.addText(t):t.children&&(e.openNode(t),
                t.children.forEach((t=>this._walk(e,t))),e.closeNode(t)),e}static _collapse(e){
            "string"!=typeof e&&e.children&&(e.children.every((e=>"string"==typeof e))?e.children=[e.children.join("")]:e.children.forEach((e=>{
                a._collapse(e)})))}}class c extends a{constructor(e){super(),this.options=e}
        addText(e){""!==e&&this.add(e)}startScope(e){this.openNode(e)}endScope(){
            this.closeNode()}__addSublanguage(e,t){const n=e.root
        ;t&&(n.scope="language:"+t),this.add(n)}toHTML(){
            return new o(this,this.options).value()}finalize(){
            return this.closeAllNodes(),!0}}function l(e){
        return e?"string"==typeof e?e:e.source:null}function g(e){return h("(?=",e,")")}
        function u(e){return h("(?:",e,")*")}function d(e){return h("(?:",e,")?")}
        function h(...e){return e.map((e=>l(e))).join("")}function f(...e){const t=(e=>{
            const t=e[e.length-1]
            ;return"object"==typeof t&&t.constructor===Object?(e.splice(e.length-1,1),t):{}
        })(e);return"("+(t.capture?"":"?:")+e.map((e=>l(e))).join("|")+")"}
        function p(e){return RegExp(e.toString()+"|").exec("").length-1}
        const b=/\[(?:[^\\\]]|\\.)*\]|\(\??|\\([1-9][0-9]*)|\\./
        ;function m(e,{joinWith:t}){let n=0;return e.map((e=>{n+=1;const t=n
        ;let i=l(e),s="";for(;i.length>0;){const e=b.exec(i);if(!e){s+=i;break}
            s+=i.substring(0,e.index),
                i=i.substring(e.index+e[0].length),"\\"===e[0][0]&&e[1]?s+="\\"+(Number(e[1])+t):(s+=e[0],
            "("===e[0]&&n++)}return s})).map((e=>`(${e})`)).join(t)}
        const E="[a-zA-Z]\\w*",x="[a-zA-Z_]\\w*",w="\\b\\d+(\\.\\d+)?",y="(-?)(\\b0[xX][a-fA-F0-9]+|(\\b\\d+(\\.\\d*)?|\\.\\d+)([eE][-+]?\\d+)?)",_="\\b(0b[01]+)",O={
            begin:"\\\\[\\s\\S]",relevance:0},v={scope:"string",begin:"'",end:"'",
            illegal:"\\n",contains:[O]},k={scope:"string",begin:'"',end:'"',illegal:"\\n",
            contains:[O]},N=(e,t,n={})=>{const s=i({scope:"comment",begin:e,end:t,
            contains:[]},n);s.contains.push({scope:"doctag",
            begin:"[ ]*(?=(TODO|FIXME|NOTE|BUG|OPTIMIZE|HACK|XXX):)",
            end:/(TODO|FIXME|NOTE|BUG|OPTIMIZE|HACK|XXX):/,excludeBegin:!0,relevance:0})
        ;const o=f("I","a","is","so","us","to","at","if","in","it","on",/[A-Za-z]+['](d|ve|re|ll|t|s|n)/,/[A-Za-z]+[-][a-z]+/,/[A-Za-z][a-z]{2,}/)
        ;return s.contains.push({begin:h(/[ ]+/,"(",o,/[.]?[:]?([.][ ]|[ ])/,"){3}")}),s
        },S=N("//","$"),M=N("/\\*","\\*/"),R=N("#","$");var j=Object.freeze({
            __proto__:null,APOS_STRING_MODE:v,BACKSLASH_ESCAPE:O,BINARY_NUMBER_MODE:{
                scope:"number",begin:_,relevance:0},BINARY_NUMBER_RE:_,COMMENT:N,
            C_BLOCK_COMMENT_MODE:M,C_LINE_COMMENT_MODE:S,C_NUMBER_MODE:{scope:"number",
                begin:y,relevance:0},C_NUMBER_RE:y,END_SAME_AS_BEGIN:e=>Object.assign(e,{
                "on:begin":(e,t)=>{t.data._beginMatch=e[1]},"on:end":(e,t)=>{
                    t.data._beginMatch!==e[1]&&t.ignoreMatch()}}),HASH_COMMENT_MODE:R,IDENT_RE:E,
            MATCH_NOTHING_RE:/\b\B/,METHOD_GUARD:{begin:"\\.\\s*"+x,relevance:0},
            NUMBER_MODE:{scope:"number",begin:w,relevance:0},NUMBER_RE:w,
            PHRASAL_WORDS_MODE:{
                begin:/\b(a|an|the|are|I'm|isn't|don't|doesn't|won't|but|just|should|pretty|simply|enough|gonna|going|wtf|so|such|will|you|your|they|like|more)\b/
            },QUOTE_STRING_MODE:k,REGEXP_MODE:{scope:"regexp",begin:/\/(?=[^/\n]*\/)/,
                end:/\/[gimuy]*/,contains:[O,{begin:/\[/,end:/\]/,relevance:0,contains:[O]}]},
            RE_STARTERS_RE:"!|!=|!==|%|%=|&|&&|&=|\\*|\\*=|\\+|\\+=|,|-|-=|/=|/|:|;|<<|<<=|<=|<|===|==|=|>>>=|>>=|>=|>>>|>>|>|\\?|\\[|\\{|\\(|\\^|\\^=|\\||\\|=|\\|\\||~",
            SHEBANG:(e={})=>{const t=/^#![ ]*\//
            ;return e.binary&&(e.begin=h(t,/.*\b/,e.binary,/\b.*/)),i({scope:"meta",begin:t,
                end:/$/,relevance:0,"on:begin":(e,t)=>{0!==e.index&&t.ignoreMatch()}},e)},
            TITLE_MODE:{scope:"title",begin:E,relevance:0},UNDERSCORE_IDENT_RE:x,
            UNDERSCORE_TITLE_MODE:{scope:"title",begin:x,relevance:0}});function A(e,t){
            "."===e.input[e.index-1]&&t.ignoreMatch()}function I(e,t){
            void 0!==e.className&&(e.scope=e.className,delete e.className)}function T(e,t){
            t&&e.beginKeywords&&(e.begin="\\b("+e.beginKeywords.split(" ").join("|")+")(?!\\.)(?=\\b|\\s)",
                e.__beforeBegin=A,e.keywords=e.keywords||e.beginKeywords,delete e.beginKeywords,
            void 0===e.relevance&&(e.relevance=0))}function L(e,t){
            Array.isArray(e.illegal)&&(e.illegal=f(...e.illegal))}function B(e,t){
            if(e.match){
                if(e.begin||e.end)throw Error("begin & end are not supported with match")
                    ;e.begin=e.match,delete e.match}}function P(e,t){
            void 0===e.relevance&&(e.relevance=1)}const D=(e,t)=>{if(!e.beforeMatch)return
                ;if(e.starts)throw Error("beforeMatch cannot be used with starts")
                ;const n=Object.assign({},e);Object.keys(e).forEach((t=>{delete e[t]
            })),e.keywords=n.keywords,e.begin=h(n.beforeMatch,g(n.begin)),e.starts={
                relevance:0,contains:[Object.assign(n,{endsParent:!0})]
            },e.relevance=0,delete n.beforeMatch
            },H=["of","and","for","in","not","or","if","then","parent","list","value"],C="keyword"
        ;function $(e,t,n=C){const i=Object.create(null)
        ;return"string"==typeof e?s(n,e.split(" ")):Array.isArray(e)?s(n,e):Object.keys(e).forEach((n=>{
            Object.assign(i,$(e[n],t,n))})),i;function s(e,n){
            t&&(n=n.map((e=>e.toLowerCase()))),n.forEach((t=>{const n=t.split("|")
            ;i[n[0]]=[e,U(n[0],n[1])]}))}}function U(e,t){
            return t?Number(t):(e=>H.includes(e.toLowerCase()))(e)?0:1}const z={},W=e=>{
            console.error(e)},X=(e,...t)=>{console.log("WARN: "+e,...t)},G=(e,t)=>{
            z[`${e}/${t}`]||(console.log(`Deprecated as of ${e}. ${t}`),z[`${e}/${t}`]=!0)
        },K=Error();function F(e,t,{key:n}){let i=0;const s=e[n],o={},r={}
        ;for(let e=1;e<=t.length;e++)r[e+i]=s[e],o[e+i]=!0,i+=p(t[e-1])
        ;e[n]=r,e[n]._emit=o,e[n]._multi=!0}function Z(e){(e=>{
            e.scope&&"object"==typeof e.scope&&null!==e.scope&&(e.beginScope=e.scope,
                delete e.scope)})(e),"string"==typeof e.beginScope&&(e.beginScope={
            _wrap:e.beginScope}),"string"==typeof e.endScope&&(e.endScope={_wrap:e.endScope
        }),(e=>{if(Array.isArray(e.begin)){
            if(e.skip||e.excludeBegin||e.returnBegin)throw W("skip, excludeBegin, returnBegin not compatible with beginScope: {}"),
                K
                ;if("object"!=typeof e.beginScope||null===e.beginScope)throw W("beginScope must be object"),
                K;F(e,e.begin,{key:"beginScope"}),e.begin=m(e.begin,{joinWith:""})}})(e),(e=>{
            if(Array.isArray(e.end)){
                if(e.skip||e.excludeEnd||e.returnEnd)throw W("skip, excludeEnd, returnEnd not compatible with endScope: {}"),
                    K
                    ;if("object"!=typeof e.endScope||null===e.endScope)throw W("endScope must be object"),
                    K;F(e,e.end,{key:"endScope"}),e.end=m(e.end,{joinWith:""})}})(e)}function V(e){
            function t(t,n){
                return RegExp(l(t),"m"+(e.case_insensitive?"i":"")+(e.unicodeRegex?"u":"")+(n?"g":""))
            }class n{constructor(){
                this.matchIndexes={},this.regexes=[],this.matchAt=1,this.position=0}
                addRule(e,t){
                    t.position=this.position++,this.matchIndexes[this.matchAt]=t,this.regexes.push([t,e]),
                        this.matchAt+=p(e)+1}compile(){0===this.regexes.length&&(this.exec=()=>null)
                ;const e=this.regexes.map((e=>e[1]));this.matcherRe=t(m(e,{joinWith:"|"
                }),!0),this.lastIndex=0}exec(e){this.matcherRe.lastIndex=this.lastIndex
                ;const t=this.matcherRe.exec(e);if(!t)return null
                    ;const n=t.findIndex(((e,t)=>t>0&&void 0!==e)),i=this.matchIndexes[n]
                ;return t.splice(0,n),Object.assign(t,i)}}class s{constructor(){
                this.rules=[],this.multiRegexes=[],
                    this.count=0,this.lastIndex=0,this.regexIndex=0}getMatcher(e){
                if(this.multiRegexes[e])return this.multiRegexes[e];const t=new n
                ;return this.rules.slice(e).forEach((([e,n])=>t.addRule(e,n))),
                    t.compile(),this.multiRegexes[e]=t,t}resumingScanAtSamePosition(){
                return 0!==this.regexIndex}considerAll(){this.regexIndex=0}addRule(e,t){
                this.rules.push([e,t]),"begin"===t.type&&this.count++}exec(e){
                const t=this.getMatcher(this.regexIndex);t.lastIndex=this.lastIndex
                ;let n=t.exec(e)
                ;if(this.resumingScanAtSamePosition())if(n&&n.index===this.lastIndex);else{
                    const t=this.getMatcher(0);t.lastIndex=this.lastIndex+1,n=t.exec(e)}
                return n&&(this.regexIndex+=n.position+1,
                this.regexIndex===this.count&&this.considerAll()),n}}
            if(e.compilerExtensions||(e.compilerExtensions=[]),
            e.contains&&e.contains.includes("self"))throw Error("ERR: contains `self` is not supported at the top-level of a language.  See documentation.")
                ;return e.classNameAliases=i(e.classNameAliases||{}),function n(o,r){const a=o
            ;if(o.isCompiled)return a
                ;[I,B,Z,D].forEach((e=>e(o,r))),e.compilerExtensions.forEach((e=>e(o,r))),
                o.__beforeBegin=null,[T,L,P].forEach((e=>e(o,r))),o.isCompiled=!0;let c=null
            ;return"object"==typeof o.keywords&&o.keywords.$pattern&&(o.keywords=Object.assign({},o.keywords),
                c=o.keywords.$pattern,
                delete o.keywords.$pattern),c=c||/\w+/,o.keywords&&(o.keywords=$(o.keywords,e.case_insensitive)),
                a.keywordPatternRe=t(c,!0),
            r&&(o.begin||(o.begin=/\B|\b/),a.beginRe=t(a.begin),o.end||o.endsWithParent||(o.end=/\B|\b/),
            o.end&&(a.endRe=t(a.end)),
                a.terminatorEnd=l(a.end)||"",o.endsWithParent&&r.terminatorEnd&&(a.terminatorEnd+=(o.end?"|":"")+r.terminatorEnd)),
            o.illegal&&(a.illegalRe=t(o.illegal)),
            o.contains||(o.contains=[]),o.contains=[].concat(...o.contains.map((e=>(e=>(e.variants&&!e.cachedVariants&&(e.cachedVariants=e.variants.map((t=>i(e,{
                variants:null},t)))),e.cachedVariants?e.cachedVariants:q(e)?i(e,{
                starts:e.starts?i(e.starts):null
            }):Object.isFrozen(e)?i(e):e))("self"===e?o:e)))),o.contains.forEach((e=>{n(e,a)
            })),o.starts&&n(o.starts,r),a.matcher=(e=>{const t=new s
            ;return e.contains.forEach((e=>t.addRule(e.begin,{rule:e,type:"begin"
            }))),e.terminatorEnd&&t.addRule(e.terminatorEnd,{type:"end"
            }),e.illegal&&t.addRule(e.illegal,{type:"illegal"}),t})(a),a}(e)}function q(e){
            return!!e&&(e.endsWithParent||q(e.starts))}class J extends Error{
            constructor(e,t){super(e),this.name="HTMLInjectionError",this.html=t}}
        const Y=n,Q=i,ee=Symbol("nomatch"),te=n=>{
            const i=Object.create(null),s=Object.create(null),o=[];let r=!0
            ;const a="Could not find the language '{}', did you forget to load/include a language module?",l={
                disableAutodetect:!0,name:"Plain text",contains:[]};let p={
                ignoreUnescapedHTML:!1,throwUnescapedHTML:!1,noHighlightRe:/^(no-?highlight)$/i,
                languageDetectRe:/\blang(?:uage)?-([\w-]+)\b/i,classPrefix:"hljs-",
                cssSelector:"pre code",languages:null,__emitter:c};function b(e){
                return p.noHighlightRe.test(e)}function m(e,t,n){let i="",s=""
            ;"object"==typeof t?(i=e,
                n=t.ignoreIllegals,s=t.language):(G("10.7.0","highlight(lang, code, ...args) has been deprecated."),
                G("10.7.0","Please use highlight(code, options) instead.\nhttps://github.com/highlightjs/highlight.js/issues/2277"),
                s=e,i=t),void 0===n&&(n=!0);const o={code:i,language:s};N("before:highlight",o)
            ;const r=o.result?o.result:E(o.language,o.code,n)
            ;return r.code=o.code,N("after:highlight",r),r}function E(e,n,s,o){
                const c=Object.create(null);function l(){if(!N.keywords)return void M.addText(R)
                    ;let e=0;N.keywordPatternRe.lastIndex=0;let t=N.keywordPatternRe.exec(R),n=""
                ;for(;t;){n+=R.substring(e,t.index)
                ;const s=_.case_insensitive?t[0].toLowerCase():t[0],o=(i=s,N.keywords[i]);if(o){
                    const[e,i]=o
                    ;if(M.addText(n),n="",c[s]=(c[s]||0)+1,c[s]<=7&&(j+=i),e.startsWith("_"))n+=t[0];else{
                        const n=_.classNameAliases[e]||e;u(t[0],n)}}else n+=t[0]
                ;e=N.keywordPatternRe.lastIndex,t=N.keywordPatternRe.exec(R)}var i
                ;n+=R.substring(e),M.addText(n)}function g(){null!=N.subLanguage?(()=>{
                    if(""===R)return;let e=null;if("string"==typeof N.subLanguage){
                        if(!i[N.subLanguage])return void M.addText(R)
                            ;e=E(N.subLanguage,R,!0,S[N.subLanguage]),S[N.subLanguage]=e._top
                    }else e=x(R,N.subLanguage.length?N.subLanguage:null)
                    ;N.relevance>0&&(j+=e.relevance),M.__addSublanguage(e._emitter,e.language)
                })():l(),R=""}function u(e,t){
                    ""!==e&&(M.startScope(t),M.addText(e),M.endScope())}function d(e,t){let n=1
                ;const i=t.length-1;for(;n<=i;){if(!e._emit[n]){n++;continue}
                    const i=_.classNameAliases[e[n]]||e[n],s=t[n];i?u(s,i):(R=s,l(),R=""),n++}}
                function h(e,t){
                    return e.scope&&"string"==typeof e.scope&&M.openNode(_.classNameAliases[e.scope]||e.scope),
                    e.beginScope&&(e.beginScope._wrap?(u(R,_.classNameAliases[e.beginScope._wrap]||e.beginScope._wrap),
                        R=""):e.beginScope._multi&&(d(e.beginScope,t),R="")),N=Object.create(e,{parent:{
                            value:N}}),N}function f(e,n,i){let s=((e,t)=>{const n=e&&e.exec(t)
                ;return n&&0===n.index})(e.endRe,i);if(s){if(e["on:end"]){const i=new t(e)
                ;e["on:end"](n,i),i.isMatchIgnored&&(s=!1)}if(s){
                    for(;e.endsParent&&e.parent;)e=e.parent;return e}}
                    if(e.endsWithParent)return f(e.parent,n,i)}function b(e){
                    return 0===N.matcher.regexIndex?(R+=e[0],1):(T=!0,0)}function m(e){
                    const t=e[0],i=n.substring(e.index),s=f(N,e,i);if(!s)return ee;const o=N
                    ;N.endScope&&N.endScope._wrap?(g(),
                        u(t,N.endScope._wrap)):N.endScope&&N.endScope._multi?(g(),
                        d(N.endScope,e)):o.skip?R+=t:(o.returnEnd||o.excludeEnd||(R+=t),
                        g(),o.excludeEnd&&(R=t));do{
                        N.scope&&M.closeNode(),N.skip||N.subLanguage||(j+=N.relevance),N=N.parent
                    }while(N!==s.parent);return s.starts&&h(s.starts,e),o.returnEnd?0:t.length}
                let w={};function y(i,o){const a=o&&o[0];if(R+=i,null==a)return g(),0
                    ;if("begin"===w.type&&"end"===o.type&&w.index===o.index&&""===a){
                    if(R+=n.slice(o.index,o.index+1),!r){const t=Error(`0 width match regex (${e})`)
                    ;throw t.languageName=e,t.badRule=w.rule,t}return 1}
                    if(w=o,"begin"===o.type)return(e=>{
                        const n=e[0],i=e.rule,s=new t(i),o=[i.__beforeBegin,i["on:begin"]]
                        ;for(const t of o)if(t&&(t(e,s),s.isMatchIgnored))return b(n)
                            ;return i.skip?R+=n:(i.excludeBegin&&(R+=n),
                            g(),i.returnBegin||i.excludeBegin||(R=n)),h(i,e),i.returnBegin?0:n.length})(o)
                        ;if("illegal"===o.type&&!s){
                        const e=Error('Illegal lexeme "'+a+'" for mode "'+(N.scope||"<unnamed>")+'"')
                        ;throw e.mode=N,e}if("end"===o.type){const e=m(o);if(e!==ee)return e}
                    if("illegal"===o.type&&""===a)return 1
                        ;if(I>1e5&&I>3*o.index)throw Error("potential infinite loop, way more iterations than matches")
                        ;return R+=a,a.length}const _=O(e)
                ;if(!_)throw W(a.replace("{}",e)),Error('Unknown language: "'+e+'"')
                    ;const v=V(_);let k="",N=o||v;const S={},M=new p.__emitter(p);(()=>{const e=[]
                ;for(let t=N;t!==_;t=t.parent)t.scope&&e.unshift(t.scope)
                ;e.forEach((e=>M.openNode(e)))})();let R="",j=0,A=0,I=0,T=!1;try{
                    if(_.__emitTokens)_.__emitTokens(n,M);else{for(N.matcher.considerAll();;){
                        I++,T?T=!1:N.matcher.considerAll(),N.matcher.lastIndex=A
                        ;const e=N.matcher.exec(n);if(!e)break;const t=y(n.substring(A,e.index),e)
                        ;A=e.index+t}y(n.substring(A))}return M.finalize(),k=M.toHTML(),{language:e,
                        value:k,relevance:j,illegal:!1,_emitter:M,_top:N}}catch(t){
                    if(t.message&&t.message.includes("Illegal"))return{language:e,value:Y(n),
                        illegal:!0,relevance:0,_illegalBy:{message:t.message,index:A,
                            context:n.slice(A-100,A+100),mode:t.mode,resultSoFar:k},_emitter:M};if(r)return{
                        language:e,value:Y(n),illegal:!1,relevance:0,errorRaised:t,_emitter:M,_top:N}
                        ;throw t}}function x(e,t){t=t||p.languages||Object.keys(i);const n=(e=>{
                const t={value:Y(e),illegal:!1,relevance:0,_top:l,_emitter:new p.__emitter(p)}
                ;return t._emitter.addText(e),t})(e),s=t.filter(O).filter(k).map((t=>E(t,e,!1)))
            ;s.unshift(n);const o=s.sort(((e,t)=>{
                if(e.relevance!==t.relevance)return t.relevance-e.relevance
                    ;if(e.language&&t.language){if(O(e.language).supersetOf===t.language)return 1
                    ;if(O(t.language).supersetOf===e.language)return-1}return 0})),[r,a]=o,c=r
            ;return c.secondBest=a,c}function w(e){let t=null;const n=(e=>{
                let t=e.className+" ";t+=e.parentNode?e.parentNode.className:""
                ;const n=p.languageDetectRe.exec(t);if(n){const t=O(n[1])
                ;return t||(X(a.replace("{}",n[1])),
                    X("Falling back to no-highlight mode for this block.",e)),t?n[1]:"no-highlight"}
                return t.split(/\s+/).find((e=>b(e)||O(e)))})(e);if(b(n))return
                ;if(N("before:highlightElement",{el:e,language:n
            }),e.dataset.highlighted)return void console.log("Element previously highlighted. To highlight again, first unset `dataset.highlighted`.",e)
                ;if(e.children.length>0&&(p.ignoreUnescapedHTML||(console.warn("One of your code blocks includes unescaped HTML. This is a potentially serious security risk."),
                console.warn("https://github.com/highlightjs/highlight.js/wiki/security"),
                console.warn("The element with unescaped HTML:"),
                console.warn(e)),p.throwUnescapedHTML))throw new J("One of your code blocks includes unescaped HTML.",e.innerHTML)
                ;t=e;const i=t.textContent,o=n?m(i,{language:n,ignoreIllegals:!0}):x(i)
            ;e.innerHTML=o.value,e.dataset.highlighted="yes",((e,t,n)=>{const i=t&&s[t]||n
            ;e.classList.add("hljs"),e.classList.add("language-"+i)
            })(e,n,o.language),e.result={language:o.language,re:o.relevance,
                relevance:o.relevance},o.secondBest&&(e.secondBest={
                language:o.secondBest.language,relevance:o.secondBest.relevance
            }),N("after:highlightElement",{el:e,result:o,text:i})}let y=!1;function _(){
                "loading"!==document.readyState?document.querySelectorAll(p.cssSelector).forEach(w):y=!0
            }function O(e){return e=(e||"").toLowerCase(),i[e]||i[s[e]]}
            function v(e,{languageName:t}){"string"==typeof e&&(e=[e]),e.forEach((e=>{
                s[e.toLowerCase()]=t}))}function k(e){const t=O(e)
            ;return t&&!t.disableAutodetect}function N(e,t){const n=e;o.forEach((e=>{
                e[n]&&e[n](t)}))}
            "undefined"!=typeof window&&window.addEventListener&&window.addEventListener("DOMContentLoaded",(()=>{
                y&&_()}),!1),Object.assign(n,{highlight:m,highlightAuto:x,highlightAll:_,
                highlightElement:w,
                highlightBlock:e=>(G("10.7.0","highlightBlock will be removed entirely in v12.0"),
                    G("10.7.0","Please use highlightElement now."),w(e)),configure:e=>{p=Q(p,e)},
                initHighlighting:()=>{
                    _(),G("10.6.0","initHighlighting() deprecated.  Use highlightAll() now.")},
                initHighlightingOnLoad:()=>{
                    _(),G("10.6.0","initHighlightingOnLoad() deprecated.  Use highlightAll() now.")
                },registerLanguage:(e,t)=>{let s=null;try{s=t(n)}catch(t){
                    if(W("Language definition for '{}' could not be registered.".replace("{}",e)),
                        !r)throw t;W(t),s=l}
                    s.name||(s.name=e),i[e]=s,s.rawDefinition=t.bind(null,n),s.aliases&&v(s.aliases,{
                        languageName:e})},unregisterLanguage:e=>{delete i[e]
                ;for(const t of Object.keys(s))s[t]===e&&delete s[t]},
                listLanguages:()=>Object.keys(i),getLanguage:O,registerAliases:v,
                autoDetection:k,inherit:Q,addPlugin:e=>{(e=>{
                    e["before:highlightBlock"]&&!e["before:highlightElement"]&&(e["before:highlightElement"]=t=>{
                        e["before:highlightBlock"](Object.assign({block:t.el},t))
                    }),e["after:highlightBlock"]&&!e["after:highlightElement"]&&(e["after:highlightElement"]=t=>{
                        e["after:highlightBlock"](Object.assign({block:t.el},t))})})(e),o.push(e)},
                removePlugin:e=>{const t=o.indexOf(e);-1!==t&&o.splice(t,1)}}),n.debugMode=()=>{
                r=!1},n.safeMode=()=>{r=!0},n.versionString="11.10.0",n.regex={concat:h,
                lookahead:g,either:f,optional:d,anyNumberOfTimes:u}
            ;for(const t in j)"object"==typeof j[t]&&e(j[t]);return Object.assign(n,j),n
        },ne=te({});return ne.newInstance=()=>te({}),ne}()
;"object"==typeof exports&&"undefined"!=typeof module&&(module.exports=hljs);/*! `css` grammar compiled for Highlight.js 11.10.0 */
(()=>{var e=(()=>{"use strict"
    ;const e=["a","abbr","address","article","aside","audio","b","blockquote","body","button","canvas","caption","cite","code","dd","del","details","dfn","div","dl","dt","em","fieldset","figcaption","figure","footer","form","h1","h2","h3","h4","h5","h6","header","hgroup","html","i","iframe","img","input","ins","kbd","label","legend","li","main","mark","menu","nav","object","ol","optgroup","option","p","picture","q","quote","samp","section","select","source","span","strong","summary","sup","table","tbody","td","textarea","tfoot","th","thead","time","tr","ul","var","video","defs","g","marker","mask","pattern","svg","switch","symbol","feBlend","feColorMatrix","feComponentTransfer","feComposite","feConvolveMatrix","feDiffuseLighting","feDisplacementMap","feFlood","feGaussianBlur","feImage","feMerge","feMorphology","feOffset","feSpecularLighting","feTile","feTurbulence","linearGradient","radialGradient","stop","circle","ellipse","image","line","path","polygon","polyline","rect","text","use","textPath","tspan","foreignObject","clipPath"],r=["any-hover","any-pointer","aspect-ratio","color","color-gamut","color-index","device-aspect-ratio","device-height","device-width","display-mode","forced-colors","grid","height","hover","inverted-colors","monochrome","orientation","overflow-block","overflow-inline","pointer","prefers-scheme","prefers-contrast","prefers-reduced-motion","prefers-reduced-transparency","resolution","scan","scripting","update","width","min-width","max-width","min-height","max-height"].sort().reverse(),t=["active","any-link","blank","checked","current","default","defined","dir","disabled","drop","empty","enabled","first","first-child","first-of-type","fullscreen","future","focus","focus-visible","focus-within","has","host","host-context","hover","indeterminate","in-range","invalid","is","lang","last-child","last-of-type","left","link","local-link","not","nth-child","nth-col","nth-last-child","nth-last-col","nth-last-of-type","nth-of-type","only-child","only-of-type","optional","out-of-range","past","placeholder-shown","read-only","read-write","required","right","root","scope","target","target-within","user-invalid","valid","visited","where"].sort().reverse(),i=["after","backdrop","before","cue","cue-region","first-letter","first-line","grammar-error","marker","part","placeholder","selection","slotted","spelling-error"].sort().reverse(),o=["accent-color","align-content","align-items","align-self","alignment-baseline","all","animation","animation-delay","animation-direction","animation-duration","animation-fill-mode","animation-iteration-count","animation-name","animation-play-state","animation-timing-function","appearance","backface-visibility","background","background-attachment","background-blend-mode","background-clip","background-color","background-image","background-origin","background-position","background-repeat","background-size","baseline-shift","block-size","border","border-block","border-block-color","border-block-end","border-block-end-color","border-block-end-style","border-block-end-width","border-block-start","border-block-start-color","border-block-start-style","border-block-start-width","border-block-style","border-block-width","border-bottom","border-bottom-color","border-bottom-left-radius","border-bottom-right-radius","border-bottom-style","border-bottom-width","border-collapse","border-color","border-image","border-image-outset","border-image-repeat","border-image-slice","border-image-source","border-image-width","border-inline","border-inline-color","border-inline-end","border-inline-end-color","border-inline-end-style","border-inline-end-width","border-inline-start","border-inline-start-color","border-inline-start-style","border-inline-start-width","border-inline-style","border-inline-width","border-left","border-left-color","border-left-style","border-left-width","border-radius","border-right","border-end-end-radius","border-end-start-radius","border-right-color","border-right-style","border-right-width","border-spacing","border-start-end-radius","border-start-start-radius","border-style","border-top","border-top-color","border-top-left-radius","border-top-right-radius","border-top-style","border-top-width","border-width","bottom","box-decoration-break","box-shadow","box-sizing","break-after","break-before","break-inside","cx","cy","caption-side","caret-color","clear","clip","clip-path","clip-rule","color","color-interpolation","color-interpolation-filters","color-profile","color-rendering","color-scheme","column-count","column-fill","column-gap","column-rule","column-rule-color","column-rule-style","column-rule-width","column-span","column-width","columns","contain","content","content-visibility","counter-increment","counter-reset","cue","cue-after","cue-before","cursor","direction","display","dominant-baseline","empty-cells","enable-background","fill","fill-opacity","fill-rule","filter","flex","flex-basis","flex-direction","flex-flow","flex-grow","flex-shrink","flex-wrap","float","flow","flood-color","flood-opacity","font","font-display","font-family","font-feature-settings","font-kerning","font-language-override","font-size","font-size-adjust","font-smoothing","font-stretch","font-style","font-synthesis","font-variant","font-variant-caps","font-variant-east-asian","font-variant-ligatures","font-variant-numeric","font-variant-position","font-variation-settings","font-weight","gap","glyph-orientation-horizontal","glyph-orientation-vertical","grid","grid-area","grid-auto-columns","grid-auto-flow","grid-auto-rows","grid-column","grid-column-end","grid-column-start","grid-gap","grid-row","grid-row-end","grid-row-start","grid-template","grid-template-areas","grid-template-columns","grid-template-rows","hanging-punctuation","height","hyphens","icon","image-orientation","image-rendering","image-resolution","ime-mode","inline-size","inset","inset-block","inset-block-end","inset-block-start","inset-inline","inset-inline-end","inset-inline-start","isolation","kerning","justify-content","justify-items","justify-self","left","letter-spacing","lighting-color","line-break","line-height","list-style","list-style-image","list-style-position","list-style-type","marker","marker-end","marker-mid","marker-start","mask","margin","margin-block","margin-block-end","margin-block-start","margin-bottom","margin-inline","margin-inline-end","margin-inline-start","margin-left","margin-right","margin-top","marks","mask","mask-border","mask-border-mode","mask-border-outset","mask-border-repeat","mask-border-slice","mask-border-source","mask-border-width","mask-clip","mask-composite","mask-image","mask-mode","mask-origin","mask-position","mask-repeat","mask-size","mask-type","max-block-size","max-height","max-inline-size","max-width","min-block-size","min-height","min-inline-size","min-width","mix-blend-mode","nav-down","nav-index","nav-left","nav-right","nav-up","none","normal","object-fit","object-position","opacity","order","orphans","outline","outline-color","outline-offset","outline-style","outline-width","overflow","overflow-wrap","overflow-x","overflow-y","padding","padding-block","padding-block-end","padding-block-start","padding-bottom","padding-inline","padding-inline-end","padding-inline-start","padding-left","padding-right","padding-top","page-break-after","page-break-before","page-break-inside","pause","pause-after","pause-before","perspective","perspective-origin","pointer-events","position","quotes","r","resize","rest","rest-after","rest-before","right","rotate","row-gap","scale","scroll-margin","scroll-margin-block","scroll-margin-block-end","scroll-margin-block-start","scroll-margin-bottom","scroll-margin-inline","scroll-margin-inline-end","scroll-margin-inline-start","scroll-margin-left","scroll-margin-right","scroll-margin-top","scroll-padding","scroll-padding-block","scroll-padding-block-end","scroll-padding-block-start","scroll-padding-bottom","scroll-padding-inline","scroll-padding-inline-end","scroll-padding-inline-start","scroll-padding-left","scroll-padding-right","scroll-padding-top","scroll-snap-align","scroll-snap-stop","scroll-snap-type","scrollbar-color","scrollbar-gutter","scrollbar-width","shape-image-threshold","shape-margin","shape-outside","shape-rendering","stop-color","stop-opacity","stroke","stroke-dasharray","stroke-dashoffset","stroke-linecap","stroke-linejoin","stroke-miterlimit","stroke-opacity","stroke-width","speak","speak-as","src","tab-size","table-layout","text-anchor","text-align","text-align-all","text-align-last","text-combine-upright","text-decoration","text-decoration-color","text-decoration-line","text-decoration-skip-ink","text-decoration-style","text-decoration-thickness","text-emphasis","text-emphasis-color","text-emphasis-position","text-emphasis-style","text-indent","text-justify","text-orientation","text-overflow","text-rendering","text-shadow","text-transform","text-underline-offset","text-underline-position","top","transform","transform-box","transform-origin","transform-style","transition","transition-delay","transition-duration","transition-property","transition-timing-function","translate","unicode-bidi","vector-effect","vertical-align","visibility","voice-balance","voice-duration","voice-family","voice-pitch","voice-range","voice-rate","voice-stress","voice-volume","white-space","widows","width","will-change","word-break","word-spacing","word-wrap","writing-mode","x","y","z-index"].sort().reverse()
    ;return n=>{const a=n.regex,l=(e=>({IMPORTANT:{scope:"meta",begin:"!important"},
        BLOCK_COMMENT:e.C_BLOCK_COMMENT_MODE,HEXCOLOR:{scope:"number",
            begin:/#(([0-9a-fA-F]{3,4})|(([0-9a-fA-F]{2}){3,4}))\b/},FUNCTION_DISPATCH:{
            className:"built_in",begin:/[\w-]+(?=\()/},ATTRIBUTE_SELECTOR_MODE:{
            scope:"selector-attr",begin:/\[/,end:/\]/,illegal:"$",
            contains:[e.APOS_STRING_MODE,e.QUOTE_STRING_MODE]},CSS_NUMBER_MODE:{
            scope:"number",
            begin:e.NUMBER_RE+"(%|em|ex|ch|rem|vw|vh|vmin|vmax|cm|mm|in|pt|pc|px|deg|grad|rad|turn|s|ms|Hz|kHz|dpi|dpcm|dppx)?",
            relevance:0},CSS_VARIABLE:{className:"attr",begin:/--[A-Za-z_][A-Za-z0-9_-]*/}
    }))(n),s=[n.APOS_STRING_MODE,n.QUOTE_STRING_MODE];return{name:"CSS",
        case_insensitive:!0,illegal:/[=|'\$]/,keywords:{keyframePosition:"from to"},
        classNameAliases:{keyframePosition:"selector-tag"},contains:[l.BLOCK_COMMENT,{
            begin:/-(webkit|moz|ms|o)-(?=[a-z])/},l.CSS_NUMBER_MODE,{
            className:"selector-id",begin:/#[A-Za-z0-9_-]+/,relevance:0},{
            className:"selector-class",begin:"\\.[a-zA-Z-][a-zA-Z0-9_-]*",relevance:0
        },l.ATTRIBUTE_SELECTOR_MODE,{className:"selector-pseudo",variants:[{
                begin:":("+t.join("|")+")"},{begin:":(:)?("+i.join("|")+")"}]},l.CSS_VARIABLE,{
            className:"attribute",begin:"\\b("+o.join("|")+")\\b"},{begin:/:/,end:/[;}{]/,
            contains:[l.BLOCK_COMMENT,l.HEXCOLOR,l.IMPORTANT,l.CSS_NUMBER_MODE,...s,{
                begin:/(url|data-uri)\(/,end:/\)/,relevance:0,keywords:{built_in:"url data-uri"
                },contains:[...s,{className:"string",begin:/[^)]/,endsWithParent:!0,
                    excludeEnd:!0}]},l.FUNCTION_DISPATCH]},{begin:a.lookahead(/@/),end:"[{;]",
            relevance:0,illegal:/:/,contains:[{className:"keyword",begin:/@-?\w[\w]*(-\w+)*/
            },{begin:/\s/,endsWithParent:!0,excludeEnd:!0,relevance:0,keywords:{
                    $pattern:/[a-z-]+/,keyword:"and or not only",attribute:r.join(" ")},contains:[{
                    begin:/[a-z-]+(?=:)/,className:"attribute"},...s,l.CSS_NUMBER_MODE]}]},{
            className:"selector-tag",begin:"\\b("+e.join("|")+")\\b"}]}}})()
;hljs.registerLanguage("css",e)})();/*! `javascript` grammar compiled for Highlight.js 11.10.0 */
(()=>{var e=(()=>{"use strict"
    ;const e="[A-Za-z$_][0-9A-Za-z$_]*",n=["as","in","of","if","for","while","finally","var","new","function","do","return","void","else","break","catch","instanceof","with","throw","case","default","try","switch","continue","typeof","delete","let","yield","const","class","debugger","async","await","static","import","from","export","extends"],a=["true","false","null","undefined","NaN","Infinity"],t=["Object","Function","Boolean","Symbol","Math","Date","Number","BigInt","String","RegExp","Array","Float32Array","Float64Array","Int8Array","Uint8Array","Uint8ClampedArray","Int16Array","Int32Array","Uint16Array","Uint32Array","BigInt64Array","BigUint64Array","Set","Map","WeakSet","WeakMap","ArrayBuffer","SharedArrayBuffer","Atomics","DataView","JSON","Promise","Generator","GeneratorFunction","AsyncFunction","Reflect","Proxy","Intl","WebAssembly"],s=["Error","EvalError","InternalError","RangeError","ReferenceError","SyntaxError","TypeError","URIError"],r=["setInterval","setTimeout","clearInterval","clearTimeout","require","exports","eval","isFinite","isNaN","parseFloat","parseInt","decodeURI","decodeURIComponent","encodeURI","encodeURIComponent","escape","unescape"],c=["arguments","this","super","console","window","document","localStorage","sessionStorage","module","global"],i=[].concat(r,t,s)
    ;return o=>{const l=o.regex,b=e,d={begin:/<[A-Za-z0-9\\._:-]+/,
            end:/\/[A-Za-z0-9\\._:-]+>|\/>/,isTrulyOpeningTag:(e,n)=>{
                const a=e[0].length+e.index,t=e.input[a]
                ;if("<"===t||","===t)return void n.ignoreMatch();let s
                ;">"===t&&(((e,{after:n})=>{const a="</"+e[0].slice(1)
                ;return-1!==e.input.indexOf(a,n)})(e,{after:a})||n.ignoreMatch())
                ;const r=e.input.substring(a)
                ;((s=r.match(/^\s*=/))||(s=r.match(/^\s+extends\s+/))&&0===s.index)&&n.ignoreMatch()
            }},g={$pattern:e,keyword:n,literal:a,built_in:i,"variable.language":c
        },u="[0-9](_?[0-9])*",m=`\\.(${u})`,E="0|[1-9](_?[0-9])*|0[0-7]*[89][0-9]*",A={
            className:"number",variants:[{
                begin:`(\\b(${E})((${m})|\\.)?|(${m}))[eE][+-]?(${u})\\b`},{
                begin:`\\b(${E})\\b((${m})\\b|\\.)?|(${m})\\b`},{
                begin:"\\b(0|[1-9](_?[0-9])*)n\\b"},{
                begin:"\\b0[xX][0-9a-fA-F](_?[0-9a-fA-F])*n?\\b"},{
                begin:"\\b0[bB][0-1](_?[0-1])*n?\\b"},{begin:"\\b0[oO][0-7](_?[0-7])*n?\\b"},{
                begin:"\\b0[0-7]+n?\\b"}],relevance:0},y={className:"subst",begin:"\\$\\{",
            end:"\\}",keywords:g,contains:[]},h={begin:".?html`",end:"",starts:{end:"`",
                returnEnd:!1,contains:[o.BACKSLASH_ESCAPE,y],subLanguage:"xml"}},N={
            begin:".?css`",end:"",starts:{end:"`",returnEnd:!1,
                contains:[o.BACKSLASH_ESCAPE,y],subLanguage:"css"}},_={begin:".?gql`",end:"",
            starts:{end:"`",returnEnd:!1,contains:[o.BACKSLASH_ESCAPE,y],
                subLanguage:"graphql"}},f={className:"string",begin:"`",end:"`",
            contains:[o.BACKSLASH_ESCAPE,y]},p={className:"comment",
            variants:[o.COMMENT(/\/\*\*(?!\/)/,"\\*/",{relevance:0,contains:[{
                    begin:"(?=@[A-Za-z]+)",relevance:0,contains:[{className:"doctag",
                        begin:"@[A-Za-z]+"},{className:"type",begin:"\\{",end:"\\}",excludeEnd:!0,
                        excludeBegin:!0,relevance:0},{className:"variable",begin:b+"(?=\\s*(-)|$)",
                        endsParent:!0,relevance:0},{begin:/(?=[^\n])\s/,relevance:0}]}]
            }),o.C_BLOCK_COMMENT_MODE,o.C_LINE_COMMENT_MODE]
        },v=[o.APOS_STRING_MODE,o.QUOTE_STRING_MODE,h,N,_,f,{match:/\$\d+/},A]
    ;y.contains=v.concat({begin:/\{/,end:/\}/,keywords:g,contains:["self"].concat(v)
    });const S=[].concat(p,y.contains),w=S.concat([{begin:/(\s*)\(/,end:/\)/,
        keywords:g,contains:["self"].concat(S)}]),R={className:"params",begin:/(\s*)\(/,
        end:/\)/,excludeBegin:!0,excludeEnd:!0,keywords:g,contains:w},O={variants:[{
            match:[/class/,/\s+/,b,/\s+/,/extends/,/\s+/,l.concat(b,"(",l.concat(/\./,b),")*")],
            scope:{1:"keyword",3:"title.class",5:"keyword",7:"title.class.inherited"}},{
            match:[/class/,/\s+/,b],scope:{1:"keyword",3:"title.class"}}]},k={relevance:0,
        match:l.either(/\bJSON/,/\b[A-Z][a-z]+([A-Z][a-z]*|\d)*/,/\b[A-Z]{2,}([A-Z][a-z]+|\d)+([A-Z][a-z]*)*/,/\b[A-Z]{2,}[a-z]+([A-Z][a-z]+|\d)*([A-Z][a-z]*)*/),
        className:"title.class",keywords:{_:[...t,...s]}},I={variants:[{
            match:[/function/,/\s+/,b,/(?=\s*\()/]},{match:[/function/,/\s*(?=\()/]}],
        className:{1:"keyword",3:"title.function"},label:"func.def",contains:[R],
        illegal:/%/},x={
        match:l.concat(/\b/,(T=[...r,"super","import"].map((e=>e+"\\s*\\(")),
            l.concat("(?!",T.join("|"),")")),b,l.lookahead(/\s*\(/)),
        className:"title.function",relevance:0};var T;const C={
            begin:l.concat(/\./,l.lookahead(l.concat(b,/(?![0-9A-Za-z$_(])/))),end:b,
            excludeBegin:!0,keywords:"prototype",className:"property",relevance:0},M={
            match:[/get|set/,/\s+/,b,/(?=\()/],className:{1:"keyword",3:"title.function"},
            contains:[{begin:/\(\)/},R]
        },B="(\\([^()]*(\\([^()]*(\\([^()]*\\)[^()]*)*\\)[^()]*)*\\)|"+o.UNDERSCORE_IDENT_RE+")\\s*=>",$={
            match:[/const|var|let/,/\s+/,b,/\s*/,/=\s*/,/(async\s*)?/,l.lookahead(B)],
            keywords:"async",className:{1:"keyword",3:"title.function"},contains:[R]}
    ;return{name:"JavaScript",aliases:["js","jsx","mjs","cjs"],keywords:g,exports:{
            PARAMS_CONTAINS:w,CLASS_REFERENCE:k},illegal:/#(?![$_A-z])/,
        contains:[o.SHEBANG({label:"shebang",binary:"node",relevance:5}),{
            label:"use_strict",className:"meta",relevance:10,
            begin:/^\s*['"]use (strict|asm)['"]/
        },o.APOS_STRING_MODE,o.QUOTE_STRING_MODE,h,N,_,f,p,{match:/\$\d+/},A,k,{
            className:"attr",begin:b+l.lookahead(":"),relevance:0},$,{
            begin:"("+o.RE_STARTERS_RE+"|\\b(case|return|throw)\\b)\\s*",
            keywords:"return throw case",relevance:0,contains:[p,o.REGEXP_MODE,{
                className:"function",begin:B,returnBegin:!0,end:"\\s*=>",contains:[{
                    className:"params",variants:[{begin:o.UNDERSCORE_IDENT_RE,relevance:0},{
                        className:null,begin:/\(\s*\)/,skip:!0},{begin:/(\s*)\(/,end:/\)/,
                        excludeBegin:!0,excludeEnd:!0,keywords:g,contains:w}]}]},{begin:/,/,relevance:0
            },{match:/\s+/,relevance:0},{variants:[{begin:"<>",end:"</>"},{
                    match:/<[A-Za-z0-9\\._:-]+\s*\/>/},{begin:d.begin,
                    "on:begin":d.isTrulyOpeningTag,end:d.end}],subLanguage:"xml",contains:[{
                    begin:d.begin,end:d.end,skip:!0,contains:["self"]}]}]},I,{
            beginKeywords:"while if switch catch for"},{
            begin:"\\b(?!function)"+o.UNDERSCORE_IDENT_RE+"\\([^()]*(\\([^()]*(\\([^()]*\\)[^()]*)*\\)[^()]*)*\\)\\s*\\{",
            returnBegin:!0,label:"func.def",contains:[R,o.inherit(o.TITLE_MODE,{begin:b,
                className:"title.function"})]},{match:/\.\.\./,relevance:0},C,{match:"\\$"+b,
            relevance:0},{match:[/\bconstructor(?=\s*\()/],className:{1:"title.function"},
            contains:[R]},x,{relevance:0,match:/\b[A-Z][A-Z_0-9]+\b/,
            className:"variable.constant"},O,M,{match:/\$[(.]/}]}}})()
;hljs.registerLanguage("javascript",e)})();/*! `php` grammar compiled for Highlight.js 11.10.0 */
(()=>{var e=(()=>{"use strict";return e=>{
    const t=e.regex,a=/(?![A-Za-z0-9])(?![$])/,r=t.concat(/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/,a),n=t.concat(/(\\?[A-Z][a-z0-9_\x7f-\xff]+|\\?[A-Z]+(?=[A-Z][a-z0-9_\x7f-\xff])){1,}/,a),o={
        scope:"variable",match:"\\$+"+r},c={scope:"subst",variants:[{begin:/\$\w+/},{
            begin:/\{\$/,end:/\}/}]},i=e.inherit(e.APOS_STRING_MODE,{illegal:null
    }),s="[ \t\n]",l={scope:"string",variants:[e.inherit(e.QUOTE_STRING_MODE,{
            illegal:null,contains:e.QUOTE_STRING_MODE.contains.concat(c)}),i,{
            begin:/<<<[ \t]*(?:(\w+)|"(\w+)")\n/,end:/[ \t]*(\w+)\b/,
            contains:e.QUOTE_STRING_MODE.contains.concat(c),"on:begin":(e,t)=>{
                t.data._beginMatch=e[1]||e[2]},"on:end":(e,t)=>{
                t.data._beginMatch!==e[1]&&t.ignoreMatch()}},e.END_SAME_AS_BEGIN({
            begin:/<<<[ \t]*'(\w+)'\n/,end:/[ \t]*(\w+)\b/})]},d={scope:"number",variants:[{
            begin:"\\b0[bB][01]+(?:_[01]+)*\\b"},{begin:"\\b0[oO][0-7]+(?:_[0-7]+)*\\b"},{
            begin:"\\b0[xX][\\da-fA-F]+(?:_[\\da-fA-F]+)*\\b"},{
            begin:"(?:\\b\\d+(?:_\\d+)*(\\.(?:\\d+(?:_\\d+)*))?|\\B\\.\\d+)(?:[eE][+-]?\\d+)?"
        }],relevance:0
    },_=["false","null","true"],p=["__CLASS__","__DIR__","__FILE__","__FUNCTION__","__COMPILER_HALT_OFFSET__","__LINE__","__METHOD__","__NAMESPACE__","__TRAIT__","die","echo","exit","include","include_once","print","require","require_once","array","abstract","and","as","binary","bool","boolean","break","callable","case","catch","class","clone","const","continue","declare","default","do","double","else","elseif","empty","enddeclare","endfor","endforeach","endif","endswitch","endwhile","enum","eval","extends","final","finally","float","for","foreach","from","global","goto","if","implements","instanceof","insteadof","int","integer","interface","isset","iterable","list","match|0","mixed","new","never","object","or","private","protected","public","readonly","real","return","string","switch","throw","trait","try","unset","use","var","void","while","xor","yield"],b=["Error|0","AppendIterator","ArgumentCountError","ArithmeticError","ArrayIterator","ArrayObject","AssertionError","BadFunctionCallException","BadMethodCallException","CachingIterator","CallbackFilterIterator","CompileError","Countable","DirectoryIterator","DivisionByZeroError","DomainException","EmptyIterator","ErrorException","Exception","FilesystemIterator","FilterIterator","GlobIterator","InfiniteIterator","InvalidArgumentException","IteratorIterator","LengthException","LimitIterator","LogicException","MultipleIterator","NoRewindIterator","OutOfBoundsException","OutOfRangeException","OuterIterator","OverflowException","ParentIterator","ParseError","RangeException","RecursiveArrayIterator","RecursiveCachingIterator","RecursiveCallbackFilterIterator","RecursiveDirectoryIterator","RecursiveFilterIterator","RecursiveIterator","RecursiveIteratorIterator","RecursiveRegexIterator","RecursiveTreeIterator","RegexIterator","RuntimeException","SeekableIterator","SplDoublyLinkedList","SplFileInfo","SplFileObject","SplFixedArray","SplHeap","SplMaxHeap","SplMinHeap","SplObjectStorage","SplObserver","SplPriorityQueue","SplQueue","SplStack","SplSubject","SplTempFileObject","TypeError","UnderflowException","UnexpectedValueException","UnhandledMatchError","ArrayAccess","BackedEnum","Closure","Fiber","Generator","Iterator","IteratorAggregate","Serializable","Stringable","Throwable","Traversable","UnitEnum","WeakReference","WeakMap","Directory","__PHP_Incomplete_Class","parent","php_user_filter","self","static","stdClass"],E={
        keyword:p,literal:(e=>{const t=[];return e.forEach((e=>{
            t.push(e),e.toLowerCase()===e?t.push(e.toUpperCase()):t.push(e.toLowerCase())
        })),t})(_),built_in:b},u=e=>e.map((e=>e.replace(/\|\d+$/,""))),g={variants:[{
            match:[/new/,t.concat(s,"+"),t.concat("(?!",u(b).join("\\b|"),"\\b)"),n],scope:{
                1:"keyword",4:"title.class"}}]},h=t.concat(r,"\\b(?!\\()"),m={variants:[{
            match:[t.concat(/::/,t.lookahead(/(?!class\b)/)),h],scope:{2:"variable.constant"
            }},{match:[/::/,/class/],scope:{2:"variable.language"}},{
            match:[n,t.concat(/::/,t.lookahead(/(?!class\b)/)),h],scope:{1:"title.class",
                3:"variable.constant"}},{match:[n,t.concat("::",t.lookahead(/(?!class\b)/))],
            scope:{1:"title.class"}},{match:[n,/::/,/class/],scope:{1:"title.class",
                3:"variable.language"}}]},I={scope:"attr",
        match:t.concat(r,t.lookahead(":"),t.lookahead(/(?!::)/))},f={relevance:0,
        begin:/\(/,end:/\)/,keywords:E,contains:[I,o,m,e.C_BLOCK_COMMENT_MODE,l,d,g]
    },O={relevance:0,
        match:[/\b/,t.concat("(?!fn\\b|function\\b|",u(p).join("\\b|"),"|",u(b).join("\\b|"),"\\b)"),r,t.concat(s,"*"),t.lookahead(/(?=\()/)],
        scope:{3:"title.function.invoke"},contains:[f]};f.contains.push(O)
    ;const v=[I,m,e.C_BLOCK_COMMENT_MODE,l,d,g];return{case_insensitive:!1,
        keywords:E,contains:[{begin:t.concat(/#\[\s*/,n),beginScope:"meta",end:/]/,
            endScope:"meta",keywords:{literal:_,keyword:["new","array"]},contains:[{
                begin:/\[/,end:/]/,keywords:{literal:_,keyword:["new","array"]},
                contains:["self",...v]},...v,{scope:"meta",match:n}]
        },e.HASH_COMMENT_MODE,e.COMMENT("//","$"),e.COMMENT("/\\*","\\*/",{contains:[{
                scope:"doctag",match:"@[A-Za-z]+"}]}),{match:/__halt_compiler\(\);/,
            keywords:"__halt_compiler",starts:{scope:"comment",end:e.MATCH_NOTHING_RE,
                contains:[{match:/\?>/,scope:"meta",endsParent:!0}]}},{scope:"meta",variants:[{
                begin:/<\?php/,relevance:10},{begin:/<\?=/},{begin:/<\?/,relevance:.1},{
                begin:/\?>/}]},{scope:"variable.language",match:/\$this\b/},o,O,m,{
            match:[/const/,/\s/,r],scope:{1:"keyword",3:"variable.constant"}},g,{
            scope:"function",relevance:0,beginKeywords:"fn function",end:/[;{]/,
            excludeEnd:!0,illegal:"[$%\\[]",contains:[{beginKeywords:"use"
            },e.UNDERSCORE_TITLE_MODE,{begin:"=>",endsParent:!0},{scope:"params",
                begin:"\\(",end:"\\)",excludeBegin:!0,excludeEnd:!0,keywords:E,
                contains:["self",o,m,e.C_BLOCK_COMMENT_MODE,l,d]}]},{scope:"class",variants:[{
                beginKeywords:"enum",illegal:/[($"]/},{beginKeywords:"class interface trait",
                illegal:/[:($"]/}],relevance:0,end:/\{/,excludeEnd:!0,contains:[{
                beginKeywords:"extends implements"},e.UNDERSCORE_TITLE_MODE]},{
            beginKeywords:"namespace",relevance:0,end:";",illegal:/[.']/,
            contains:[e.inherit(e.UNDERSCORE_TITLE_MODE,{scope:"title.class"})]},{
            beginKeywords:"use",relevance:0,end:";",contains:[{
                match:/\b(as|const|function)\b/,scope:"keyword"},e.UNDERSCORE_TITLE_MODE]},l,d]}
}})();hljs.registerLanguage("php",e)})();/*! `php-template` grammar compiled for Highlight.js 11.10.0 */
(()=>{var n=(()=>{"use strict";return n=>({name:"PHP template",
    subLanguage:"xml",contains:[{begin:/<\?(php|=)?/,end:/\?>/,subLanguage:"php",
        contains:[{begin:"/\\*",end:"\\*/",skip:!0},{begin:'b"',end:'"',skip:!0},{
            begin:"b'",end:"'",skip:!0},n.inherit(n.APOS_STRING_MODE,{illegal:null,
            className:null,contains:null,skip:!0}),n.inherit(n.QUOTE_STRING_MODE,{
            illegal:null,className:null,contains:null,skip:!0})]}]})})()
;hljs.registerLanguage("php-template",n)})();/*! `plaintext` grammar compiled for Highlight.js 11.10.0 */
(()=>{var t=(()=>{"use strict";return t=>({name:"Plain text",
    aliases:["text","txt"],disableAutodetect:!0})})()
;hljs.registerLanguage("plaintext",t)})();/*! `shell` grammar compiled for Highlight.js 11.10.0 */
(()=>{var s=(()=>{"use strict";return s=>({name:"Shell Session",
    aliases:["console","shellsession"],contains:[{className:"meta.prompt",
        begin:/^\s{0,3}[/~\w\d[\]()@-]*[>%$#][ ]?/,starts:{end:/[^\\](?=\s*$)/,
            subLanguage:"bash"}}]})})();hljs.registerLanguage("shell",s)})();/*! `sql` grammar compiled for Highlight.js 11.10.0 */
(()=>{var e=(()=>{"use strict";return e=>{
    const r=e.regex,t=e.COMMENT("--","$"),n=["true","false","unknown"],a=["bigint","binary","blob","boolean","char","character","clob","date","dec","decfloat","decimal","float","int","integer","interval","nchar","nclob","national","numeric","real","row","smallint","time","timestamp","varchar","varying","varbinary"],i=["abs","acos","array_agg","asin","atan","avg","cast","ceil","ceiling","coalesce","corr","cos","cosh","count","covar_pop","covar_samp","cume_dist","dense_rank","deref","element","exp","extract","first_value","floor","json_array","json_arrayagg","json_exists","json_object","json_objectagg","json_query","json_table","json_table_primitive","json_value","lag","last_value","lead","listagg","ln","log","log10","lower","max","min","mod","nth_value","ntile","nullif","percent_rank","percentile_cont","percentile_disc","position","position_regex","power","rank","regr_avgx","regr_avgy","regr_count","regr_intercept","regr_r2","regr_slope","regr_sxx","regr_sxy","regr_syy","row_number","sin","sinh","sqrt","stddev_pop","stddev_samp","substring","substring_regex","sum","tan","tanh","translate","translate_regex","treat","trim","trim_array","unnest","upper","value_of","var_pop","var_samp","width_bucket"],s=["create table","insert into","primary key","foreign key","not null","alter table","add constraint","grouping sets","on overflow","character set","respect nulls","ignore nulls","nulls first","nulls last","depth first","breadth first"],o=i,c=["abs","acos","all","allocate","alter","and","any","are","array","array_agg","array_max_cardinality","as","asensitive","asin","asymmetric","at","atan","atomic","authorization","avg","begin","begin_frame","begin_partition","between","bigint","binary","blob","boolean","both","by","call","called","cardinality","cascaded","case","cast","ceil","ceiling","char","char_length","character","character_length","check","classifier","clob","close","coalesce","collate","collect","column","commit","condition","connect","constraint","contains","convert","copy","corr","corresponding","cos","cosh","count","covar_pop","covar_samp","create","cross","cube","cume_dist","current","current_catalog","current_date","current_default_transform_group","current_path","current_role","current_row","current_schema","current_time","current_timestamp","current_path","current_role","current_transform_group_for_type","current_user","cursor","cycle","date","day","deallocate","dec","decimal","decfloat","declare","default","define","delete","dense_rank","deref","describe","deterministic","disconnect","distinct","double","drop","dynamic","each","element","else","empty","end","end_frame","end_partition","end-exec","equals","escape","every","except","exec","execute","exists","exp","external","extract","false","fetch","filter","first_value","float","floor","for","foreign","frame_row","free","from","full","function","fusion","get","global","grant","group","grouping","groups","having","hold","hour","identity","in","indicator","initial","inner","inout","insensitive","insert","int","integer","intersect","intersection","interval","into","is","join","json_array","json_arrayagg","json_exists","json_object","json_objectagg","json_query","json_table","json_table_primitive","json_value","lag","language","large","last_value","lateral","lead","leading","left","like","like_regex","listagg","ln","local","localtime","localtimestamp","log","log10","lower","match","match_number","match_recognize","matches","max","member","merge","method","min","minute","mod","modifies","module","month","multiset","national","natural","nchar","nclob","new","no","none","normalize","not","nth_value","ntile","null","nullif","numeric","octet_length","occurrences_regex","of","offset","old","omit","on","one","only","open","or","order","out","outer","over","overlaps","overlay","parameter","partition","pattern","per","percent","percent_rank","percentile_cont","percentile_disc","period","portion","position","position_regex","power","precedes","precision","prepare","primary","procedure","ptf","range","rank","reads","real","recursive","ref","references","referencing","regr_avgx","regr_avgy","regr_count","regr_intercept","regr_r2","regr_slope","regr_sxx","regr_sxy","regr_syy","release","result","return","returns","revoke","right","rollback","rollup","row","row_number","rows","running","savepoint","scope","scroll","search","second","seek","select","sensitive","session_user","set","show","similar","sin","sinh","skip","smallint","some","specific","specifictype","sql","sqlexception","sqlstate","sqlwarning","sqrt","start","static","stddev_pop","stddev_samp","submultiset","subset","substring","substring_regex","succeeds","sum","symmetric","system","system_time","system_user","table","tablesample","tan","tanh","then","time","timestamp","timezone_hour","timezone_minute","to","trailing","translate","translate_regex","translation","treat","trigger","trim","trim_array","true","truncate","uescape","union","unique","unknown","unnest","update","upper","user","using","value","values","value_of","var_pop","var_samp","varbinary","varchar","varying","versioning","when","whenever","where","width_bucket","window","with","within","without","year","add","asc","collation","desc","final","first","last","view"].filter((e=>!i.includes(e))),l={
        begin:r.concat(/\b/,r.either(...o),/\s*\(/),relevance:0,keywords:{built_in:o}}
    ;return{name:"SQL",case_insensitive:!0,illegal:/[{}]|<\//,keywords:{
            $pattern:/\b[\w\.]+/,keyword:((e,{exceptions:r,when:t}={})=>{const n=t
            ;return r=r||[],e.map((e=>e.match(/\|\d+$/)||r.includes(e)?e:n(e)?e+"|0":e))
            })(c,{when:e=>e.length<3}),literal:n,type:a,
            built_in:["current_catalog","current_date","current_default_transform_group","current_path","current_role","current_schema","current_transform_group_for_type","current_user","session_user","system_time","system_user","current_time","localtime","current_timestamp","localtimestamp"]
        },contains:[{begin:r.either(...s),relevance:0,keywords:{$pattern:/[\w\.]+/,
                keyword:c.concat(s),literal:n,type:a}},{className:"type",
            begin:r.either("double precision","large object","with timezone","without timezone")
        },l,{className:"variable",begin:/@[a-z0-9][a-z0-9_]*/},{className:"string",
            variants:[{begin:/'/,end:/'/,contains:[{begin:/''/}]}]},{begin:/"/,end:/"/,
            contains:[{begin:/""/}]},e.C_NUMBER_MODE,e.C_BLOCK_COMMENT_MODE,t,{
            className:"operator",begin:/[-+*/=%^~]|&&?|\|\|?|!=?|<(?:=>?|<|>)?|>[>=]?/,
            relevance:0}]}}})();hljs.registerLanguage("sql",e)})();/*! `xml` grammar compiled for Highlight.js 11.10.0 */
(()=>{var e=(()=>{"use strict";return e=>{
    const a=e.regex,n=a.concat(/[\p{L}_]/u,a.optional(/[\p{L}0-9_.-]*:/u),/[\p{L}0-9_.-]*/u),s={
        className:"symbol",begin:/&[a-z]+;|&#[0-9]+;|&#x[a-f0-9]+;/},t={begin:/\s/,
        contains:[{className:"keyword",begin:/#?[a-z_][a-z1-9_-]+/,illegal:/\n/}]
    },i=e.inherit(t,{begin:/\(/,end:/\)/}),c=e.inherit(e.APOS_STRING_MODE,{
        className:"string"}),l=e.inherit(e.QUOTE_STRING_MODE,{className:"string"}),r={
        endsWithParent:!0,illegal:/</,relevance:0,contains:[{className:"attr",
            begin:/[\p{L}0-9._:-]+/u,relevance:0},{begin:/=\s*/,relevance:0,contains:[{
                className:"string",endsParent:!0,variants:[{begin:/"/,end:/"/,contains:[s]},{
                    begin:/'/,end:/'/,contains:[s]},{begin:/[^\s"'=<>`]+/}]}]}]};return{
        name:"HTML, XML",
        aliases:["html","xhtml","rss","atom","xjb","xsd","xsl","plist","wsf","svg"],
        case_insensitive:!0,unicodeRegex:!0,contains:[{className:"meta",begin:/<![a-z]/,
            end:/>/,relevance:10,contains:[t,l,c,i,{begin:/\[/,end:/\]/,contains:[{
                    className:"meta",begin:/<![a-z]/,end:/>/,contains:[t,i,l,c]}]}]
        },e.COMMENT(/<!--/,/-->/,{relevance:10}),{begin:/<!\[CDATA\[/,end:/\]\]>/,
            relevance:10},s,{className:"meta",end:/\?>/,variants:[{begin:/<\?xml/,
                relevance:10,contains:[l]},{begin:/<\?[a-z][a-z0-9]+/}]},{className:"tag",
            begin:/<style(?=\s|>)/,end:/>/,keywords:{name:"style"},contains:[r],starts:{
                end:/<\/style>/,returnEnd:!0,subLanguage:["css","xml"]}},{className:"tag",
            begin:/<script(?=\s|>)/,end:/>/,keywords:{name:"script"},contains:[r],starts:{
                end:/<\/script>/,returnEnd:!0,subLanguage:["javascript","handlebars","xml"]}},{
            className:"tag",begin:/<>|<\/>/},{className:"tag",
            begin:a.concat(/</,a.lookahead(a.concat(n,a.either(/\/>/,/>/,/\s/)))),
            end:/\/?>/,contains:[{className:"name",begin:n,relevance:0,starts:r}]},{
            className:"tag",begin:a.concat(/<\//,a.lookahead(a.concat(n,/>/))),contains:[{
                className:"name",begin:n,relevance:0},{begin:/>/,relevance:0,endsParent:!0}]}]}}
})();hljs.registerLanguage("xml",e)})();/*! `yaml` grammar compiled for Highlight.js 11.10.0 */
(()=>{var e=(()=>{"use strict";return e=>{
    const n="true false yes no null",a="[\\w#;/?:@&=+$,.~*'()[\\]]+",s={
            className:"string",relevance:0,variants:[{begin:/'/,end:/'/},{begin:/"/,end:/"/
            },{begin:/\S+/}],contains:[e.BACKSLASH_ESCAPE,{className:"template-variable",
                variants:[{begin:/\{\{/,end:/\}\}/},{begin:/%\{/,end:/\}/}]}]},i=e.inherit(s,{
            variants:[{begin:/'/,end:/'/},{begin:/"/,end:/"/},{begin:/[^\s,{}[\]]+/}]}),l={
            end:",",endsWithParent:!0,excludeEnd:!0,keywords:n,relevance:0},t={begin:/\{/,
            end:/\}/,contains:[l],illegal:"\\n",relevance:0},g={begin:"\\[",end:"\\]",
            contains:[l],illegal:"\\n",relevance:0},b=[{className:"attr",variants:[{
                begin:/\w[\w :()\./-]*:(?=[ \t]|$)/},{begin:/"\w[\w :()\./-]*":(?=[ \t]|$)/},{
                begin:/'\w[\w :()\./-]*':(?=[ \t]|$)/}]},{className:"meta",begin:"^---\\s*$",
            relevance:10},{className:"string",
            begin:"[\\|>]([1-9]?[+-])?[ ]*\\n( +)[^ ][^\\n]*\\n(\\2[^\\n]+\\n?)*"},{
            begin:"<%[%=-]?",end:"[%-]?%>",subLanguage:"ruby",excludeBegin:!0,excludeEnd:!0,
            relevance:0},{className:"type",begin:"!\\w+!"+a},{className:"type",
            begin:"!<"+a+">"},{className:"type",begin:"!"+a},{className:"type",begin:"!!"+a
        },{className:"meta",begin:"&"+e.UNDERSCORE_IDENT_RE+"$"},{className:"meta",
            begin:"\\*"+e.UNDERSCORE_IDENT_RE+"$"},{className:"bullet",begin:"-(?=[ ]|$)",
            relevance:0},e.HASH_COMMENT_MODE,{beginKeywords:n,keywords:{literal:n}},{
            className:"number",
            begin:"\\b[0-9]{4}(-[0-9][0-9]){0,2}([Tt \\t][0-9][0-9]?(:[0-9][0-9]){2})?(\\.[0-9]*)?([ \\t])*(Z|[-+][0-9][0-9]?(:[0-9][0-9])?)?\\b"
        },{className:"number",begin:e.C_NUMBER_RE+"\\b",relevance:0},t,g,s],r=[...b]
    ;return r.pop(),r.push(i),l.contains=r,{name:"YAML",case_insensitive:!0,
        aliases:["yml"],contains:b}}})();hljs.registerLanguage("yaml",e)})();

if (typeof(PhpDebugBar) == 'undefined') {
    // namespace
    var PhpDebugBar = {};
    PhpDebugBar.$ = jQuery;
}

(function($) {

    if (typeof(localStorage) == 'undefined') {
        // provide mock localStorage object for dumb browsers
        localStorage = {
            setItem: function(key, value) {},
            getItem: function(key) { return null; }
        };
    }

    if (typeof(PhpDebugBar.utils) == 'undefined') {
        PhpDebugBar.utils = {};
    }

    /**
     * Returns the value from an object property.
     * Using dots in the key, it is possible to retrieve nested property values
     *
     * @param {Object} dict
     * @param {String} key
     * @param {Object} default_value
     * @return {Object}
     */
    var getDictValue = PhpDebugBar.utils.getDictValue = function(dict, key, default_value) {
        var d = dict, parts = key.split('.');
        for (var i = 0; i < parts.length; i++) {
            if (!d[parts[i]]) {
                return default_value;
            }
            d = d[parts[i]];
        }
        return d;
    }

    /**
     * Counts the number of properties in an object
     *
     * @param {Object} obj
     * @return {Integer}
     */
    var getObjectSize = PhpDebugBar.utils.getObjectSize = function(obj) {
        if (Object.keys) {
            return Object.keys(obj).length;
        }
        var count = 0;
        for (var k in obj) {
            if (obj.hasOwnProperty(k)) {
                count++;
            }
        }
        return count;
    }

    /**
     * Returns a prefixed css class name
     *
     * @param {String} cls
     * @return {String}
     */
    PhpDebugBar.utils.csscls = function(cls, prefix) {
        if (cls.indexOf(' ') > -1) {
            var clss = cls.split(' '), out = [];
            for (var i = 0, c = clss.length; i < c; i++) {
                out.push(PhpDebugBar.utils.csscls(clss[i], prefix));
            }
            return out.join(' ');
        }
        if (cls.indexOf('.') === 0) {
            return '.' + prefix + cls.substr(1);
        }
        return prefix + cls;
    };

    /**
     * Creates a partial function of csscls where the second
     * argument is already defined
     *
     * @param  {string} prefix
     * @return {Function}
     */
    PhpDebugBar.utils.makecsscls = function(prefix) {
        var f = function(cls) {
            return PhpDebugBar.utils.csscls(cls, prefix);
        };
        return f;
    }

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-');


    // ------------------------------------------------------------------

    /**
     * Base class for all elements with a visual component
     *
     * @param {Object} options
     * @constructor
     */
    var Widget = PhpDebugBar.Widget = function(options) {
        this._attributes = $.extend({}, this.defaults);
        this._boundAttributes = {};
        this.$el = $('<' + this.tagName + ' />');
        if (this.className) {
            this.$el.addClass(this.className);
        }
        this.initialize.apply(this, [options || {}]);
        this.render.apply(this);
    };

    $.extend(Widget.prototype, {

        tagName: 'div',

        className: null,

        defaults: {},

        /**
         * Called after the constructor
         *
         * @param {Object} options
         */
        initialize: function(options) {
            this.set(options);
        },

        /**
         * Called after the constructor to render the element
         */
        render: function() {},

        /**
         * Sets the value of an attribute
         *
         * @param {String} attr Can also be an object to set multiple attributes at once
         * @param {Object} value
         */
        set: function(attr, value) {
            if (typeof(attr) != 'string') {
                for (var k in attr) {
                    this.set(k, attr[k]);
                }
                return;
            }

            this._attributes[attr] = value;
            if (typeof(this._boundAttributes[attr]) !== 'undefined') {
                for (var i = 0, c = this._boundAttributes[attr].length; i < c; i++) {
                    this._boundAttributes[attr][i].apply(this, [value]);
                }
            }
        },

        /**
         * Checks if an attribute exists and is not null
         *
         * @param {String} attr
         * @return {[type]} [description]
         */
        has: function(attr) {
            return typeof(this._attributes[attr]) !== 'undefined' && this._attributes[attr] !== null;
        },

        /**
         * Returns the value of an attribute
         *
         * @param {String} attr
         * @return {Object}
         */
        get: function(attr) {
            return this._attributes[attr];
        },

        /**
         * Registers a callback function that will be called whenever the value of the attribute changes
         *
         * If cb is a jQuery element, text() will be used to fill the element
         *
         * @param {String} attr
         * @param {Function} cb
         */
        bindAttr: function(attr, cb) {
            if (Array.isArray(attr)) {
                for (var i = 0, c = attr.length; i < c; i++) {
                    this.bindAttr(attr[i], cb);
                }
                return;
            }

            if (typeof(this._boundAttributes[attr]) == 'undefined') {
                this._boundAttributes[attr] = [];
            }
            if (typeof(cb) == 'object') {
                var el = cb;
                cb = function(value) { el.text(value || ''); };
            }
            this._boundAttributes[attr].push(cb);
            if (this.has(attr)) {
                cb.apply(this, [this._attributes[attr]]);
            }
        }

    });


    /**
     * Creates a subclass
     *
     * Code from Backbone.js
     *
     * @param {Array} props Prototype properties
     * @return {Function}
     */
    Widget.extend = function(props) {
        var parent = this;

        var child = function() { return parent.apply(this, arguments); };
        $.extend(child, parent);

        var Surrogate = function() { this.constructor = child; };
        Surrogate.prototype = parent.prototype;
        child.prototype = new Surrogate;
        $.extend(child.prototype, props);

        child.__super__ = parent.prototype;

        return child;
    };

    // ------------------------------------------------------------------

    /**
     * Tab
     *
     * A tab is composed of a tab label which is always visible and
     * a tab panel which is visible only when the tab is active.
     *
     * The panel must contain a widget. A widget is an object which has
     * an element property containing something appendable to a jQuery object.
     *
     * Options:
     *  - title
     *  - badge
     *  - widget
     *  - data: forward data to widget data
     */
    var Tab = Widget.extend({

        className: csscls('panel'),

        render: function() {
            this.$tab = $('<a />').addClass(csscls('tab'));
            this.$icon = $('<i />').appendTo(this.$tab);
            this.bindAttr('icon', function(icon) {
                if (icon) {
                    this.$icon.attr('class', 'phpdebugbar-fa phpdebugbar-fa-' + icon);
                } else {
                    this.$icon.attr('class', '');
                }
            });

            this.bindAttr('title', $('<span />').addClass(csscls('text')).appendTo(this.$tab));

            this.$badge = $('<span />').addClass(csscls('badge')).appendTo(this.$tab);
            this.bindAttr('badge', function(value) {
                if (value !== null) {
                    this.$badge.text(value);
                    this.$badge.addClass(csscls('visible'));
                } else {
                    this.$badge.removeClass(csscls('visible'));
                }
            });

            this.bindAttr('widget', function(widget) {
                this.$el.empty().append(widget.$el);
            });

            this.bindAttr('data', function(data) {
                if (this.has('widget')) {
                    this.get('widget').set('data', data);
                    this.$tab.attr('data-empty', $.isEmptyObject(data));
                }
            })
        }

    });

    // ------------------------------------------------------------------

    /**
     * Indicator
     *
     * An indicator is a text and an icon to display single value information
     * right inside the always visible part of the debug bar
     *
     * Options:
     *  - icon
     *  - title
     *  - tooltip
     *  - data: alias of title
     */
    var Indicator = Widget.extend({

        tagName: 'span',

        className: csscls('indicator'),

        render: function() {
            this.$icon = $('<i />').appendTo(this.$el);
            this.bindAttr('icon', function(icon) {
                if (icon) {
                    this.$icon.attr('class', 'phpdebugbar-fa phpdebugbar-fa-' + icon);
                } else {
                    this.$icon.attr('class', '');
                }
            });

            this.bindAttr(['title', 'data'], $('<span />').addClass(csscls('text')).appendTo(this.$el));

            this.$tooltip = $('<span />').addClass(csscls('tooltip disabled')).appendTo(this.$el);
            this.bindAttr('tooltip', function(tooltip) {
                if (tooltip) {
                    var dl = $('<dl />');
                    if (Array.isArray(tooltip) || typeof tooltip === 'object') {
                        $.each(tooltip, function(key, value) {
                            $('<dt />').text(key).appendTo(dl);
                            $('<dd />').text(value).appendTo(dl);
                        });
                        this.$tooltip.html(dl).removeClass(csscls('disabled'));
                    } else {
                        this.$tooltip.text(tooltip).removeClass(csscls('disabled'));
                    }
                } else {
                    this.$tooltip.addClass(csscls('disabled'));
                }
            });
        }

    });


    /**
     * Displays datasets in a table
     *
     */
    var Settings = Widget.extend({

        tagName: 'form',

        className: csscls('settings'),

        settings: {},

        initialize: function(options) {
            this.set(options);

            var debugbar = this.get('debugbar');
            this.settings = JSON.parse(localStorage.getItem('phpdebugbar-settings')) || {};

            $.each(debugbar.options, (key, value)=>  {
                if (key in this.settings) {
                    debugbar.options[key] = this.settings[key];
                }

                // Theme requires dark/light mode detection
                if (key === 'theme') {
                    debugbar.setTheme(debugbar.options[key]);
                } else {
                    debugbar.$el.attr('data-' + key, debugbar.options[key]);
                }
            })
        },

        clearSettings: function() {
            var debugbar = this.get('debugbar');

            // Remove item from storage
            localStorage.removeItem('phpdebugbar-settings');
            localStorage.removeItem('phpdebugbar-ajaxhandler-autoshow');
            this.settings = {};

            // Reset options
            debugbar.options = { ...debugbar.defaultOptions };

            // Reset ajax handler
            if (debugbar.ajaxHandler) {
                var autoshow = debugbar.ajaxHandler.defaultAutoShow;
                debugbar.ajaxHandler.setAutoShow(autoshow);
                this.set('autoshow', autoshow);
                if(debugbar.controls['__datasets']) {
                    debugbar.controls['__datasets'].get('widget').set('autoshow', $(this).is(':checked'));
                }
            }

            this.initialize(debugbar.options);
        },

        storeSetting: function(key, value) {
            this.settings[key] = value;

            var debugbar = this.get('debugbar');
            debugbar.options[key] = value;
            if (key !== 'theme') {
                debugbar.$el.attr('data-' + key, value);
            }

            localStorage.setItem('phpdebugbar-settings', JSON.stringify(this.settings));
        },

        render: function() {
            this.$el.empty();

            var debugbar = this.get('debugbar');
            var self = this;

            var fields = {};

            // Set Theme
            fields["Theme"] = $('<select>' +
                '<option value="auto">Auto (System preference)</option>' +
                '<option value="light">Light</option>' +
                '<option value="dark">Dark</option>' +
                '</select>')
                .val(debugbar.options.theme)
                .on('change', function() {
                    self.storeSetting('theme', $(this).val())
                    debugbar.setTheme($(this).val());
                });

            fields["Open Button Position"] = $('<select>' +
                '<option value="bottomLeft">Bottom Left</option>' +
                '<option value="bottomRight">Bottom Right</option>' +
                '<option value="topLeft">Top Left</option>' +
                '<option value="topRight">Top Right</option>' +
                '</select>')
                .val(debugbar.options.openBtnPosition)
                .on('change', function() {
                    self.storeSetting('openBtnPosition', $(this).val())
                });

            this.$hideEmptyTabs = $('<input type=checkbox>')
                .prop('checked', debugbar.options.hideEmptyTabs)
                .on('click', function() {
                    self.storeSetting('hideEmptyTabs', $(this).is(':checked'));
                });

            fields["Hide Empty Tabs"] = $('<label/>').append(this.$hideEmptyTabs, 'Hide empty tabs until they have data');

            this.$autoshow = $('<input type=checkbox>')
                .prop('checked', debugbar.ajaxHandler && debugbar.ajaxHandler.autoShow)
                .on('click', function() {
                    if (debugbar.ajaxHandler) {
                        debugbar.ajaxHandler.setAutoShow($(this).is(':checked'));
                    }
                    if (debugbar.controls['__datasets']) {
                        debugbar.controls['__datasets'].get('widget').set('autoshow', $(this).is(':checked'));
                    }
                });

            this.bindAttr('autoshow', function() {
                this.$autoshow.prop('checked', this.get('autoshow')).closest('.form-row').show();
            })
            fields["Autoshow"] = $('<label/>').append(this.$autoshow, 'Automatically show new incoming Ajax requests');

            fields["Reset to defaults"] = $('<button>Reset settings</button>').on('click', function(e) {
                e.preventDefault();
                self.clearSettings();
                self.render();
            })

            $.each(fields, function(key, value) {
                $('<div />').addClass(csscls('form-row')).append(
                    $('<div />').addClass(csscls('form-label')).text(key),
                    $('<div />').addClass(csscls('form-input')).html(value)
                ).appendTo(self.$el);

            })

            if (!this.ajaxHandler) {
                this.$autoshow.closest('.form-row').hide();
            }
        },

    });

    // ------------------------------------------------------------------

    /**
     * Dataset title formater
     *
     * Formats the title of a dataset for the select box
     */
    var DatasetTitleFormater = PhpDebugBar.DatasetTitleFormater = function(debugbar) {
        this.debugbar = debugbar;
    };

    $.extend(DatasetTitleFormater.prototype, {

        /**
         * Formats the title of a dataset
         *
         * @this {DatasetTitleFormater}
         * @param {String} id
         * @param {Object} data
         * @param {String} suffix
         * @return {String}
         */
        format: function(id, data, suffix, nb) {
            if (suffix) {
                suffix = ' ' + suffix;
            } else {
                suffix = '';
            }

            var nb = nb || getObjectSize(this.debugbar.datasets) ;

            if (typeof(data['__meta']) === 'undefined') {
                return "#" + nb + suffix;
            }

            var uri = data['__meta']['uri'].split('/'), filename = uri.pop();

            // URI ends in a trailing /, avoid returning an empty string
            if (!filename) {
                filename = (uri.pop() || '') + '/'; // add the trailing '/' back
            }

            // filename is a number, path could be like /action/{id}
            if (uri.length && !isNaN(filename)) {
                filename = uri.pop() + '/' + filename;
            }

            // truncate the filename in the label, if it's too long
            var maxLength = 150;
            if (filename.length > maxLength) {
                filename = filename.substr(0, maxLength) + '...';
            }

            var label = "#" + nb + " " + filename + suffix + ' (' + data['__meta']['datetime'].split(' ')[1] + ')';
            return label;
        }

    });

    // ------------------------------------------------------------------


    /**
     * DebugBar
     *
     * Creates a bar that appends itself to the body of your page
     * and sticks to the bottom.
     *
     * The bar can be customized by adding tabs and indicators.
     * A data map is used to fill those controls with data provided
     * from datasets.
     */
    var DebugBar = PhpDebugBar.DebugBar = Widget.extend({

        className: "phpdebugbar " + csscls('minimized'),

        options: {
            bodyMarginBottom: true,
            theme: 'auto',
            openBtnPosition: 'bottomLeft',
            hideEmptyTabs: false,
        },

        initialize: function(options = {}) {
            this.options = $.extend(this.options, options);
            this.defaultOptions = { ...this.options };
            this.controls = {};
            this.dataMap = {};
            this.datasets = {};
            this.firstTabName = null;
            this.activePanelName = null;
            this.activeDatasetId = null;
            this.datesetTitleFormater = new DatasetTitleFormater(this);
            this.bodyMarginBottomHeight = parseInt($('body').css('margin-bottom'));
            try {
                this.isIframe = window.self !== window.top && window.top.phpdebugbar;
            } catch (error) {
                this.isIframe = false;
            }
            this.registerResizeHandler();
            this.registerMediaListener();

            // Attach settings
            this.settings = new PhpDebugBar.DebugBar.Tab({"icon":"sliders", "title":"Settings", "widget": new Settings({
                    'debugbar': this,
                })});
        },

        /**
         * Register resize event, for resize debugbar with reponsive css.
         *
         * @this {DebugBar}
         */
        registerResizeHandler: function() {
            if (typeof this.resize.bind == 'undefined' || this.isIframe) return;

            var f = this.resize.bind(this);
            this.respCSSSize = 0;
            $(window).resize(f);
            setTimeout(f, 20);
        },

        registerMediaListener: function() {
            const mediaQueryList = window.matchMedia("(prefers-color-scheme: dark)");
            mediaQueryList.addEventListener('change', event => {
                if (this.options.theme === 'auto') {
                    this.setTheme('auto');
                }
            })
        },

        setTheme: function(theme) {
            this.options.theme = theme;

            if (theme === 'auto') {
                const mediaQueryList = window.matchMedia("(prefers-color-scheme: dark)");
                theme = mediaQueryList.matches ? 'dark' : 'light';
            }

            this.$el.attr('data-theme', theme)
            if (this.openHandler) {
                this.openHandler.$el.attr('data-theme', theme)
            }
        },

        /**
         * Resizes the debugbar to fit the current browser window
         */
        resize: function() {
            var contentSize = this.respCSSSize;
            if (this.respCSSSize == 0) {
                this.$header.find("> *:visible").each(function () {
                    contentSize += $(this).outerWidth(true);
                });
            }

            var currentSize = this.$header.width();
            var cssClass = csscls("mini-design");
            var bool = this.$header.hasClass(cssClass);

            if (currentSize <= contentSize && !bool) {
                this.respCSSSize = contentSize;
                this.$header.addClass(cssClass);
            } else if (contentSize < currentSize && bool) {
                this.respCSSSize = 0;
                this.$header.removeClass(cssClass);
            }

            // Reset height to ensure bar is still visible
            this.setHeight(this.$body.height());
        },

        /**
         * Initialiazes the UI
         *
         * @this {DebugBar}
         */
        render: function() {
            if (this.isIframe) {
                this.$el.hide();
            }

            var self = this;
            this.$el.appendTo('body');
            this.$dragCapture = $('<div />').addClass(csscls('drag-capture')).appendTo(this.$el);
            this.$resizehdle = $('<div />').addClass(csscls('resize-handle')).appendTo(this.$el);
            this.$header = $('<div />').addClass(csscls('header')).appendTo(this.$el);
            this.$headerBtn = $('<a />').addClass(csscls('restore-btn')).appendTo(this.$header);
            this.$headerBtn.click(function() {
                self.close();
            });
            this.$headerLeft = $('<div />').addClass(csscls('header-left')).appendTo(this.$header);
            this.$headerRight = $('<div />').addClass(csscls('header-right')).appendTo(this.$header);
            var $body = this.$body = $('<div />').addClass(csscls('body')).appendTo(this.$el);
            this.recomputeBottomOffset();

            // dragging of resize handle
            var pos_y, orig_h;
            this.$resizehdle.on('mousedown', function(e) {
                orig_h = $body.height(), pos_y = e.pageY;
                $body.parents().on('mousemove', mousemove).on('mouseup', mouseup);
                self.$dragCapture.show();
                e.preventDefault();
            });
            var mousemove = function(e) {
                var h = orig_h + (pos_y - e.pageY);
                self.setHeight(h);
            };
            var mouseup = function() {
                $body.parents().off('mousemove', mousemove).off('mouseup', mouseup);
                self.$dragCapture.hide();
            };

            // close button
            this.$closebtn = $('<a />').addClass(csscls('close-btn')).appendTo(this.$headerRight);
            this.$closebtn.click(function() {
                self.close();
            });

            // minimize button
            this.$minimizebtn = $('<a />').addClass(csscls('minimize-btn') ).appendTo(this.$headerRight);
            this.$minimizebtn.click(function() {
                self.minimize();
            });

            // maximize button
            this.$maximizebtn = $('<a />').addClass(csscls('maximize-btn') ).appendTo(this.$headerRight);
            this.$maximizebtn.click(function() {
                self.restore();
            });

            // restore button
            this.$restorebtn = $('<a />').addClass(csscls('restore-btn')).hide().appendTo(this.$el);
            this.$restorebtn.click(function() {
                self.restore();
            });

            // open button
            this.$openbtn = $('<a />').addClass(csscls('open-btn')).appendTo(this.$headerRight).hide();
            this.$openbtn.click(function() {
                self.openHandler.show(function(id, dataset) {
                    self.addDataSet(dataset, id, "(opened)");
                    self.showTab();
                });
            });

            // select box for data sets
            this.$datasets = $('<select />').addClass(csscls('datasets-switcher')).attr('name', 'datasets-switcher')
                .appendTo(this.$headerRight);
            this.$datasets.change(function() {
                self.showDataSet(this.value);
            });

            this.controls['__settings'] = this.settings;
            this.settings.$tab.addClass(csscls('tab-settings'));
            this.settings.$tab.attr('data-collector', '__settings');
            this.settings.$el.attr('data-collector', '__settings');
            this.settings.$tab.insertAfter(this.$minimizebtn).show();
            this.settings.$tab.click(() => {
                if (!this.isMinimized() && this.activePanelName == '__settings') {
                    this.minimize();
                } else {
                    this.showTab('__settings');
                    this.settings.get('widget').render();
                }
            });
            this.settings.$el.appendTo(this.$body);
        },

        /**
         * Sets the height of the debugbar body section
         * Forces the height to lie within a reasonable range
         * Stores the height in local storage so it can be restored
         * Resets the document body bottom offset
         *
         * @this {DebugBar}
         */
        setHeight: function(height) {
            var min_h = 40;
            var max_h = $(window).innerHeight() - this.$header.height() - 10;
            height = Math.min(height, max_h);
            height = Math.max(height, min_h);
            this.$body.css('height', height);
            localStorage.setItem('phpdebugbar-height', height);
            this.recomputeBottomOffset();
        },

        /**
         * Restores the state of the DebugBar using localStorage
         * This is not called by default in the constructor and
         * needs to be called by subclasses in their init() method
         *
         * @this {DebugBar}
         */
        restoreState: function() {
            if (this.isIframe) return;
            // bar height
            var height = localStorage.getItem('phpdebugbar-height');
            this.setHeight(height || this.$body.height());

            // bar visibility
            var open = localStorage.getItem('phpdebugbar-open');
            if (open && open == '0') {
                this.close();
            } else {
                var visible = localStorage.getItem('phpdebugbar-visible');
                if (visible && visible == '1') {
                    var tab = localStorage.getItem('phpdebugbar-tab');
                    if (this.isTab(tab)) {
                        this.showTab(tab);
                    } else {
                        this.showTab();
                    }
                }
            }
        },

        /**
         * Creates and adds a new tab
         *
         * @this {DebugBar}
         * @param {String} name Internal name
         * @param {Object} widget A widget object with an element property
         * @param {String} title The text in the tab, if not specified, name will be used
         * @return {Tab}
         */
        createTab: function(name, widget, title) {
            var tab = new Tab({
                title: title || (name.replace(/[_\-]/g, ' ').charAt(0).toUpperCase() + name.slice(1)),
                widget: widget
            });
            return this.addTab(name, tab);
        },

        /**
         * Adds a new tab
         *
         * @this {DebugBar}
         * @param {String} name Internal name
         * @param {Tab} tab Tab object
         * @return {Tab}
         */
        addTab: function(name, tab) {
            if (this.isControl(name)) {
                throw new Error(name + ' already exists');
            }

            var self = this;
            tab.$tab.appendTo(this.$headerLeft).click(function() {
                if (!self.isMinimized() && self.activePanelName == name) {
                    self.minimize();
                } else {
                    self.showTab(name);
                }
            })
            tab.$tab.attr('data-empty', true);
            tab.$tab.attr('data-collector', name);
            tab.$el.attr('data-collector', name);
            tab.$el.appendTo(this.$body);

            this.controls[name] = tab;
            if (this.firstTabName == null) {
                this.firstTabName = name;
            }
            return tab;
        },

        /**
         * Creates and adds an indicator
         *
         * @this {DebugBar}
         * @param {String} name Internal name
         * @param {String} icon
         * @param {String|Object} tooltip
         * @param {String} position "right" or "left", default is "right"
         * @return {Indicator}
         */
        createIndicator: function(name, icon, tooltip, position) {
            var indicator = new Indicator({
                icon: icon,
                tooltip: tooltip
            });
            return this.addIndicator(name, indicator, position);
        },

        /**
         * Adds an indicator
         *
         * @this {DebugBar}
         * @param {String} name Internal name
         * @param {Indicator} indicator Indicator object
         * @return {Indicator}
         */
        addIndicator: function(name, indicator, position) {
            if (this.isControl(name)) {
                throw new Error(name + ' already exists');
            }

            if (position == 'left') {
                indicator.$el.insertBefore(this.$headerLeft.children().first());
            } else {
                indicator.$el.appendTo(this.$headerRight);
            }

            this.controls[name] = indicator;
            return indicator;
        },

        /**
         * Returns a control
         *
         * @param {String} name
         * @return {Object}
         */
        getControl: function(name) {
            if (this.isControl(name)) {
                return this.controls[name];
            }
        },

        /**
         * Checks if there's a control under the specified name
         *
         * @this {DebugBar}
         * @param {String} name
         * @return {Boolean}
         */
        isControl: function(name) {
            return typeof(this.controls[name]) != 'undefined';
        },

        /**
         * Checks if a tab with the specified name exists
         *
         * @this {DebugBar}
         * @param {String} name
         * @return {Boolean}
         */
        isTab: function(name) {
            return this.isControl(name) && this.controls[name] instanceof Tab;
        },

        /**
         * Checks if an indicator with the specified name exists
         *
         * @this {DebugBar}
         * @param {String} name
         * @return {Boolean}
         */
        isIndicator: function(name) {
            return this.isControl(name) && this.controls[name] instanceof Indicator;
        },

        /**
         * Removes all tabs and indicators from the debug bar and hides it
         *
         * @this {DebugBar}
         */
        reset: function() {
            this.minimize();
            var self = this;
            $.each(this.controls, function(name, control) {
                if (self.isTab(name)) {
                    control.$tab.remove();
                }
                control.$el.remove();
            });
            this.controls = {};
        },

        /**
         * Open the debug bar and display the specified tab
         *
         * @this {DebugBar}
         * @param {String} name If not specified, display the first tab
         */
        showTab: function(name) {
            if (!name) {
                if (this.activePanelName) {
                    name = this.activePanelName;
                } else {
                    name = this.firstTabName;
                }
            }

            if (!this.isTab(name)) {
                throw new Error("Unknown tab '" + name + "'");
            }

            this.$resizehdle.show();
            this.$body.show();
            this.recomputeBottomOffset();

            $(this.$header).find('> div > .' + csscls('active')).removeClass(csscls('active'));
            $(this.$body).find('> .' + csscls('active')).removeClass(csscls('active'));

            this.controls[name].$tab.addClass(csscls('active'));
            this.controls[name].$el.addClass(csscls('active'));
            this.activePanelName = name;

            this.$el.removeClass(csscls('minimized'));
            localStorage.setItem('phpdebugbar-visible', '1');
            localStorage.setItem('phpdebugbar-tab', name);

            this.resize();
        },

        /**
         * Hide panels and minimize the debug bar
         *
         * @this {DebugBar}
         */
        minimize: function() {
            this.$header.find('> div > .' + csscls('active')).removeClass(csscls('active'));
            this.$body.hide();
            this.$resizehdle.hide();
            this.recomputeBottomOffset();
            localStorage.setItem('phpdebugbar-visible', '0');
            this.$el.addClass(csscls('minimized'));
            this.resize();
        },

        /**
         * Checks if the panel is minimized
         *
         * @return {Boolean}
         */
        isMinimized: function() {
            return this.$el.hasClass(csscls('minimized'));
        },

        /**
         * Close the debug bar
         *
         * @this {DebugBar}
         */
        close: function() {
            this.$resizehdle.hide();
            this.$header.hide();
            this.$body.hide();
            this.$restorebtn.show();
            localStorage.setItem('phpdebugbar-open', '0');
            this.$el.addClass(csscls('closed'));
            this.recomputeBottomOffset();
        },

        /**
         * Checks if the panel is closed
         *
         * @return {Boolean}
         */
        isClosed: function() {
            return this.$el.hasClass(csscls('closed'));
        },

        /**
         * Restore the debug bar
         *
         * @this {DebugBar}
         */
        restore: function() {
            this.$resizehdle.show();
            this.$header.show();
            this.$restorebtn.hide();
            localStorage.setItem('phpdebugbar-open', '1');
            var tab = localStorage.getItem('phpdebugbar-tab');
            if (this.isTab(tab)) {
                this.showTab(tab);
            } else {
                this.showTab();
            }
            this.$el.removeClass(csscls('closed'));
            this.resize();
        },

        /**
         * Recomputes the margin-bottom css property of the body so
         * that the debug bar never hides any content
         */
        recomputeBottomOffset: function() {
            if (this.options.bodyMarginBottom) {
                if (this.isClosed()) {
                    return $('body').css('margin-bottom', this.bodyMarginBottomHeight || '');
                }

                var offset = parseInt(this.$el.height()) + (this.bodyMarginBottomHeight || 0);
                $('body').css('margin-bottom', offset);
            }
        },

        /**
         * Sets the data map used by dataChangeHandler to populate
         * indicators and widgets
         *
         * A data map is an object where properties are control names.
         * The value of each property should be an array where the first
         * item is the name of a property from the data object (nested properties
         * can be specified) and the second item the default value.
         *
         * Example:
         *     {"memory": ["memory.peak_usage_str", "0B"]}
         *
         * @this {DebugBar}
         * @param {Object} map
         */
        setDataMap: function(map) {
            this.dataMap = map;
        },

        /**
         * Same as setDataMap() but appends to the existing map
         * rather than replacing it
         *
         * @this {DebugBar}
         * @param {Object} map
         */
        addDataMap: function(map) {
            $.extend(this.dataMap, map);
        },

        /**
         * Resets datasets and add one set of data
         *
         * For this method to be usefull, you need to specify
         * a dataMap using setDataMap()
         *
         * @this {DebugBar}
         * @param {Object} data
         * @return {String} Dataset's id
         */
        setData: function(data) {
            this.datasets = {};
            return this.addDataSet(data);
        },

        /**
         * Adds a dataset
         *
         * If more than one dataset are added, the dataset selector
         * will be displayed.
         *
         * For this method to be usefull, you need to specify
         * a dataMap using setDataMap()
         *
         * @this {DebugBar}
         * @param {Object} data
         * @param {String} id The name of this set, optional
         * @param {String} suffix
         * @param {Bool} show Whether to show the new dataset, optional (default: true)
         * @return {String} Dataset's id
         */
        addDataSet: function(data, id, suffix, show) {
            if (!data || !data.__meta) return;
            if (this.isIframe) {
                window.top.phpdebugbar.addDataSet(data, id, '(iframe)' + (suffix || ''), show);
                return;
            }

            var nb = getObjectSize(this.datasets) + 1;
            id = id || nb;
            data.__meta['nb'] = nb;
            data.__meta['suffix'] = suffix;
            this.datasets[id] = data;

            var label = this.datesetTitleFormater.format(id, this.datasets[id], suffix, nb);

            if (this.datasetTab) {
                this.datasetTab.set('data', this.datasets);
                var datasetSize = getObjectSize(this.datasets);
                this.datasetTab.set('badge', datasetSize > 1 ? datasetSize : null);
                this.datasetTab.$tab.show();
            }

            this.$datasets.append($('<option value="' + id + '">' + label + '</option>'));
            if (this.$datasets.children().length > 1) {
                this.$datasets.show();
            }

            if (typeof(show) == 'undefined' || show) {
                this.showDataSet(id);
            }

            this.resize();

            return id;
        },

        /**
         * Loads a dataset using the open handler
         *
         * @param {String} id
         * @param {Bool} show Whether to show the new dataset, optional (default: true)
         */
        loadDataSet: function(id, suffix, callback, show) {
            if (!this.openHandler) {
                throw new Error('loadDataSet() needs an open handler');
            }
            var self = this;
            this.openHandler.load(id, function(data) {
                self.addDataSet(data, id, suffix, show);
                self.resize();
                callback && callback(data);
            });
        },

        /**
         * Returns the data from a dataset
         *
         * @this {DebugBar}
         * @param {String} id
         * @return {Object}
         */
        getDataSet: function(id) {
            return this.datasets[id];
        },

        /**
         * Switch the currently displayed dataset
         *
         * @this {DebugBar}
         * @param {String} id
         */
        showDataSet: function(id) {
            this.activeDatasetId = id;
            this.dataChangeHandler(this.datasets[id]);

            if (this.$datasets.val() !== id) {
                this.$datasets.val(id);
            }

            if (this.datasetTab) {
                this.datasetTab.get('widget').set('id', id);
            }
        },

        /**
         * Called when the current dataset is modified.
         *
         * @this {DebugBar}
         * @param {Object} data
         */
        dataChangeHandler: function(data) {
            var self = this;
            $.each(this.dataMap, function(key, def) {
                var d = getDictValue(data, def[0], def[1]);
                if (key.indexOf(':') != -1) {
                    key = key.split(':');
                    self.getControl(key[0]).set(key[1], d);
                } else {
                    self.getControl(key).set('data', d);
                }
            });
            self.resize();
        },

        /**
         * Sets the handler to open past dataset
         *
         * @this {DebugBar}
         * @param {object} handler
         */
        setOpenHandler: function(handler) {
            this.openHandler = handler;
            this.openHandler.$el.attr('data-theme', this.$el.attr('data-theme'));
            if (handler !== null) {
                this.$openbtn.show();
            } else {
                this.$openbtn.hide();
            }
        },

        /**
         * Returns the handler to open past dataset
         *
         * @this {DebugBar}
         * @return {object}
         */
        getOpenHandler: function() {
            return this.openHandler;
        },

        enableAjaxHandlerTab: function() {
            this.datasetTab = new PhpDebugBar.DebugBar.Tab({"icon":"history", "title":"Request history", "widget": new PhpDebugBar.Widgets.DatasetWidget({
                    'debugbar': this
                })});
            this.datasetTab.$tab.addClass(csscls('tab-history'));
            this.datasetTab.$tab.attr('data-collector', '__datasets');
            this.datasetTab.$el.attr('data-collector', '__datasets');
            this.datasetTab.$tab.insertAfter(this.$openbtn).hide();
            this.datasetTab.$tab.click(() => {
                if (!this.isMinimized() && this.activePanelName == '__datasets') {
                    this.minimize();
                } else {
                    this.showTab('__datasets');
                }
            });
            this.datasetTab.$el.appendTo(this.$body);
            this.controls['__datasets'] = this.datasetTab;
        },
    });

    DebugBar.Tab = Tab;
    DebugBar.Indicator = Indicator;

    // ------------------------------------------------------------------

    /**
     * AjaxHandler
     *
     * Extract data from headers of an XMLHttpRequest and adds a new dataset
     *
     * @param {Bool} autoShow Whether to immediately show new datasets, optional (default: true)
     */
    var AjaxHandler = PhpDebugBar.AjaxHandler = function(debugbar, headerName, autoShow) {
        this.debugbar = debugbar;
        this.headerName = headerName || 'phpdebugbar';
        this.autoShow = typeof(autoShow) == 'undefined' ? true : autoShow;
        this.defaultAutoShow = this.autoShow;
        if (localStorage.getItem('phpdebugbar-ajaxhandler-autoshow') !== null) {
            this.autoShow = localStorage.getItem('phpdebugbar-ajaxhandler-autoshow') == '1';
        }
        if (debugbar.controls['__settings']) {
            debugbar.controls['__settings'].get('widget').set('autoshow', this.autoShow);
        }
    };

    $.extend(AjaxHandler.prototype, {

        /**
         * Handles a Fetch API Response or an XMLHttpRequest
         *
         * @this {AjaxHandler}
         * @param {Response|XMLHttpRequest} response
         * @return {Bool}
         */
        handle: function(response) {
            // Check if the debugbar header is available
            if (this.isFetch(response) && !response.headers.has(this.headerName + '-id')) {
                return true;
            } else if (this.isXHR(response) && response.getAllResponseHeaders().indexOf(this.headerName) === -1) {
                return true;
            }
            if (!this.loadFromId(response)) {
                return this.loadFromData(response);
            }
            return true;
        },

        getHeader: function(response, header) {
            if (this.isFetch(response)) {
                return response.headers.get(header)
            }

            return response.getResponseHeader(header)
        },

        isFetch: function(response) {
            return Object.prototype.toString.call(response) == '[object Response]'
        },

        isXHR: function(response) {
            return Object.prototype.toString.call(response) == '[object XMLHttpRequest]'
        },

        setAutoShow: function(autoshow) {
            this.autoShow = autoshow;
            localStorage.setItem('phpdebugbar-ajaxhandler-autoshow', autoshow ? '1' : '0');
        },

        /**
         * Checks if the HEADER-id exists and loads the dataset using the open handler
         *
         * @param {Response|XMLHttpRequest} response
         * @return {Bool}
         */
        loadFromId: function(response) {
            var id = this.extractIdFromHeaders(response);
            if (id && this.debugbar.openHandler) {
                this.debugbar.loadDataSet(id, "(ajax)", undefined, this.autoShow);
                return true;
            }
            return false;
        },

        /**
         * Extracts the id from the HEADER-id
         *
         * @param {Response|XMLHttpRequest} response
         * @return {String}
         */
        extractIdFromHeaders: function(response) {
            return this.getHeader(response, this.headerName + '-id');
        },

        /**
         * Checks if the HEADER exists and loads the dataset
         *
         * @param {Response|XMLHttpRequest} response
         * @return {Bool}
         */
        loadFromData: function(response) {
            var raw = this.extractDataFromHeaders(response);
            if (!raw) {
                return false;
            }

            var data = this.parseHeaders(raw);
            if (data.error) {
                throw new Error('Error loading debugbar data: ' + data.error);
            } else if(data.data) {
                this.debugbar.addDataSet(data.data, data.id, "(ajax)", this.autoShow);
            }
            return true;
        },

        /**
         * Extract the data as a string from headers of an XMLHttpRequest
         *
         * @this {AjaxHandler}
         * @param {Response|XMLHttpRequest} response
         * @return {string}
         */
        extractDataFromHeaders: function(response) {
            var data = this.getHeader(response, this.headerName);
            if (!data) {
                return;
            }
            for (var i = 1;; i++) {
                var header = this.getHeader(response, this.headerName + '-' + i);
                if (!header) {
                    break;
                }
                data += header;
            }
            return decodeURIComponent(data);
        },

        /**
         * Parses the string data into an object
         *
         * @this {AjaxHandler}
         * @param {string} data
         * @return {string}
         */
        parseHeaders: function(data) {
            return JSON.parse(data);
        },

        /**
         * Attaches an event listener to fetch
         *
         * @this {AjaxHandler}
         */
        bindToFetch: function() {
            var self = this;
            var proxied = window.fetch;

            if (proxied !== undefined && proxied.polyfill !== undefined) {
                return;
            }

            window.fetch = function () {
                var promise = proxied.apply(this, arguments);

                promise.then(function (response) {
                    self.handle(response);
                }).catch(function(reason) {
                    // Fetch request failed or aborted via AbortController.abort().
                    // Catch is required to not trigger React's error handler.
                });

                return promise;
            };
        },

        /**
         * @deprecated use bindToXHR instead
         */
        bindToJquery: function(jq) {
            var self = this;
            jq(document).ajaxComplete(function(e, xhr, settings) {
                if (!settings.ignoreDebugBarAjaxHandler) {
                    self.handle(xhr);
                }
            });
        },

        /**
         * Attaches an event listener to XMLHttpRequest
         *
         * @this {AjaxHandler}
         */
        bindToXHR: function() {
            var self = this;
            var proxied = XMLHttpRequest.prototype.open;
            XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
                var xhr = this;
                this.addEventListener("readystatechange", function() {
                    var skipUrl = self.debugbar.openHandler ? self.debugbar.openHandler.get('url') : null;
                    var href = (typeof url === 'string') ? url : url.href;

                    if (xhr.readyState == 4 && href.indexOf(skipUrl) !== 0) {
                        self.handle(xhr);
                    }
                }, false);
                proxied.apply(this, Array.prototype.slice.call(arguments));
            };
        }

    });

})(PhpDebugBar.$);

if (typeof(PhpDebugBar) == 'undefined') {
    // namespace
    var PhpDebugBar = {};
    PhpDebugBar.$ = jQuery;
}

(function($) {

    /**
     * @namespace
     */
    PhpDebugBar.Widgets = {};

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Replaces spaces with &nbsp; and line breaks with <br>
     *
     * @param {String} text
     * @return {String}
     */
    var htmlize = PhpDebugBar.Widgets.htmlize = function(text) {
        return text.replace(/\n/g, '<br>').replace(/\s/g, "&nbsp;")
    };

    /**
     * Returns a string representation of value, using JSON.stringify
     * if it's an object.
     *
     * @param {Object} value
     * @param {Boolean} prettify Uses htmlize() if true
     * @return {String}
     */
    var renderValue = PhpDebugBar.Widgets.renderValue = function(value, prettify) {
        if (typeof(value) !== 'string') {
            if (prettify) {
                return htmlize(JSON.stringify(value, undefined, 2));
            }
            return JSON.stringify(value);
        }
        return value;
    };

    /**
     * Highlights a block of code
     *
     * @param  {String} code
     * @param  {String} lang
     * @return {String}
     */
    var highlight = PhpDebugBar.Widgets.highlight = function(code, lang) {
        if (typeof(code) === 'string') {
            if (typeof(hljs) === 'undefined') {
                return htmlize(code);
            }
            if (lang && hljs.getLanguage(lang)) {
                return hljs.highlight(code, {language: lang}).value;
            }
            return hljs.highlightAuto(code).value;
        }

        if (typeof(hljs) === 'object') {
            code.each(function(i, e) { hljs.highlightElement(e); });
        }
        return code;
    };

    /**
     * Creates a <pre> element with a block of code
     *
     * @param  {String} code
     * @param  {String} lang
     * @param  {Number} [firstLineNumber] If provided, shows line numbers beginning with the given value.
     * @param  {Number} [highlightedLine] If provided, the given line number will be highlighted.
     * @return {String}
     */
    var createCodeBlock = PhpDebugBar.Widgets.createCodeBlock = function(code, lang, firstLineNumber, highlightedLine) {
        var pre = $('<pre />').addClass(csscls('code-block'));
        // Add a newline to prevent <code> element from vertically collapsing too far if the last
        // code line was empty: that creates problems with the horizontal scrollbar being
        // incorrectly positioned - most noticeable when line numbers are shown.
        var codeElement = $('<code />').text(code + '\n').appendTo(pre);

        // Format the code
        if (lang) {
            codeElement.addClass("language-" + lang);
        }
        highlight(codeElement).removeClass('hljs');

        // Show line numbers in a list
        if (!isNaN(parseFloat(firstLineNumber))) {
            var lineCount = code.split('\n').length;
            var $lineNumbers = $('<ul />').prependTo(pre);
            pre.children().addClass(csscls('numbered-code'));
            for (var i = firstLineNumber; i < firstLineNumber + lineCount; i++) {
                var li = $('<li />').text(i).appendTo($lineNumbers);

                // Add a span with a special class if we are supposed to highlight a line.
                if (highlightedLine === i) {
                    li.addClass(csscls('highlighted-line')).append('<span>&nbsp;</span>');
                }
            }
        }

        return pre;
    };

    var getDictValue = PhpDebugBar.utils.getDictValue = function(dict, key, default_value) {
        var d = dict, parts = key.split('.');
        for (var i = 0; i < parts.length; i++) {
            if (!d[parts[i]]) {
                return default_value;
            }
            d = d[parts[i]];
        }
        return d;
    }

    // ------------------------------------------------------------------
    // Generic widgets
    // ------------------------------------------------------------------

    /**
     * Displays array element in a <ul> list
     *
     * Options:
     *  - data
     *  - itemRenderer: a function used to render list items (optional)
     */
    var ListWidget = PhpDebugBar.Widgets.ListWidget = PhpDebugBar.Widget.extend({

        tagName: 'ul',

        className: csscls('list'),

        initialize: function(options) {
            if (!options['itemRenderer']) {
                options['itemRenderer'] = this.itemRenderer;
            }
            this.set(options);
        },

        render: function() {
            this.bindAttr(['itemRenderer', 'data'], function() {
                this.$el.empty();
                if (!this.has('data')) {
                    return;
                }

                var data = this.get('data');
                for (var i = 0; i < data.length; i++) {
                    var li = $('<li />').addClass(csscls('list-item')).appendTo(this.$el);
                    this.get('itemRenderer')(li, data[i]);
                }
            });
        },

        /**
         * Renders the content of a <li> element
         *
         * @param {jQuery} li The <li> element as a jQuery Object
         * @param {Object} value An item from the data array
         */
        itemRenderer: function(li, value) {
            li.html(renderValue(value));
        }

    });

    // ------------------------------------------------------------------

    /**
     * Displays object property/value paris in a <dl> list
     *
     * Options:
     *  - data
     *  - itemRenderer: a function used to render list items (optional)
     */
    var KVListWidget = PhpDebugBar.Widgets.KVListWidget = ListWidget.extend({

        tagName: 'dl',

        className: csscls('kvlist'),

        render: function() {
            this.bindAttr(['itemRenderer', 'data'], function() {
                this.$el.empty();
                if (!this.has('data')) {
                    return;
                }

                var self = this;
                $.each(this.get('data'), function(key, value) {
                    var dt = $('<dt />').addClass(csscls('key')).appendTo(self.$el);
                    var dd = $('<dd />').addClass(csscls('value')).appendTo(self.$el);
                    self.get('itemRenderer')(dt, dd, key, value);
                });
            });
        },

        /**
         * Renders the content of the <dt> and <dd> elements
         *
         * @param {jQuery} dt The <dt> element as a jQuery Object
         * @param {jQuery} dd The <dd> element as a jQuery Object
         * @param {String} key Property name
         * @param {Object} value Property value
         */
        itemRenderer: function(dt, dd, key, value) {
            dt.text(key);
            dd.html(htmlize(value));
        }

    });

    // ------------------------------------------------------------------

    /**
     * An extension of KVListWidget where the data represents a list
     * of variables
     *
     * Options:
     *  - data
     */
    var VariableListWidget = PhpDebugBar.Widgets.VariableListWidget = KVListWidget.extend({

        className: csscls('kvlist varlist'),

        itemRenderer: function(dt, dd, key, value) {
            $('<span />').attr('title', key).text(key).appendTo(dt);

            var v = value && value.value || value;
            if (v && v.length > 100) {
                v = v.substr(0, 100) + "...";
            }
            var prettyVal = null;
            dd.text(v).click(function() {
                if (dd.hasClass(csscls('pretty'))) {
                    dd.text(v).removeClass(csscls('pretty'));
                } else {
                    prettyVal = prettyVal || createCodeBlock(value);
                    dd.addClass(csscls('pretty')).empty().append(prettyVal);
                }
            });
        }

    });

    // ------------------------------------------------------------------

    /**
     * An extension of KVListWidget where the data represents a list
     * of variables whose contents are HTML; this is useful for showing
     * variable output from VarDumper's HtmlDumper.
     *
     * Options:
     *  - data
     */
    var HtmlVariableListWidget = PhpDebugBar.Widgets.HtmlVariableListWidget = KVListWidget.extend({

        className: csscls('kvlist htmlvarlist'),

        itemRenderer: function(dt, dd, key, value) {
            $('<span />').attr('title', $('<i />').html(key || '').text()).html(key || '').appendTo(dt);
            dd.html(value && value.value || value);

            if (value && value.xdebug_link) {
                var header = $('<span />').addClass(csscls('filename')).text(value.xdebug_link.filename + ( value.xdebug_link.line ? "#" + value.xdebug_link.line : ''));
                if (value.xdebug_link) {
                    if (value.xdebug_link.ajax) {
                        $('<a title="' + value.xdebug_link.url + '"></a>').on('click', function () {
                            $.ajax(value.xdebug_link.url);
                        }).addClass(csscls('editor-link')).appendTo(header);
                    } else {
                        $('<a href="' + value.xdebug_link.url + '"></a>').addClass(csscls('editor-link')).appendTo(header);
                    }
                }
                header.appendTo(dd);
            }
        }

    });

    // ------------------------------------------------------------------

    /**
     * Iframe widget
     *
     * Options:
     *  - data
     */
    var IFrameWidget = PhpDebugBar.Widgets.IFrameWidget = PhpDebugBar.Widget.extend({

        tagName: 'iframe',

        className: csscls('iframe'),

        render: function() {
            this.$el.attr({
                seamless: "seamless",
                border: "0",
                width: "100%",
                height: "100%"
            });
            this.bindAttr('data', function(url) { this.$el.attr('src', url); });
        }

    });


    // ------------------------------------------------------------------
    // Collector specific widgets
    // ------------------------------------------------------------------

    /**
     * Widget for the MessagesCollector
     *
     * Uses ListWidget under the hood
     *
     * Options:
     *  - data
     */
    var MessagesWidget = PhpDebugBar.Widgets.MessagesWidget = PhpDebugBar.Widget.extend({

        className: csscls('messages'),

        render: function() {
            var self = this;

            this.$list = new ListWidget({ itemRenderer: function(li, value) {
                    if (value.message_html) {
                        var val = $('<span />').addClass(csscls('value')).html(value.message_html).appendTo(li);
                    } else {
                        var m = value.message;
                        if (m.length > 100) {
                            m = m.substr(0, 100) + "...";
                        }

                        var val = $('<span />').addClass(csscls('value')).text(m).appendTo(li);
                        if (!value.is_string || value.message.length > 100) {
                            var prettyVal = value.message;
                            if (!value.is_string) {
                                prettyVal = null;
                            }
                            li.css('cursor', 'pointer').click(function () {
                                if (window.getSelection().type == "Range") {
                                    return''
                                }
                                if (val.hasClass(csscls('pretty'))) {
                                    val.text(m).removeClass(csscls('pretty'));
                                } else {
                                    prettyVal = prettyVal || createCodeBlock(value.message, 'php');
                                    val.addClass(csscls('pretty')).empty().append(prettyVal);
                                }
                            });
                        }
                    }
                    if (value.xdebug_link) {
                        var header = $('<span />').addClass(csscls('filename')).text(value.xdebug_link.filename + ( value.xdebug_link.line ? "#" + value.xdebug_link.line : ''));
                        if (value.xdebug_link) {
                            if (value.xdebug_link.ajax) {
                                $('<a title="' + value.xdebug_link.url + '"></a>').on('click', function () {
                                    $.ajax(value.xdebug_link.url);
                                }).addClass(csscls('editor-link')).appendTo(header);
                            } else {
                                $('<a href="' + value.xdebug_link.url + '"></a>').addClass(csscls('editor-link')).appendTo(header);
                            }
                        }
                        header.appendTo(li);
                    }
                    if (value.collector) {
                        $('<span />').addClass(csscls('collector')).text(value.collector).prependTo(li);
                    }
                    if (value.label) {
                        val.addClass(csscls(value.label));
                        $('<span />').addClass(csscls('label')).text(value.label).prependTo(li);
                    }
                }});

            this.$list.$el.appendTo(this.$el);
            this.$toolbar = $('<div><i class="phpdebugbar-fa phpdebugbar-fa-search"></i></div>').addClass(csscls('toolbar')).appendTo(this.$el);

            $('<input type="text" name="search" aria-label="Search" placeholder="Search" />')
                .on('change', function() { self.set('search', this.value); })
                .appendTo(this.$toolbar);

            this.bindAttr('data', function(data) {
                this.set({excludelabel: [], excludecollector: [], search: ''});
                this.$toolbar.find(csscls('.filter')).remove();

                var labels = [], collectors = [], self = this,
                    createFilterItem = function (type, value) {
                        $('<a />')
                            .addClass(csscls('filter')).addClass(csscls(type))
                            .text(value).attr('rel', value)
                            .on('click', function() { self.onFilterClick(this, type); })
                            .appendTo(self.$toolbar)
                    };

                data.forEach(function (item) {
                    if (!labels.includes(item.label || 'none')) {
                        labels.push(item.label || 'none');
                    }

                    if (!collectors.includes(item.collector || 'none')) {
                        collectors.push(item.collector || 'none');
                    }
                });

                if (labels.length > 1) {
                    labels.forEach(label => createFilterItem('label', label));
                }

                if (collectors.length === 1) {
                    return;
                }

                $('<a />').addClass(csscls('filter')).css('visibility', 'hidden').appendTo(self.$toolbar);
                collectors.forEach(collector => createFilterItem('collector', collector));
            });

            this.bindAttr(['excludelabel', 'excludecollector', 'search'], function() {
                var excludelabel = this.get('excludelabel') || [],
                    excludecollector = this.get('excludecollector') || [],
                    search = this.get('search'),
                    caseless = false,
                    fdata = [];

                if (search && search === search.toLowerCase()) {
                    caseless = true;
                }

                this.get('data').forEach(function (item) {
                    var message = caseless ? item.message.toLowerCase() : item.message;

                    if (
                        !excludelabel.includes(item.label || undefined) &&
                        !excludecollector.includes(item.collector || undefined) &&
                        (!search || message.indexOf(search) > -1)
                    ) {
                        fdata.push(item);
                    }
                });

                this.$list.set('data', fdata);
            });
        },

        onFilterClick: function(el, type) {
            $(el).toggleClass(csscls('excluded'));

            var excluded = [];
            this.$toolbar.find(csscls('.filter') + csscls('.excluded') + csscls('.' + type)).each(function() {
                excluded.push(this.rel === 'none' || !this.rel ? undefined : this.rel);
            });

            this.set('exclude' + type, excluded);
        }

    });

    // ------------------------------------------------------------------

    /**
     * Widget for the TimeDataCollector
     *
     * Options:
     *  - data
     */
    var TimelineWidget = PhpDebugBar.Widgets.TimelineWidget = PhpDebugBar.Widget.extend({

        tagName: 'ul',

        className: csscls('timeline'),

        render: function() {
            this.bindAttr('data', function(data) {

                // ported from php DataFormatter
                var formatDuration = function(seconds) {
                    if (seconds < 0.001)
                        return (seconds * 1000000).toFixed() + 'μs';
                    else if (seconds < 0.1)
                        return (seconds * 1000).toFixed(2) + 'ms';
                    else if (seconds < 1)
                        return (seconds * 1000).toFixed() + 'ms';
                    return (seconds).toFixed(2) +  's';
                };

                // ported from php DataFormatter
                var formatBytes = function formatBytes(size) {
                    if (size === 0 || size === null) {
                        return '0B';
                    }

                    var sign = size < 0 ? '-' : '',
                        size = Math.abs(size),
                        base = Math.log(size) / Math.log(1024),
                        suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
                    return sign + (Math.round(Math.pow(1024, base - Math.floor(base)) * 100) / 100) + suffixes[Math.floor(base)];
                }

                this.$el.empty();
                if (data.measures) {
                    var aggregate = {};

                    for (var i = 0; i < data.measures.length; i++) {
                        var measure = data.measures[i];

                        if(!aggregate[measure.label])
                            aggregate[measure.label] = { count: 0, duration: 0, memory : 0 };

                        aggregate[measure.label]['count'] += 1;
                        aggregate[measure.label]['duration'] += measure.duration;
                        aggregate[measure.label]['memory'] += (measure.memory || 0);

                        var m = $('<div />').addClass(csscls('measure')),
                            li = $('<li />'),
                            left = (measure.relative_start * 100 / data.duration).toFixed(2),
                            width = Math.min((measure.duration * 100 / data.duration).toFixed(2), 100 - left);

                        m.append($('<span />').addClass(csscls('value')).css({
                            left: left + "%",
                            width: width + "%"
                        }));
                        m.append($('<span />').addClass(csscls('label'))
                            .text(measure.label + " (" + measure.duration_str +(measure.memory ? '/' + measure.memory_str: '') + ")"));

                        if (measure.collector) {
                            $('<span />').addClass(csscls('collector')).text(measure.collector).appendTo(m);
                        }

                        m.appendTo(li);
                        this.$el.append(li);

                        if (measure.params && !$.isEmptyObject(measure.params)) {
                            var table = $('<table><tr><th colspan="2">Params</th></tr></table>').hide().addClass(csscls('params')).appendTo(li);
                            for (var key in measure.params) {
                                if (typeof measure.params[key] !== 'function') {
                                    table.append('<tr><td class="' + csscls('name') + '">' + key + '</td><td class="' + csscls('value') +
                                        '"><pre><code>' + measure.params[key] + '</code></pre></td></tr>');
                                }
                            }
                            li.css('cursor', 'pointer').click(function() {
                                if (window.getSelection().type == "Range") {
                                    return''
                                }
                                var table = $(this).find('table');
                                if (table.is(':visible')) {
                                    table.hide();
                                } else {
                                    table.show();
                                }
                            });
                        }
                    }

                    // convert to array and sort by duration
                    aggregate = $.map(aggregate, function(data, label) {
                        return {
                            label: label,
                            data: data
                        }
                    }).sort(function(a, b) {
                        return b.data.duration - a.data.duration
                    });

                    // build table and add
                    var aggregateTable = $('<table></table>').addClass(csscls('params'));
                    $.each(aggregate, function(i, aggregate) {
                        width = Math.min((aggregate.data.duration * 100 / data.duration).toFixed(2), 100);

                        aggregateTable.append('<tr><td class="' + csscls('name') + '">' +
                            aggregate.data.count + ' x ' + $('<i />').text(aggregate.label).html() + ' (' + width + '%)</td><td class="' + csscls('value') + '">' +
                            '<div class="' + csscls('measure') +'">' +
                            '<span class="' + csscls('value') + '"></span>' +
                            '<span class="' + csscls('label') + '">' + formatDuration(aggregate.data.duration) + (aggregate.data.memory ? '/' + formatBytes(aggregate.data.memory) : '') + '</span>' +
                            '</div></td></tr>');
                        aggregateTable.find('span.' + csscls('value') + ':last').css({width: width + "%" });
                    });

                    this.$el.append('<li/>').find('li:last').append(aggregateTable);
                }
            });
        }

    });

    // ------------------------------------------------------------------

    /**
     * Widget for the displaying exceptions
     *
     * Options:
     *  - data
     */
    var ExceptionsWidget = PhpDebugBar.Widgets.ExceptionsWidget = PhpDebugBar.Widget.extend({

        className: csscls('exceptions'),

        render: function() {
            this.$list = new ListWidget({ itemRenderer: function(li, e) {
                    $('<span />').addClass(csscls('message')).text(e.message).appendTo(li);
                    if (e.file) {
                        var header = $('<span />').addClass(csscls('filename')).text(e.file + "#" + e.line);
                        if (e.xdebug_link) {
                            if (e.xdebug_link.ajax) {
                                $('<a title="' + e.xdebug_link.url + '"></a>').on('click', function () {
                                    fetch(e.xdebug_link.url);
                                }).addClass(csscls('editor-link')).appendTo(header);
                            } else {
                                $('<a href="' + e.xdebug_link.url + '"></a>').addClass(csscls('editor-link')).appendTo(header);
                            }
                        }
                        header.appendTo(li);
                    }
                    if (e.type) {
                        $('<span />').addClass(csscls('type')).text(e.type).appendTo(li);
                    }
                    if (e.surrounding_lines) {
                        var startLine = (e.line - 3) <= 0 ? 1 : e.line - 3;
                        var pre = createCodeBlock(e.surrounding_lines.join(""), 'php', startLine, e.line).addClass(csscls('file')).appendTo(li);
                        if (!e.stack_trace_html) {
                            // This click event makes the var-dumper hard to use.
                            li.click(function () {
                                if (pre.is(':visible')) {
                                    pre.hide();
                                } else {
                                    pre.show();
                                }
                            });
                        }
                    }
                    if (e.stack_trace_html) {
                        var $trace = $('<span />').addClass(csscls('filename')).html(e.stack_trace_html);
                        $trace.appendTo(li);
                    } else if (e.stack_trace) {
                        e.stack_trace.split("\n").forEach(function (trace) {
                            var $traceLine = $('<div />');
                            $('<span />').addClass(csscls('filename')).text(trace).appendTo($traceLine);
                            $traceLine.appendTo(li);
                        });
                    }
                }});
            this.$list.$el.appendTo(this.$el);

            this.bindAttr('data', function(data) {
                this.$list.set('data', data);
                if (data.length == 1) {
                    this.$list.$el.children().first().find(csscls('.file')).show();
                }
            });

        }

    });

    /**
     * Displays datasets in a table
     *
     */
    var DatasetWidget = PhpDebugBar.Widgets.DatasetWidget = PhpDebugBar.Widget.extend({

        initialize: function(options) {
            if (!options['itemRenderer']) {
                options['itemRenderer'] = this.itemRenderer;
            }
            this.set(options);
            this.set('autoshow', null);
            this.set('id', null);
            this.set('sort', localStorage.getItem('debugbar-history-sort') || 'asc');
            this.$el.addClass(csscls('dataset-history'))

            this.renderHead();
        },

        renderHead: function() {
            this.$el.empty();
            this.$actions = $('<div />').addClass(csscls('dataset-actions')).appendTo(this.$el);

            var self = this;
            var debugbar = self.get('debugbar');

            this.$autoshow = $('<input type=checkbox>')
                .on('click', function() {
                    if (debugbar.ajaxHandler) {
                        debugbar.ajaxHandler.setAutoShow($(this).is(':checked'));
                    }
                    if (debugbar.controls['__settings']) {
                        debugbar.controls['__settings'].get('widget').set('autoshow', this.autoShow);
                    }
                });

            $('<label>Autoshow</label>')
                .append(this.$autoshow)
                .appendTo(this.$actions)


            this.$clearbtn = $('<a>Clear</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.$table.empty();
                });

            this.$showBtn = $('<a>Show all</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.searchInput.val(null);
                    self.methodInput.val(null);
                    self.set('search', null);
                    self.set('method', null);
                });

            this.methodInput = $('<select name="method" style="width:100px"><option>(method)</option><option>GET</option><option>POST</option><option>PUT</option><option>DELETE</option></select>')
                .on('change', function() { self.set('method', this.value)})
                .appendTo(this.$actions)

            this.searchInput = $('<input type="text" name="search" aria-label="Search" placeholder="Search" />')
                .on('input', function() { self.set('search', this.value); })
                .appendTo(this.$actions);


            this.$table = $('<tbody />');

            $('<table/>')
                .append($('<thead/>')
                    .append($('<tr/>')
                        .append($('<th></th>').css('width', '30px'))
                        .append($('<th>Date ↕</th>').css('width', '175px').click(function() {
                            self.set('sort', self.get('sort') === 'asc' ? 'desc' : 'asc')
                            localStorage.setItem('debugbar-history-sort', self.get('sort'))
                        }))
                        .append($('<th>Method</th>').css('width', '80px'))
                        .append($('<th>URL</th>'))
                        .append($('<th width="40%">Data</th>')))
                )
                .append(this.$table)
                .appendTo(this.$el);


        },

        renderDatasets: function() {
            this.$table.empty();
            var self = this;
            $.each(this.get('data'), function(key, data) {
                if (!data.__meta) {
                    return;
                }

                self.get('itemRenderer')(self, data);
            });
        },

        render: function() {
            this.bindAttr('data', function() {
                if (this.get('autoshow') === null && this.get('debugbar').ajaxHandler) {
                    this.set('autoshow', this.get('debugbar').ajaxHandler.autoShow);
                }

                if (!this.has('data')) {
                    return;
                }

                // Render the latest item
                var datasets = this.get('data');
                var data = datasets[Object.keys(datasets)[Object.keys(datasets).length - 1]]
                if (!data.__meta) {
                    return;
                }

                this.get('itemRenderer')(this, data);
            });
            this.bindAttr(['itemRenderer', 'search', 'method', 'sort'], function() {
                this.renderDatasets();
            })
            this.bindAttr('autoshow', function() {
                var autoshow = this.get('autoshow');
                this.$autoshow.prop('checked', autoshow);
            })
            this.bindAttr('id', function() {
                var id = this.get('id');
                this.$table.find('.' + csscls('active')).removeClass(csscls('active'));
                this.$table.find('tr[data-id=' + id+']').addClass(csscls('active'));
            })
        },

        /**
         * Renders the content of a dataset item
         *
         * @param {Object} value An item from the data array
         */
        itemRenderer: function(widget, data) {
            var meta = data.__meta;

            var $badges = $('<td />');
            var tr = $('<tr />');
            if (widget.get('sort') === 'asc') {
                tr.appendTo(widget.$table);
            } else {
                tr.prependTo(widget.$table);
            }

            var clickHandler = function() {
                var debugbar = widget.get('debugbar');
                debugbar.showDataSet(meta.id, debugbar.datesetTitleFormater.format('', data, meta.suffix, meta.nb));
                widget.$table.find('.' + csscls('active')).removeClass(csscls('active'));
                tr.addClass(csscls('active'));

                if ($(this).data('tab')) {
                    debugbar.showTab($(this).data('tab'));
                }
            }

            tr.attr('data-id', meta['id'])
                .append($('<td>#' + meta['nb'] + '</td>').click(clickHandler))
                .append($('<td>' + meta['datetime'] + '</td>').click(clickHandler))
                .append($('<td>' + meta['method'] + '</td>').click(clickHandler))
                .append($('<td />').append(meta['uri'] + (meta['suffix'] ? ' ' + meta['suffix'] : '')).click(clickHandler))
                .css('cursor', 'pointer')
                .addClass(csscls('table-row'))

            var debugbar = widget.get('debugbar');
            $.each(debugbar.dataMap, function(key, def) {
                var d = getDictValue(data, def[0], def[1]);
                if (key.indexOf(':') != -1) {
                    key = key.split(':');
                    if (key[1] === 'badge' && d > 0) {
                        var control = debugbar.getControl(key[0]);
                        var $a = $('<a>').attr('title', control.get('title')).data('tab', key[0]);
                        if (control.$icon) {
                            $a.append(debugbar.getControl(key[0]).$icon.clone());
                        }
                        if (control.$badge) {
                            $a.append(debugbar.getControl(key[0]).$badge.clone().css('display', 'inline-block').text(d));
                        }
                        $a.appendTo($badges).click(clickHandler);
                    }
                }
            });
            tr.append($badges);

            if (debugbar.activeDatasetId === meta['id']) {
                tr.addClass(csscls('active'));
            }

            var search = widget.get('search');
            var method = widget.get('method');
            if ((search && meta['uri'].indexOf(search) == -1) || (method && meta['method'] !== method)) {
                tr.hide();
            }
        }

    });



})(PhpDebugBar.$);

if (typeof(PhpDebugBar) == 'undefined') {
    // namespace
    var PhpDebugBar = {};
    PhpDebugBar.$ = jQuery;
}

(function($) {

    var csscls = function(cls) {
        return PhpDebugBar.utils.csscls(cls, 'phpdebugbar-openhandler-');
    };

    PhpDebugBar.OpenHandler = PhpDebugBar.Widget.extend({

        className: 'phpdebugbar-openhandler',

        defaults: {
            items_per_page: 20
        },

        render: function() {
            var self = this;

            this.$el.appendTo('body').hide();
            this.$closebtn = $('<a><i class="phpdebugbar-fa phpdebugbar-fa-times"></i></a>');
            this.$table = $('<tbody />');
            $('<div>PHP DebugBar | Open</div>').addClass(csscls('header')).append(this.$closebtn).appendTo(this.$el);
            $('<table><thead><tr><th width="155">Date</th><th width="75">Method</th><th>URL</th><th width="125">IP</th><th width="100">Filter data</th></tr></thead></table>').append(this.$table).appendTo(this.$el);
            this.$actions = $('<div />').addClass(csscls('actions')).appendTo(this.$el);

            this.$closebtn.on('click', function() {
                self.hide();
            });

            this.$loadmorebtn = $('<a>Load more</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.find(self.last_find_request, self.last_find_request.offset + self.get('items_per_page'), self.handleFind.bind(self));
                });

            this.$showonlycurrentbtn = $('<a>Show only current URL</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.$table.empty();
                    self.find({uri: window.location.pathname}, 0, self.handleFind.bind(self));
                });

            this.$showallbtn = $('<a>Show all</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.refresh();
                });

            this.$clearbtn = $('<a>Delete all</a>')
                .appendTo(this.$actions)
                .on('click', function() {
                    self.clear(function() {
                        self.hide();
                    });
                });

            this.addSearch();

            this.$overlay = $('<div />').addClass(csscls('overlay')).hide().appendTo('body');
            this.$overlay.on('click', function() {
                self.hide();
            });
        },

        refresh: function() {
            this.$table.empty();
            this.$loadmorebtn.show();
            this.find({}, 0, this.handleFind.bind(this));
        },

        addSearch: function(){
            var self = this;
            var searchBtn = $('<button />')
                .text('Search')
                .attr('type', 'submit')
                .on('click', function(e) {
                    self.$table.empty();
                    var search = {};
                    var a = $(this).parent().serializeArray();
                    $.each(a, function() {
                        if(this.value){
                            search[this.name] = this.value;
                        }
                    });

                    self.find(search, 0, self.handleFind.bind(self));
                    e.preventDefault();
                });

            $('<form />')
                .append('<br/><b>Filter results</b><br/>')
                .append('<select name="method"><option selected>(Method)</option><option>GET</option><option>POST</option><option>PUT</option><option>DELETE</option></select>')
                .append('<input type="text" name="uri" placeholder="URI">')
                .append('<input type="text" name="ip" placeholder="IP">')
                .append(searchBtn)
                .appendTo(this.$actions);
        },

        handleFind: function(data) {
            var self = this;
            $.each(data, function(i, meta) {
                var a = $('<a />')
                    .text('Load dataset')
                    .on('click', function(e) {
                        self.hide();
                        self.load(meta['id'], function(data) {
                            self.callback(meta['id'], data);
                        });
                        e.preventDefault();
                    });

                var method = $('<a />')
                    .text(meta['method'])
                    .on('click', function(e) {
                        self.$table.empty();
                        self.find({method: meta['method']}, 0, self.handleFind.bind(self));
                        e.preventDefault();
                    });

                var uri = $('<a />')
                    .text(meta['uri'])
                    .on('click', function(e) {
                        self.hide();
                        self.load(meta['id'], function(data) {
                            self.callback(meta['id'], data);
                        });
                        e.preventDefault();
                    });

                var ip = $('<a />')
                    .text(meta['ip'])
                    .on('click', function(e) {
                        self.$table.empty();
                        self.find({ip: meta['ip']}, 0, self.handleFind.bind(self));
                        e.preventDefault();
                    });

                var search = $('<a />')
                    .text('Show URL')
                    .on('click', function(e) {
                        self.$table.empty();
                        self.find({uri: meta['uri']}, 0, self.handleFind.bind(self));
                        e.preventDefault();
                    });

                $('<tr />')
                    .append('<td>' + meta['datetime'] + '</td>')
                    .append('<td>' + meta['method'] + '</td>')
                    .append($('<td />').append(uri))
                    .append($('<td />').append(ip))
                    .append($('<td />').append(search))
                    .appendTo(self.$table);
            });
            if (data.length < this.get('items_per_page')) {
                this.$loadmorebtn.hide();
            }
        },

        show: function(callback) {
            this.callback = callback;
            this.$el.show();
            this.$overlay.show();
            this.refresh();
        },

        hide: function() {
            this.$el.hide();
            this.$overlay.hide();
        },

        find: function(filters, offset, callback) {
            var data = $.extend({}, filters, {max: this.get('items_per_page'), offset: offset || 0});
            this.last_find_request = data;
            this.ajax(data, callback);
        },

        load: function(id, callback) {
            this.ajax({op: "get", id: id}, callback);
        },

        clear: function(callback) {
            this.ajax({op: "clear"}, callback);
        },

        ajax: function(data, callback) {
            var url = this.get('url');
            if (data) {
                url = url + ( url.includes('?') ? '&' : '?' ) + new URLSearchParams(data);
            }

            fetch(url, {
                method: "GET",
                headers: {
                    "Accept": "application/json",
                },
            })
                .then((data) => data.json())
                .then(callback);
        }

    });

})(PhpDebugBar.$);

(function ($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying cache events
     *
     * Options:
     *  - data
     */
    var LaravelCacheWidget = PhpDebugBar.Widgets.LaravelCacheWidget = PhpDebugBar.Widgets.TimelineWidget.extend({

        tagName: 'ul',

        className: csscls('timeline cache'),

        onForgetClick: function (e, el) {
            e.stopPropagation();

            $.ajax({
                url: $(el).attr("data-url"),
                type: 'DELETE',
                success: function (result) {
                    $(el).fadeOut(200);
                }
            });
        },

        render: function () {
            LaravelCacheWidget.__super__.render.apply(this);

            this.bindAttr('data', function (data) {

                if (data.measures) {
                    var self = this;
                    var lines = this.$el.find('.' + csscls('measure'));

                    for (var i = 0; i < data.measures.length; i++) {
                        var measure = data.measures[i];
                        var m = lines[i];

                        if (measure.params && !$.isEmptyObject(measure.params)) {
                            if (measure.params.delete && measure.params.key) {
                                $('<a />')
                                    .addClass(csscls('forget'))
                                    .text('forget')
                                    .attr('data-url', measure.params.delete)
                                    .one('click', function (e) {
                                        self.onForgetClick(e, this); })
                                    .appendTo(m);
                            }
                        }
                    }
                }
            });
        }
    });

})(PhpDebugBar.$);

(function($) {

    let css = PhpDebugBar.utils.makecsscls('phpdebugbar-');
    let csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for displaying sql queries.
     *
     * Options:
     *  - data
     */
    const QueriesWidget = PhpDebugBar.Widgets.LaravelQueriesWidget = PhpDebugBar.Widget.extend({

        className: csscls('sqlqueries'),

        duplicateQueries: new Set(),

        hiddenConnections: new Set(),

        copyToClipboard: function (code) {
            if (document.selection) {
                const range = document.body.createTextRange();
                range.moveToElementText(code);
                range.select();
            } else if (window.getSelection) {
                const range = document.createRange();
                range.selectNodeContents(code);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
            }

            var isCopied = false;
            try {
                isCopied = document.execCommand('copy');
                console.log('Query copied to the clipboard');
            } catch (err) {
                alert('Oops, unable to copy');
            }

            window.getSelection().removeAllRanges();

            return isCopied;
        },

        explainMysql: function ($element, statement, rows, visual) {
            const headings = [];
            for (const key in rows[0]) {
                headings.push($('<th/>').text(key));
            }

            const values = [];
            for (const row of rows) {
                const $tr = $('<tr/>');
                for (const key in row) {
                    $tr.append($('<td/>').text(row[key]));
                }
                values.push($tr);
            }

            const $table = $('<table><thead></thead><tbody></tbody></table>').addClass(csscls('explain'));
            $table.find('thead').append($('<tr/>').append(headings));
            $table.find('tbody').append(values);

            $element.append($table);
            if (visual) {
                $element.append(this.explainVisual(statement, visual.confirm));
            }
        },

        explainPgsql: function ($element, statement, rows, visual) {
            const $ul = $('<ul />').addClass(csscls('table-list'));
            const $li = $('<li />').addClass(csscls('table-list-item'));

            for (const row of rows) {
                $ul.append($li.clone().html($('<span/>').text(row).text().replaceAll(' ', '&nbsp;')));
            }

            $element.append([$ul, this.explainVisual(statement, visual.confirm)]);
        },

        explainVisual: function (statement, confirmMessage) {
            const $explainLink = $('<a href="#" target="_blank" rel="noopener"/>')
                .addClass(csscls('visual-link'));
            const $explainButton = $('<a>Visual Explain</a>')
                .addClass(csscls('visual-explain'))
                .on('click', () => {
                    if (!confirm(confirmMessage)) return;
                    fetch(statement.explain.url, {
                        method: "POST",
                        body: JSON.stringify({
                            connection: statement.explain.connection,
                            query: statement.explain.query,
                            bindings: statement.bindings,
                            hash: statement.explain.hash,
                            mode: 'visual',
                        }),
                    }).then(response => {
                        response.json()
                            .then(json => {
                                if (!response.ok) return alert(json.message);
                                $explainLink.attr('href', json.data).text(json.data);
                                window.open(json.data, '_blank', 'noopener');
                            })
                            .catch(err => alert(`Response body could not be parsed. (${err})`));
                    }).catch(e => {
                        alert(e.message);
                    });
                });

            return $('<div/>').append([$explainButton, $explainLink]);
        },

        identifyDuplicates: function(statements) {
            if (! Array.isArray(statements)) statements = [];

            const makeStatementHash = (statement) => {
                return [
                    statement.sql,
                    statement.connection,
                    JSON.stringify(statement.bindings),
                ].join('::');
            };

            const countedStatements = {};
            for (const statement of statements) {
                if (statement.type === 'query') {
                    countedStatements[makeStatementHash(statement)] = (countedStatements[makeStatementHash(statement)] ?? 0) + 1;
                }
            }

            this.duplicateQueries = new Set();
            for (const statement of statements) {
                if (countedStatements[makeStatementHash(statement)] > 1) {
                    this.duplicateQueries.add(statement);
                }
            }
        },

        render: function () {
            const $status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

            const $list = new PhpDebugBar.Widgets.ListWidget({
                itemRenderer: this.renderQuery.bind(this),
            });
            this.$el.append($list.$el);

            this.bindAttr('data', function (data) {
                this.identifyDuplicates(data.statements);

                this.renderStatus($status, data);
                $list.set('data', data.statements);
            });
        },

        renderStatus: function ($status, data) {
            $status.empty();

            const connections = new Set();
            for (const statement of data.statements) {
                connections.add(statement.connection);
            }

            const $text = $('<span />').text(`${data.nb_statements} ${data.nb_statements == 1 ? 'statement was' : 'statements were'} executed`);
            if (data.nb_excluded_statements) {
                $text.append(`, ${data.nb_excluded_statements} ${data.nb_excluded_statements == 1 ? 'has' : 'have'} been excluded`);
            }
            if (data.nb_failed_statements > 0 || this.duplicateQueries.size > 0) {
                const details = [];
                if (data.nb_failed_statements) {
                    details.push(`${data.nb_failed_statements} failed`);
                }
                if (this.duplicateQueries.size > 0) {
                    details.push(`${this.duplicateQueries.size} ${this.duplicateQueries.size == 1 ? 'duplicate' : 'duplicates'}`);
                }
                $text.append(` (${details.join(', ')})`);
            }
            $status.append($text);

            const filters = [];
            if (this.duplicateQueries.size > 0) {
                filters.push($('<a />')
                    .text('Show only duplicates')
                    .addClass(csscls('duplicates'))
                    .click((event) => {
                        if ($(event.target).text() === 'Show only duplicates') {
                            $(event.target).text('Show All');
                            this.$el.find('[data-duplicate=false]').hide();
                        } else {
                            $(event.target).text('Show only duplicates');
                            this.$el.find('[data-duplicate]').show();
                        }
                    })
                );
            }
            if (connections.size > 1) {
                for (const connection of connections.values()) {
                    filters.push($('<a />')
                        .addClass(csscls('connection'))
                        .text(connection)
                        .attr({'data-filter': connection, 'data-active': true})
                        .on('click', (event) => {
                            if ($(event.target).attr('data-active') === 'true') {
                                $(event.target).attr('data-active', false).css('opacity', 0.3);
                                this.hiddenConnections.add($(event.target).attr('data-filter'));
                            } else {
                                $(event.target).attr('data-active', true).css('opacity', 1.0);
                                this.hiddenConnections.delete($(event.target).attr('data-filter'));
                            }

                            this.$el.find(`[data-connection]`).show();
                            for (const hiddenConnection of this.hiddenConnections) {
                                this.$el.find(`[data-connection="${hiddenConnection}"]`).hide();
                            }
                        })
                    );
                }
            }
            $status.append(filters);

            if (data.accumulated_duration_str) {
                $status.append($('<span title="Accumulated duration" />').addClass(csscls('duration')).text(data.accumulated_duration_str));
            }
            if (data.memory_usage_str) {
                $status.append($('<span title="Memory usage" />').addClass(csscls('memory')).text(data.memory_usage_str));
            }
        },

        renderQuery: function ($li, statement) {
            if (statement.type === 'transaction') {
                $li.attr('data-connection', statement.connection)
                    .attr('data-duplicate', false)
                    .append($('<strong />').addClass(csscls('sql name')).text(statement.sql));
            } else {
                const $code = $('<code />').html(PhpDebugBar.Widgets.highlight(statement.sql, 'sql')).addClass(csscls('sql')),
                    duplicated = this.duplicateQueries.has(statement);
                $li.attr('data-connection', statement.connection)
                    .attr('data-duplicate', duplicated)
                    .toggleClass(csscls('sql-duplicate'), duplicated)
                    .append($code);

                if (statement.show_copy) {
                    $('<span title="Copy to clipboard" />')
                        .addClass(csscls('copy-clipboard'))
                        .css('cursor', 'pointer')
                        .on('click', (event) => {
                            event.stopPropagation();
                            if (this.copyToClipboard($code.get(0))) {
                                $(event.target).addClass(csscls('copy-clipboard-check'));
                                setTimeout(function(){
                                    $(event.target).removeClass(csscls('copy-clipboard-check'));
                                }, 2000)
                            }
                        }).prependTo($li);
                }
            }

            if (statement.width_percent) {
                $('<div />').addClass(csscls('bg-measure')).append(
                    $('<div />').addClass(csscls('value')).css({
                        left: `${statement.start_percent}%`,
                        width: `${Math.max(statement.width_percent, 0.01)}%`,
                    })
                ).appendTo($li);
            }

            if ('is_success' in statement && !statement.is_success) {
                $li.addClass(csscls('error')).prepend($('<span />').addClass(csscls('error')).text(`[${statement.error_code}] ${statement.error_message}`));
            }
            if (statement.duration_str) {
                $li.prepend($('<span title="Duration" />').addClass(csscls('duration')).text(statement.duration_str));
            }
            if (statement.memory_str) {
                $li.prepend($('<span title="Memory usage" />').addClass(csscls('memory')).text(statement.memory_str));
            }
            if (statement.connection) {
                $li.prepend($('<span title="Connection" />').addClass(csscls('database')).text(statement.connection));
            }
            if (statement.xdebug_link) {
                $('<span title="Filename" />')
                    .addClass(csscls('filename'))
                    .text(statement.xdebug_link.filename + '#' + (statement.xdebug_link.line || '?'))
                    .append($('<a/>')
                        .attr('href', statement.xdebug_link.url)
                        .addClass(csscls('editor-link'))
                        .on('click', event => {
                            event.stopPropagation();
                            if (statement.xdebug_link.ajax) {
                                event.preventDefault();
                                fetch(statement.xdebug_link.url);
                            }
                        })
                    ).prependTo($li);
            }

            const $details = $('<table></table>').addClass(csscls('params'))
            if (statement.bindings && !$.isEmptyObject(statement.bindings)) {
                $details.append(this.renderDetailStrings('Bindings', 'thumb-tack', statement.bindings, true));
            }
            if (statement.hints && !$.isEmptyObject(statement.hints)) {
                $details.append(this.renderDetailStrings('Hints', 'question-circle', statement.hints));
            }
            if (statement.backtrace && !$.isEmptyObject(statement.backtrace)) {
                $details.append(this.renderDetailBacktrace('Backtrace', 'list-ul', statement.backtrace));
            }
            if (statement.explain && ['mariadb', 'mysql'].includes(statement.explain.driver)) {
                $details.append(this.renderDetailExplain('Performance', 'tachometer', statement, this.explainMysql.bind(this)));
            }
            if (statement.explain && statement.explain.driver === 'pgsql') {
                $details.append(this.renderDetailExplain('Performance', 'tachometer', statement, this.explainPgsql.bind(this)));
            }

            if($details.children().length) {
                $li.addClass(csscls('expandable'))
                    .on('click', (event) => {
                        if (window.getSelection().type == "Range") {
                            return;
                        }

                        if ($(event.target).closest(`.${csscls('params')}`).length) {
                            return;
                        }

                        if ($li.find(`.${csscls('params')}:visible`).length) {
                            $li.find(`.${csscls('params')}`).css('display', 'none');
                        } else {
                            $li.find(`.${csscls('params')}`).css('display', 'table');
                        }
                    });
            }

            $li.append($details);
        },

        renderDetail: function (caption, icon, $value) {
            return $('<tr />').append(
                $('<td />').addClass(csscls('name')).html(caption + ((icon || '') && `<i class="${css('text-muted fa fa-'+icon)}" />`)),
                $('<td />').addClass(csscls('value')).append($value),
            );
        },

        renderDetailStrings: function (caption, icon, values, showLineNumbers = false) {
            const $ul = $('<ul />').addClass(csscls('table-list'));
            const $li = $('<li />').addClass(csscls('table-list-item'));
            const $muted = $('<span />').addClass(css('text-muted'));

            $.each(values, (i, value) => {
                if (showLineNumbers) {
                    $ul.append($li.clone().append([$muted.clone().text(`${i}:`), '&nbsp;', $('<span/>').text(value)]));
                } else {
                    if (caption === 'Hints') {
                        $ul.append($li.clone().append(value));
                    } else {
                        $ul.append($li.clone().text(value));
                    }
                }
            });

            return this.renderDetail(caption, icon, $ul);
        },

        renderDetailBacktrace: function (caption, icon, traces) {
            const $muted = $('<span />').addClass(css('text-muted'));

            const values = [];
            for (const trace of traces.values()) {
                const $span = $('<span/>').text(trace.name || trace.file);
                if (trace.namespace) {
                    $span.prepend(`${trace.namespace}::`);
                }
                if (trace.line) {
                    $span.append($muted.clone().text(`:${trace.line}`));
                }

                values.push($span.text());
            }

            return this.renderDetailStrings(caption, icon, values);
        },

        renderDetailExplain: function (caption, icon, statement, explainFn) {
            const $btn = $('<button/>')
                .text('Run EXPLAIN')
                .addClass(csscls('explain-btn'))
                .on('click', () => {
                    fetch(statement.explain.url, {
                        method: "POST",
                        body: JSON.stringify({
                            connection: statement.explain.connection,
                            query: statement.explain.query,
                            bindings: statement.bindings,
                            hash: statement.explain.hash,
                        }),
                    }).then(response => {
                        response.json()
                            .then(json => {
                                if (!response.ok) return alert(json.message);
                                $detail.find(`.${csscls('value')}`).children().remove();
                                explainFn($detail.find(`.${csscls('value')}`), statement, json.data, json.visual);
                            })
                            .catch(err => alert(`Response body could not be parsed. (${err})`));
                    }).catch(e => {
                        alert(e.message);
                    });
                });
            const $detail = this.renderDetail(caption, icon, $btn);

            return $detail;
        },
    });
})(PhpDebugBar.$);

(function($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying templates data
     *
     * Options:
     *  - data
     */
    var TemplatesWidget = PhpDebugBar.Widgets.TemplatesWidget = PhpDebugBar.Widget.extend({

        className: csscls('templates'),

        render: function() {
            this.$status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

            this.$list = new  PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, tpl) {
                    $('<span />').addClass(csscls('name')).text(tpl.name).appendTo(li);

                    if (typeof tpl.xdebug_link !== 'undefined' && tpl.xdebug_link !== null) {
                        var header = $('<span />').addClass(csscls('filename')).text(tpl.xdebug_link.filename + ( tpl.xdebug_link.line ? "#" + tpl.xdebug_link.line : ''));
                        if (tpl.xdebug_link) {
                            if (tpl.xdebug_link.ajax) {
                                $('<a title="' + tpl.xdebug_link.url + '"></a>').on('click', function () {
                                    fetch(tpl.xdebug_link.url);
                                }).addClass(csscls('editor-link')).appendTo(header);
                            } else {
                                $('<a href="' + tpl.xdebug_link.url + '"></a>').addClass(csscls('editor-link')).appendTo(header);
                            }
                        }
                        header.appendTo(li);
                    }

                    if (tpl.render_time_str) {
                        $('<span title="Render time" />').addClass(csscls('render-time')).text(tpl.render_time_str).appendTo(li);
                    }
                    if (tpl.memory_str) {
                        $('<span title="Memory usage" />').addClass(csscls('memory')).text(tpl.memory_str).appendTo(li);
                    }
                    if (typeof(tpl.param_count) != 'undefined') {
                        $('<span title="Parameter count" />').addClass(csscls('param-count')).text(tpl.param_count).appendTo(li);
                    }
                    if (typeof(tpl.type) != 'undefined' && tpl.type) {
                        $('<span title="Type" />').addClass(csscls('type')).text(tpl.type).appendTo(li);
                    }
                    if (typeof(tpl.editorLink) != 'undefined' && tpl.editorLink) {
                        $('<a href="'+ tpl.editorLink +'" />').on('click', function (event) {
                            event.stopPropagation();
                        }).addClass(csscls('editor-link')).text('file').appendTo(li);
                    }
                    if (tpl.params && !$.isEmptyObject(tpl.params)) {
                        var table = $('<table><tr><th colspan="2">Params</th></tr></table>').addClass(csscls('params')).appendTo(li);
                        for (var key in tpl.params) {
                            if (typeof tpl.params[key] !== 'function') {
                                table.append('<tr><td class="' + csscls('name') + '">' + key + '</td><td class="' + csscls('value') +
                                    '"><pre><code>' + tpl.params[key] + '</code></pre></td></tr>');
                            }
                        }
                        li.css('cursor', 'pointer').click(function() {
                            if (window.getSelection().type == "Range") {
                                return''
                            }
                            if (table.is(':visible')) {
                                table.hide();
                            } else {
                                table.show();
                            }
                        });
                    }
                }});
            this.$list.$el.appendTo(this.$el);
            this.$callgraph = $('<div />').addClass(csscls('callgraph')).appendTo(this.$el);

            this.bindAttr('data', function(data) {
                this.$list.set('data', data.templates);
                this.$status.empty();
                this.$callgraph.empty();

                var sentence = data.sentence || "templates were rendered";
                $('<span />').text(data.nb_templates + " " + sentence).appendTo(this.$status);

                if (data.accumulated_render_time_str) {
                    this.$status.append($('<span title="Accumulated render time" />').addClass(csscls('render-time')).text(data.accumulated_render_time_str));
                }
                if (data.memory_usage_str) {
                    this.$status.append($('<span title="Memory usage" />').addClass(csscls('memory')).text(data.memory_usage_str));
                }
                if (data.nb_blocks > 0) {
                    $('<div />').text(data.nb_blocks + " blocks were rendered").appendTo(this.$status);
                }
                if (data.nb_macros > 0) {
                    $('<div />').text(data.nb_macros + " macros were rendered").appendTo(this.$status);
                }
                if (typeof data.callgraph !== 'undefined') {
                    this.$callgraph.html(data.callgraph);
                }
            });
        }

    });

})(PhpDebugBar.$);

(function($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying sql queries
     *
     * Options:
     *  - data
     */
    var SQLQueriesWidget = PhpDebugBar.Widgets.SQLQueriesWidget = PhpDebugBar.Widget.extend({

        className: csscls('sqlqueries'),

        onFilterClick: function(el) {
            $(el).toggleClass(csscls('excluded'));
            this.$list.$el.find("li[connection=" + $(el).attr("rel") + "]").toggle();
        },
        onCopyToClipboard: function (el) {
            var code = $(el).parent('li').find('code').get(0);
            var copy = function () {
                try {
                    if (document.execCommand('copy')) {
                        $(el).addClass(csscls('copy-clipboard-check'));
                        setTimeout(function(){
                            $(el).removeClass(csscls('copy-clipboard-check'));
                        }, 2000)
                    }
                } catch (err) {
                    console.log('Oops, unable to copy');
                }
            };
            var select = function (node) {
                if (document.selection) {
                    var range = document.body.createTextRange();
                    range.moveToElementText(node);
                    range.select();
                } else if (window.getSelection) {
                    var range = document.createRange();
                    range.selectNodeContents(node);
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);
                }
                copy();
                window.getSelection().removeAllRanges();
            };
            select(code);
        },
        renderList: function (caption, icon, data) {
            var $ul = $('<ul />').addClass(csscls('table-list')), $parts;
            var $li = $('<li />').addClass(csscls('table-list-item'));
            var $span = $('<span />').addClass('phpdebugbar-text-muted');
            for (var key in data) {
                var value = typeof data[key] === 'function' ? data[key].name + ' {}' : data[key];
                $li.clone().append(typeof value === 'object' && value !== null
                    ? [$span.clone().text(value.index || key).append('.'), '&nbsp;']
                        .concat(value.namespace ? [value.namespace + '::'] : [])
                        .concat([value.name || value.file])
                        .concat(value.line ? [$span.clone().text(':' + value.line)] : [])
                    : [$span.clone().text(key + ':'), '&nbsp;', value]
                ).appendTo($ul);
            }
            caption += icon ? ' <i class="phpdebugbar-fa phpdebugbar-fa-' + icon + ' phpdebugbar-text-muted"></i>' : '';
            return $('<tr />').append(
                $('<td />').addClass(csscls('name')).html(caption),
                $('<td />').addClass(csscls('value')).append($ul)
            );
        },
        render: function() {
            this.$status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

            this.$toolbar = $('<div />').addClass(csscls('toolbar')).appendTo(this.$el);

            var filters = [], self = this;

            this.$list = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, stmt) {
                    if (stmt.type === 'transaction') {
                        $('<strong />').addClass(csscls('sql')).addClass(csscls('name')).text(stmt.sql).appendTo(li);
                    } else {
                        $('<code />').addClass(csscls('sql')).html(PhpDebugBar.Widgets.highlight(stmt.sql, 'sql')).appendTo(li);
                    }
                    if (stmt.width_percent) {
                        $('<div />').addClass(csscls('bg-measure')).append(
                            $('<div />').addClass(csscls('value')).css({
                                left: stmt.start_percent + '%',
                                width: Math.max(stmt.width_percent, 0.01) + '%',
                            })
                        ).appendTo(li);
                    }
                    if (stmt.duration_str) {
                        $('<span title="Duration" />').addClass(csscls('duration')).text(stmt.duration_str).appendTo(li);
                    }
                    if (stmt.memory_str) {
                        $('<span title="Memory usage" />').addClass(csscls('memory')).text(stmt.memory_str).appendTo(li);
                    }
                    if (typeof(stmt.row_count) != 'undefined') {
                        $('<span title="Row count" />').addClass(csscls('row-count')).text(stmt.row_count).appendTo(li);
                    }
                    if (typeof(stmt.stmt_id) != 'undefined' && stmt.stmt_id) {
                        $('<span title="Prepared statement ID" />').addClass(csscls('stmt-id')).text(stmt.stmt_id).appendTo(li);
                    }
                    if (stmt.connection) {
                        $('<span title="Connection" />').addClass(csscls('database')).text(stmt.connection).appendTo(li);
                        li.attr("connection",stmt.connection);
                        if ( $.inArray(stmt.connection, filters) == -1 ) {
                            filters.push(stmt.connection);
                            $('<a />')
                                .addClass(csscls('filter'))
                                .text(stmt.connection)
                                .attr('rel', stmt.connection)
                                .on('click', function() { self.onFilterClick(this); })
                                .appendTo(self.$toolbar);
                            if (filters.length>1) {
                                self.$toolbar.show();
                                self.$list.$el.css("margin-bottom","20px");
                            }
                        }
                    }
                    if (typeof(stmt.is_success) != 'undefined' && !stmt.is_success) {
                        li.addClass(csscls('error'));
                        li.append($('<span />').addClass(csscls('error')).text("[" + stmt.error_code + "] " + stmt.error_message));
                    }
                    if ((!stmt.type || stmt.type === 'query')) {
                        $('<span title="Copy to clipboard" />')
                            .addClass(csscls('copy-clipboard'))
                            .css('cursor', 'pointer')
                            .html("&#8203;")
                            .on('click', function (event) {
                                self.onCopyToClipboard(this);
                                event.stopPropagation();
                            })
                            .appendTo(li);
                    }
                    if (typeof(stmt.xdebug_link) !== 'undefined' && stmt.xdebug_link) {
                        var header = $('<span title="Filename" />').addClass(csscls('filename')).text(stmt.xdebug_link.filename + ( stmt.xdebug_link.line ? "#" + stmt.xdebug_link.line : ''));
                        $('<a href="' + stmt.xdebug_link.url + '"></a>').on('click', function () {
                            event.stopPropagation();
                            if (stmt.xdebug_link.ajax) {
                                fetch(stmt.xdebug_link.url);
                                event.preventDefault();
                            }
                        }).addClass(csscls('editor-link')).appendTo(header);
                        header.appendTo(li);
                    }
                    var table = $('<table></table>').addClass(csscls('params'));
                    if (stmt.params && !$.isEmptyObject(stmt.params)) {
                        self.renderList('Params', 'thumb-tack', stmt.params).appendTo(table);
                    }
                    if (stmt.bindings && !$.isEmptyObject(stmt.bindings)) {
                        self.renderList('Bindings', 'thumb-tack', stmt.bindings).appendTo(table);
                    }
                    if (stmt.hints && !$.isEmptyObject(stmt.hints)) {
                        self.renderList('Hints', 'question-circle', stmt.hints).appendTo(table);
                    }
                    if (stmt.backtrace && !$.isEmptyObject(stmt.backtrace)) {
                        self.renderList('Backtrace', 'list-ul', stmt.backtrace).appendTo(table);
                    }
                    if (table.find('tr').length) {
                        table.appendTo(li);
                        li.css('cursor', 'pointer').click(function() {
                            if (window.getSelection().type == "Range") {
                                return''
                            }
                            if (table.is(':visible')) {
                                table.hide();
                            } else {
                                table.show();
                            }
                        });
                    }
                }});
            this.$list.$el.appendTo(this.$el);

            this.bindAttr('data', function(data) {
                // the PDO collector maybe is empty
                if (data.length <= 0 || !data.statements) {
                    return false;
                }
                filters = [];
                this.$toolbar.hide().find(csscls('.filter')).remove();
                this.$list.set('data', data.statements);
                this.$status.empty();

                // Search for duplicate statements.
                for (var sql = {}, duplicate = 0, i = 0; i < data.statements.length; i++) {
                    if (data.statements[i].type && data.statements[i].type !== 'query') {
                        continue;
                    }
                    var stmt = data.statements[i].sql;
                    if (data.statements[i].params && !$.isEmptyObject(data.statements[i].params)) {
                        stmt += JSON.stringify(data.statements[i].params);
                    }
                    if (data.statements[i].bindings && !$.isEmptyObject(data.statements[i].bindings)) {
                        stmt += JSON.stringify(data.statements[i].bindings);
                    }
                    if (data.statements[i].connection) {
                        stmt += '@' + data.statements[i].connection;
                    }
                    sql[stmt] = sql[stmt] || { keys: [] };
                    sql[stmt].keys.push(i);
                }
                // Add classes to all duplicate SQL statements.
                for (var stmt in sql) {
                    if (sql[stmt].keys.length > 1) {
                        duplicate += sql[stmt].keys.length;
                        for (var i = 0; i < sql[stmt].keys.length; i++) {
                            this.$list.$el.find('.' + csscls('list-item')).eq(sql[stmt].keys[i])
                                .addClass(csscls('sql-duplicate'));
                        }
                    }
                }

                var t = $('<span />').text(data.nb_statements + " statements were executed").appendTo(this.$status);
                if (data.nb_failed_statements) {
                    t.append(", " + data.nb_failed_statements + " of which failed");
                }
                if (duplicate) {
                    t.append(", " + duplicate + " of which were duplicates");
                    t.append(", " + (data.nb_statements - duplicate) + " unique. ");

                    // add toggler for displaying only duplicated queries
                    var duplicatedText = 'Show only duplicated';
                    $('<a />').addClass(csscls('duplicates')).click(function () {
                        $(this).toggleClass('shown-duplicated')
                            .text($(this).hasClass('shown-duplicated') ? 'Show All' : duplicatedText);
                        $('.' + self.className + ' .' + csscls('list-item'))
                            .not('.' + csscls('sql-duplicate')).toggle();
                    }).text(duplicatedText).appendTo(t);
                }
                if (data.accumulated_duration_str) {
                    this.$status.append($('<span title="Accumulated duration" />').addClass(csscls('duration')).text(data.accumulated_duration_str));
                }
                if (data.memory_usage_str) {
                    this.$status.append($('<span title="Memory usage" />').addClass(csscls('memory')).text(data.memory_usage_str));
                }
            });
        }

    });

})(PhpDebugBar.$);

(function($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying mails data
     *
     * Options:
     *  - data
     */
    var MailsWidget = PhpDebugBar.Widgets.MailsWidget = PhpDebugBar.Widget.extend({

        className: csscls('mails'),

        render: function() {
            this.$list = new  PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, mail) {
                    $('<span />').addClass(csscls('subject')).text(mail.subject).appendTo(li);
                    $('<span />').addClass(csscls('to')).text(mail.to).appendTo(li);
                    if (mail.body || mail.html) {
                        var header = $('<span />').addClass(csscls('filename')).text('');
                        $('<a title="Mail Preview">View Mail</a>').on('click', function () {
                            var popup = window.open('about:blank', 'Mail Preview', 'width=650,height=440,scrollbars=yes');
                            var documentToWriteTo = popup.document;
                            var headers = !mail.headers ? '' : $('<pre style="border: 1px solid #ddd; padding: 5px;" />')
                                .append($('<code />').text(mail.headers));

                            var body = $('<pre style="border: 1px solid #ddd; padding: 5px;" />').text(mail.body)
                            var html = null;
                            if (mail.html) {
                                body = $('<details />').append($('<summary>Text version</summary>')).append(body);
                                html = $('<iframe width="100%" height="400px" sandbox="" referrerpolicy="no-referrer"/>').attr("srcdoc", mail.html)
                            }

                            documentToWriteTo.open();
                            documentToWriteTo.write(headers.prop('outerHTML') + body.prop('outerHTML') + (html ? html.prop('outerHTML') : ''));
                            documentToWriteTo.close();
                        }).addClass(csscls('editor-link')).appendTo(header);

                        header.appendTo(li);
                    }

                    if (mail.headers) {
                        var headers = $('<pre />').addClass(csscls('headers')).appendTo(li);
                        $('<code />').text(mail.headers).appendTo(headers);
                        li.click(function() {
                            if (headers.is(':visible')) {
                                headers.hide();
                            } else {
                                headers.show();
                            }
                        });
                    }
                }});
            this.$list.$el.appendTo(this.$el);

            this.bindAttr('data', function(data) {
                this.$list.set('data', data);
            });
        }

    });

})(PhpDebugBar.$);

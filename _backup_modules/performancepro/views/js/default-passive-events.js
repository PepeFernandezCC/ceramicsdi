/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Commercial Software License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

"use strict";const e=["scroll","wheel","touchstart","touchmove","touchenter","touchend","touchleave","mouseout","mouseleave","mouseup","mousedown","mousemove","mouseenter","mousewheel","mouseover"];let t;(()=>{let e=!1;try{const t=Object.defineProperty({},"passive",{get(){e=!0}});window.addEventListener("test",null,t),window.removeEventListener("test",null,t)}catch(e){}return e})()&&(t=EventTarget.prototype.addEventListener,EventTarget.prototype.addEventListener=function(o,s,n){const r="object"==typeof n&&null!==n,i=r?n.capture:n;let u;(n=r?(e=>{const t=Object.getOwnPropertyDescriptor(e,"passive");return t&&!0!==t.writable&&void 0===t.set?Object.assign({},e):e})(n):{}).passive=void 0!==(u=n.passive)?u:-1!==e.indexOf(o)&&!0,n.capture=void 0!==i&&i,t.call(this,o,s,n)},EventTarget.prototype.addEventListener._original=t);

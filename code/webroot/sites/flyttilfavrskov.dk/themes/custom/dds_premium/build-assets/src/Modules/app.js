import NovicellLazyLoad from 'novicell-lazyload';
import debounce from 'lodash/debounce';
import Vue from 'vue';

// This is the global JS file that will be accessible from any component


/* Molecules */
import '@/02-molecules/navigation/navigation';
import '@/02-molecules/language-menu/language-menu';
import '@/02-molecules/video/video';
import '@/02-molecules/image-slideshow/image-slideshow';

/* Organisms */
import '@/03-organisms/burger-menu/burger-menu';
import '@/03-organisms/search-overlay/search-overlay';
import '@/03-organisms/hero/hero';
import '@/03-organisms/inline-navigation/inline-navigation';
import '@/03-organisms/accordion/accordion';
import '@/03-organisms/parcels/parcels';
import '@/03-organisms/submenu/submenu';

const lazy = new NovicellLazyLoad({
  includeWebp: true, // optional
});

document.addEventListener('lazybeforeunveil', (event) => {
  lazy.lazyLoad(event);
}, true);

window.addEventListener('resize', () => {
  debounce(lazy.checkImages);
}, 100, false);

// Disable vue and change its config
if (process.env.NODE_ENV === 'production') {
  Vue.config.productionTip = false;
  Vue.config.devtools = false;
  Vue.config.debug = false;
  Vue.config.silent = true;
}

import Vue from 'vue';
import HoverIntent from 'hoverintent';

document.addEventListener('DOMContentLoaded', () => {
  const navigation = document.getElementById('js-navigation');

  if (navigation) {
    const vm = new Vue({
      delimiters: ['${', '}'],
      el: navigation,
      mounted() {
        this.setupHoverIntent();
      },
      methods: {
        setupHoverIntent() {
          const navigationItems = this.$el.querySelectorAll('.js-navigation-item');
          for (let i = 0; i < navigationItems.length; i += 1) {
            const currentItem = navigationItems[i];
            HoverIntent(currentItem, () => {
              currentItem.classList.add('navigation-item--show-sub-navigation');
            }, () => {
              currentItem.classList.remove('navigation-item--show-sub-navigation');
            }).options({
              timeout: 400,
              interval: 55,
            });
          }
        },
      },
    });
  }
});

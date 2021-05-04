import Vue from 'vue';

document.addEventListener('DOMContentLoaded', () => {
  const searchOverlay = document.getElementById('js-search-overlay');
  const searchOverlayTriggers = document.querySelectorAll('.js-search-toggle');

  if (searchOverlay && (searchOverlayTriggers.length > 0)) {
    const vm = new Vue({
      delimiters: ['${', '}'],
      el: searchOverlay,
      data: {
        isOpen: false,
      },
      mounted() {
        searchOverlayTriggers.forEach((current) => {
          current.addEventListener('click', () => {
            this.openSearchOverlay();
          });
        });
      },
      methods: {
        openSearchOverlay() {
          const searchOverlayInput = document.getElementById('js-search-overlay-input');
          searchOverlayInput.focus();
          this.isOpen = true;
          document.body.classList.add('no-scroll');
          document.addEventListener('keydown', this.handleEsc);
        },
        closeSearchOverlay() {
          document.removeEventListener('keydown', this.handleEsc);
          this.isOpen = false;
          document.body.classList.remove('no-scroll');
        },
        handleEsc(e) {
          if (e.keyCode === 27) {
            this.closeSearchOverlay();
          }
        },
      },
    });
  }
});

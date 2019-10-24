import Vue from 'vue';

document.addEventListener('DOMContentLoaded', () => {
  const languageMenu = document.getElementById('js-language-menu');

  if (languageMenu) {
    const vm = new Vue({
      delimiters: ['${', '}'],
      el: languageMenu,
      data: {
        isOpen: false,
      },
      methods: {
        toggleLanguageMenu() {
          if (!this.isOpen) {
            this.openLanguageMenu();
          } else {
            this.closeLanguageMenu();
          }
        },
        openLanguageMenu() {
          this.isOpen = true;
          document.addEventListener('keydown', this.handleEsc);
          document.addEventListener('click', this.handleClickOutside);
        },
        closeLanguageMenu() {
          document.removeEventListener('keydown', this.handleEsc);
          document.removeEventListener('click', this.handleClickOutside);
          this.isOpen = false;
        },
        handleEsc(e) {
          if (e.keyCode === 27) {
            this.closeLanguageMenu();
          }
        },
        handleClickOutside(e) {
          const isClickInside = this.$el.contains(e.target);

          if (!isClickInside) {
            this.closeLanguageMenu();
          }
        },
      },
    });
  }
});

import Vue from 'vue';

document.addEventListener('DOMContentLoaded', () => {
  const burgerMenu = document.getElementById('js-burger-menu');
  const showSubNavigationClass = 'burger-menu-list-item--show-sub-navigation';

  if (burgerMenu) {
    const vm = new Vue({
      delimiters: ['${', '}'],
      el: burgerMenu,
      data: {
        isOpen: false,
      },
      mounted() {
        const burger = document.getElementById('js-burger');
        burger.addEventListener('click', () => {
          this.openBurgerMenu();
        });
      },
      methods: {
        triggerSubNavigation(e) {
          e.preventDefault();
          const trigger = e.currentTarget;
          const parent = trigger.closest('.js-burger-menu-list-item');

          parent.classList.toggle(showSubNavigationClass);
        },
        openBurgerMenu() {
          this.isOpen = true;
          document.body.classList.add('no-scroll');
          document.addEventListener('keydown', this.handleEsc);
          document.addEventListener('click', this.handleClickOutside);
        },
        closeBurgerMenu() {
          this.isOpen = false;
          document.removeEventListener('keydown', this.handleEsc);
          document.removeEventListener('click', this.handleClickOutside);
          document.body.classList.remove('no-scroll');
        },
        handleEsc(e) {
          if (e.keyCode === 27) {
            this.closeBurgerMenu();
          }
        },
        handleClickOutside(e) {
          const burgerMenuElem = document.getElementById('js-burger-menu');
          const burgerElem = document.getElementById('js-burger');
          const isClickInside = burgerMenuElem.contains(e.target) || burgerElem.contains(e.target);

          if (!isClickInside) {
            this.closeBurgerMenu();
          }
        },
      },
    });
  }
});

AOS.init({
  duration: 1000,
  once: true
});

const swiper = new Swiper('.swiper-container', {
  loop: true,
  slidesPerView: 2,
  spaceBetween: 30,
  autoplay: {
    delay: 2000,
  },
  breakpoints: {
    768: { slidesPerView: 4 },
  },
});

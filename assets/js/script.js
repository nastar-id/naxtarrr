const body = document.getElementsByTagName('html')[0];
const navbar = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('bg-dark', body.scrollTop > 50);
});

const carousel = document.querySelector('.carousel');

if(carousel) {
  const prevButton = document.querySelector('.carousel-button.left');
  const nextButton = document.querySelector('.carousel-button.right');
  
  let scrollAmount = 0;
  const scrollUnit = 300;
  
  prevButton.addEventListener('click', () => {
    scrollAmount -= scrollUnit;
    scrollCarousel();
  });
  
  nextButton.addEventListener('click', () => {
    scrollAmount += scrollUnit;
    scrollCarousel();
  });
  
  function scrollCarousel() {
    const maxScroll = carousel.scrollWidth - carousel.clientWidth;
  
    if (scrollAmount < 0) {
      scrollAmount = 0;
    } else if (scrollAmount > maxScroll) {
      scrollAmount = maxScroll;
    }
  
    carousel.scrollTo({
      left: scrollAmount,
      behavior: 'smooth',
    });
  }
}


const typed3 = new Typed('#typed3', {
  strings: ['Junior Web Developer', 'Web Security Enthusiast', 'Technology Nerd'],
  typeSpeed: 50,
  backSpeed: 25,
  smartBackspace: true,
  loop: true
});

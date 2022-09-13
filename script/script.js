const body = document.getElementsByTagName('html')[0];
const navbar = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('bg-primary', body.scrollTop > 50);
});

const typed3 = new Typed('#typed3', {
  strings: ['Junior Web Developer', 'Web Security Enthusiast', 'Technology Nerd'],
  typeSpeed: 50,
  backSpeed: 25,
  smartBackspace: true,
  loop: true
});

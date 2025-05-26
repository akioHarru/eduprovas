// script.js
document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('nav a');
  
    links.forEach(link => {
      link.addEventListener('mouseenter', () => {
        link.style.transform = 'scale(1.1)';
        link.style.transition = 'transform 0.2s ease';
      });
  
      link.addEventListener('mouseleave', () => {
        link.style.transform = 'scale(1)';
      });
    });
  });
  
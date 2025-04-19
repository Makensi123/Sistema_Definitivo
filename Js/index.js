document.addEventListener('DOMContentLoaded', function() {
    const logo = document.querySelector('.logo-img');
    
    logo.addEventListener('mousemove', function(e) {
        const xAxis = (window.innerWidth / 2 - e.pageX) / 25;
        const yAxis = (window.innerHeight / 2 - e.pageY) / 25;
        logo.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
    });
    
    logo.addEventListener('mouseenter', function() {
        logo.style.transition = 'all 0.1s ease';
    });
    
    logo.addEventListener('mouseleave', function() {
        logo.style.transform = 'rotateY(0deg) rotateX(0deg)';
        logo.style.transition = 'all 0.5s ease';
    });
});
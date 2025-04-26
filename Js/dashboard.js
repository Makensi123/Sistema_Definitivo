document.addEventListener('DOMContentLoaded', function () {
    // Efecto hover más pronunciado para las cards
    const cards = document.querySelectorAll('.card-feature');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.querySelector('i').style.transform = 'scale(1.1)';
        });
        card.addEventListener('mouseleave', () => {
            card.querySelector('i').style.transform = 'scale(1)';
        });
    });

    // Efecto de carga progresiva
    setTimeout(() => {
        document.querySelectorAll('.card-feature').forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }, 500);
});
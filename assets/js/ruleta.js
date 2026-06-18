const cards = document.querySelectorAll('.card');

function animateSelection() {

    // mesclar
    cards.forEach(card => {
        card.style.transform = `scale(0.9) rotate(${Math.random() * 10 - 5}deg)`;
        card.style.filter = "blur(1px)";
        card.style.opacity = "0.7";
    });

    // mostar la que salio
    setTimeout(() => {

        cards.forEach(card => {

            const id = parseInt(card.dataset.id);

            if (id === selectedId) {
                card.style.transform = "scale(1.3)";
                card.style.filter = "none";
                card.style.opacity = "1";
                card.style.zIndex = "10";
            } else {
                card.style.opacity = "0.2";
                card.style.transform = "scale(0.8)";
            }

        });

    }, 1500);

    // redirigir al juego
    setTimeout(() => {
        window.location.href = "/tpfinal_mvc/Game/jugar";
    }, 3000);
}

animateSelection();
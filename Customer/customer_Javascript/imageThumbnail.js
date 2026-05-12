function changeImage(thumbnail) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = thumbnail.src;

    // Remove active class from all thumbnails
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => {
        thumb.classList.remove('active-thumbnail');
    });

    // Add active class to the clicked thumbnail
    thumbnail.classList.add('active-thumbnail');
}

function jumpToSlide(index) {
    // Select the carousel
    const carousel = document.getElementById('mainImageCarousel');

    // Use Bootstrap's carousel API to jump to the desired slide
    const carouselInstance = new bootstrap.Carousel(carousel);
    carouselInstance.to(index);
}


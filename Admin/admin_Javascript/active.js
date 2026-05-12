document.querySelectorAll('.w3-bar-item').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.w3-bar-item').forEach(el => el.classList.remove('active'));
        this.classList.add('active');
    });
});

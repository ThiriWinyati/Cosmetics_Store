function removeFromCart(cartId) {
    if (confirm('Are you sure you want to remove this item?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'cart.php';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_cart_id';
        input.value = cartId;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
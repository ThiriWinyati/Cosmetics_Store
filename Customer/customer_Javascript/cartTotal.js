// JavaScript to update the cart total based on shipping selection
document.querySelectorAll('input[name="shipping"]').forEach(function(element) {
    element.addEventListener('change', function() {
        var shippingCost = parseFloat(this.value);
        var subtotal = parseFloat(document.getElementById('subtotal').innerText.replace(/[$,]/g, ''));
        var total = subtotal + shippingCost;

        document.getElementById('cart-total').innerText = total.toFixed(2);
    });
});
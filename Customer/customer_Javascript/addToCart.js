// In your addToCart.js file
$(document).ready(function() {
    $('#add_to_cart').on('click', function(e) {
        e.preventDefault();

        var productId = $('#product_id').val();
        var quantity = $('#quantity').val();

        $.ajax({
            type: 'POST',
            url: 'viewDetails.php',
            data: {add_to_cart: true, product_id: productId, quantity: quantity},
            dataType: 'json',
            success: function(data) {
                // Update the cart dropdown
                var cartHtml = '';
                $.each(data, function(index, item) {
                    cartHtml += '<div class="dropdown-item d-flex justify-content-between">';
                    cartHtml += '<span>' + item.Name + ' x ' + item.Quantity + '</span>';
                    cartHtml += '<span>$' + item.Price * item.Quantity + '</span>';
                    cartHtml += '</div>';
                });
                $('#cartDropdown').html(cartHtml);
            }
        });
    });
});


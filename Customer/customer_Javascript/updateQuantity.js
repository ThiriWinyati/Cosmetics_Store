function updateQuantity(index, change) {
    const quantityInput = document.getElementById('quantity-' + index);
    let currentQuantity = parseInt(quantityInput.value);
    currentQuantity += change;
    
    // Prevent quantity from going below 1
    if (currentQuantity < 1) {
        alert("Quantity cannot be less than 1.");
        return;
    }
    
    // Update the input value
    quantityInput.value = currentQuantity;

    // Trigger the form submission to update the database
    const form = document.getElementById("cart-form");
    form.submit();  // Submit the form to update the database
}

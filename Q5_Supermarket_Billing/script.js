// ============================================
// Q5 - SUPERMARKET BILLING SYSTEM
// ============================================

// Function to calculate the subtotal of a product
function calculateSubtotal(quantity, price) {
    return quantity * price;
}


// Function to format currency
function formatCurrency(amount) {
    return "₹" + amount.toFixed(2);
}


// Function to display an error message
function showError(message) {
    const errorMessage = document.getElementById("errorMessage");

    errorMessage.textContent = message;
    errorMessage.classList.add("show");
}


// Function to clear the error message
function clearError() {
    const errorMessage = document.getElementById("errorMessage");

    errorMessage.textContent = "";
    errorMessage.classList.remove("show");
}


// ============================================
// MAIN BILL CALCULATION FUNCTION
// ============================================

function calculateBill() {

    clearError();

    // Get product names
    const product1 = document.getElementById("product1").value.trim();
    const product2 = document.getElementById("product2").value.trim();
    const product3 = document.getElementById("product3").value.trim();


    // Get quantities
    const quantity1 =
        Number(document.getElementById("quantity1").value);

    const quantity2 =
        Number(document.getElementById("quantity2").value);

    const quantity3 =
        Number(document.getElementById("quantity3").value);


    // Get unit prices
    const price1 =
        Number(document.getElementById("price1").value);

    const price2 =
        Number(document.getElementById("price2").value);

    const price3 =
        Number(document.getElementById("price3").value);


    // ============================================
    // VALIDATION
    // ============================================

    if (product1 === "") {
        showError("Please enter the name of Product 1.");
        return;
    }

    if (product2 === "") {
        showError("Please enter the name of Product 2.");
        return;
    }

    if (product3 === "") {
        showError("Please enter the name of Product 3.");
        return;
    }


    if (
        quantity1 <= 0 ||
        quantity2 <= 0 ||
        quantity3 <= 0 ||
        isNaN(quantity1) ||
        isNaN(quantity2) ||
        isNaN(quantity3)
    ) {
        showError("Quantity must be greater than 0.");
        return;
    }


    if (
        price1 < 0 ||
        price2 < 0 ||
        price3 < 0 ||
        isNaN(price1) ||
        isNaN(price2) ||
        isNaN(price3)
    ) {
        showError("Unit price cannot be negative.");
        return;
    }


    // ============================================
    // CALCULATE INDIVIDUAL PRODUCT TOTALS
    // ============================================

    const subtotal1 =
        calculateSubtotal(quantity1, price1);

    const subtotal2 =
        calculateSubtotal(quantity2, price2);

    const subtotal3 =
        calculateSubtotal(quantity3, price3);


    // ============================================
    // CALCULATE TOTAL BILL
    // ============================================

    const totalBill =
        subtotal1 +
        subtotal2 +
        subtotal3;


    // ============================================
    // CALCULATE DISCOUNT
    // ============================================

    let discount = 0;

    if (totalBill > 2000) {

        discount = totalBill * 0.10;

    }


    // ============================================
    // CALCULATE FINAL AMOUNT
    // ============================================

    const finalAmount =
        totalBill - discount;


    // ============================================
    // DISPLAY BILL TABLE
    // ============================================

    const billTableBody =
        document.getElementById("billTableBody");

    billTableBody.innerHTML = `

        <tr>

            <td>${product1}</td>

            <td>${quantity1}</td>

            <td>${formatCurrency(price1)}</td>

            <td>${formatCurrency(subtotal1)}</td>

        </tr>

        <tr>

            <td>${product2}</td>

            <td>${quantity2}</td>

            <td>${formatCurrency(price2)}</td>

            <td>${formatCurrency(subtotal2)}</td>

        </tr>

        <tr>

            <td>${product3}</td>

            <td>${quantity3}</td>

            <td>${formatCurrency(price3)}</td>

            <td>${formatCurrency(subtotal3)}</td>

        </tr>

    `;


    // ============================================
    // DISPLAY TOTALS
    // ============================================

    document.getElementById("totalAmount").textContent =
        formatCurrency(totalBill);

    document.getElementById("discountAmount").textContent =
        formatCurrency(discount);

    document.getElementById("finalAmount").textContent =
        formatCurrency(finalAmount);


    // ============================================
    // DISPLAY DISCOUNT MESSAGE
    // ============================================

    const discountMessage =
        document.getElementById("discountMessage");


    if (discount > 0) {

        discountMessage.textContent =
            "🎉 10% discount applied because your bill exceeds ₹2000.";

        discountMessage.classList.add(
            "discount-applied"
        );

    } else {

        discountMessage.textContent =
            "No discount applied. A 10% discount is available when the total exceeds ₹2000.";

        discountMessage.classList.remove(
            "discount-applied"
        );

    }

}


// ============================================
// RESET FUNCTION
// ============================================

function resetBill() {

    // Clear all input fields
    document.getElementById("product1").value = "";
    document.getElementById("quantity1").value = "";
    document.getElementById("price1").value = "";

    document.getElementById("product2").value = "";
    document.getElementById("quantity2").value = "";
    document.getElementById("price2").value = "";

    document.getElementById("product3").value = "";
    document.getElementById("quantity3").value = "";
    document.getElementById("price3").value = "";


    // Clear bill table
    document.getElementById("billTableBody").innerHTML = "";


    // Reset totals
    document.getElementById("totalAmount").textContent =
        "₹0.00";

    document.getElementById("discountAmount").textContent =
        "₹0.00";

    document.getElementById("finalAmount").textContent =
        "₹0.00";


    // Reset messages
    clearError();

    document.getElementById("discountMessage").textContent =
        "No discount applied.";

    document.getElementById("discountMessage")
        .classList.remove("discount-applied");

}


// ============================================
// BUTTON CONNECTIONS
// ============================================

// Calculate Bill button
document
    .getElementById("calculateButton")
    .addEventListener("click", calculateBill);


// Reset button
document
    .getElementById("resetButton")
    .addEventListener("click", resetBill);
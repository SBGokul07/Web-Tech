// ============================================
// Q5 - SUPERMARKET CUSTOMER BILL CALCULATOR
// ============================================

// User-defined function to calculate the bill
function calculateBill() {

    // Get quantities and prices from the form
    let quantity1 = Number(document.getElementById("quantity1").value);
    let price1 = Number(document.getElementById("price1").value);

    let quantity2 = Number(document.getElementById("quantity2").value);
    let price2 = Number(document.getElementById("price2").value);

    let quantity3 = Number(document.getElementById("quantity3").value);
    let price3 = Number(document.getElementById("price3").value);

    // Validate input
    if (
        quantity1 <= 0 || price1 < 0 ||
        quantity2 <= 0 || price2 < 0 ||
        quantity3 <= 0 || price3 < 0 ||
        isNaN(quantity1) || isNaN(price1) ||
        isNaN(quantity2) || isNaN(price2) ||
        isNaN(quantity3) || isNaN(price3)
    ) {
        document.getElementById("result").innerHTML =
            "<p class='error'>Please enter valid quantities and prices.</p>";

        return;
    }

    // Calculate individual product totals
    let productTotal1 = quantity1 * price1;
    let productTotal2 = quantity2 * price2;
    let productTotal3 = quantity3 * price3;

    // Calculate total bill
    let totalBill = productTotal1 + productTotal2 + productTotal3;

    // Initialize discount
    let discount = 0;

    // Apply 10% discount if total exceeds ₹2000
    if (totalBill > 2000) {
        discount = totalBill * 0.10;
    }

    // Calculate final payable amount
    let finalAmount = totalBill - discount;

    // Display result
    document.getElementById("result").innerHTML = `
        <h2>Bill Summary</h2>

        <div class="bill-row">
            <span>Product 1</span>
            <span>₹${productTotal1.toFixed(2)}</span>
        </div>

        <div class="bill-row">
            <span>Product 2</span>
            <span>₹${productTotal2.toFixed(2)}</span>
        </div>

        <div class="bill-row">
            <span>Product 3</span>
            <span>₹${productTotal3.toFixed(2)}</span>
        </div>

        <hr>

        <div class="bill-row">
            <strong>Total Bill</strong>
            <strong>₹${totalBill.toFixed(2)}</strong>
        </div>

        <div class="bill-row discount">
            <span>Discount (10%)</span>
            <span>₹${discount.toFixed(2)}</span>
        </div>

        <div class="bill-row final">
            <strong>Final Payable Amount</strong>
            <strong>₹${finalAmount.toFixed(2)}</strong>
        </div>

        ${
            discount > 0
            ? "<p class='success'>🎉 10% discount applied!</p>"
            : "<p class='info'>No discount applicable. Spend more than ₹2000 to get 10% off.</p>"
        }
    `;
}


// User-defined function to clear the form
function clearBill() {

    document.getElementById("billingForm").reset();

    document.getElementById("result").innerHTML = `
        <p class="placeholder">
            Enter product details and click "Calculate Bill".
        </p>
    `;
}
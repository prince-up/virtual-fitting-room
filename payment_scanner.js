// QR Code Scanner Configuration
const scanner = new Html5QrcodeScanner("qr-reader", {
    fps: 10,
    qrbox: 250
});

// Function to handle successful scan
function onScanSuccess(decodedText, decodedResult) {
    // Stop the scanner
    scanner.clear();
    document.getElementById('qr-reader').style.display = 'none';
    
    // Process the payment
    processPayment(decodedText);
}

// Function to handle scan failure
function onScanFailure(error) {
    console.warn(`QR Code scan error: ${error}`);
}

// Function to process the payment
async function processPayment(paymentData) {
    try {
        // Show loading state
        document.getElementById('payment-status').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-spinner fa-spin me-2"></i>Processing payment...
            </div>
        `;

        // Parse payment data (assuming it's in UPI format)
        const paymentDetails = parseUPIData(paymentData);
        
        // Validate payment details
        if (!validatePaymentDetails(paymentDetails)) {
            throw new Error('Invalid payment details');
        }

        // Process payment through your backend
        const response = await fetch('process_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                paymentDetails: paymentDetails,
                amount: getTotalAmount(),
                orderId: generateOrderId()
            })
        });

        if (!response.ok) {
            throw new Error('Payment processing failed');
        }

        const result = await response.json();

        // Show success message
        document.getElementById('payment-status').innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>Payment successful!
                <p class="mb-0">Transaction ID: ${result.transactionId}</p>
            </div>
        `;

        // Clear cart and redirect to success page
        setTimeout(() => {
            localStorage.removeItem('cart');
            window.location.href = 'payment_success.php';
        }, 2000);

    } catch (error) {
        // Show error message
        document.getElementById('payment-status').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>Payment failed: ${error.message}
            </div>
        `;
    }
}

// Function to parse UPI payment data
function parseUPIData(data) {
    // Example UPI format: upi://pay?pa=merchant@upi&pn=MerchantName&am=100.00
    const params = new URLSearchParams(data.split('?')[1]);
    return {
        vpa: params.get('pa'),
        merchantName: params.get('pn'),
        amount: params.get('am')
    };
}

// Function to validate payment details
function validatePaymentDetails(details) {
    return details.vpa && details.merchantName && details.amount;
}

// Function to get total amount from cart
function getTotalAmount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

// Function to generate order ID
function generateOrderId() {
    return 'ORD' + Date.now() + Math.random().toString(36).substr(2, 9);
}

// Start scanner when page loads
document.addEventListener('DOMContentLoaded', () => {
    scanner.render(onScanSuccess, onScanFailure);
}); 
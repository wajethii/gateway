async function payNow(amount, cardElement) {
    const email = prompt("Enter your email address:");
    if (!email) return alert("Payment canceled.");

    const handler = PaystackPop.setup({
        key: 'pk_live_ddf8b229efa8b8d52e6bc93e8deb17bb1984015b', // Replace with your Paystack public key
        email: email,
        amount: amount * 100, // Paystack works with kobo (100 kobo = 1 KES)
        currency: 'KES', // Kenyan Shillings
        callback: function (response) {
            alert("Payment successful. Reference: " + response.reference);
            // Send the reference to your backend for verification
            verifyPayment(response.reference);
        },
        onClose: function () {
            alert("Payment process closed.");
        }
    });

    handler.openIframe();
}

function verifyPayment(reference) {
    fetch('/verify-payment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reference })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert("Payment verified!");
            } else {
                alert("Payment verification failed.");
            }
        })
        .catch(err => alert("Error verifying payment: " + err));
}
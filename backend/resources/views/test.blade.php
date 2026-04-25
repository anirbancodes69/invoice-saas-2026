<!DOCTYPE html>
<html>
<head>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<button id="pay-btn">Pay Now</button>

<script>
document.getElementById('pay-btn').onclick = function () {

    var options = {
        key: "rzp_test_ShgbRUKDhjSHMg", // ✅ correct format

        order_id: "order_Shgomv30OBqcey", // ✅ from backend

        name: "Invoice SaaS",
        description: "Invoice Payment",

        // 👇 Force better UI behavior
        method: {
            upi: true,
            card: true,
            netbanking: true,
            wallet: true
        },

        handler: function (response){
            console.log("SUCCESS:", response);

            alert("Payment successful");

            /*
            response contains:
            - razorpay_payment_id
            - razorpay_order_id
            - razorpay_signature
            */
        },

        modal: {
            ondismiss: function(){
                console.log("Payment popup closed");
            }
        }
    };

    var rzp = new Razorpay(options);
    rzp.open();
}
</script>

</body>
</html>
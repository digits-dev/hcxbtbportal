<!DOCTYPE html>
<html>
<head>
    <title>Re-upload Proof of Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: black;
            color: #ffffff;
            padding: 10px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none !important;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            font-size: 0.9em;
            color: #777;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Re-upload Proof of Payment</h2>
        </div>
        <div class="content">
            <p>Dear {{ $order['customer_name'] }},</p>
            <p>We have reviewed your submitted proof of payment for your loan application with Home Credit. Unfortunately, it was not accepted due to missing or unclear details.</p>
            <p>Please re-upload a valid proof of payment using the link below so we can continue processing your application:</p>
            <p style="text-align: center;">
                <a href="{{ $order['payment_link'] }}" class="button">Re-upload Proof of Payment</a>
            </p>
            <p>Make sure your payment proof clearly shows the transaction details, amount paid, and reference number (if applicable). Our team will review it again after submission.</p>
            <p>If you have any questions or need assistance, feel free to reach out to us.</p>
            <p>Sincerely,<br>Beyond the Box</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Beyond the Box. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

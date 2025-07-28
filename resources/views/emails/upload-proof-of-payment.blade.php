<!DOCTYPE html>
<html>
<head>
    <title>Proof of Payment Required</title>
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
           background-color: #e11931;
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
            text-decoration: none;
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
            <h2>Proof of Payment Required</h2>
        </div>
        <div class="content">
            <p>Dear {{ $order['customer_name'] }},</p>
            <p>Thank you for your recent loan application with Home Credit. As part of your application, a downpayment is required.</p>
            <p>To proceed with your application, please upload your proof of payment using the link below:</p>
            <p style="text-align: center;">
                <a href="{{ $order['payment_link']  }}" class="button">Upload Proof of Payment</a>
            </p>
            <p>Please ensure the proof of payment clearly shows the transaction details and the amount paid. Once uploaded, our team will review it and continue processing your loan.</p>
            <p>If you have any questions, please do not hesitate to contact us.</p>
            <p>Sincerely,<br>Beyond the Box</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Beyond the Box. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
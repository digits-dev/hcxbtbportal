<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - </title>
    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* Main styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f4f4f4;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        .header {
            background: black;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .header p {
            color: #e8eaff;
            margin: 10px 0 0 0;
            font-size: 16px;
        }
        
        .content {
            padding: 30px;
        }
        
        .order-summary {
            background-color: #f8f9ff;
            border: 1px solid #e1e5f2;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .order-number {
            background-color: black;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .section {
            margin: 25px 0;
            padding: 20px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .section:last-child {
            border-bottom: none;
        }
        
        .section-title {
            color: #374151;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 15px 0;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            padding: 8px 0;
            color: #6b7280;
            font-weight: 500;
            width: 40%;
            vertical-align: top;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #111827;
            font-weight: 400;
            vertical-align: top;
        }
        
        .product-card {
            background-color: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        
        .product-name {
            color: #111827;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 10px 0;
        }
        
        .product-details {
            display: table;
            width: 100%;
            margin: 10px 0;
        }
        
        .product-detail {
            display: table-cell;
            padding: 5px 10px 5px 0;
            color: black;
            font-size: 14px;
        }
        
        .product-price {
            color: #059669;
            font-size: 24px;
            font-weight: 700;
            text-align: right;
            margin-top: 10px;
        }
        
        .financial-summary {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin: 10px 0;
        }
        
        .total-label {
            display: table-cell;
            color: #374151;
            font-weight: 500;
            font-size: 16px;
        }
        
        .total-value {
            display: table-cell;
            text-align: right;
            color: #111827;
            font-weight: 600;
            font-size: 16px;
        }
        
        .grand-total {
            border-top: 2px solid #0ea5e9;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .grand-total .total-label {
            font-size: 18px;
            font-weight: 600;
            color: #0ea5e9;
        }
        
        .grand-total .total-value {
            font-size: 20px;
            font-weight: 700;
            color: #0ea5e9;
        }
        
        .status-badge {
            background-color: #10b981;
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .next-steps {
            background-color: #fffbeb;
            border: 1px solid #fbbf24;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .next-steps h3 {
            color: #92400e;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        
        .next-steps ul {
            color: #78350f;
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .next-steps li {
            margin: 5px 0;
        }
        
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer p {
            color: #6b7280;
            margin: 5px 0;
            font-size: 14px;
        }
        
        .contact-info {
            margin: 20px 0;
        }
        
        .contact-info a {
            color: #667eea;
            text-decoration: none;
        }
        
        /* Mobile responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            
            .header, .content, .footer {
                padding: 20px !important;
            }
            
            .info-label, .info-value {
                display: block !important;
                width: 100% !important;
            }
            
            .info-label {
                font-weight: 600;
                margin-bottom: 5px;
            }
            
            .info-value {
                margin-bottom: 15px;
                padding-left: 10px;
            }
            
            .product-detail {
                display: block !important;
                padding: 3px 0 !important;
            }
            
            .total-label, .total-value {
                display: block !important;
                text-align: left !important;
            }
            
            .total-value {
                font-weight: 700;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Thank you for your purchase!</p>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <!-- Greeting -->
            <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0;">
                Dear {{ $order['customer_name'] }},
            </p>
            
            <p style="font-size: 16px; color: #374151; line-height: 1.6; margin: 0 0 20px 0;">
                We're excited to confirm that we've received your order! Your Apple product is being processed and will be delivered to your specified address soon.
            </p>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <div class="order-number">Reference #{{ $order['reference_number'] }}</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Order Date:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($order['order_date'])->format('F j, Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value"><span class="status-badge">Confirmed</span></div>
                    </div>
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="section">
                <h2 class="section-title">Customer Information</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Name:</div>
                        <div class="info-value">{{ $order['customer_name'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">{{ $order['email_address'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">{{ $order['contact_details'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Delivery Address:</div>
                        <div class="info-value" style="white-space: pre-line;">{{ $order['delivery_address'] }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Product Information -->
                    <div class="section">
                <h2 class="section-title">Item Details</h2>

                @foreach ($order['items'] as $item)
                    <div class="product-card">
                        <div class="product-details">
                            <div class="product-detail"><strong>Item Description:</strong> {{ $item['item_description'] }}</div>
                            <div class="product-detail"><strong>Qty:</strong> {{ $item['qty'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            
            <!-- Financial Summary -->
            <div class="section">
                <h2 class="section-title">Payment Summary</h2>
                <div class="financial-summary">
                    {{-- <div class="total-row">
                        <div class="total-label">Product Price:</div>
                        <div class="total-value">${{ number_format($orderselectedSKU['price'], 2) }}</div>
                    </div>
                     --}}
                    @if($order['has_downpayment'] == 'yes')
                    <div class="total-row">
                        <div class="total-label">Downpayment:</div>
                        <div class="total-value">${{ number_format($order['downpayment_value'], 2) }}</div>
                    </div>
                    @endif
                    
                    <div class="total-row">
                        <div class="total-label">Financed Amount:</div>
                        <div class="total-value">{{ number_format($order['financed_amount'], 2) }}</div>
                    </div>
                 
                </div>
            </div>
            
            
   
            
            <!-- Support Information -->
            <p style="font-size: 16px; color: #374151; line-height: 1.6; margin: 25px 0;">
                If you have any questions about your order, please don't hesitate to contact our customer support team. We're here to help!
            </p>
            
            <p style="font-size: 16px; color: #374151; margin: 20px 0 0 0;">
                Thank you for choosing us for your Apple product needs!
            </p>
            
            <p style="font-size: 16px; color: #374151; margin: 10px 0 0 0;">
                Best regards,<br>
                <strong>Beyond the Box</strong>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="contact-info">
                <p><strong>Need Help?</strong></p>
                <p>Email: <a href="inquiry@beyondthebox.ph">inquiry@beyondthebox.ph</a></p>
                <p>Address: Beyond the Box Service Center
                    3/F VMall Greenhills, San Juan City;
                    L/GF C3 Bonifacio High Street, Taguig</p>
            </div>
            <p style="font-size: 12px;">
                © {{ date('Y') }} Beyond the Box. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
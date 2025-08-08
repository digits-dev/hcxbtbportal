<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <meta name="x-apple-disable-message-reformatting" />
    <title>Order Scheduled</title>
    <style>
      body { margin:0; padding:0; background:#f6f7f9; color:#111827; }
      a { color:#0ea5a4; text-decoration:none; }
      .wrapper { width:100%; background:#f6f7f9; padding:24px 12px; }
      .container { max-width:640px; margin:0 auto; background:#ffffff; border-radius:10px; overflow:hidden; border:1px solid #eef0f3; }
      .header { padding:20px 24px; background:#0f172a; color:#ffffff; font-family:Arial, Helvetica, sans-serif; }
      .brand { font-size:18px; font-weight:700; }
      .content { padding:24px; font-family:Arial, Helvetica, sans-serif; line-height:1.5; }
      .preheader { display:none; font-size:1px; color:#f6f7f9; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; }
      .h1 { font-size:20px; margin:0 0 12px; }
      .muted { color:#6b7280; }
      .divider { height:1px; background:#eef0f3; margin:16px 0; }
      .kvs { width:100%; border-collapse:collapse; }
      .kvs th, .kvs td { text-align:left; padding:8px 0; vertical-align:top; }
      .kvs th { width:180px; color:#374151; font-weight:600; padding-right:16px; }
      .badge { display:inline-block; padding:4px 10px; border-radius:999px; background:#0ea5a4; color:#ffffff; font-weight:700; font-size:12px; }
      .footer { padding:18px 24px; background:#f9fafb; color:#6b7280; font-family:Arial, Helvetica, sans-serif; font-size:12px; }
      @media (max-width: 480px) {
        .content { padding:18px; }
        .header { padding:16px 18px; }
        .footer { padding:16px 18px; }
        .kvs th { width:42%; }
      }
    </style>
  </head>
  <body>
    <div class="preheader">Your order has been scheduled. See delivery details below.</div>

    <div class="wrapper">
      <table class="container" role="presentation" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td class="header">
               <div class="brand" style="text-align: center;">Order Scheduled</div>
          </td>
        </tr>
        <tr>
          <td class="content">
            <h1 class="h1">Order {{ $order['reference_number'] }} is scheduled for {{ \Carbon\Carbon::parse($order['schedule_date'])->format('F j, Y') }}.</h1>
            <p style="margin:0 0 16px;">
              Hi {{ $order['customer_name'] }}, your order has been scheduled. Below are the details for your reference.
            </p>

            <div class="divider"></div>

            <table class="kvs" role="presentation" width="100%">
              <tr>
                <th>Order Reference</th>
                <td>{{ $order['reference_number'] }}</td>
              </tr>
              <tr>
                <th>Schedule Date</th>
                <td>{{ \Carbon\Carbon::parse($order['schedule_date'])->format('F j, Y') }}</td>
              </tr>
              <tr>
                <th>Delivery Option</th>
                <td><span class="badge">{{ $order['transaction_type'] ==  "third party" ? "Third Party" : "Logistics"  }}</span></td>
              </tr>
            </table>

            @if ($order['transaction_type'] ==  "logistics")
              <div class="divider"></div>
              <h2 style="font-size:16px; margin:0 0 8px;">Remarks</h2>
              <p style="margin:0;">{{ $order['logistics_remarks']}}</p>
            @endif

            @if ($order['transaction_type'] === 'third party')
              <div class="divider"></div>
              <h2 style="font-size:16px; margin:0 0 8px;">Third-Party Delivery Details</h2>
              <table class="kvs" role="presentation" width="100%">
                <tr>
                  <th>Carrier Name</th>
                  <td>{{ $order['carrier_name']}}</td>
                </tr>
                <tr>
                  <th>Delivery Reference</th>
                  <td>{{ $order['delivery_reference']}}</td>
                </tr>
              </table>
            @endif

            <div class="divider"></div>

            <p style="margin:0 0 16px;">
              If any of the above looks incorrect, reply to this email or contact us at <a href="inquiry@beyondthebox.ph">inquiry@beyondthebox.ph</a>.
            </p>

            <p style="margin:0;">
              Thanks,<br />
            <strong>Beyond the Box</strong>
            </p>
          </td>
        </tr>
        <tr>
          <td class="footer">
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
          </td>
        </tr>
      </table>
    </div>
  </body>
</html> 
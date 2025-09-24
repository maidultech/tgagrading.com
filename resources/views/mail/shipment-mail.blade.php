<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Order Has Been Shipped!</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      color: #333;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 20px auto;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #f9f9f9;
    }
    h1 {
      color: #0046be; /* UPS Blue */
      font-size: 24px;
      margin-bottom: 20px;
    }
    p {
      margin: 10px 0;
    }
    .tracking-info {
      background-color: #fff;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-top: 20px;
    }
    .tracking-info a {
      color: #0046be;
      text-decoration: none;
    }
    .tracking-info a:hover {
      text-decoration: underline;
    }
    .footer {
      margin-top: 20px;
      font-size: 12px;
      color: #777;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Your Order Has Been Shipped!</h1>
    <p>Dear {{ $order->rUser->name }},</p>
    <p>We're excited to let you know that your order <strong>#{{ $order->order_number }}</strong> has been shipped via <b>{{ str($order->shipping_method)->headline()->upper()->value() }}</b> and is on its way to you.</p>

    <div class="tracking-info">
      <p><strong>Tracking Number:</strong> <a href="{{ str($order->admin_tracking_note)->after('You can track your order at ') }}" target="_blank">{{ str($order->admin_tracking_id)->after('Tracking Number: ') }}</a></p>
    </div>

    <p>You can track your shipment at any time using the link above. If you have any questions, feel free to contact us at <a href="mailto:{{ $setting->support_email }}">{{ $setting->support_email }}</a>.</p>

    <p>Thank you for shopping with us!</p>
    <p>Best regards,<br>{{ $setting->site_name }}</p>

    <div class="footer">
      <p>This is an automated email. Please do not reply to this message.</p>
    </div>
  </div>
</body>
</html>
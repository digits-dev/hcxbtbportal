<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Digits IMFS Email</title>
  </head>
  <body style="margin:0; padding:20px; background-color:#f8fafc; font-family:Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="max-width:1000px; margin:auto; background:#ffffff; border:1px solid #e2e8f0;">
      <!-- Header -->
      <tr>
        <td align="center" bgcolor="#1e3a8a" style="padding: 30px 20px;">
            <table cellpadding="0" cellspacing="0" border="0" style="margin:auto;">
            <tr>
                <!-- Logo -->
                <td style="padding-right: 15px;">
                <img src="https://dbs.digitstrading.ph/images/login-page/digits-logo.png" alt="Digits Logo" width="50" style="display:block;" />
                </td>

                <!-- Vertical Line -->
                <td style="padding-right: 15px;">
                <div style="width:1.5px; height:60px; background-color:white;"></div>
                </td>

                <!-- Text -->
                <td style="color:#ffffff; font-size:18px; font-weight:600; font-family:Arial, sans-serif;">
                    Digits Item Masterfile System
                </td>
            </tr>
            </table>
        </td>
    </tr>

      <!-- Content -->
      <tr>
        <td style="padding:20px;">
          <table width="100%" cellpadding="10" cellspacing="10" style="margin-bottom:20px;">
            <!-- Brand -->
            <tr>
              <td style="font-size:14px; color:#1e3a8a; font-weight:bold;">Brand:</td>
              <td style="font-size:14px; color:#334155;"><b>{{$brand_description}}</b></td>
            </tr>
            <!-- Email -->
            <tr>
              <td style="font-size:14px; color:#1e3a8a; font-weight:bold;">Contact Email:</td>
              <td style="font-size:14px;">
                <a href="{{ url('mailto:' . $contact_email) }}" style="color:#1e3a8a; text-decoration:none;">{{$contact_email}}</a>
              </td>
            </tr>
            <!-- Name -->
            <tr>
              <td style="font-size:14px; color:#1e3a8a; font-weight:bold;">Contact Name:</td>
              <td style="font-size:14px; color:#334155;"><b>{{$contact_name}}</b></td>
            </tr>
          </table>

          <!-- Message Box -->
          <table width="100%" cellpadding="10" cellspacing="0" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
            <tr>
              <td style="color:#475569; font-size:14px;">
                Kindly coordinate with the supplier for aftersales procedures and policy.
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#f8fafc; padding:20px; border-top:1px solid #e2e8f0; font-size:12px; color:#1e3a8a;">
          ⚠️ Do not reply to this email address. This email was sent automatically by our system. For any inquiries, please contact our support team.
        </td>
      </tr>
    </table>
  </body>
</html>

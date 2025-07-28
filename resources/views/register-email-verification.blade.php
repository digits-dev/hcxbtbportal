<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>

</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: Arial, sans-serif; line-height: 1.6;">
    <!-- Main Container -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Email Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #131a29 0%, #0b0e13 100%); padding: 40px 30px; border-radius: 8px 8px 0 0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <!-- Logo/Icon -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="width: 60px; height: 60px; background-color: #ffffff; border-radius: 50%; text-align: center; vertical-align: middle;">
                                                    <span style="color: #0b0e13; font-size: 30px; font-weight: bold;">✓</span>
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- Header Title -->
                                        <h1 style="color: #ffffff; font-size: 28px; font-weight: bold; margin: 20px 0 0 0; text-align: center;">Verify Your Email</h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <!-- Message Content -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f9fafb; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 30px;">
                                        <p style="color: #374151; font-size: 16px; margin: 0 0 15px 0;">Hi there! 👋</p>
                                        <p style="color: #374151; font-size: 16px; margin: 0 0 15px 0;">
                                            Thank you for signing up! To complete your registration and secure your account, please verify your email address by clicking the button below.
                                        </p>
                                        <p style="color: #374151; font-size: 16px; margin: 0 0 25px 0;">
                                            This verification link will expire for security purposes.
                                        </p>

                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td align="center" style="padding: 20px 0;">
                                                    <a href="{{ url('/register/' . $encryptedEmail) }}" style="display: inline-block; background: linear-gradient(135deg, #0b0e19 0%, #0b0e13 100%); color: #ffffff; font-size: 16px; font-weight: bold; text-decoration: none; padding: 15px 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                                        Verify Email Address
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Alternative Link -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top: 1px solid #e5e7eb; margin-top: 25px; padding-top: 25px;">
                                            <tr>
                                                <td>
                                                    <p style="color: #6b7280; font-size: 14px; margin: 0 0 10px 0;">
                                                        If the button doesn't work, copy and paste this link into your browser:
                                                    </p>
                                                    <div style="background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; padding: 15px; word-break: break-all;">
                                                        <a href="{{ url('/register/' . $encryptedEmail) }}" style="color: #3b82f6; font-size: 14px; text-decoration: none;">{{ url('/reset_password_email/' . $encryptedEmail) }}</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Security Notice -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="width: 30px; vertical-align: top;">
                                                    <span style="color: #d97706; font-size: 20px;">⚠️</span>
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="color: #92400e; font-size: 14px; font-weight: 600; margin: 0 0 5px 0;">Security Notice</p>
                                                    <p style="color: #b45309; font-size: 14px; margin: 0;">
                                                        If you didn't create an account with us, please ignore this email or contact our support team.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f3f4f6; padding: 30px; border-top: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <p style="color: #6b7280; font-size: 14px; margin: 0 0 10px 0;">
                                            This email was sent to {{$email}} because you signed up for an account.
                                        </p>
                                        <p style="color: #9ca3af; font-size: 12px; margin: 0 0 20px 0;">
                                            Home Credit | Beyond the Box Portal. All rights reserved.
                                        </p>
   
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
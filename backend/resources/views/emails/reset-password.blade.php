<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>R&#233;initialisation de mot de passe - TOPIDEALSPACE</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;">

    <!-- Wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">

                <!-- Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; background-color: #f9f9f9; border-radius: 10px; overflow: hidden;">

                    <!-- Logo / Header -->
                    <tr>
                        <td align="center" style="padding: 30px 20px 10px 20px;">
                            <h1 style="margin: 0; color: #4F46E5; font-size: 26px; font-family: Arial, Helvetica, sans-serif;">&#127962; TOPIDEALSPACE</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 20px 30px 30px 30px;">

                            <!-- Greeting -->
                            <h2 style="margin: 0 0 18px 0; color: #1f2937; font-size: 20px; font-family: Arial, Helvetica, sans-serif;">
                                Bonjour {{ $clientName }},
                            </h2>

                            <p style="margin: 0 0 12px 0; font-size: 15px; color: #333333; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                Vous avez demand&#233; la r&#233;initialisation de votre mot de passe.
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 15px; color: #333333; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                Voici votre code de v&#233;rification :
                            </p>

                            <!-- OTP Code -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td align="center" bgcolor="#4F46E5" style="background-color: #4F46E5; padding: 22px 20px; border-radius: 8px;">
                                        <span style="font-size: 36px; font-weight: bold; color: #ffffff; letter-spacing: 10px; font-family: 'Courier New', Courier, monospace;">{{ $otp }}</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Warning -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 14px 16px; border-radius: 4px;">
                                        <p style="margin: 0 0 8px 0; font-size: 14px; font-weight: bold; color: #92400e; font-family: Arial, Helvetica, sans-serif;">&#9888;&#65039; Important :</p>
                                        <p style="margin: 0 0 5px 0; font-size: 14px; color: #92400e; font-family: Arial, Helvetica, sans-serif;">&#8226; Ce code est valide pendant <strong>10 minutes</strong></p>
                                        <p style="margin: 0 0 5px 0; font-size: 14px; color: #92400e; font-family: Arial, Helvetica, sans-serif;">&#8226; Ne partagez ce code avec personne</p>
                                        <p style="margin: 0; font-size: 14px; color: #92400e; font-family: Arial, Helvetica, sans-serif;">&#8226; Si vous n&#8217;avez pas demand&#233; cette r&#233;initialisation, ignorez cet email</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Closing -->
                            <p style="margin: 0 0 18px 0; font-size: 15px; color: #333333; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                Pour r&#233;initialiser votre mot de passe, entrez ce code sur la page de r&#233;initialisation.
                            </p>

                            <p style="margin: 0; font-size: 15px; color: #333333; font-family: Arial, Helvetica, sans-serif;">
                                Cordialement,<br>
                                <strong>L&#8217;&#233;quipe TOPIDEALSPACE</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px 30px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 6px 0; font-size: 13px; color: #666666; font-family: Arial, Helvetica, sans-serif;">
                                &#169; {{ date('Y') }} TOPIDEALSPACE. Tous droits r&#233;serv&#233;s.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999999; font-family: Arial, Helvetica, sans-serif;">
                                Cet email a &#233;t&#233; envoy&#233; automatiquement, merci de ne pas y r&#233;pondre.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- End Container -->

            </td>
        </tr>
    </table>
    <!-- End Wrapper -->

</body>
</html>
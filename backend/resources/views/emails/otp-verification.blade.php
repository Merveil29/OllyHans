<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>V&#233;rification de votre compte - TOPIDEALSPACE</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;">

    <!-- Wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">

                <!-- Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 8px; overflow: hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" bgcolor="#1d4ed8" style="background-color: #1d4ed8; padding: 30px 20px;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">&#127942; TOPIDEALSPACE</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">

                            <!-- Title -->
                            <h2 style="margin: 0 0 20px 0; color: #1f2937; font-size: 21px; font-family: Arial, Helvetica, sans-serif;">
                                Bonjour {{ $clientName }} ! &#128075;
                            </h2>

                            <!-- Intro -->
                            <p style="margin: 0 0 18px 0; font-size: 15px; color: #4b5563; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                Merci de vous &#234;tre inscrit sur <strong style="color: #1d4ed8;">TOPIDEALSPACE</strong>,
                                votre plateforme de confiance pour acheter et vendre en toute s&#233;curit&#233;.
                            </p>

                            <p style="margin: 0 0 25px 0; font-size: 15px; color: #4b5563; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                Pour finaliser la cr&#233;ation de votre compte et garantir la s&#233;curit&#233; de vos informations,
                                veuillez entrer le code de v&#233;rification ci-dessous :
                            </p>

                            <!-- OTP Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td align="center" bgcolor="#f3f4f6" style="background-color: #f3f4f6; border: 2px dashed #3b82f6; border-radius: 8px; padding: 25px 20px;">
                                        <p style="margin: 0 0 10px 0; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; font-family: Arial, Helvetica, sans-serif;">Votre code de v&#233;rification</p>
                                        <p style="margin: 0; font-size: 42px; font-weight: 700; color: #1d4ed8; letter-spacing: 8px; font-family: 'Courier New', Courier, monospace;">{{ $otp }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Warning -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 18px;">
                                <tr>
                                    <td style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 14px 16px; border-radius: 4px;">
                                        <p style="margin: 0; font-size: 14px; color: #92400e; font-family: Arial, Helvetica, sans-serif;">
                                            &#9200;&#65039; <strong>Attention :</strong> Ce code expire dans <strong>10 minutes</strong>.
                                            Veuillez l&#8217;utiliser rapidement pour activer votre compte.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="background-color: #dbeafe; border-left: 4px solid #3b82f6; padding: 14px 16px; border-radius: 4px;">
                                        <p style="margin: 0; font-size: 14px; color: #1e40af; font-family: Arial, Helvetica, sans-serif;">
                                            &#128274; <strong>S&#233;curit&#233; :</strong> Si vous n&#8217;avez pas demand&#233; cette inscription,
                                            veuillez ignorer cet email. Aucune action ne sera effectu&#233;e sur votre compte.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Closing -->
                            <p style="margin: 0 0 18px 0; font-size: 15px; color: #4b5563; line-height: 1.6; font-family: Arial, Helvetica, sans-serif;">
                                Une fois votre email v&#233;rifi&#233;, vous pourrez profiter de toutes les fonctionnalit&#233;s
                                de TOPIDEALSPACE et commencer &#224; publier vos annonces !
                            </p>

                            <p style="margin: 0; font-size: 15px; color: #4b5563; font-family: Arial, Helvetica, sans-serif;">
                                Cordialement,<br>
                                <strong style="color: #1d4ed8;">L&#8217;&#233;quipe TOPIDEALSPACE</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" bgcolor="#f9fafb" style="background-color: #f9fafb; padding: 20px 30px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 6px 0; font-size: 13px; color: #6b7280; font-family: Arial, Helvetica, sans-serif;">
                                &#169; {{ date('Y') }} TOPIDEALSPACE. Tous droits r&#233;serv&#233;s.
                            </p>
                            <p style="margin: 0; font-size: 13px; color: #6b7280; font-family: Arial, Helvetica, sans-serif;">
                                Besoin d&#8217;aide ?
                                <a href="mailto:{{ config('mail.from.address') }}" style="color: #3b82f6; text-decoration: none;">Contactez-nous</a>
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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Vos identifiants administrateur - TOPIDEALSPACE</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;">

    <!-- Wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">

                <!-- Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td align="center" bgcolor="#6c63d5" style="background-color: #6c63d5; padding: 40px 20px;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-family: Arial, Helvetica, sans-serif;">&#127881; Bienvenue sur TOPIDEALSPACE</h1>
                            <p style="margin: 10px 0 0 0; color: #e0dbff; font-size: 15px; font-family: Arial, Helvetica, sans-serif;">Espace Administrateur</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; font-size: 17px; color: #333333; font-family: Arial, Helvetica, sans-serif;">
                                Bonjour <strong>{{ $user->user_prenom }} {{ $user->user_nom }}</strong>,
                            </p>

                            <!-- Info box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #f8f9fa; border-left: 4px solid #6c63d5; padding: 15px 18px; border-radius: 4px;">
                                        <p style="margin: 0 0 6px 0; font-weight: bold; color: #6c63d5; font-size: 15px; font-family: Arial, Helvetica, sans-serif;">&#10024; Compte administrateur cr&#233;&#233;</p>
                                        <p style="margin: 0; color: #444444; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">
                                            Un compte administrateur a &#233;t&#233; cr&#233;&#233; pour vous sur la plateforme TOPIDEALSPACE.
                                            Vous avez maintenant acc&#232;s &#224; l&#8217;espace d&#8217;administration.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Credentials box title -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 2px solid #e9ecef; border-radius: 8px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 15px 0; font-size: 16px; font-weight: bold; color: #333333; font-family: Arial, Helvetica, sans-serif;">&#128272; Vos identifiants de connexion</p>

                                        <!-- Email -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 12px;">
                                            <tr>
                                                <td style="background-color: #f8f9fa; padding: 10px 12px; border-radius: 5px;">
                                                    <p style="margin: 0 0 5px 0; font-size: 13px; font-weight: bold; color: #6c63d5; font-family: Arial, Helvetica, sans-serif;">Adresse e-mail :</p>
                                                    <p style="margin: 0; font-size: 15px; color: #333333; font-family: 'Courier New', Courier, monospace; background-color: #ffffff; padding: 8px 10px; border: 1px solid #dee2e6; border-radius: 4px;">{{ $user->user_email }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Login -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 12px;">
                                            <tr>
                                                <td style="background-color: #f8f9fa; padding: 10px 12px; border-radius: 5px;">
                                                    <p style="margin: 0 0 5px 0; font-size: 13px; font-weight: bold; color: #6c63d5; font-family: Arial, Helvetica, sans-serif;">Nom d&#8217;utilisateur (Login) :</p>
                                                    <p style="margin: 0; font-size: 15px; color: #333333; font-family: 'Courier New', Courier, monospace; background-color: #ffffff; padding: 8px 10px; border: 1px solid #dee2e6; border-radius: 4px;">{{ $user->user_login }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Password -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="background-color: #f8f9fa; padding: 10px 12px; border-radius: 5px;">
                                                    <p style="margin: 0 0 5px 0; font-size: 13px; font-weight: bold; color: #6c63d5; font-family: Arial, Helvetica, sans-serif;">Mot de passe temporaire :</p>
                                                    <p style="margin: 0; font-size: 15px; color: #333333; font-family: 'Courier New', Courier, monospace; background-color: #ffffff; padding: 8px 10px; border: 1px solid #dee2e6; border-radius: 4px;">{{ $password }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                            <!-- Warning -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px 18px; border-radius: 4px;">
                                        <p style="margin: 0 0 8px 0; font-size: 14px; font-weight: bold; color: #856404; font-family: Arial, Helvetica, sans-serif;">&#9888;&#65039; Important - S&#233;curit&#233;</p>
                                        <p style="margin: 0 0 5px 0; font-size: 14px; color: #856404; font-family: Arial, Helvetica, sans-serif;">&#8226; Changez votre mot de passe d&#232;s votre premi&#232;re connexion</p>
                                        <p style="margin: 0 0 5px 0; font-size: 14px; color: #856404; font-family: Arial, Helvetica, sans-serif;">&#8226; Ne partagez jamais vos identifiants avec personne</p>
                                        <p style="margin: 0; font-size: 14px; color: #856404; font-family: Arial, Helvetica, sans-serif;">&#8226; Un code OTP vous sera envoy&#233; par email &#224; chaque connexion pour s&#233;curiser votre compte</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Instructions -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px 18px; border-radius: 4px;">
                                        <p style="margin: 0 0 10px 0; font-size: 15px; font-weight: bold; color: #1565C0; font-family: Arial, Helvetica, sans-serif;">&#128203; Instructions de premi&#232;re connexion</p>
                                        <p style="margin: 0 0 6px 0; font-size: 14px; color: #1a4f8a; font-family: Arial, Helvetica, sans-serif;">1. Acc&#233;dez &#224; la page de connexion de TOPIDEALSPACE</p>
                                        <p style="margin: 0 0 6px 0; font-size: 14px; color: #1a4f8a; font-family: Arial, Helvetica, sans-serif;">2. Saisissez votre email ou login et votre mot de passe temporaire</p>
                                        <p style="margin: 0 0 6px 0; font-size: 14px; color: #1a4f8a; font-family: Arial, Helvetica, sans-serif;">3. Un code OTP sera automatiquement envoy&#233; &#224; votre email</p>
                                        <p style="margin: 0 0 6px 0; font-size: 14px; color: #1a4f8a; font-family: Arial, Helvetica, sans-serif;">4. Entrez le code OTP re&#231;u pour v&#233;rifier votre identit&#233;</p>
                                        <p style="margin: 0 0 6px 0; font-size: 14px; color: #1a4f8a; font-family: Arial, Helvetica, sans-serif;">5. Apr&#232;s la v&#233;rification, votre compte sera activ&#233; automatiquement</p>
                                        <p style="margin: 0; font-size: 14px; color: #1a4f8a; font-family: Arial, Helvetica, sans-serif;">6. Changez imm&#233;diatement votre mot de passe dans les param&#232;tres</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Note -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-top: 2px solid #e9ecef; padding-top: 20px;">
                                        <p style="margin: 0; font-size: 13px; color: #6c757d; font-family: Arial, Helvetica, sans-serif;">
                                            <strong>Note :</strong> &#192; chaque connexion, un nouveau code OTP vous sera envoy&#233; par email.
                                            Ceci garantit la s&#233;curit&#233; maximale de votre compte administrateur.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" bgcolor="#f8f9fa" style="background-color: #f8f9fa; padding: 20px; border-top: 1px solid #e9ecef;">
                            <p style="margin: 0 0 8px 0; font-size: 14px; font-weight: bold; color: #555555; font-family: Arial, Helvetica, sans-serif;">TOPIDEALSPACE - Plateforme de gestion</p>
                            <p style="margin: 0 0 8px 0; font-size: 14px; color: #6c757d; font-family: Arial, Helvetica, sans-serif;">
                                Besoin d&#8217;aide ? Contactez-nous &#224;
                                <a href="mailto:info@topidealspace.com" style="color: #6c63d5; text-decoration: none;">info@topidealspace.com</a>
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #adb5bd; font-family: Arial, Helvetica, sans-serif;">
                                &#169; {{ date('Y') }} TOPIDEALSPACE. Tous droits r&#233;serv&#233;s.
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
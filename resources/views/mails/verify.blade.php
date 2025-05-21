<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification de l'e-mail</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; padding: 20px;">
    <table style="max-width: 600px; margin: auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <tr>
            <td style="text-align: center;">
                <img src="https://as-compta.triosweb.fr/wp-content/uploads/2024/12/ASCOMPTABLE.png" alt="AS-Comptabilité" style="max-width: 150px; margin-bottom: 10px;">
                <h2 style="color: #2c3e50;">AS-Comptabilité</h2>
            </td>
        </tr>

        <tr>
            <td style="padding: 20px 0;">
                <div style="background-color: #3498db15; padding: 20px; border-left: 5px solid #3498db; border-radius: 6px;">
                    <p style="color: #3498db; font-size: 18px; margin: 0 0 10px;"><strong>Vérification de votre adresse e-mail</strong></p>
                    <div style="color: #333;">
                        <p>Bonjour,</p>
                        <p>Veuillez cliquer sur le lien ci-dessous pour vérifier votre adresse e-mail :</p>
                        <p>
                            <a href="{{ $link }}" style="display: inline-block; padding: 10px 20px; background-color: #3498db; color: #fff; text-decoration: none; border-radius: 4px;">
                                Vérifier mon e-mail
                            </a>
                        </p>
                        <p>Ce lien est valable pendant 24 heures.</p>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td style="text-align: center; padding-top: 30px; font-size: 12px; color: #888;">
                © {{ date('Y') }} AS-Comptabilité. Tous droits réservés.
            </td>
        </tr>
    </table>
</body>
</html>

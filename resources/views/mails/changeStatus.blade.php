@php
    $colors = [
        'accepted' => '#2ecc71',
        'rejected' => '#e74c3c',
        'pending'  => '#f39c12',
        'review'   => '#3498db',
    ];

    $statusColor = $colors[$status] ?? '#333';
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mise à jour de votre formulaire</title>
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
                <div style="background-color: {{ $statusColor }}15; padding: 20px; border-left: 5px solid {{ $statusColor }}; border-radius: 6px;">
                    <p style="color: {{ $statusColor }}; font-size: 18px; margin: 0 0 10px;"><strong>Statut du formulaire : {{ $statusLabel }}</strong></p>
                    <div style="color: #333;">
                        {!! $messageContent !!}
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

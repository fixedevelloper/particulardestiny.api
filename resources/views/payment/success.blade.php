<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement réussi</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
        }
    </style>
</head>

<body>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-lg border-0 text-center p-5" style="max-width: 500px; width: 100%; border-radius: 15px;">

        <!-- Icône succès -->
        <div class="mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 70px;"></i>
        </div>

        <!-- Titre -->
        <h2 class="text-success fw-bold mb-3">Paiement réussi 🎉</h2>

        <!-- Message -->
        <p class="text-muted mb-4">
            Merci pour votre réservation.<br>
            Votre paiement a été validé avec succès.
        </p>

        <!-- Référence -->
        @if($reference)
            <div class="alert alert-light border mb-4">
                <small class="text-muted">Référence de paiement</small><br>
                <strong class="text-dark">{{ $reference }}</strong>
            </div>
    @endif

    <!-- Boutons -->
        <div class="d-grid gap-2">
            <a href="{{config('app.frontend_url')}}/my-reservations" class="btn btn-success btn-lg">
                📋 Voir mes réservations
            </a>

            <a href="{{config('app.frontend_url')}}/" class="btn btn-outline-secondary">
                ⬅ Retour à l'accueil
            </a>
        </div>

    </div>
</div>

</body>
</html>

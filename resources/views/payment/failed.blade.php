<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement échoué</title>

    <!-- Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Style custom -->
    <style>
        body {
            background: #f5f6fa;
        }
    </style>
</head>

<body>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-lg border-0 text-center p-5" style="max-width: 500px; width: 100%; border-radius: 15px;">

        <!-- Icône -->
        <div class="mb-4">
            <i class="bi bi-x-circle-fill text-danger" style="font-size: 70px;"></i>
        </div>

        <!-- Titre -->
        <h2 class="text-danger fw-bold mb-3">Paiement échoué</h2>

        <!-- Message -->
        <p class="text-muted mb-4">
            Oups ! Le paiement n'a pas pu être traité.<br>
            Veuillez vérifier vos informations ou réessayer.
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
            <a href="{{config('app.frontend_url')}}/cart-reservation" class="btn btn-danger btn-lg">
                🔁 Réessayer le paiement
            </a>

            <a href="{{config('app.frontend_url')}}/" class="btn btn-outline-secondary">
                ⬅ Retour à l'accueil
            </a>
        </div>

    </div>
</div>

</body>
</html>

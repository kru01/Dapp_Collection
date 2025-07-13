<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>21127135 - Publication Site</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
        crossorigin="anonymous" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />

</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">PublicationDB</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php?controller=paper&action=search">Browse</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?controller=paper&action=add">Submit</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (!empty($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=author&action=profile">
                                Hello, <?= htmlspecialchars($_SESSION['user']['username']) ?>
                                <span class="badge text-bg-primary">
                                    <?= htmlspecialchars($_SESSION['user']['user_type']) ?>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="index.php?controller=auth&action=logout">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="index.php?controller=auth&action=login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
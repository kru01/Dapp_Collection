<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>21127135 - Office Management</title>

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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?controller=employee&action=home">OfficeMgmt</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php?controller=employee&action=home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?controller=leave&action=createForm">Nghỉ phép</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?controller=deviceBorrow&action=createForm">Mượn thiết bị</a></li>

                    <?php if (!empty($_SESSION['user']) && in_array($_SESSION['user']['role'], ['admin', 'hr'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=approval&action=index">Duyệt yêu cầu</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <?php if (!empty($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=employee&action=edit_profile">
                                Hello, <?= htmlspecialchars($_SESSION['user']['email']) ?>
                                <!-- <span class="badge text-bg-primary">
                                    <?= htmlspecialchars($_SESSION['user']['role']) ?>
                                </span> -->
                                <?php
                                $role = $_SESSION['user']['role'] ?? 'default';

                                if (in_array($role, ['admin', 'hr'])) {
                                    $badgeClass = match ($role) {
                                        'admin' => 'text-bg-danger',
                                        'hr'    => 'text-bg-success',
                                        default => 'text-bg-secondary'
                                    };
                                    echo '<span class="badge ' . $badgeClass . '">' . htmlspecialchars($role) . '</span>';
                                }
                                ?>
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
    <div class="container my-4">
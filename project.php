<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Project details - gallery and information">
    <title>Project - Safa Formwork & Scaffolding</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/projects.css">
    <link rel="stylesheet" href="css/project-detail-inline.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <!-- Main Header -->
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="proj-detail-hero">
            <div class="bg" id="projBg"></div>
            <div class="ov"></div>
            <div class="inner container">
                <div class="proj-detail-title" id="projTitle">Loading...</div>
                <div class="proj-detail-meta">
                    <span class="proj-badge" id="projBadge"></span>
                    <span class="proj-loc"><i class="bi bi-geo-alt-fill" style="color:#ffd18a"></i><span id="projLoc"></span></span>
                </div>
            </div>
        </section>

        <section class="proj-section">
            <div class="container">
                <div class="proj-header-wrap">
                    <a href="projects" class="proj-back-link">Back to Projects</a>
                    <div class="proj-head-card shadow-sm" id="projHead">
                        <h1 class="proj-head-title mb-0" id="headTitle">Loading...</h1>
                        <div class="proj-head-meta d-flex flex-column flex-md-row align-items-start align-items-md-center">
                            <span class="proj-chip chip-success" id="headStatus">Completed</span>
                            <span class="proj-chip chip-location"><span class="chip-icon"></span><span id="headLoc" class="ms-1">Location</span></span>
                        </div>
                    </div>
                </div>
                <div class="proj-note-card" id="projNoteCard" style="display:none">
                    <div class="proj-note-header">
                        <div class="proj-note-icon">📝</div>
                        <div class="proj-note-title">Project Notes</div>
                    </div>
                    <div class="proj-note-body" id="projDesc"></div>
                </div>
                <div class="proj-grid" id="projGrid"></div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="js/main.js"></script>
    <script src="js/project-detail.js"></script>
</body>
</html>



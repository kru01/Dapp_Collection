<?php include('views/partials/header.php'); ?>

<div class="container my-4">
    <h3>Personal Profile
        <span class="h5">
            <span class="text-secondary">of User#</span><span><?= $author['user_id'] ?></span>
        </span>
    </h3>

    <div class="row mt-3">
        <div class="col-md-4">
            <!--
            Append the file's last modified time to force browser reloads
                when the file is replaced, i.e., `v` changes.
            E.g., /uploads/123_profile.jpg?v=1720710289
            -->
            <img src="<?= $author['image_path'] ?>?v=<?= filemtime($author['image_path']) ?>" class="img-fluid border" alt="Profile Image">
        </div>
        <div class="col-md-8">
            <p><strong>Name:</strong> <?= htmlspecialchars($author['full_name']) ?></p>

            <p><strong>Website:</strong> <a href="<?= htmlspecialchars($author['website']) ?>" target="_blank">
                    <?= htmlspecialchars($author['website']) ?></a></p>

            <dl class="row">
                <dt class="col-sm-2">
                    <p>Interests:</p>
                </dt>
                <dd class="col-sm-10 text-break"><?= htmlspecialchars(implode(', ', $profile['interests'] ?? [])) ?></dd>

                <dt class="col-sm-2">Bio:</dt>
                <dd class="col-sm-10 p-2 border rounded bg-info bg-opacity-10 overflow-auto text-break shadow-sm"
                    style="max-height: 300px;">
                    <?= nl2br(htmlspecialchars($profile['bio'] ?? '')) ?>
                </dd>
            </dl>

            <?php if (!empty($is_owner) && $is_owner): ?>
                <a class="btn btn-primary" href="index.php?controller=author&action=edit">Edit Profile</a>
            <?php endif; ?>
        </div>
    </div>

    <h4 class="mt-5">Authored Papers</h4>
    <div id="paperContainer"></div>
</div>

<script>
    const handlePaper = (() => {
        const loadAuthorPapers = async (page) => {
            const res = await fetch(`index.php?controller=author&action=ajax_author_papers&id=<?= $user_id ?>&page=${page}`);
            const html = await res.text();

            document.getElementById('paperContainer').innerHTML = html;

            const url = new URL(window.location.href);
            url.searchParams.set('page', page);

            window.history.pushState({}, '', url.toString());
        }

        (async () => {
            const params = new URLSearchParams(window.location.search);
            let page = params.get('page') ?? 1;

            await loadAuthorPapers(page);

            const paperList = document.getElementById('paperList');

            if (+page > 1 && !paperList.querySelector('.list-group-item')) {
                page = 1;
                await loadAuthorPapers(page);
            }
        })();

        return {
            loadAuthorPapers
        }
    })();
</script>

<?php include('views/partials/footer.php'); ?>
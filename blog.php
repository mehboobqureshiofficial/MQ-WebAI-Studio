<?php
require __DIR__ . '/inc/db.php';
start_session();
$slug = (string)($_GET['slug'] ?? '');
$post = null;
if ($slug !== '') {
    $post = q('SELECT * FROM posts WHERE slug = ?', [$slug])->fetch();
    if (!$post || ($post['status'] !== 'published' && empty($_SESSION['uid']))) { http_response_code(404); $post = null; $notfound = true; }
}
$per = 9; $pg = max(1, (int)($_GET['p'] ?? 1));
$title = $post ? $post['title'] : 'Blog';
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= h($title) ?> — MQ WebAI Studio</title><meta name="description" content="<?= h($post ? $post['excerpt'] : 'Latest posts from MQ WebAI Studio') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/site.css" rel="stylesheet"></head><body>
<nav class="nav"><a class="brand" href="index.php">MQ WebAI Studio</a><div class="links"><a href="index.php">Home</a><a class="on" href="blog.php">Blog</a><a href="index.php#contact">Contact</a></div><a class="b" href="index.php#contact">Hire Me</a></nav>
<section><div class="container" style="max-width:<?= $post ? '820px' : '1140px' ?>">
<?php if ($post): ?>
  <p><a href="blog.php">← All posts</a></p>
  <?php if ($post['status'] !== 'published'): ?><div class="alert alert-warning">Draft preview — sirf admin ko nazar aa raha hai.</div><?php endif; ?>
  <h1><?= h($post['title']) ?></h1><p class="text-secondary"><?= date('d M Y', $post['created_at']) ?></p>
  <?php if ($post['cover']): ?><img class="post-cover mb-4" src="<?= h($post['cover']) ?>" alt="<?= h($post['title']) ?>"><?php endif; ?>
  <div class="post-body"><?= clean_html($post['content']) ?></div>
<?php elseif (!empty($notfound)): ?>
  <h1>Post nahi mili</h1><p><a href="blog.php">← Saari posts dekhein</a></p>
<?php else:
  $total = (int)q("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
  $rows = q("SELECT title, slug, excerpt, cover, created_at FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT $per OFFSET " . (($pg - 1) * $per))->fetchAll(); ?>
  <h2>Blog</h2><div class="row g-3 mt-1">
  <?php foreach ($rows as $r): ?><div class="col-md-6 col-lg-4"><div class="card2 post-card">
    <?php if ($r['cover']): ?><img src="<?= h($r['cover']) ?>" alt=""><?php endif; ?>
    <small class="text-secondary"><?= date('d M Y', $r['created_at']) ?></small><h3 class="h6 mb-0"><a href="blog.php?slug=<?= h($r['slug']) ?>"><?= h($r['title']) ?></a></h3>
    <p class="small mb-0"><?= h($r['excerpt']) ?></p></div></div><?php endforeach; ?>
  <?php if (!$rows): ?><p class="text-secondary">Abhi koi post nahi.</p><?php endif; ?></div>
  <div class="d-flex gap-2 mt-4"><?php if ($pg > 1): ?><a class="b b2" href="blog.php?p=<?= $pg - 1 ?>">← Newer</a><?php endif; ?><?php if ($pg * $per < $total): ?><a class="b b2" href="blog.php?p=<?= $pg + 1 ?>">Older →</a><?php endif; ?></div>
<?php endif; ?>
</div></section><footer class="text-center small text-secondary pb-5">© <?= date('Y') ?> MQ WebAI Studio · <?= h(setting('name')) ?></footer></body></html>

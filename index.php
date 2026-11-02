<?php
require __DIR__ . '/inc/db.php';
$items = [];
foreach (q('SELECT * FROM items ORDER BY sort_order, id') as $r) $items[$r['kind']][] = $r;
$pick = function (string $k, callable $f) use ($items) { return array_map($f, $items[$k] ?? []); };
$sec = [];
foreach (['web', 'app', 'software', 'ai', 'net'] as $k) $sec[$k] = $pick($k, function ($r) { return [$r['title'], $r['body'], 'k']; });
$MQ = [
    'cfg' => [
        'name' => setting('name'), 'email' => setting('email'), 'uniEmail' => setting('uniEmail'), 'wa' => setting('wa'),
        'addr' => setting('addr'), 'bio' => setting('bio'),
        'fb' => array_values(array_filter(array_map('trim', explode("\n", setting('fb'))))),
        'endpoint' => BASE . '/api/enquiry.php',
    ],
    'BUILD' => $pick('build', function ($r) { return [$r['title'], $r['body'], $r['extra']]; }),
    'SEC' => $sec,
    'PR' => $pick('project', function ($r) { return [$r['title'], $r['sub'], $r['body'], $r['extra']]; }),
    'ceoUrl' => BASE . '/' . setting('ceo_file', 'assets/ceo-portfolio.html'),
];
$posts = q("SELECT title, slug, excerpt, cover, created_at FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 6")->fetchAll();
?><!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>MQ WebAI Studio — Build Smart. Grow Digital. Learn with AI.</title>
<meta name="description" content="MQ WebAI Studio by <?= h(setting('name')) ?>: web development, software, networking and AI solutions.">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/site.css" rel="stylesheet">
</head><body>
<nav class="nav" aria-label="Main"><a class="brand" href="#home">MQ WebAI Studio</a><div class="links" id="links"></div><button class="b b2" data-ceo>About CEO</button><button class="b" data-hire>Hire Me</button></nav>

<header id="home" class="container pt-4"><div class="hero"><img src="<?= h(setting('hero', 'assets/hero.jpg')) ?>" alt="MQ WebAI Studio office — Build Smart. Grow Digital. Learn with AI.">
<div class="ov"><a class="b" href="#build">Explore Services</a><button class="b b2" data-chat>Meet My AI Assistant</button><button class="b b2" data-ceo>About CEO</button><button class="b b2" data-hire>Hire Me</button></div></div></header>

<section id="build"><div class="container"><h2>What MQ WebAI Studio Builds</h2><p class="text-secondary">Websites, software, apps, AI tools and networks. Tell us what your business needs.</p><div class="row g-3" id="build-grid"></div></div></section>

<section id="web"><div class="container"><h2>Web Development</h2><div class="row g-3" id="sec-web"></div></div></section>
<section id="app"><div class="container"><h2>App Development</h2><div class="row g-3" id="sec-app"></div></div></section>
<section id="software"><div class="container"><h2>Software Development</h2><div class="row g-3" id="sec-software"></div></div></section>
<section id="ai"><div class="container"><h2>AI Solutions &amp; Knowledge Center</h2><div class="row g-3" id="sec-ai"></div></div></section>
<section id="agentic"><div class="container"><h2>Agentic AI — Agent Dashboard</h2>
<div class="row g-3"><div class="col-lg-5"><div class="card2"><label class="form-label fw-semibold">Give the Master Orchestrator a task</label>
<div class="input-group"><input id="task" class="form-control" placeholder="e.g. subnet 192.168.10.37/26"><button class="b" id="runTask">Run</button></div>
<div class="small text-secondary mt-1">Try: “subnet 10.0.0.9/22”, “write a LinkedIn post about AI agents”, “list projects”, “draft enquiry”.</div>
<h3 class="h6 mt-3">Pending approvals</h3><div id="appr" class="small text-secondary">None.</div>
<h3 class="h6 mt-3">Execution log</h3><pre id="log">No tasks yet.</pre></div></div>
<div class="col-lg-7"><div class="card2"><div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Agent</th><th>Tools / permission</th><th>Status</th></tr></thead><tbody id="agents"></tbody></table></div></div></div></div></div></section>
<section id="net"><div class="container"><h2>IT &amp; Networking</h2><div class="row g-3" id="sec-net"></div>
<div class="card2 mt-3"><h3 class="h6">Working subnet calculator</h3><div class="input-group"><input id="cidr" class="form-control" value="192.168.1.77/26"><button class="b" id="calc">Calculate</button></div><pre class="mt-2" id="calcOut"></pre></div></div></section>
<section id="automation"><div class="container"><h2>AI Automation — Content Studio</h2><div class="card2"><div class="row g-2"><div class="col-md-5"><input id="topic" class="form-control" placeholder="Topic (e.g. RAG for small businesses)"></div><div class="col-md-4"><select id="plat" class="form-select"><option>Facebook</option><option>LinkedIn</option><option>WhatsApp Status</option></select></div><div class="col-md-3"><button class="b w-100" id="gen">Generate draft</button></div></div>
<div class="mt-3" id="drafts"></div><p class="small text-secondary mb-0">Drafts are saved for your approval. Automatic publishing to Facebook needs the Meta Graph API with a server holding your token — not possible in a single HTML file. WhatsApp Status can’t be auto-posted by the official API, so download the text and post it manually.</p></div></div></section>
<section id="blog"><div class="container"><div class="d-flex justify-content-between align-items-end"><h2>Latest from the Blog</h2><a href="blog.php">All posts →</a></div><div class="row g-3 mt-1">
<?php foreach ($posts as $p): ?><div class="col-md-6 col-lg-4"><div class="card2 post-card">
<?php if ($p['cover']): ?><img src="<?= h($p['cover']) ?>" alt=""><?php endif; ?>
<small class="text-secondary"><?= date('d M Y', $p['created_at']) ?></small><h3 class="h6 mb-0"><a href="blog.php?slug=<?= h($p['slug']) ?>"><?= h($p['title']) ?></a></h3><p class="small mb-0"><?= h($p['excerpt']) ?></p></div></div>
<?php endforeach; if (!$posts): ?><p class="text-secondary">Posts jald aa rahi hain.</p><?php endif; ?></div></div></section>

<section id="contact"><div class="container"><h2>Contact</h2><div class="row g-3"><div class="col-md-6"><div class="card2"><p>Email: <a href="mailto:<?= h(setting('email')) ?>"><?= h(setting('email')) ?></a></p><p>WhatsApp: <a id="waLink" target="_blank" rel="noopener">+<?= h(setting('wa')) ?></a></p><h3 class="h6">Facebook Pages</h3><div id="fb" class="small"></div><div class="mt-3 d-flex gap-2"><button class="b" data-hire>Hire Me</button><a class="b b2" href="admin/login.php">Admin</a></div></div></div>
<div class="col-md-6"><div class="card2"><h3 class="h6">Why MQ WebAI Studio</h3><p class="mb-0">Ask the MQ AI Assistant (bottom-right) about our services, hiring, or the CEO. The About CEO button opens Mehboob’s full portfolio.</p></div></div></div></div></section>
<footer class="text-center small text-secondary pb-5">© 2026 MQ WebAI Studio · Mehboob Qureshi · Build Smart. Grow Digital. Learn with AI.</footer>

<button class="b" id="fab" aria-label="Open MQ AI Assistant" data-chat>💬</button>
<div id="chat" role="dialog" aria-label="MQ AI Assistant"><div class="p-2 px-3 text-white d-flex justify-content-between" style="background:linear-gradient(135deg,var(--blue),var(--vi))"><b><span class="dot"></span> MQ AI Assistant</b><button class="btn btn-sm text-white" data-chat>✕</button></div><div id="msgs"></div>
<form id="cf" class="d-flex gap-1 p-2 border-top"><input id="ci" class="form-control" placeholder="Ask about services, skills, CEO…" autocomplete="off"><button class="b">Send</button></form></div>

<div class="modal fade" id="hire" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Hire Mehboob Qureshi</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
<div id="choice" class="d-flex gap-2 mb-3"><button class="b" id="pickMail">Contact via Email</button><a class="b b2" id="pickWa" target="_blank" rel="noopener">Contact via WhatsApp</a></div>
<form id="hf" class="row g-2" style="display:none" novalidate>
<div class="col-md-6"><input class="form-control" name="name" placeholder="Your Name *" required maxlength="80"></div><div class="col-md-6"><input class="form-control" type="email" name="email" placeholder="Your Email Address *" required></div>
<div class="col-md-6"><input class="form-control" name="company" placeholder="Company Name (optional)" maxlength="80"></div>
<div class="col-md-6"><select class="form-select" name="type"><option>Website</option><option>Web application</option><option>Software / database system</option><option>Networking / IT</option><option>AI / automation</option><option>Other</option></select></div>
<div class="col-md-8"><input class="form-control" name="subject" value="Project enquiry — MQ WebAI Studio" required maxlength="120"></div><div class="col-md-4"><input class="form-control" name="budget" placeholder="Budget (optional)" maxlength="40"></div>
<div class="col-12"><textarea class="form-control" name="message" rows="6" required maxlength="4000" placeholder="Project details *">Hello Mehboob,

I would like to discuss a project.

Goals:
Timeline:

Regards,</textarea></div>
<div class="col-12"><input type="file" name="file" class="form-control"></div><input name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
<div class="col-12"><button type="button" class="b b2" id="prev">Preview</button> <button class="b">Send Email</button></div><div class="col-12" id="hmsg" role="status"></div></form></div></div></div></div>

<div id="ceoView" hidden><div class="ceobar"><button class="b" id="ceoBack">← Back to MQ WebAI Studio</button><b>CEO Portfolio</b><button class="b b2" data-hire>Hire Me</button></div><iframe id="ceoFrame" title="CEO portfolio of Mehboob"></iframe></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.MQ=<?= json_encode($MQ, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="assets/site.js"></script>
</body></html>
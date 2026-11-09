<?php
declare(strict_types=1);

/*
 * AstraFile Manager
 * Single-file PHP file manager for shared hosting, Laravel, CodeIgniter, and plain PHP.
 * Configure with environment variables:
 * FM_ROOT=/path/to/root FM_PASSWORD=change-me FM_TOKEN=random-token FM_LOCALE=en php -S 127.0.0.1:8080 index.php
 */

ini_set('display_errors', '0');
ini_set('log_errors', '1');
session_start();

const APP_NAME = 'AstraFile';
const APP_VERSION = '1.0.0';
const LOGO_DATA_URI = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImciIHgxPSIwIiB4Mj0iMSIgeTE9IjAiIHkyPSIxIj48c3RvcCBzdG9wLWNvbG9yPSIjMDBiODk0Ii8+PHN0b3Agb2Zmc2V0PSIuNTUiIHN0b3AtY29sb3I9IiMwOTg0ZTMiLz48c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiM2YzVjZTciLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgcng9IjI2IiBmaWxsPSIjMTAxODIwIi8+PHBhdGggZD0iTTE4IDQyYzAtOCA2LTE0IDE0LTE0aDI1bDEwIDEyaDI5YzggMCAxNCA2IDE0IDE0djM5YzAgOC02IDE0LTE0IDE0SDMyYy04IDAtMTQtNi0xNC0xNFY0MnoiIGZpbGw9InVybCgjZykiLz48cGF0aCBkPSJNMjkgNTVoNzB2MzVjMCA0LTMgNy03IDdIMzZjLTQgMC03LTMtNy03VjU1eiIgZmlsbD0iI2Y4ZmJmZiIgZmlsbC1vcGFjaXR5PSIuOTUiLz48cGF0aCBkPSJNNDQgNzBoNDBNNDQgODJoMjciIHN0cm9rZT0iIzEwMTgyMCIgc3Ryb2tlLXdpZHRoPSI3IiBzdHJva2UtbGluZWNhcD0icm91bmQiLz48L3N2Zz4=';

$config = [
    'root' => getenv('FM_ROOT') ?: '/',
    'password' => getenv('FM_PASSWORD') ?: '',
    'token' => getenv('FM_TOKEN') ?: '',
    'default_locale' => getenv('FM_LOCALE') ?: 'en',
    'allow_delete' => getenv('FM_ALLOW_DELETE') !== '0',
    'allow_upload' => getenv('FM_ALLOW_UPLOAD') !== '0',
    'max_search_results' => 500,
];

$translations = [
    'en' => [
        'language' => 'Language', 'login' => 'Login', 'password' => 'Password', 'token' => 'Token', 'password_or_token' => 'Password or access token', 'unlock' => 'Unlock',
        'home' => 'Home', 'search' => 'Search', 'upload' => 'Upload', 'new_folder' => 'New folder',
        'new_file' => 'New file', 'name' => 'Name', 'size' => 'Size', 'modified' => 'Modified',
        'permissions' => 'Permissions', 'actions' => 'Actions', 'open' => 'Open', 'edit' => 'Edit',
        'download' => 'Download', 'rename' => 'Rename', 'delete' => 'Delete', 'copy' => 'Copy',
        'move' => 'Move', 'zip' => 'Zip', 'unzip' => 'Unzip', 'save' => 'Save', 'cancel' => 'Cancel',
        'current_path' => 'Current path', 'root' => 'Root', 'items' => 'items', 'path' => 'Path',
        'file_location' => 'File location',
        'mode' => 'Mode', 'create' => 'Create', 'target' => 'Target', 'confirm_delete' => 'Delete this item?',
        'drop_hint' => 'Choose files to upload into this directory.', 'empty' => 'This directory is empty.',
        'not_writable' => 'Not writable', 'writable' => 'Writable', 'search_results' => 'Search results',
        'no_results' => 'No results found.', 'logout' => 'Logout', 'preview' => 'Preview',
        'server' => 'Server', 'disk' => 'Disk', 'php' => 'PHP', 'root_locked' => 'Root locked to',
        'theme' => 'Theme', 'dark_mode' => 'Dark mode', 'theme_color' => 'Color', 'theme_black' => 'Black', 'theme_white' => 'White',
        'theme_mono' => 'Black and white', 'message_saved' => 'Saved.', 'message_done' => 'Done.', 'message_error' => 'Action failed.',
    ],
    'fa' => [
        'language' => 'زبان', 'login' => 'ورود', 'password' => 'رمز', 'token' => 'توکن', 'password_or_token' => 'رمز یا توکن ورود', 'unlock' => 'باز کردن',
        'home' => 'خانه', 'search' => 'جستجو', 'upload' => 'آپلود', 'new_folder' => 'پوشه جدید',
        'new_file' => 'فایل جدید', 'name' => 'نام', 'size' => 'حجم', 'modified' => 'ویرایش شده',
        'permissions' => 'دسترسی', 'actions' => 'عملیات', 'open' => 'باز کردن', 'edit' => 'ویرایش',
        'download' => 'دانلود', 'rename' => 'تغییر نام', 'delete' => 'حذف', 'copy' => 'کپی',
        'move' => 'انتقال', 'zip' => 'زیپ', 'unzip' => 'باز کردن زیپ', 'save' => 'ذخیره', 'cancel' => 'لغو',
        'current_path' => 'مسیر فعلی', 'root' => 'ریشه', 'items' => 'آیتم', 'path' => 'مسیر',
        'file_location' => 'محل فایل',
        'mode' => 'حالت', 'create' => 'ساختن', 'target' => 'مقصد', 'confirm_delete' => 'این مورد حذف شود؟',
        'drop_hint' => 'فایل‌ها را برای آپلود در این مسیر انتخاب کنید.', 'empty' => 'این پوشه خالی است.',
        'not_writable' => 'غیر قابل نوشتن', 'writable' => 'قابل نوشتن', 'search_results' => 'نتایج جستجو',
        'no_results' => 'نتیجه‌ای پیدا نشد.', 'logout' => 'خروج', 'preview' => 'پیش‌نمایش',
        'server' => 'سرور', 'disk' => 'دیسک', 'php' => 'PHP', 'root_locked' => 'ریشه محدود شده به',
        'theme' => 'پوسته', 'dark_mode' => 'حالت تاریک', 'theme_color' => 'رنگی', 'theme_black' => 'مشکی', 'theme_white' => 'سفید',
        'theme_mono' => 'سیاه و سفید', 'message_saved' => 'ذخیره شد.', 'message_done' => 'انجام شد.', 'message_error' => 'عملیات ناموفق بود.',
    ],
    'th' => [
        'language' => 'ภาษา', 'login' => 'เข้าสู่ระบบ', 'password' => 'รหัสผ่าน', 'token' => 'โทเคน', 'password_or_token' => 'รหัสผ่านหรือโทเคน', 'unlock' => 'ปลดล็อก',
        'home' => 'หน้าแรก', 'search' => 'ค้นหา', 'upload' => 'อัปโหลด', 'new_folder' => 'โฟลเดอร์ใหม่',
        'new_file' => 'ไฟล์ใหม่', 'name' => 'ชื่อ', 'size' => 'ขนาด', 'modified' => 'แก้ไขเมื่อ',
        'permissions' => 'สิทธิ์', 'actions' => 'การทำงาน', 'open' => 'เปิด', 'edit' => 'แก้ไข',
        'download' => 'ดาวน์โหลด', 'rename' => 'เปลี่ยนชื่อ', 'delete' => 'ลบ', 'copy' => 'คัดลอก',
        'move' => 'ย้าย', 'zip' => 'บีบอัด', 'unzip' => 'แตกไฟล์', 'save' => 'บันทึก', 'cancel' => 'ยกเลิก',
        'current_path' => 'พาธปัจจุบัน', 'root' => 'ราก', 'items' => 'รายการ', 'path' => 'พาธ',
        'file_location' => 'ตำแหน่งไฟล์',
        'mode' => 'โหมด', 'create' => 'สร้าง', 'target' => 'ปลายทาง', 'confirm_delete' => 'ลบรายการนี้หรือไม่?',
        'drop_hint' => 'เลือกไฟล์เพื่ออัปโหลดไปยังโฟลเดอร์นี้', 'empty' => 'โฟลเดอร์นี้ว่าง',
        'not_writable' => 'เขียนไม่ได้', 'writable' => 'เขียนได้', 'search_results' => 'ผลการค้นหา',
        'no_results' => 'ไม่พบผลลัพธ์', 'logout' => 'ออกจากระบบ', 'preview' => 'ตัวอย่าง',
        'server' => 'เซิร์ฟเวอร์', 'disk' => 'ดิสก์', 'php' => 'PHP', 'root_locked' => 'จำกัดรากไว้ที่',
        'theme' => 'ธีม', 'dark_mode' => 'โหมดมืด', 'theme_color' => 'สี', 'theme_black' => 'ดำ', 'theme_white' => 'ขาว',
        'theme_mono' => 'ขาวดำ', 'message_saved' => 'บันทึกแล้ว', 'message_done' => 'เสร็จแล้ว', 'message_error' => 'ทำงานไม่สำเร็จ',
    ],
    'id' => [
        'language' => 'Bahasa', 'login' => 'Masuk', 'password' => 'Kata sandi', 'token' => 'Token', 'password_or_token' => 'Kata sandi atau token akses', 'unlock' => 'Buka',
        'home' => 'Beranda', 'search' => 'Cari', 'upload' => 'Unggah', 'new_folder' => 'Folder baru',
        'new_file' => 'File baru', 'name' => 'Nama', 'size' => 'Ukuran', 'modified' => 'Diubah',
        'permissions' => 'Izin', 'actions' => 'Aksi', 'open' => 'Buka', 'edit' => 'Edit',
        'download' => 'Unduh', 'rename' => 'Ubah nama', 'delete' => 'Hapus', 'copy' => 'Salin',
        'move' => 'Pindah', 'zip' => 'Zip', 'unzip' => 'Ekstrak', 'save' => 'Simpan', 'cancel' => 'Batal',
        'current_path' => 'Path saat ini', 'root' => 'Root', 'items' => 'item', 'path' => 'Path',
        'file_location' => 'Lokasi file',
        'mode' => 'Mode', 'create' => 'Buat', 'target' => 'Tujuan', 'confirm_delete' => 'Hapus item ini?',
        'drop_hint' => 'Pilih file untuk diunggah ke direktori ini.', 'empty' => 'Direktori ini kosong.',
        'not_writable' => 'Tidak dapat ditulis', 'writable' => 'Dapat ditulis', 'search_results' => 'Hasil pencarian',
        'no_results' => 'Tidak ada hasil.', 'logout' => 'Keluar', 'preview' => 'Pratinjau',
        'server' => 'Server', 'disk' => 'Disk', 'php' => 'PHP', 'root_locked' => 'Root dibatasi ke',
        'theme' => 'Tema', 'dark_mode' => 'Mode gelap', 'theme_color' => 'Berwarna', 'theme_black' => 'Hitam', 'theme_white' => 'Putih',
        'theme_mono' => 'Hitam putih', 'message_saved' => 'Tersimpan.', 'message_done' => 'Selesai.', 'message_error' => 'Aksi gagal.',
    ],
    'ar' => ['language' => 'اللغة', 'login' => 'دخول', 'password' => 'كلمة المرور', 'unlock' => 'فتح', 'home' => 'الرئيسية', 'search' => 'بحث', 'upload' => 'رفع', 'new_folder' => 'مجلد جديد', 'new_file' => 'ملف جديد', 'name' => 'الاسم', 'size' => 'الحجم', 'modified' => 'آخر تعديل', 'permissions' => 'الصلاحيات', 'actions' => 'إجراءات', 'open' => 'فتح', 'edit' => 'تعديل', 'download' => 'تحميل', 'rename' => 'إعادة تسمية', 'delete' => 'حذف', 'copy' => 'نسخ', 'move' => 'نقل', 'zip' => 'ضغط', 'unzip' => 'فك الضغط', 'save' => 'حفظ', 'cancel' => 'إلغاء', 'current_path' => 'المسار الحالي', 'root' => 'الجذر', 'items' => 'عنصر', 'path' => 'المسار', 'mode' => 'النمط', 'create' => 'إنشاء', 'target' => 'الهدف', 'confirm_delete' => 'حذف هذا العنصر؟', 'drop_hint' => 'اختر ملفات للرفع إلى هذا المجلد.', 'empty' => 'هذا المجلد فارغ.', 'not_writable' => 'غير قابل للكتابة', 'writable' => 'قابل للكتابة', 'search_results' => 'نتائج البحث', 'no_results' => 'لا توجد نتائج.', 'logout' => 'خروج', 'preview' => 'معاينة', 'server' => 'الخادم', 'disk' => 'القرص', 'php' => 'PHP', 'root_locked' => 'الجذر مقيد إلى', 'message_saved' => 'تم الحفظ.', 'message_done' => 'تم.', 'message_error' => 'فشلت العملية.'],
    'tr' => ['language' => 'Dil', 'login' => 'Giriş', 'password' => 'Parola', 'unlock' => 'Aç', 'home' => 'Ana sayfa', 'search' => 'Ara', 'upload' => 'Yükle', 'new_folder' => 'Yeni klasör', 'new_file' => 'Yeni dosya', 'name' => 'Ad', 'size' => 'Boyut', 'modified' => 'Değişti', 'permissions' => 'İzinler', 'actions' => 'İşlemler', 'open' => 'Aç', 'edit' => 'Düzenle', 'download' => 'İndir', 'rename' => 'Yeniden adlandır', 'delete' => 'Sil', 'copy' => 'Kopyala', 'move' => 'Taşı', 'zip' => 'Zip', 'unzip' => 'Zip aç', 'save' => 'Kaydet', 'cancel' => 'İptal', 'current_path' => 'Geçerli yol', 'root' => 'Kök', 'items' => 'öğe', 'path' => 'Yol', 'mode' => 'Mod', 'create' => 'Oluştur', 'target' => 'Hedef', 'confirm_delete' => 'Bu öğe silinsin mi?', 'drop_hint' => 'Bu dizine yüklemek için dosya seçin.', 'empty' => 'Bu dizin boş.', 'not_writable' => 'Yazılamaz', 'writable' => 'Yazılabilir', 'search_results' => 'Arama sonuçları', 'no_results' => 'Sonuç yok.', 'logout' => 'Çıkış', 'preview' => 'Önizleme', 'server' => 'Sunucu', 'disk' => 'Disk', 'php' => 'PHP', 'root_locked' => 'Kök şu konuma kilitli', 'message_saved' => 'Kaydedildi.', 'message_done' => 'Tamam.', 'message_error' => 'İşlem başarısız.'],
    'es' => ['language' => 'Idioma', 'login' => 'Entrar', 'password' => 'Contraseña', 'unlock' => 'Desbloquear', 'home' => 'Inicio', 'search' => 'Buscar', 'upload' => 'Subir', 'new_folder' => 'Carpeta nueva', 'new_file' => 'Archivo nuevo', 'name' => 'Nombre', 'size' => 'Tamaño', 'modified' => 'Modificado', 'permissions' => 'Permisos', 'actions' => 'Acciones', 'open' => 'Abrir', 'edit' => 'Editar', 'download' => 'Descargar', 'rename' => 'Renombrar', 'delete' => 'Eliminar', 'copy' => 'Copiar', 'move' => 'Mover', 'zip' => 'Zip', 'unzip' => 'Extraer', 'save' => 'Guardar', 'cancel' => 'Cancelar', 'current_path' => 'Ruta actual', 'root' => 'Raíz', 'items' => 'elementos', 'path' => 'Ruta', 'mode' => 'Modo', 'create' => 'Crear', 'target' => 'Destino', 'confirm_delete' => '¿Eliminar este elemento?', 'drop_hint' => 'Elige archivos para subir a este directorio.', 'empty' => 'Este directorio está vacío.', 'not_writable' => 'No escribible', 'writable' => 'Escribible', 'search_results' => 'Resultados', 'no_results' => 'Sin resultados.', 'logout' => 'Salir', 'preview' => 'Vista previa', 'server' => 'Servidor', 'disk' => 'Disco', 'php' => 'PHP', 'root_locked' => 'Raíz limitada a', 'message_saved' => 'Guardado.', 'message_done' => 'Listo.', 'message_error' => 'La acción falló.'],
    'fr' => ['language' => 'Langue', 'login' => 'Connexion', 'password' => 'Mot de passe', 'unlock' => 'Déverrouiller', 'home' => 'Accueil', 'search' => 'Rechercher', 'upload' => 'Téléverser', 'new_folder' => 'Nouveau dossier', 'new_file' => 'Nouveau fichier', 'name' => 'Nom', 'size' => 'Taille', 'modified' => 'Modifié', 'permissions' => 'Droits', 'actions' => 'Actions', 'open' => 'Ouvrir', 'edit' => 'Modifier', 'download' => 'Télécharger', 'rename' => 'Renommer', 'delete' => 'Supprimer', 'copy' => 'Copier', 'move' => 'Déplacer', 'zip' => 'Zip', 'unzip' => 'Extraire', 'save' => 'Enregistrer', 'cancel' => 'Annuler', 'current_path' => 'Chemin actuel', 'root' => 'Racine', 'items' => 'éléments', 'path' => 'Chemin', 'mode' => 'Mode', 'create' => 'Créer', 'target' => 'Cible', 'confirm_delete' => 'Supprimer cet élément ?', 'drop_hint' => 'Choisissez des fichiers à téléverser ici.', 'empty' => 'Ce dossier est vide.', 'not_writable' => 'Non modifiable', 'writable' => 'Modifiable', 'search_results' => 'Résultats', 'no_results' => 'Aucun résultat.', 'logout' => 'Sortir', 'preview' => 'Aperçu', 'server' => 'Serveur', 'disk' => 'Disque', 'php' => 'PHP', 'root_locked' => 'Racine limitée à', 'message_saved' => 'Enregistré.', 'message_done' => 'Terminé.', 'message_error' => 'Action échouée.'],
    'de' => ['language' => 'Sprache', 'login' => 'Anmelden', 'password' => 'Passwort', 'unlock' => 'Entsperren', 'home' => 'Start', 'search' => 'Suchen', 'upload' => 'Hochladen', 'new_folder' => 'Neuer Ordner', 'new_file' => 'Neue Datei', 'name' => 'Name', 'size' => 'Größe', 'modified' => 'Geändert', 'permissions' => 'Rechte', 'actions' => 'Aktionen', 'open' => 'Öffnen', 'edit' => 'Bearbeiten', 'download' => 'Download', 'rename' => 'Umbenennen', 'delete' => 'Löschen', 'copy' => 'Kopieren', 'move' => 'Verschieben', 'zip' => 'Zip', 'unzip' => 'Entpacken', 'save' => 'Speichern', 'cancel' => 'Abbrechen', 'current_path' => 'Aktueller Pfad', 'root' => 'Root', 'items' => 'Elemente', 'path' => 'Pfad', 'mode' => 'Modus', 'create' => 'Erstellen', 'target' => 'Ziel', 'confirm_delete' => 'Dieses Element löschen?', 'drop_hint' => 'Dateien für diesen Ordner auswählen.', 'empty' => 'Dieser Ordner ist leer.', 'not_writable' => 'Nicht schreibbar', 'writable' => 'Schreibbar', 'search_results' => 'Suchergebnisse', 'no_results' => 'Keine Ergebnisse.', 'logout' => 'Abmelden', 'preview' => 'Vorschau', 'server' => 'Server', 'disk' => 'Datenträger', 'php' => 'PHP', 'root_locked' => 'Root begrenzt auf', 'message_saved' => 'Gespeichert.', 'message_done' => 'Fertig.', 'message_error' => 'Aktion fehlgeschlagen.'],
];

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function locale_code(array $translations, string $fallback): string
{
    $requested = $_GET['lang'] ?? $_SESSION['locale'] ?? $fallback;
    $requested = strtolower(preg_replace('/[^a-z]/', '', (string) $requested) ?: $fallback);
    if (!isset($translations[$requested])) {
        $requested = 'en';
    }
    $_SESSION['locale'] = $requested;
    return $requested;
}

function theme_code(): string
{
    $allowed = ['color', 'black', 'white', 'mono'];
    $requested = strtolower(preg_replace('/[^a-z]/', '', (string) ($_GET['theme'] ?? $_SESSION['theme'] ?? 'color')) ?: 'color');
    if (!in_array($requested, $allowed, true)) {
        $requested = 'color';
    }
    $_SESSION['theme'] = $requested;
    return $requested;
}

function t(string $key): string
{
    global $translations, $locale;
    return $translations[$locale][$key] ?? $translations['en'][$key] ?? $key;
}

function app_root(array $config): string
{
    $root = realpath($config['root']);
    if ($root === false || !is_dir($root)) {
        $root = DIRECTORY_SEPARATOR;
    }
    return rtrim($root, DIRECTORY_SEPARATOR) ?: DIRECTORY_SEPARATOR;
}

function clean_rel(string $path): string
{
    $path = str_replace('\\', '/', str_replace("\0", '', $path));
    $parts = [];
    foreach (explode('/', $path) as $part) {
        if ($part === '' || $part === '.') {
            continue;
        }
        if ($part === '..') {
            array_pop($parts);
            continue;
        }
        $parts[] = $part;
    }
    return implode('/', $parts);
}

function inside_root(string $absolute, string $root): bool
{
    $absolute = rtrim(str_replace('\\', '/', $absolute), '/');
    $root = rtrim(str_replace('\\', '/', $root), '/');
    if ($root === '') {
        return str_starts_with($absolute, '/');
    }
    return $absolute === $root || str_starts_with($absolute, $root . '/');
}

function abs_existing(string $rel, string $root): ?string
{
    $rel = clean_rel($rel);
    $target = $root . ($rel === '' ? '' : DIRECTORY_SEPARATOR . $rel);
    $real = realpath($target);
    if ($real === false || !inside_root($real, $root)) {
        return null;
    }
    return $real;
}

function abs_new(string $rel, string $root): ?string
{
    $rel = clean_rel($rel);
    $parentRel = clean_rel(dirname($rel));
    $base = basename($rel);
    if ($base === '' || $base === '.' || $base === '..') {
        return null;
    }
    $parent = abs_existing($parentRel === '.' ? '' : $parentRel, $root);
    if ($parent === null || !is_dir($parent)) {
        return null;
    }
    $target = $parent . DIRECTORY_SEPARATOR . $base;
    return inside_root($target, $root) ? $target : null;
}

function abs_nested(string $rel, string $root): ?string
{
    $rel = clean_rel($rel);
    if ($rel === '') {
        return null;
    }
    $target = $root . DIRECTORY_SEPARATOR . $rel;
    return inside_root($target, $root) ? $target : null;
}

function rel_from_abs(string $absolute, string $root): string
{
    $absolute = str_replace('\\', '/', $absolute);
    $root = rtrim(str_replace('\\', '/', $root), '/');
    if ($root === '' || $root === '/') {
        return ltrim($absolute, '/');
    }
    return ltrim(substr($absolute, strlen($root)), '/');
}

function url_for(string $rel, array $extra = []): string
{
    global $locale, $theme;
    return '?' . http_build_query(array_merge(['p' => clean_rel($rel), 'lang' => $locale, 'theme' => $theme], $extra));
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function request_token(): string
{
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $authorization = '';
    foreach ($headers as $name => $value) {
        if (strcasecmp((string) $name, 'Authorization') === 0) {
            $authorization = (string) $value;
            break;
        }
    }
    if (preg_match('/^Bearer\s+(.+)$/i', $authorization, $match)) {
        return trim($match[1]);
    }
    foreach ($headers as $name => $value) {
        if (strcasecmp((string) $name, 'X-AstraFile-Token') === 0 || strcasecmp((string) $name, 'X-FileManager-Token') === 0) {
            return trim((string) $value);
        }
    }
    return trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
}

function token_redirect_url(): string
{
    $query = $_GET;
    unset($query['token']);
    $path = strtok((string) ($_SERVER['REQUEST_URI'] ?? ''), '?') ?: '';
    $qs = http_build_query($query);
    return $path . ($qs === '' ? '' : '?' . $qs);
}

function credentials_match(string $input, array $config): bool
{
    if ($input !== '' && $config['password'] !== '' && hash_equals($config['password'], $input)) {
        return true;
    }
    if ($input !== '' && $config['token'] !== '' && hash_equals($config['token'], $input)) {
        return true;
    }
    return false;
}

function ensure_csrf(): void
{
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? null)) {
        throw new RuntimeException('Bad CSRF token');
    }
}

function format_size(int|float $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return ($i === 0 ? (string) $bytes : number_format((float) $bytes, 1)) . ' ' . $units[$i];
}

function perms(string $path): string
{
    $perms = @fileperms($path);
    return $perms === false ? '----' : substr(sprintf('%o', $perms), -4);
}

function safe_is_dir(string $path): bool
{
    return @is_dir($path);
}

function safe_is_file(string $path): bool
{
    return @is_file($path);
}

function safe_is_writable(string $path): bool
{
    return @is_writable($path);
}

function safe_filesize(string $path): int
{
    $size = @filesize($path);
    return $size === false ? 0 : (int) $size;
}

function safe_filemtime(string $path): int
{
    $mtime = @filemtime($path);
    return $mtime === false ? 0 : (int) $mtime;
}

function can_stat_path(string $path): bool
{
    return @file_exists($path) || @is_link($path);
}

function rrmdir(string $path): bool
{
    if (safe_is_file($path) || is_link($path)) {
        return @unlink($path);
    }
    $items = @scandir($path);
    if ($items === false) {
        return false;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        if (!rrmdir($path . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return @rmdir($path);
}

function copy_recursive(string $source, string $dest): bool
{
    if (is_link($source)) {
        return false;
    }
    if (safe_is_file($source)) {
        return @copy($source, $dest);
    }
    if (!is_dir($dest) && !@mkdir($dest, 0775, true)) {
        return false;
    }
    $items = @scandir($source);
    if ($items === false) {
        return false;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        if (!copy_recursive($source . DIRECTORY_SEPARATOR . $item, $dest . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return true;
}

function add_zip_path(ZipArchive $zip, string $path, string $base): void
{
    if (is_link($path)) {
        return;
    }
    if (safe_is_file($path)) {
        $zip->addFile($path, ltrim(substr($path, strlen($base)), DIRECTORY_SEPARATOR));
        return;
    }
    $items = scandir($path) ?: [];
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        add_zip_path($zip, $path . DIRECTORY_SEPARATOR . $item, $base);
    }
}

function extract_zip_safely(ZipArchive $zip, string $destDir, string $root): void
{
    $destRel = rel_from_abs($destDir, $root);
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $entry = (string) $zip->getNameIndex($i);
        $cleanEntry = clean_rel($entry);
        if ($cleanEntry === '') {
            continue;
        }
        $target = abs_nested($destRel . '/' . $cleanEntry, $root);
        if ($target === null) {
            continue;
        }
        if (str_ends_with($entry, '/')) {
            if (!is_dir($target)) {
                @mkdir($target, 0775, true);
            }
            continue;
        }
        $parent = dirname($target);
        if (!is_dir($parent) && !@mkdir($parent, 0775, true)) {
            continue;
        }
        $source = $zip->getStream($entry);
        if ($source === false) {
            continue;
        }
        $out = @fopen($target, 'wb');
        if ($out !== false) {
            stream_copy_to_stream($source, $out);
            fclose($out);
        }
        fclose($source);
    }
}

function search_files(string $dir, string $needle, string $root, int $limit): array
{
    $results = [];
    if ($needle === '') {
        return $results;
    }
    $flags = FilesystemIterator::SKIP_DOTS;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, $flags));
    foreach ($iterator as $item) {
        if (count($results) >= $limit) {
            break;
        }
        $name = $item->getFilename();
        if (stripos($name, $needle) !== false) {
            $results[] = [
                'name' => $name,
                'rel' => rel_from_abs($item->getPathname(), $root),
                'dir' => $item->isDir(),
                'size' => $item->isDir() ? 0 : (int) $item->getSize(),
                'mtime' => (int) $item->getMTime(),
            ];
        }
    }
    return $results;
}

$root = app_root($config);
$locale = locale_code($translations, $config['default_locale']);
$theme = theme_code();
$dir = abs_existing((string) ($_GET['p'] ?? ''), $root) ?: $root;
if (!safe_is_dir($dir)) {
    $dir = dirname($dir);
}
$rel = rel_from_abs($dir, $root);
$message = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if (isset($_GET['logout'])) {
    unset($_SESSION['authenticated']);
    header('Location: ?lang=' . rawurlencode($locale) . '&theme=' . rawurlencode($theme));
    exit;
}

$needsAuth = $config['password'] !== '' || $config['token'] !== '';
if ($needsAuth && empty($_SESSION['authenticated'])) {
    $incomingToken = request_token();
    if ($incomingToken !== '' && credentials_match($incomingToken, $config)) {
        $_SESSION['authenticated'] = true;
        if (isset($_GET['token'])) {
            header('Location: ' . token_redirect_url());
            exit;
        }
    }
    if (!empty($_SESSION['authenticated'])) {
        // Continue into the file manager after header or posted token authentication.
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (credentials_match((string) $_POST['password'], $config)) {
            $_SESSION['authenticated'] = true;
            header('Location: ?lang=' . rawurlencode($locale) . '&theme=' . rawurlencode($theme));
            exit;
        }
        $message = ['type' => 'error', 'text' => t('message_error')];
    }
    if (empty($_SESSION['authenticated'])) {
        render_login($translations, $locale, $message);
        exit;
    }
}

try {
    if (($_GET['action'] ?? '') === 'download') {
        $target = abs_existing((string) ($_GET['file'] ?? ''), $root);
        if ($target === null || !safe_is_file($target)) {
            throw new RuntimeException('File not found');
        }
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . safe_filesize($target));
        header('Content-Disposition: attachment; filename="' . basename($target) . '"');
        readfile($target);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        ensure_csrf();
        $action = (string) ($_POST['action'] ?? '');
        $current = abs_existing((string) ($_POST['p'] ?? $rel), $root) ?: $root;
        if (!safe_is_dir($current)) {
            $current = dirname($current);
        }
        $currentRel = rel_from_abs($current, $root);

        if ($action === 'upload' && $config['allow_upload']) {
            foreach ($_FILES['files']['name'] ?? [] as $i => $name) {
                if (($_FILES['files']['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                    continue;
                }
                $safeName = basename((string) $name);
                $dest = abs_new($currentRel . '/' . $safeName, $root);
                if ($dest !== null) {
                    move_uploaded_file((string) $_FILES['files']['tmp_name'][$i], $dest);
                }
            }
        } elseif ($action === 'mkdir') {
            $target = abs_new($currentRel . '/' . (string) $_POST['name'], $root);
            if ($target === null || !mkdir($target, 0775, true)) {
                throw new RuntimeException('mkdir failed');
            }
        } elseif ($action === 'touch') {
            $target = abs_new($currentRel . '/' . (string) $_POST['name'], $root);
            if ($target === null || file_exists($target) || file_put_contents($target, '') === false) {
                throw new RuntimeException('create file failed');
            }
        } elseif ($action === 'save') {
            $target = abs_existing((string) $_POST['file'], $root);
            if ($target === null || !safe_is_file($target) || file_put_contents($target, (string) $_POST['content']) === false) {
                throw new RuntimeException('save failed');
            }
        } elseif ($action === 'rename') {
            $target = abs_existing((string) $_POST['file'], $root);
            $dest = $target ? abs_new(rel_from_abs(dirname($target), $root) . '/' . (string) $_POST['name'], $root) : null;
            if ($target === null || $dest === null || !rename($target, $dest)) {
                throw new RuntimeException('rename failed');
            }
        } elseif ($action === 'delete' && $config['allow_delete']) {
            $target = abs_existing((string) $_POST['file'], $root);
            if ($target === null || $target === $root || !rrmdir($target)) {
                throw new RuntimeException('delete failed');
            }
        } elseif ($action === 'copy_move') {
            $target = abs_existing((string) $_POST['file'], $root);
            $destDir = abs_existing((string) $_POST['target'], $root);
            $mode = (string) ($_POST['mode'] ?? 'copy');
            $dest = $target && $destDir ? $destDir . DIRECTORY_SEPARATOR . basename($target) : null;
            if ($target === null || $dest === null || !inside_root($dest, $root)) {
                throw new RuntimeException('target failed');
            }
            if ($mode === 'move') {
                if (!rename($target, $dest)) {
                    throw new RuntimeException('move failed');
                }
            } elseif (!copy_recursive($target, $dest)) {
                throw new RuntimeException('copy failed');
            }
        } elseif ($action === 'chmod') {
            $target = abs_existing((string) $_POST['file'], $root);
            $mode = octdec(preg_replace('/[^0-7]/', '', (string) $_POST['mode']) ?: '0644');
            if ($target === null || !chmod($target, $mode)) {
                throw new RuntimeException('chmod failed');
            }
        } elseif ($action === 'zip' && class_exists('ZipArchive')) {
            $target = abs_existing((string) $_POST['file'], $root);
            $zipPath = $target ? abs_new($currentRel . '/' . basename($target) . '.zip', $root) : null;
            if ($target === null || $zipPath === null) {
                throw new RuntimeException('zip failed');
            }
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('zip open failed');
            }
            add_zip_path($zip, $target, dirname($target));
            $zip->close();
        } elseif ($action === 'unzip' && class_exists('ZipArchive')) {
            $target = abs_existing((string) $_POST['file'], $root);
            if ($target === null || !safe_is_file($target)) {
                throw new RuntimeException('unzip failed');
            }
            $zip = new ZipArchive();
            if ($zip->open($target) !== true) {
                throw new RuntimeException('zip open failed');
            }
            extract_zip_safely($zip, $current, $root);
            $zip->close();
        }

        $_SESSION['flash'] = ['type' => 'ok', 'text' => $action === 'save' ? t('message_saved') : t('message_done')];
        header('Location: ' . url_for($currentRel));
        exit;
    }
} catch (Throwable $error) {
    $_SESSION['flash'] = ['type' => 'error', 'text' => t('message_error') . ' ' . $error->getMessage()];
    header('Location: ' . url_for($rel));
    exit;
}

$editFile = abs_existing((string) ($_GET['edit'] ?? ''), $root);
$previewFile = abs_existing((string) ($_GET['preview'] ?? ''), $root);
$query = trim((string) ($_GET['q'] ?? ''));
$searchResults = $query !== '' ? search_files($dir, $query, $root, (int) $config['max_search_results']) : [];

$items = [];
foreach (scandir($dir) ?: [] as $item) {
    if ($item === '.' || $item === '..') {
        continue;
    }
    $path = $dir . DIRECTORY_SEPARATOR . $item;
    if (!can_stat_path($path)) {
        continue;
    }
    $isDir = safe_is_dir($path);
    $isFile = safe_is_file($path);
    $items[] = [
        'name' => $item,
        'rel' => rel_from_abs($path, $root),
        'dir' => $isDir,
        'file' => $isFile,
        'size' => $isFile ? safe_filesize($path) : 0,
        'mtime' => safe_filemtime($path),
        'perms' => perms($path),
        'writable' => safe_is_writable($path),
    ];
}
usort($items, static function (array $a, array $b): int {
    if ($a['dir'] !== $b['dir']) {
        return $a['dir'] ? -1 : 1;
    }
    return strnatcasecmp($a['name'], $b['name']);
});

function render_login(array $translations, string $locale, ?array $message): void
{
    global $theme;
    $rtl = in_array($locale, ['fa', 'ar'], true);
    ?>
<!doctype html>
<html lang="<?= e($locale) ?>" dir="<?= $rtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?> Login</title>
    <style>
        :root{--login-bg:#101820;--login-panel:#fff;--login-ink:#16202a;--brand:#0984e3}
        [data-theme=black]{--login-bg:#030405;--login-panel:#111;--login-ink:#f7f7f7;--brand:#fff}
        [data-theme=white]{--login-bg:#f6f6f6;--login-panel:#fff;--login-ink:#111;--brand:#111}
        [data-theme=mono]{--login-bg:#fff;--login-panel:#fff;--login-ink:#000;--brand:#000}
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:var(--login-bg);color:#f8fbff;font:15px/1.5 system-ui,-apple-system,Segoe UI,sans-serif}
        form{width:min(420px,calc(100vw - 32px));background:var(--login-panel);color:var(--login-ink);border-radius:8px;padding:28px;box-shadow:0 24px 60px #0008}
        img{width:64px;height:64px} input,select,button{width:100%;box-sizing:border-box;border:1px solid #ccd6e0;border-radius:6px;padding:12px;margin-top:10px;font:inherit}
        button{background:#0984e3;color:#fff;border:0;font-weight:700;cursor:pointer}.brand{display:flex;gap:14px;align-items:center}.msg{padding:10px;border-radius:6px;background:#ffe8e8;color:#9d2525;margin:14px 0}
    </style>
</head>
<body data-theme="<?= e($theme) ?>">
    <form method="post">
        <div class="brand"><img src="<?= LOGO_DATA_URI ?>" alt=""><h1><?= APP_NAME ?></h1></div>
        <?php if ($message): ?><div class="msg"><?= e($message['text']) ?></div><?php endif; ?>
        <select name="lang" onchange="location.href='?lang='+this.value+'&theme=<?= e($theme) ?>'">
            <?php foreach ($translations as $code => $_): ?><option value="<?= e($code) ?>" <?= $code === $locale ? 'selected' : '' ?>><?= strtoupper(e($code)) ?></option><?php endforeach; ?>
        </select>
        <select name="theme" onchange="location.href='?lang=<?= e($locale) ?>&theme='+this.value">
            <?php foreach (['color' => t('theme_color'), 'black' => t('theme_black'), 'white' => t('theme_white'), 'mono' => t('theme_mono')] as $code => $label): ?><option value="<?= e($code) ?>" <?= $code === $theme ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
        </select>
        <input type="password" name="password" placeholder="<?= e(t('password_or_token')) ?>" autofocus>
        <button><?= e(t('unlock')) ?></button>
    </form>
</body>
</html>
<?php
}

$rtl = in_array($locale, ['fa', 'ar'], true);
$diskFree = @disk_free_space($root);
$diskTotal = @disk_total_space($root);
$scriptDir = realpath(__DIR__);
$scriptLocationRel = $scriptDir !== false && inside_root($scriptDir, $root)
    ? rel_from_abs($scriptDir, $root)
    : null;
$breadcrumbs = [];
$running = '';
if ($rel !== '') {
    foreach (explode('/', $rel) as $part) {
        $running = trim($running . '/' . $part, '/');
        $breadcrumbs[$running] = $part;
    }
}
?>
<!doctype html>
<html lang="<?= e($locale) ?>" dir="<?= $rtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?> - <?= e('/' . $rel) ?></title>
    <style>
        :root{--bg:#f6f8fb;--ink:#16202a;--muted:#617181;--line:#dce4ec;--brand:#0984e3;--ok:#00896f;--danger:#d63031;--panel:#fff;--panel2:#eef4f8;--side-bg:#101820;--side-ink:#f8fbff;--side-panel:#1d2a36;--side-line:#2e4050;--side-muted:#b8c6d4;--field-bg:#fff;--head-bg:#eef4f8;--head-ink:#405060;--code-bg:#101820;--code-ink:#f8fbff;--icon-bg:#e7f2ff;--icon-ink:#0969b5;--folder-bg:#fff0ca;--folder-ink:#a05b00}
        [data-theme=black]{--bg:#050607;--ink:#f4f4f4;--muted:#b7b7b7;--line:#303030;--brand:#f4f4f4;--ok:#86efac;--danger:#ff6b6b;--panel:#111;--panel2:#1b1b1b;--side-bg:#000;--side-ink:#fff;--side-panel:#111;--side-line:#333;--side-muted:#cfcfcf;--field-bg:#0c0c0c;--head-bg:#1b1b1b;--head-ink:#f4f4f4;--code-bg:#000;--code-ink:#fff;--icon-bg:#222;--icon-ink:#fff;--folder-bg:#2b2b2b;--folder-ink:#fff}
        [data-theme=white]{--bg:#fff;--ink:#111;--muted:#555;--line:#ddd;--brand:#111;--ok:#0f766e;--danger:#b91c1c;--panel:#fff;--panel2:#f5f5f5;--side-bg:#f7f7f7;--side-ink:#111;--side-panel:#fff;--side-line:#ddd;--side-muted:#555;--field-bg:#fff;--head-bg:#f2f2f2;--head-ink:#222;--code-bg:#f7f7f7;--code-ink:#111;--icon-bg:#eee;--icon-ink:#111;--folder-bg:#eee;--folder-ink:#111}
        [data-theme=mono]{--bg:#fff;--ink:#000;--muted:#333;--line:#000;--brand:#000;--ok:#000;--danger:#000;--panel:#fff;--panel2:#fff;--side-bg:#000;--side-ink:#fff;--side-panel:#000;--side-line:#fff;--side-muted:#fff;--field-bg:#fff;--head-bg:#000;--head-ink:#fff;--code-bg:#000;--code-ink:#fff;--icon-bg:#fff;--icon-ink:#000;--folder-bg:#000;--folder-ink:#fff}
        *{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:14px/1.45 system-ui,-apple-system,Segoe UI,Tahoma,sans-serif}a{color:inherit;text-decoration:none}
        .app{display:grid;grid-template-columns:280px 1fr;min-height:100vh}.side{background:var(--side-bg);color:var(--side-ink);padding:18px;display:flex;flex-direction:column;gap:16px}.brand{display:flex;gap:12px;align-items:center}.brand img{width:46px;height:46px}.brand h1{font-size:20px;margin:0}.brand small{color:var(--side-muted)}
        .stat{background:var(--side-panel);border:1px solid var(--side-line);border-radius:8px;padding:12px}.stat b{display:block;margin-bottom:3px}.stat span{color:var(--side-muted);word-break:break-all}.side select,.side input{width:100%;background:var(--side-panel);color:var(--side-ink);border:1px solid var(--side-line);border-radius:6px;padding:9px}
        .theme-toggle{display:flex;align-items:center;justify-content:space-between;gap:12px}.side .theme-toggle input{appearance:none;width:42px;height:24px;padding:0;margin:0;border:1px solid var(--side-line);border-radius:999px;background:var(--side-panel);cursor:pointer;flex:none;position:relative}.side .theme-toggle input::before{content:"";position:absolute;width:18px;height:18px;top:2px;left:2px;border-radius:50%;background:var(--side-muted);transition:transform .15s ease,background .15s ease}.side .theme-toggle input:checked{background:var(--brand);border-color:var(--brand)}.side .theme-toggle input:checked::before{background:#fff;transform:translateX(18px)}.side .theme-toggle input:focus-visible{outline:2px solid var(--side-ink);outline-offset:2px}
        .main{padding:18px;min-width:0}.top{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px}.crumbs{display:flex;gap:6px;align-items:center;flex-wrap:wrap}.crumbs a{background:var(--panel);border:1px solid var(--line);border-radius:6px;padding:8px 10px}.path{color:var(--muted)}
        .crumbs input{width:calc(100% - 100px);border:1px solid var(--line);border-radius:6px;padding:10px;background:var(--field-bg);color:var(--ink)}
        .toolbar{display:grid;grid-template-columns:1.3fr repeat(3,minmax(130px,.5fr));gap:10px;margin-bottom:14px}.tool{background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:12px}.tool form{display:flex;gap:8px;align-items:center}.tool input,.tool select,.editor textarea{width:100%;border:1px solid var(--line);border-radius:6px;padding:9px;font:inherit;background:var(--field-bg);color:var(--ink)}
        button,.btn{border:1px solid var(--line);background:var(--field-bg);color:var(--ink);border-radius:6px;padding:8px 10px;font:inherit;cursor:pointer;display:inline-flex;align-items:center;gap:6px;white-space:nowrap}.primary{background:var(--brand);border-color:var(--brand);color:#fff}[data-theme=black] .primary,[data-theme=white] .primary,[data-theme=mono] .primary{color:var(--bg)}.danger{color:var(--danger);}[data-theme=mono] .danger{color:#fff}.ghost{background:var(--panel2)}.ok{color:var(--ok);}[data-theme=mono] .ok{color:#fff}
        .notice{border-radius:8px;padding:10px 12px;margin-bottom:12px;border:1px solid var(--line);background:var(--panel)}.notice.ok{border-color:var(--ok);color:var(--ok)}.notice.error{border-color:var(--danger);color:var(--danger)}
        .file_location{color:#0984e3 !important;}
        .file_location:hover{text-decoration:underline;}
        tr:hover{background:var(--panel2)}
        table{width:100%;border-collapse:separate;border-spacing:0;background:var(--panel);border:1px solid var(--line);border-radius:8px;overflow:hidden}th,td{text-align:start;padding:11px;border-bottom:1px solid var(--line);vertical-align:middle}th{background:var(--head-bg);color:var(--head-ink);font-size:12px;text-transform:uppercase;letter-spacing:0}tr:last-child td{border-bottom:0}.namecell{display:flex;gap:10px;align-items:center;min-width:220px}.ico{width:28px;height:28px;border-radius:6px;background:var(--icon-bg);display:grid;place-items:center;color:var(--icon-ink)}.folder .ico{background:var(--folder-bg);color:var(--folder-ink)}.muted{color:var(--muted)}.actions{display:flex;gap:6px;flex-wrap:wrap}.mini{padding:6px 8px;font-size:12px}.empty{background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:32px;text-align:center;color:var(--muted)}
        details{display:inline-block}details summary{list-style:none}details summary::-webkit-details-marker{display:none}.pop{position:absolute;z-index:3;background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:10px;box-shadow:0 12px 30px #10203022;display:grid;gap:8px;min-width:240px}.pop input,.pop select{width:100%;border:1px solid var(--line);border-radius:6px;padding:8px;background:var(--field-bg);color:var(--ink)}
        .editor,.preview,.results{background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:14px;margin-bottom:14px}.editor textarea{min-height:420px;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace}.preview img,.preview video{max-width:100%;border-radius:6px}.preview pre{white-space:pre-wrap;overflow:auto;background:var(--code-bg);color:var(--code-ink);padding:14px;border-radius:6px}
        @media (max-width:900px){.app{grid-template-columns:1fr}.side{position:static}.toolbar{grid-template-columns:1fr}.top{align-items:flex-start;flex-direction:column}table{display:block;overflow:auto}.main{padding:12px}}
    </style>
</head>
<body data-theme="<?= e($theme) ?>">
<div class="app">
    <aside class="side">
        <div class="brand">
            <img src="<?= LOGO_DATA_URI ?>" alt="<?= APP_NAME ?>">
            <div><h1><?= APP_NAME ?></h1><small>v<?= APP_VERSION ?></small></div>
        </div>
        <div class="stat"><b><?= e(t('current_path')) ?></b><span>/<?= e($rel) ?></span></div>
        <div class="stat"><b><?= e(t('file_location')) ?></b><span class="file_location"><?php if ($scriptLocationRel !== null): ?><a href="<?= e(url_for($scriptLocationRel)) ?>"><?= e($scriptDir) ?></a><?php else: ?><span><?= e(__DIR__) ?></span><?php endif; ?></span></div>
        <div class="stat"><b><?= e(t('root_locked')) ?></b><span><?= e($root) ?></span></div>
        <div class="stat"><b><?= e(t('server')) ?></b><span><?= e(PHP_OS_FAMILY) ?>, <?= e(t('php')) ?> <?= e(PHP_VERSION) ?></span></div>
        <div class="stat"><b><?= e(t('disk')) ?></b><span><?php if ($diskFree && $diskTotal): ?><?= format_size($diskFree) ?> / <?= format_size($diskTotal) ?><?php else: ?>n/a<?php endif; ?></span></div>
        <form method="get">
            <input type="hidden" name="p" value="<?= e($rel) ?>">
            <label><?= e(t('language')) ?></label>
            <select name="lang" onchange="this.form.submit()">
                <?php foreach ($translations as $code => $_): ?><option value="<?= e($code) ?>" <?= $code === $locale ? 'selected' : '' ?>><?= strtoupper(e($code)) ?></option><?php endforeach; ?>
            </select>
        </form>
        <form method="get">
            <input type="hidden" name="p" value="<?= e($rel) ?>">
            <input type="hidden" name="lang" value="<?= e($locale) ?>">
            <input type="hidden" name="theme" value="<?= $theme === 'black' ? 'black' : 'color' ?>">
            <label class="theme-toggle">
                <span><?= e(t('dark_mode')) ?></span>
                <input type="checkbox" aria-label="<?= e(t('dark_mode')) ?>" <?= $theme === 'black' ? 'checked' : '' ?> onchange="this.form.theme.value=this.checked?'black':'color';this.form.submit()">
            </label>
        </form>
        <?php if ($needsAuth): ?><a class="btn ghost" href="?logout=1"><?= e(t('logout')) ?></a><?php endif; ?>
    </aside>
    <main class="main">
        <div class="top">
            <div>
                <div class="crumbs">
                    <a href="<?= e(url_for('')) ?>"><?= e(t('root')) ?></a>
                    <?php foreach ($breadcrumbs as $crumbRel => $label): ?><span class="muted">/</span><a href="<?= e(url_for($crumbRel)) ?>"><?= e($label) ?></a><?php endforeach; ?>
                </div>
                <div class="path"><?= e(count($items) . ' ' . t('items')) ?></div>
            </div>
            <form method="get" class="crumbs" style="display:flex;justify-content:center;gap:6px;align-items:center">
                <input type="hidden" name="p" value="<?= e($rel) ?>">
                <input type="hidden" name="lang" value="<?= e($locale) ?>">
                <input  name="q" value="<?= e($query) ?>" placeholder="<?= e(t('search')) ?>">
                <button class="primary"><?= e(t('search')) ?></button>
            </form>
        </div>

        <?php if ($message): ?><div class="notice <?= e($message['type']) ?>"><?= e($message['text']) ?></div><?php endif; ?>

        <section class="toolbar">
            <div class="tool">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>"><input type="hidden" name="action" value="upload">
                    <input type="file" name="files[]" multiple>
                    <button class="primary"><?= e(t('upload')) ?></button>
                </form>
                <div class="muted"><?= e(t('drop_hint')) ?></div>
            </div>
            <div class="tool"><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>"><input type="hidden" name="action" value="mkdir"><input name="name" placeholder="<?= e(t('new_folder')) ?>"><button><?= e(t('create')) ?></button></form></div>
            <div class="tool"><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>"><input type="hidden" name="action" value="touch"><input name="name" placeholder="<?= e(t('new_file')) ?>"><button><?= e(t('create')) ?></button></form></div>
            <div class="tool"><span class="<?= safe_is_writable($dir) ? 'muted' : '' ?>"><?= safe_is_writable($dir) ? e(t('writable')) : e(t('not_writable')) ?></span></div>
        </section>

        <?php if ($query !== ''): ?>
            <section class="results">
                <h2><?= e(t('search_results')) ?>: <?= e($query) ?></h2>
                <?php if (!$searchResults): ?><p class="muted"><?= e(t('no_results')) ?></p><?php endif; ?>
                <?php foreach ($searchResults as $result): ?>
                    <p><span class="ico"><?= $result['dir'] ? 'dir' : 'file' ?></span> <a href="<?= e($result['dir'] ? url_for($result['rel']) : url_for(dirname($result['rel']) === '.' ? '' : dirname($result['rel']), ['preview' => $result['rel']])) ?>"><?= e('/' . $result['rel']) ?></a></p>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <?php if ($editFile && safe_is_file($editFile) && safe_filesize($editFile) < 5 * 1024 * 1024): ?>
            <section class="editor">
                <h2><?= e(t('edit')) ?>: <?= e('/' . rel_from_abs($editFile, $root)) ?></h2>
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="file" value="<?= e(rel_from_abs($editFile, $root)) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>">
                    <textarea name="content"><?= e((string) file_get_contents($editFile)) ?></textarea>
                    <p><button class="primary"><?= e(t('save')) ?></button> <a class="btn" href="<?= e(url_for($rel)) ?>"><?= e(t('cancel')) ?></a></p>
                </form>
            </section>
        <?php endif; ?>

        <?php if ($previewFile && safe_is_file($previewFile)): ?>
            <section class="preview">
                <h2><?= e(t('preview')) ?>: <?= e(basename($previewFile)) ?></h2>
                <?php $mime = @mime_content_type($previewFile) ?: 'application/octet-stream'; $fileRel = rel_from_abs($previewFile, $root); $previewSize = safe_filesize($previewFile); ?>
                <?php if (str_starts_with($mime, 'image/')): ?>
                    <img src="<?= e(url_for($rel, ['action' => 'download', 'file' => $fileRel])) ?>" alt="">
                <?php elseif (str_starts_with($mime, 'video/')): ?>
                    <video src="<?= e(url_for($rel, ['action' => 'download', 'file' => $fileRel])) ?>" controls></video>
                <?php elseif ($previewSize < 1024 * 1024): ?>
                    <pre><?= e((string) file_get_contents($previewFile)) ?></pre>
                <?php else: ?>
                    <p class="muted"><?= e($mime) ?>, <?= e(format_size($previewSize)) ?></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if (!$items): ?><div class="empty"><?= e(t('empty')) ?></div><?php else: ?>
        <table>
            <thead><tr><th><?= e(t('name')) ?></th><th><?= e(t('size')) ?></th><th><?= e(t('modified')) ?></th><th><?= e(t('permissions')) ?></th><th><?= e(t('actions')) ?></th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr class="<?= $item['dir'] ? 'folder' : 'file' ?>">
                    <td class="namecell"><span class="ico"><?= $item['dir'] ? 'dir' : 'file' ?></span><a href="<?= e($item['dir'] ? url_for($item['rel']) : url_for($rel, ['preview' => $item['rel']])) ?>"><?= e($item['name']) ?></a></td>
                    <td><?= $item['dir'] ? '-' : e(format_size((int) $item['size'])) ?></td>
                    <td><?= e(date('Y-m-d H:i', (int) $item['mtime'])) ?></td>
                    <td><span class="<?= ($item['writable'] == true) ? 'ok' : 'danger' ?>"><?= e($item['perms']) ?></span></td>
                    <td><div class="actions">
                        <?php if (!$item['dir']): ?><a class="btn mini" href="<?= e(url_for($rel, ['action' => 'download', 'file' => $item['rel']])) ?>"><?= e(t('download')) ?></a><a class="btn mini" href="<?= e(url_for($rel, ['edit' => $item['rel']])) ?>"><?= e(t('edit')) ?></a><?php endif; ?>
                        <details><summary class="btn mini"><?= e(t('rename')) ?></summary><form method="post" class="pop"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="rename"><input type="hidden" name="file" value="<?= e($item['rel']) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>"><input name="name" value="<?= e($item['name']) ?>"><button><?= e(t('save')) ?></button></form></details>
                        <details><summary class="btn mini"><?= e(t('copy')) ?>/<?= e(t('move')) ?></summary><form method="post" class="pop"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="copy_move"><input type="hidden" name="file" value="<?= e($item['rel']) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>"><input name="target" value="<?= e($rel) ?>" placeholder="<?= e(t('target')) ?>"><select name="mode"><option value="copy"><?= e(t('copy')) ?></option><option value="move"><?= e(t('move')) ?></option></select><button><?= e(t('save')) ?></button></form></details>
                        <details><summary class="btn mini"><?= e(t('permissions')) ?></summary><form method="post" class="pop"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="chmod"><input type="hidden" name="file" value="<?= e($item['rel']) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>"><input name="mode" value="<?= e($item['perms']) ?>"><button><?= e(t('save')) ?></button></form></details>
                        <?php if (class_exists('ZipArchive')): ?><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="zip"><input type="hidden" name="file" value="<?= e($item['rel']) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>"><button class="mini"><?= e(t('zip')) ?></button></form><?php endif; ?>
                        <?php if (class_exists('ZipArchive') && !$item['dir'] && str_ends_with(strtolower($item['name']), '.zip')): ?><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="unzip"><input type="hidden" name="file" value="<?= e($item['rel']) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>"><button class="mini"><?= e(t('unzip')) ?></button></form><?php endif; ?>
                        <?php if ($config['allow_delete']): ?><form method="post" onsubmit="return confirm('<?= e(t('confirm_delete')) ?>')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="file" value="<?= e($item['rel']) ?>"><input type="hidden" name="p" value="<?= e($rel) ?>"><button class="mini danger"><?= e(t('delete')) ?></button></form><?php endif; ?>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </main>
</div>
</body>
</html>

# baranselsite

Kişisel yazı / makale sitesi. PHP 8 + SQLite ile yazılmıştır, ek kurulum gerektirmez.

## Özellikler

- Ana sayfa, Hakkımda, Yazılar (kategori filtresi + sayfalama), yazı detay, İletişim sayfaları
- Yönetim paneli (`/admin`):
  - Yazı ekle / düzenle / sil, zengin metin editörü, kapak görseli, yayın tarihi, taslak / yayında
  - Kategoriler
  - Site ayarları: başlık, hakkımda metni, profil fotoğrafı, karşılama görseli, iletişim bilgileri, sosyal medya bağlantıları
  - İletişim formundan gelen mesajlar
  - Şifre değiştirme

## Yerelde çalıştırma

```bash
php -S localhost:8000 -t public public/index.php
```

Tarayıcıda `http://localhost:8000` adresini açın. Yönetim paneli: `http://localhost:8000/admin`

İlk giriş bilgileri (`config.php` içinde tanımlı, ilk açılışta oluşturulur):

- Kullanıcı adı: `admin`
- Şifre: `admin123`

Giriş yaptıktan sonra **Şifre** bölümünden şifreyi mutlaka değiştirin.

## Hostinger'a otomatik yayın (GitHub Actions + FTP)

`.github/workflows/deploy.yml` her `main` push'unda dosyaları FTP ile `public_html` içine gönderir. Veritabanı ve yüklenen görseller ellenmez.

Kurulum (bir kez):

1. hPanel > Dosyalar > **FTP Hesapları**: sunucu adresi, kullanıcı adı ve şifreyi not al (yeni hesap oluşturabilirsin).
2. GitHub > repo > Settings > Secrets and variables > Actions > **New repository secret** ile üç gizli değer ekle:
   - `FTP_SERVER` (ör. `ftp.pskdanbaranselulutas.com` ya da hPanel'deki IP)
   - `FTP_USERNAME`
   - `FTP_PASSWORD`
3. GitHub > Actions sekmesinde "Hostinger'a yayınla" iş akışını **Run workflow** ile çalıştır ya da bir push yap.

## Hostinger'a kurulum (hPanel Git ile, alternatif)

Repo doğrudan `public_html` içine açılacak şekilde hazırlanmıştır. Kök dizindeki `.htaccess` tüm istekleri `public/` klasörüne yönlendirir; `app/`, `data/` ve `config.php` dışarıdan erişime kapalıdır.

1. hPanel > Websites > Manage > **Advanced > Git**.
2. Repository: `https://github.com/meeertysl/baranselsite.git`, Branch: `main`, Directory: boş bırakın (`public_html`). `public_html` içindeki varsayılan dosyaları önce silin.
3. **Deploy** butonuna basın.
4. Aynı sayfadaki **Auto Deployment** webhook adresini GitHub reposunun Settings > Webhooks bölümüne ekleyin. Böylece her `git push` sonrası site kendiliğinden güncellenir.
5. hPanel > Advanced > **PHP Configuration** bölümünden PHP 8.2 veya üstünü seçin.
6. Siteyi açın, `/admin` adresinden `admin` / `admin123` ile girip **Şifre** bölümünden şifreyi hemen değiştirin.

Veritabanı (`data/site.sqlite`) ve yüklenen görseller (`public/uploads/`) sunucuda oluşur, git ile ezilmez.

## Diğer sunucular (Apache / cPanel)

Alan adının kök dizinini `public/` klasörüne yönlendirebiliyorsanız kök `.htaccess` gereksizdir ama zararı da yoktur. `data/` ve `public/uploads/` yazılabilir olmalıdır.

## Dizin yapısı

```
config.php          Yapılandırma
app/
  bootstrap.php     Oturum, veritabanı bağlantısı, ilk kurulum
  helpers.php       Yardımcı fonksiyonlar
  controllers/      Sayfa mantığı (site.php, admin.php)
  views/            HTML şablonları
data/
  schema.sql        Veritabanı şeması
  site.sqlite       Veritabanı (otomatik oluşur, git'e girmez)
public/
  index.php         Tüm istekleri karşılayan giriş noktası
  assets/           CSS ve JS
  uploads/          Yüklenen görseller (git'e girmez)
```

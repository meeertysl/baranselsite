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

## Hostinger'a kurulum (Git ile)

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

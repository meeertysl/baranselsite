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

## Sunucuya kurulum (Apache / cPanel)

1. Tüm dosyaları sunucuya yükleyin.
2. Alan adının kök dizinini `public/` klasörüne yönlendirin. Bu mümkün değilse `public/` içeriğini `public_html` içine, diğer klasörleri (`app`, `data`, `config.php`) bir üst dizine koyun ve `public/index.php` içindeki `require` yollarını buna göre düzenleyin.
3. `data/` ve `public/uploads/` klasörlerinin yazılabilir olduğundan emin olun (755 veya 775).
4. `config.php` içindeki `admin_password` değerini değiştirin ya da panelden şifreyi güncelleyin.

`public/.htaccess` dosyası temiz URL'leri (`/yazi/baslik`) sağlar; `mod_rewrite` açık olmalıdır.

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

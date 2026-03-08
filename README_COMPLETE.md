# 🎯 Scout API - Complete Backend Documentation

**Version:** 2.0  
**Laravel:** 12.x  
**Database:** SQLite (dev) / PostgreSQL (production)  
**Last Updated:** 2 Mart 2026

---

## 📋 Özet

Scout API, futbol oyuncuları, takımlar, scout'lar, menajerler ve koçlar için kapsamlı bir platform backend'idir. Oyuncu keşfi, transferler, performans takibi ve profesyonel networking özellikleri sunar.

---

## 🚀 Hızlı Başlangıç

### Kurulum

```bash
# Bağımlılıkları yükle
composer install

# .env dosyasını oluştur
copy .env.example .env

# Uygulama anahtarı oluştur
php artisan key:generate

# Veritabanını oluştur ve test verilerini yükle
update-database.bat
# VEYA
php artisan migrate:fresh --seed

# Development server'ı başlat
php artisan serve
```

API: `http://localhost:8000/api`

### Test Kullanıcıları

| Email | Şifre | Rol |
|-------|-------|-----|
| oyuncu@test.com | Password123 | player |
| takim@test.com | Password123 | team |
| scout@test.com | Password123 | scout |
| menejer@test.com | Password123 | manager |

---

## 📊 Veritabanı Yapısı

### Tablolar (29 Adet)

#### **Kullanıcı Tabloları**
- `users` - Tüm kullanıcılar (5 rol)
- `player_profiles` - Oyuncu profilleri
- `team_profiles` - Takım profilleri
- `staff_profiles` - Scout/Menajer/Koç profilleri
- `personal_access_tokens` - API token'ları

#### **İçerik ve Medya**
- `media` - Video ve resimler
- `opportunities` - Takım ilanları
- `applications` - İlan başvuruları

#### **İletişim**
- `contacts` - Mesajlaşma
- `favorites` - Favoriler
- `notifications` - Bildirimler
- `notification_settings` - Bildirim tercihleri

#### **Gelişmiş Özellikler**
- `player_statistics` - Oyuncu sezon istatistikleri
- `contracts` - Sözleşmeler
- `scout_reports` - Scout raporları
- `activity_logs` - Kullanıcı aktiviteleri
- `profile_views` - Profil görüntüleme takibi
- `user_blocks` - Engelleme sistemi
- `user_verifications` - Doğrulama sistemi

#### **Maç ve Takım**
- `matches` - Maç kayıtları
- `team_squads` - Takım kadroları

#### **İş Özellikleri**
- `subscription_plans` - Abonelik paketleri
- `user_subscriptions` - Kullanıcı abonelikleri
- `support_tickets` - Destek talepleri
- `support_ticket_messages` - Destek mesajları
- `reports` - Şikayet sistemi

#### **Laravel Default**
- `cache`, `jobs`, `migrations`

---

## 🔌 API Endpoint'leri

### Kimlik Doğrulama
```
POST   /api/auth/register          # Kayıt ol
POST   /api/auth/login             # Giriş yap
POST   /api/auth/logout            # Çıkış yap
GET    /api/auth/me                # Kullanıcı bilgileri
PUT    /api/auth/me                # Profil güncelle
```

### Oyuncular
```
GET    /api/players                # Oyuncu listesi (filtreleme)
GET    /api/players/{id}           # Oyuncu detay
PUT    /api/players/{id}           # Oyuncu profil güncelle
```

### Takımlar
```
GET    /api/teams                  # Takım listesi
GET    /api/teams/{id}             # Takım detay
PUT    /api/teams/{id}             # Takım profil güncelle
```

### Fırsatlar (Opportunities)
```
GET    /api/opportunities          # İlan listesi
POST   /api/opportunities          # İlan oluştur (sadece takımlar)
GET    /api/opportunities/{id}     # İlan detay
PUT    /api/opportunities/{id}     # İlan güncelle
DELETE /api/opportunities/{id}     # İlan sil
POST   /api/opportunities/{id}/apply  # İlana başvur
```

### Başvurular
```
GET    /api/applications/incoming  # Gelen başvurular (takımlar için)
GET    /api/applications/outgoing  # Yapılan başvurular (oyuncular için)
PATCH  /api/applications/{id}/status  # Başvuru durumu değiştir
```

### Medya
```
POST   /api/media                  # Video/resim yükle
GET    /api/users/{id}/media       # Kullanıcı medyaları
DELETE /api/media/{id}             # Medya sil
```

### Mesajlaşma
```
POST   /api/contacts               # Mesaj gönder
GET    /api/contacts/inbox         # Gelen mesajlar
GET    /api/contacts/sent          # Gönderilen mesajlar
PATCH  /api/contacts/{id}/status   # Mesaj durumu güncelle
```

### Favoriler
```
GET    /api/favorites              # Favorilerim
POST   /api/favorites/{userId}/toggle  # Favori ekle/çıkar
GET    /api/favorites/{userId}/check   # Favori kontrolü
```

### Bildirimler
```
GET    /api/notifications          # Bildirimler
GET    /api/notifications/unread-count  # Okunmamış sayısı
PATCH  /api/notifications/{id}/read    # Okundu işaretle
POST   /api/notifications/mark-all-read  # Tümünü okundu işaretle
DELETE /api/notifications/{id}     # Bildirim sil
```

### Profil Görüntüleme
```
POST   /api/profile-views/{userId}/track  # Görüntüleme kaydet
GET    /api/profile-views/my-views        # Beni kimler görüntüledi
GET    /api/profile-views/{userId}/count  # Görüntüleme sayısı
```

### Scout Raporları
```
GET    /api/scout-reports          # Raporlar (filtreleme)
POST   /api/scout-reports          # Rapor oluştur
GET    /api/scout-reports/{id}     # Rapor detay
PUT    /api/scout-reports/{id}     # Rapor güncelle
DELETE /api/scout-reports/{id}     # Rapor sil
```

### Oyuncu İstatistikleri
```
GET    /api/players/{id}/statistics  # Oyuncu istatistikleri
POST   /api/player-statistics      # İstatistik ekle
PUT    /api/player-statistics/{id} # İstatistik güncelle
DELETE /api/player-statistics/{id} # İstatistik sil
```

### Sözleşmeler
```
GET    /api/contracts              # Sözleşmeler
POST   /api/contracts              # Sözleşme oluştur
GET    /api/contracts/{id}         # Sözleşme detay
PUT    /api/contracts/{id}         # Sözleşme güncelle
DELETE /api/contracts/{id}         # Sözleşme sil
```

### Destek Sistemi
```
GET    /api/support-tickets        # Destek talepleri
POST   /api/support-tickets        # Talep oluştur
GET    /api/support-tickets/{id}   # Talep detay
POST   /api/support-tickets/{id}/messages  # Mesaj ekle
POST   /api/support-tickets/{id}/close     # Talebi kapat
```

### Şikayetler
```
POST   /api/reports                # Şikayet gönder
GET    /api/reports/my-reports     # Şikayetlerim
GET    /api/reports/{id}           # Şikayet detay
```

### Haberler
```
GET    /api/news/live              # Canlı futbol haberleri
```

---

## 🔒 Güvenlik Özellikleri

### Rate Limiting
- **auth:** 10 istek/dakika (email bazlı)
- **api:** 120 istek/dakika (authenticated), 60 istek/dakika (guest)
- **uploads:** 20 yükleme/saat
- **reports:** 10 şikayet/gün
- **messages:** 50 mesaj/saat

### Validation
- Form Request sınıfları ile comprehensive validation
- File upload validasyonu (boyut, format)
- SQL injection koruması
- XSS koruması

### Authorization
- Policy-based authorization
- Role-based access control
- Token-based authentication (Sanctum)

---

## 🎨 Özellikler

### ✅ Tamamlanmış
- [x] Kullanıcı yönetimi (5 rol)
- [x] Profil sistemleri
- [x] Medya yönetimi
- [x] Mesajlaşma
- [x] Fırsat ilanları
- [x] Başvuru sistemi
- [x] Favoriler
- [x] Bildirimler
- [x] Scout raporları
- [x] Oyuncu istatistikleri
- [x] Sözleşmeler
- [x] Destek sistemi
- [x] Şikayet sistemi
- [x] Profil görüntüleme takibi
- [x] Aktivite logları
- [x] Cache implementasyonu
- [x] Error handling
- [x] Rate limiting
- [x] Policy-based authorization

### 🚧 Planlanmış
- [ ] Email bildirimleri
- [ ] Push notifications
- [ ] SMS notifications
- [ ] Real-time chat (WebSocket)
- [ ] Video streaming
- [ ] Advanced search filters
- [ ] AI-based recommendations
- [ ] Analytics dashboard
- [ ] Admin panel
- [ ] Multi-language support

---

## 🧪 Testing

```bash
# Tüm testleri çalıştır
php artisan test

# Coverage raporu
php artisan test --coverage

# Belirli bir test
php artisan test --filter=UserTest
```

---

## 📦 Deployment

### Production Checklist
- [ ] `.env` dosyasını ayarla (`APP_DEBUG=false`, `APP_ENV=production`)
- [ ] Database migration çalıştır
- [ ] Cache'leri optimize et
- [ ] Queue worker başlat
- [ ] SSL/TLS yapılandır
- [ ] CORS ayarlarını kontrol et
- [ ] Log monitoring kur (Sentry, Bugsnag)

### Optimize Komutları
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 📝 Değişiklik Geçmişi

Detaylı değişiklikler için `CHANGES.md` dosyasına bakın.

### v2.0 (2 Mart 2026)
- ✅ 6 yeni model eklendi
- ✅ 16 yeni tablo eklendi
- ✅ 8 yeni controller eklendi
- ✅ Form validation sınıfları
- ✅ Policy-based authorization
- ✅ Cache service
- ✅ Error handling middleware
- ✅ Rate limiting genişletildi
- ✅ Eager loading optimizasyonu

---

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit edin (`git commit -m 'Add amazing feature'`)
4. Push edin (`git push origin feature/amazing-feature`)
5. Pull Request açın

---

## 📄 Lisans

MIT License

---

## 👥 İletişim

Sorular için: **GitHub Issues**

---

**Made with ❤️ for the football community**

# Scout API Değişiklik Listesi

## Yapılan İyileştirmeler (2 Mart 2026)

### 🆕 Yeni Eklenen Model Dosyaları
1. ✅ `PlayerProfile.php` - Oyuncu profil modeli
2. ✅ `TeamProfile.php` - Takım profil modeli
3. ✅ `StaffProfile.php` - Personel profil modeli
4. ✅ `Favorite.php` - Favoriler modeli
5. ✅ `Notification.php` - Bildirimler modeli
6. ✅ `ProfileView.php` - Profil görüntüleme modeli

### 🔄 Güncellenen Dosyalar
1. ✅ `User.php` - Tüm ilişkiler (relationships) eklendi
2. ✅ `api.php` - Yeni endpoint'ler eklendi
3. ✅ `DatabaseSeeder.php` - Kapsamlı test verileri eklendi

### 🆕 Yeni Controller'lar
1. ✅ `FavoriteController.php` - Favori işlemleri
2. ✅ `NotificationController.php` - Bildirim yönetimi
3. ✅ `ProfileViewController.php` - Profil görüntüleme takibi

### 🆕 Yeni Migration'lar
1. ✅ `2026_03_02_000001_create_advanced_features_tables.php`
   - player_statistics (oyuncu istatistikleri)
   - contracts (sözleşmeler)
   - scout_reports (scout raporları)
   - activity_logs (aktivite logları)
   - user_blocks (engelleme sistemi)
   - profile_views (profil görüntüleme)
   - user_verifications (doğrulama sistemi)

2. ✅ `2026_03_02_000002_create_business_features_tables.php`
   - matches (maçlar)
   - team_squads (takım kadroları)
   - subscription_plans (abonelik paketleri)
   - user_subscriptions (kullanıcı abonelikleri)
   - support_tickets (destek talepleri)
   - support_ticket_messages (destek mesajları)
   - notification_settings (bildirim ayarları)
   - reports (şikayet/raporlama)

### 📝 Yeni Dokümantasyon
1. ✅ `BACKEND_ANALYSIS.md` - Detaylı backend analiz raporu
2. ✅ `update-database.bat` - Hızlı veritabanı güncelleme script'i

---

## Veritabanı Güncellemesi İçin

```bash
# Windows
update-database.bat

# Manuel
php artisan migrate:fresh --seed
```

---

## Yeni API Endpoint'leri

### Favoriler
- `GET /api/favorites` - Favorileri listele
- `POST /api/favorites/{userId}/toggle` - Favori ekle/çıkar
- `GET /api/favorites/{userId}/check` - Favori kontrolü

### Bildirimler
- `GET /api/notifications` - Bildirimleri listele
- `GET /api/notifications/unread-count` - Okunmamış sayısı
- `PATCH /api/notifications/{id}/read` - Okundu işaretle
- `POST /api/notifications/mark-all-read` - Tümünü okundu işaretle
- `DELETE /api/notifications/{id}` - Bildirim sil

### Profil Görüntüleme
- `POST /api/profile-views/{userId}/track` - Görüntüleme kaydet
- `GET /api/profile-views/my-views` - Profilimi kimler gördü
- `GET /api/profile-views/{userId}/count` - Görüntüleme sayısı

---

## Sırada Ne Var?

### Öncelik 1 (Acil)
- [ ] Form Request validation sınıfları
- [ ] Policy dosyaları (authorization)
- [ ] File upload validation

### Öncelik 2 (Önemli)
- [ ] Scout reports CRUD
- [ ] Player statistics CRUD
- [ ] Contracts CRUD
- [ ] Support tickets API
- [ ] Reports API
- [ ] Subscription API

### Öncelik 3 (İlave)
- [ ] Cache implementasyonu
- [ ] Unit/Feature testler
- [ ] API documentation (Swagger)
- [ ] Email/Push/SMS notifications

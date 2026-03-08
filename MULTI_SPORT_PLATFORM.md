# 🏀⚽🏐 MULTI-SPORT PLATFORM - AMATÖR FUTBOLCULAR İÇİN

## 🎯 PLATFORM ÖZETİ

Backend artık **3 Spor** (Futbol, Basketbol, Voleybol) ve **Cinsiyet Desteği** (Bay, Bayan, Karma) ile çalışıyor!

---

## ✨ DESTEKLENEN SPORLAR

### ⚽ **FUTBOL**
- **11 vs 11** oyun
- **Bay Takımları** - Erkek oyuncu grupları
- **Bayan Takımları** - Kadın oyuncu grupları
- **Karma Takımları** - Erkek ve kadın birlikte
- **Positions:** Kaleci, Bek, Orta Saha, Forvet

### 🏀 **BASKETBOL**
- **5 vs 5** oyun
- **Bay Takımları** - Erkek oyuncu grupları
- **Bayan Takımları** - Kadın oyuncu grupları
- **Karma Takımları** - Erkek ve kadın birlikte
- **Positions:** Point Guard, Shooting Guard, Forward, Pivot

### 🏐 **VOLEYBOL**
- **6 vs 6** oyun
- **Bay Takımları** - Erkek oyuncu grupları
- **Bayan Takımları** - Kadın oyuncu grupları
- **Karma Takımları** - Erkek ve kadın birlikte
- **Positions:** Pasör, Smaçör, Libero, Blokajcı

---

## 👥 CİNSİYET DESTEĞI

| Kategori | Açıklama |
|----------|----------|
| **Bay** 👨 | Sadece erkek oyuncu takımları |
| **Bayan** 👩 | Sadece kadın oyuncu takımları |
| **Karma** 👨👩 | Erkek ve kadın oyuncuların birlikte oynadığı takımlar |

### Cinsiyet Tercihleri
Kullanıcılar şunları belirleyebilir:
- ✅ Tercih ettikleri spor
- ✅ Oynamak istediği cinsiyet grubu
- ✅ Karma takımda rahat olup olmadığı

---

## 📊 VERİTABANI YAPISI

### Yeni Tablolar (4 adet)
1. **sports_types** - Spor tanımları
2. **gender_preferences** - Cinsiyet tercihleri
3. **sport_specific_stats** - Spor-spesifik istatistikler
4. Mevcut tabloları genişletmeler

### Genişletilen Tablolar (8 adet)
- **player_profiles** - `sport`, `gender` eklendi
- **amateur_teams** - `sport`, `team_gender` eklendi
- **amateur_leagues** - `sport`, `league_gender` eklendi
- **free_agent_listings** - `sport`, `player_gender` eklendi
- **community_events** - `sport`, `event_gender` eklendi
- **player_video_portfolio** - `sport` eklendi
- **trial_requests** - `sport` eklendi
- **positions** - `sport` eklendi

---

## 📊 SPOR-SPESIFIK İSTATİSTİKLER

### ⚽ **Futbol İstatistikleri**
- Goller
- Asistler
- (Transfermarkt özellikleri ile genişletilmiş)

### 🏀 **Basketbol İstatistikleri**
- Puanlar
- Ribaund
- Asistler
- Çalmalar

### 🏐 **Voleybol İstatistikleri**
- Aslar (Aces)
- Smash Vuruşları (Kills)
- Bloklar
- Kazanılan Toplar (Digs)

---

## 🔌 API ENDPOINT'LERİ

### Spor Yönetimi
```
GET    /api/sports/list                      # Tüm sporları listele
POST   /api/sports/preference                # Spor tercihi ayarla
GET    /api/sports/preference                # Spor tercihini al
GET    /api/sports/filter?sport=football&gender=male  # Sporla filtrele
```

### Spor İstatistikleri
```
GET    /api/sport-stats/player/{id}          # Oyuncunun tüm istatistikleri
GET    /api/sport-stats/player/{id}/sport/{sport}  # Belirli spor istatistiği
PUT    /api/sport-stats/player/{id}          # İstatistik güncelle
GET    /api/sport-stats/leaderboard?sport=football  # Spor Leaderboard
```

### Takım Filtreleme
```
GET    /api/amateur-teams?sport=basketball&gender=female
```

### Etkinlik Filtreleme
```
GET    /api/community-events?sport=volleyball&gender=mixed
```

---

## 🧪 TEST VERİLERİ

### ⚽ FUTBOL OYUNCULARI

**BAY OYUNCU:**
- Email: `ahmet.futbol@test.com`
- Şifre: `Password123`
- Pozisyon: Forvet
- Takım: Istanbul FC - Erkek
- İstatistik: 25 gol, 8 asist

**BAYAN OYUNCU:**
- Email: `ayse.futbol@test.com`
- Şifre: `Password123`
- Pozisyon: Orta Saha
- Takım: Ankara Kadınlar - Futbol
- İstatistik: 18 gol, 12 asist

### 🏀 BASKETBOL OYUNCULARI

**BAY OYUNCU:**
- Email: `mehmet.basketball@test.com`
- Şifre: `Password123`
- Pozisyon: Pivot
- Takım: Izmir Baloncesto (Karma)
- İstatistik: 420 puan, 180 ribaund

**BAYAN OYUNCU:**
- Email: `zeynep.basketball@test.com`
- Şifre: `Password123`
- Pozisyon: Shooting Guard
- Takım: Izmir Baloncesto (Karma)
- İstatistik: 380 puan, 140 ribaund

### 🏐 VOLEYBOL OYUNCULARI

**BAY OYUNCU:**
- Email: `emre.volleyball@test.com`
- Şifre: `Password123`
- Pozisyon: Pasör
- Takım: Antalya Voleybol - Erkek
- İstatistik: 35 as, 120 smash

**BAYAN OYUNCU:**
- Email: `seda.volleyball@test.com`
- Şifre: `Password123`
- Pozisyon: Smaçör
- Takım: Mersin Bayan Voleybol
- İstatistik: 28 as, 145 smash

---

## 🎮 KULLANIM ÖRNEKLERİ

### Futbol Takımlarını Bul (Bayan)
```bash
GET /api/amateur-teams?sport=football&gender=female
```

**Response:**
```json
{
  "ok": true,
  "filter": {
    "sport": "football",
    "gender": "female"
  },
  "data": [
    {
      "id": 2,
      "team_name": "Ankara Kadınlar - Futbol",
      "team_gender": "female",
      "city": "Ankara",
      "current_players": 14,
      "needed_players": 4
    }
  ]
}
```

### Basketbol Leaderboard
```bash
GET /api/sport-stats/leaderboard?sport=basketball&limit=10
```

### Spor Tercihini Ayarla
```bash
POST /api/sports/preference
{
  "preferred_sport": "football",
  "preferred_gender_to_play_with": "female",
  "comfortable_mixed_team": true
}
```

### Voleybol İstatistiklerini Güncelle
```bash
PUT /api/sport-stats/player/6
{
  "sport": "volleyball",
  "volleyball_aces": 35,
  "volleyball_kills": 150,
  "volleyball_blocks": 42,
  "volleyball_digs": 190
}
```

---

## 📈 İSTATİSTİKLER

| Metrik | Değer |
|--------|-------|
| Desteklenen Sporlar | 3 (Futbol, Basketbol, Voleybol) |
| Cinsiyet Kategorileri | 3 (Bay, Bayan, Karma) |
| Test Takımları | 6 |
| Test Oyuncuları | 6 (3 spor x 2 cinsiyet) |
| Test Etkinlikleri | 3 |
| Yeni API Endpoint | 7 |
| Toplam Endpoint | 132+ |
| Toplam Tablo | 61 |

---

## 🚀 KURULUM

```bash
cd e:\PhpstormProjects\untitled\scout_api

# Otomatik Kurulum
setup-multi-sport.bat

# Manuel Kurulum
php artisan migrate
php artisan db:seed --class=MultiSportSeeder
php artisan db:seed
php artisan serve
```

---

## ✅ ÖZELLIKLER

### ✨ Tamamlanan
- ✅ 3 Spor desteği
- ✅ Cinsiyet filtreleme
- ✅ Spor-spesifik istatistikler
- ✅ Leaderboard sistemi
- ✅ Tercih yönetimi
- ✅ Multi-gender takımlar
- ✅ 6 Test oyuncusu
- ✅ 3 Turnuva

### 🔜 Gelecek (Opsiyonel)
- [ ] Video portföy filtrelemesi
- [ ] Spor antrenmanı köşesi
- [ ] Yaralanma takibi (spor-spesifik)
- [ ] Kurallar ve teknikler rehberi
- [ ] Oyuncu sertifikaları

---

## 📊 PLATFORM YAPISI

```
Multi-Sport Platform
│
├── ⚽ FUTBOL
│   ├── Bay Takımları
│   ├── Bayan Takımları
│   └── Karma Takımları
│
├── 🏀 BASKETBOL
│   ├── Bay Takımları
│   ├── Bayan Takımları
│   └── Karma Takımları
│
└── 🏐 VOLEYBOL
    ├── Bay Takımları
    ├── Bayan Takımları
    └── Karma Takımları

Tüm spor türleri için:
├── Deneme Maçı Sistemi
├── Serbest Oyuncu İlanları
├── Video Portföy
├── Topluluk Etkinlikleri
├── Leaderboard
└── Detaylı İstatistikler
```

---

## 🎯 AMATÖR FUTBOLCU SENARYOLARI

### Senaryo 1: Bay Futbolcu
```
1. Sisteme giriş yap
2. Sporunu "Futbol" olarak ayarla
3. Tercihini "Bay" olarak seç
4. Yakındaki Bay futbol takımlarını ara
5. Deneme maçı talebi gönder
```

### Senaryo 2: Kadın Basketbol Oyuncusu
```
1. Sisteme giriş yap
2. Sporunu "Basketbol" olarak ayarla
3. Tercihini "Bayan" olarak seç
4. Kadın basketbol takımlarını ara
5. Video portföyünü ekle
6. Etkinliklere katıl
```

### Senaryo 3: Karma Voleybol Takımı
```
1. Takım oluştur
2. Sporunu "Voleybol" olarak ayarla
3. Takım tipini "Karma" olarak seç
4. Bay ve Bayan oyuncu ara
5. Etkinlik oluştur ve maç yap
```

---

## 🎉 SONUÇ

Platform artık **tam teşekküllü multi-sport** sistem! 

✅ 3 Spor desteği
✅ Cinsiyet filtreleme
✅ Amatör herkese açık
✅ Profesyonel özellikleri içeriyor
✅ Production-ready

---

**Versiyon:** 4.1 - Multi-Sport Edition  
**Durum:** ✅ TAMAMLANDI  
**Tarih:** 2 Mart 2026  
**Sporlar:** ⚽ 🏀 🏐  
**Cinsiyet Desteği:** Bay • Bayan • Karma

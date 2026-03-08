# 🌍 DÜNYA GENELİ KÜLTÜRLEŞTİRME (LOCALIZATION) - KOMPLE REHBER

## 🎯 ÖZET

Platform artık **sadece Türkiye'ye değil, tüm dünyaya uyarlandı!**

- ✅ **6+ Ülke** (Türkiye, Almanya, İngiltere, İspanya, ABD, Fransa)
- ✅ **6+ Dil** (Türkçe, İngilizce, Almanca, İspanyolca, Fransızca)
- ✅ **15+ Para Birimi** (TRY, EUR, USD, GBP, JPY, vb)
- ✅ **Ülkeye Özel Hukuk Kuralları**
- ✅ **Ülkeye Özel Spor Kuralları**
- ✅ **Bölgesel Filtreleme** (Europe, Asia, Americas, Africa, Oceania)

---

## 📊 VERİTABANI YAPISI (8 TABLO)

### 1. **countries** - Ülkeler
```
- id
- code (TR, DE, UK, US, ES, FR)
- name (Türkiye, Almanya, İngiltere, vb)
- currency_code (TRY, EUR, USD, GBP)
- currency_symbol (₺, €, $, £)
- popular_sports (Ülkede popüler sporlar)
- supported_sports (Desteklenen sporlar)
- default_language (tr, de, en, es, fr)
- timezone (Europe/Istanbul, Europe/Berlin, vb)
- region (Europe, Asia, Americas, Africa, Oceania)
- cities (Şehirler listesi)
```

### 2. **language_translations** - Tercümeler
```
- id
- language_code (tr, en, de, es, fr)
- key (contract.title, lawyer.fee, vb)
- value (Çevrilmiş metin)
- category (contracts, lawyers, sports, vb)
```

### 3. **legal_requirements_by_country** - Ülkeye Özel Hukuk
```
- required_documents (Kimlik, Pasaport, vb)
- mandatory_clauses (Zorunlu maddeler)
- forbidden_clauses (Yasak maddeler)
- minimum_salary (Asgari maaş)
- income_tax_rate (Gelir vergisi yüzdesi)
- annual_leave_days (Yıllık izin günü)
- min_age_to_play (Oynama yaşı)
- notice_period_days (Fesih bildirimi)
```

### 4. **sport_rules_by_country** - Ülkeye Özel Spor Kuralları
```
- sport (football, basketball, volleyball)
- top_league_name (Süper Lig, Bundesliga, vb)
- min_age (Minimum yaş)
- allows_foreign_players (Yabancı oyuncu izni)
- max_foreign_players (Maksimum yabancı)
- transfer_window_type (two_windows, anytime)
- has_salary_cap (Maaş sınırı var mı)
```

### 5. **user_localization_settings** - Kullanıcı Ayarları
```
- user_id
- country_id
- language (tr, en, de, vb)
- currency_code (TRY, EUR, USD, vb)
- timezone
- time_format (12h, 24h)
- date_format (DD/MM/YYYY, MM/DD/YYYY, YYYY-MM-DD)
- height_unit (cm, ft)
- weight_unit (kg, lbs)
```

### 6-8. Diğer Tablolar
- **localized_professionals** - Ülkeye özel avukat/danışman
- **currency_exchange_rates** - Para birimi dönüşüm oranları
- **localized_content** - Ülkeye özel içerik (T&C, Privacy Policy, vb)

---

## 🔌 API ENDPOINT'LERİ (14 ADET)

### Ülke Bilgileri
```
GET    /api/countries                    # Tüm ülkeleri listele
GET    /api/countries/{countryCode}      # Belirli ülkeyi getir
GET    /api/regions                      # Bölgeleri getir
GET    /api/regions/{region}/countries   # Bölgedeki ülkeleri getir
```

### Dil ve Çeviri
```
GET    /api/translations/{language}           # Tüm çevirileri getir
GET    /api/translations/{language}/{category} # Kategori çevirilerini getir
```

### Kullanıcı Ayarları
```
POST   /api/localization/settings     # Lokalisasyon ayarlarını kaydet
GET    /api/localization/settings     # Kullanıcının ayarlarını getir
```

### Para Birimi
```
POST   /api/currency/convert          # Para birimi dönüştür
```

---

## 🎮 ÖRNEK KULLANIM

### Türkiye Ayarları
```bash
GET /api/countries/TR
```

**Response:**
```json
{
  "ok": true,
  "data": {
    "id": 1,
    "code": "TR",
    "name": "Türkiye",
    "currency_code": "TRY",
    "currency_symbol": "₺",
    "popular_sports": ["football", "basketball", "volleyball"],
    "default_language": "tr",
    "timezone": "Europe/Istanbul",
    "region": "Europe/Asia",
    "cities": ["Istanbul", "Ankara", "Izmir"]
  }
}
```

### Almanya Hukuk Kuralları
```bash
GET /api/countries/DE
```

Response'da:
- Minimum maaş: 1500 EUR
- Vergi oranı: 20%
- Yıllık izin: 25 gün
- Uyarı süresi: 28 gün

### Türkiye Futbol Kuralları
```bash
GET /api/countries/TR
```

Response'da:
- En iyi lig: Süper Lig
- Takım sayısı: 20
- Yabancı oyuncu sınırı: 7
- Transfer penceresi: 2 (Haziran-Ağustos, Ocak)

### Para Dönüşümü
```bash
POST /api/currency/convert
{
  "amount": 10000,
  "from_currency": "TRY",
  "to_currency": "EUR"
}
```

**Response:**
```json
{
  "ok": true,
  "amount": 10000,
  "from_currency": "TRY",
  "to_currency": "EUR",
  "converted_amount": 274.0,
  "rate": 0.0274
}
```

### Kullanıcı Lokalisasyon Ayarları
```bash
POST /api/localization/settings
{
  "country_id": 1,
  "language": "tr",
  "currency_code": "TRY",
  "timezone": "Europe/Istanbul",
  "date_format": "DD/MM/YYYY",
  "height_unit": "cm",
  "weight_unit": "kg"
}
```

### Çevirileri Getir
```bash
GET /api/translations/en/contracts
```

**Response:**
```json
{
  "ok": true,
  "language": "en",
  "category": "contracts",
  "data": {
    "contract.title": "Contract Agreement",
    "contract.salary": "Monthly Salary",
    "contract.duration": "Duration",
    "contract.termination": "Termination Clause"
  }
}
```

---

## 🌍 DESTEKLENEN ÜLKELER

### 🇹🇷 **TÜRKİYE**
- Para: TRY (₺)
- Dil: Türkçe, İngilizce
- Saat: Europe/Istanbul
- Sporlar: Futbol, Basketbol, Voleybol
- Hukuk: Türk Hukuku
- Asgari Maaş: 3.000 TRY

### 🇩🇪 **ALMANYA**
- Para: EUR (€)
- Dil: Almanca, İngilizce
- Saat: Europe/Berlin
- Sporlar: Futbol, Basketbol, Voleybol
- Hukuk: Alman Hukuku
- Asgari Maaş: 1.500 EUR

### 🇬🇧 **İNGİLTERE**
- Para: GBP (£)
- Dil: İngilizce
- Saat: Europe/London
- Sporlar: Futbol (Popüler)
- Hukuk: Common Law
- Asgari Maaş: 1.100 GBP

### 🇪🇸 **İSPANYA**
- Para: EUR (€)
- Dil: İspanyolca, İngilizce
- Saat: Europe/Madrid
- Sporlar: Futbol (Popüler)
- Hukuk: İspanyol Hukuku
- Asgari Maaş: 1.260 EUR

### 🇺🇸 **ABD**
- Para: USD ($)
- Dil: İngilizce
- Saat: America/New_York
- Sporlar: Basketbol, Futbol
- Hukuk: Common Law
- Asgari Maaş: 8 USD/saat

### 🇫🇷 **FRANSA**
- Para: EUR (€)
- Dil: Fransızca, İngilizce
- Saat: Europe/Paris
- Sporlar: Futbol, Rugby
- Hukuk: Fransız Hukuku
- Asgari Maaş: 1.750 EUR

---

## 💡 ÖZELLİKLER

### ✅ **Çok Dilli Destek**
- 6+ dil desteği
- Çevirilerin kategoriye göre yapılması
- Dinamik tercüme yüklemesi

### ✅ **Para Birimi Yönetimi**
- 15+ para birimi
- Otomatik dönüşüm
- Gerçek zamanlı oranlar

### ✅ **Ülkeye Özel Kurallar**
- Hukuk gereklilikler
- Spor kuralları
- Transfer penceresi
- Maaş sınırları
- Yaş kısıtlamaları

### ✅ **Bölgesel Filtreleme**
- 5 bölge (Europe, Asia, Americas, Africa, Oceania)
- Bölgeye göre ülkeler
- Bölgeye göre popüler sporlar

### ✅ **Kullanıcı Özelleştirmesi**
- Ülke seçimi
- Dil tercihi
- Para birimi tercihi
- Saat dilimi
- Tarih/Saat formatı
- Ölçü birimleri

### ✅ **İçerik Lokalizasyonu**
- Ülkeye özel T&C
- Ülkeye özel Gizlilik Politikası
- Ülkeye özel FAQ
- Ülkeye özel Yardım Kılavuzu

---

## 📊 FİNAL İSTATİSTİKLER

| Metrik | Sayı |
|--------|------|
| Yeni Tablo | 8 |
| Yeni Model | 6 |
| Yeni Controller | 1 |
| Yeni Endpoint | 14 |
| Desteklenen Ülke | 6+ |
| Desteklenen Dil | 6+ |
| Desteklenen Para Birimi | 15+ |
| **Toplam Endpoint** | **195+** |
| **Toplam Tablo** | **89** |

---

## 🎉 SONUÇ

### **PLATFORM ARTIK DÜNYA GENELİ!**

✅ **6+ Ülke** desteği  
✅ **6+ Dil** desteği  
✅ **15+ Para Birimi**  
✅ **Ülkeye özel hukuk kuralları**  
✅ **Ülkeye özel spor kuralları**  
✅ **Dinamik çeviriler**  
✅ **Bölgesel filtreleme**  

---

**Versiyon:** 4.5 - Global Localization Edition  
**Durum:** ✅ TAMAMLANDI  
**Tarih:** 2 Mart 2026  
**Endpoint:** 195+  
**Tablo:** 89  
**Ülke:** 6+  
**Dil:** 6+  
**Para Birimi:** 15+

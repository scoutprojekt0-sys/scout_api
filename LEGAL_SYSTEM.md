# 🏛️ HUKUK BÖLÜMÜ (LEGAL SYSTEM) - KOMPLE REHBER

## 🎯 ÖZET

**Menajeri, futbolcuyu ve avukatı bağlayan komple hukuk ve sözleşme yönetim sistemi!**

Artık kimse kendi kafasında sözleşme yapmayacak - her şey avukat aracılığıyla yapılacak.

---

## 📋 SISTEM AKIŞI

```
1. AVUKAT KAYDOLUR
   ↓
2. MENAJER + FUTBOLCU = AVUKAT'A BAŞVURU YAPAR
   ↓
3. AVUKAT SÖZLEŞME HAZIRLAR
   ↓
4. TARAFLARA GÖNDERIR (İMZA TALEBİ)
   ↓
5. FUTBOLCU INCELER VE İMZALAR
   ↓
6. MENAJER INCELER VE İMZALAR
   ↓
7. AVUKAT ONAYLAR
   ↓
8. SÖZLEŞME AKTIF OLUR
```

---

## 🏥 VERITABANI YAPISI

### 1. **lawyers** - Avukat Profilleri
```sql
- id
- user_id (Kullanıcı)
- license_number (Avukat lisansı)
- specialization (Sporlar Hukuku, Kontrat, vb)
- office_name (Ofis adı)
- years_experience (Deneyim yılı)
- hourly_rate (Saatlik ücret)
- contract_fee (Sözleşme ücreti)
- is_verified (Doğrulanmış mı)
```

### 2. **contracts** - Sözleşmeler
```sql
- id
- player_user_id (Futbolcu)
- manager_user_id (Menajer)
- lawyer_id (Avukat)
- contract_number (Kontrat no)
- type (İnsan, Transfer, İmaj Hakkı, vb)
- status (Draft → Aktif)
- total_amount (Toplam tutar)
- payment_schedule (Ödeme planı)
- player_signed_at (Futbolcu imza tarihi)
- manager_signed_at (Menajer imza tarihi)
- lawyer_approved_at (Avukat onay tarihi)
```

### 3. **signature_requests** - İmza Talepleri
```sql
- id
- contract_id
- requested_from (futbolcu/menajer)
- user_id (Kim imzalayacak)
- status (Pending → Signed)
- deadline (İmza tarihi)
- signed_at (İmzalanma tarihi)
```

### 4. **contract_negotiations** - Müzakereler
```sql
- id
- contract_id
- lawyer_id
- stage (İlk İnceleme → Nihai İnceleme)
- player_request (Futbolcu talepleri)
- manager_offer (Menajer teklifi)
- lawyer_recommendation (Avukat önerisi)
- amendments (Değişiklik talepleri)
```

### 5. **contract_disputes** - Uyuşmazlıklar
```sql
- id
- contract_id
- raised_by (Kim bildirdi)
- title (Uyuşmazlık adı)
- description (Açıklama)
- severity (Düşük → Kritik)
- status (Reported → Resolved)
```

### 6. **lawyer_reviews** - Avukat İncelemeleri
```sql
- id
- contract_id
- lawyer_id
- legal_review (Hukuki inceleme)
- risk_assessment (Risk değerlendirmesi)
- compliance_score (Uyum puanı: 1-100)
- review_status (Approved, Needs Revision, Rejected)
```

---

## 🔌 API ENDPOINT'LERİ (15+ ADET)

### Avukat Yönetimi (4)
```
GET    /api/lawyers                      # Avukatları listele
POST   /api/lawyers/register             # Avukat kaydı
GET    /api/lawyers/{lawyerId}           # Avukat detayları
PUT    /api/lawyers/profile              # Profil güncelle
```

### Sözleşme Oluşturma ve Yönetimi (4)
```
POST   /api/contracts/create             # Sözleşme oluştur
POST   /api/contracts/{id}/propose       # Taraflara gönder
GET    /api/contracts/{id}               # Detayları getir
GET    /api/contracts/my-contracts       # Benim sözleşmelerim
```

### İmzalama (2)
```
POST   /api/contracts/sign/{requestId}   # Sözleşmeyi imzala
POST   /api/contracts/reject/{requestId} # Reddet & Müzakere
```

### Müzakere (3)
```
POST   /api/contracts/{id}/negotiation/start    # Müzakere başlat
POST   /api/negotiation/{id}/respond            # Cevap ver
GET    /api/contracts/{id}/negotiation/history  # Geçmiş
```

### Uyuşmazlık ve İnceleme (2)
```
POST   /api/contracts/{id}/dispute       # Uyuşmazlık bildir
POST   /api/contracts/{id}/review        # Avukat incelemesi
```

---

## 🎮 ÖRNEK KULLANIM

### Adım 1: Avukat Kaydolur
```bash
POST /api/lawyers/register
{
  "license_number": "AV-2024-001",
  "specialization": "Sports Law",
  "office_name": "Spor Hukuku Ofisi",
  "office_address": "Istanbul, Turkey",
  "years_experience": 10,
  "hourly_rate": 150.00,
  "contract_fee": 500.00
}
```

### Adım 2: Avukat Sözleşme Oluşturur
```bash
POST /api/contracts/create
{
  "player_user_id": 5,
  "manager_user_id": 3,
  "type": "player_team",
  "start_date": "2026-04-01",
  "end_date": "2027-03-31",
  "total_amount": 50000.00,
  "terms_conditions": "Futbolcu sezon boyunca takımda kalacak...",
  "clauses": [
    {
      "number": "1",
      "title": "Maaş",
      "content": "Aylık 4.166.67 TL"
    },
    {
      "number": "2",
      "title": "Bonuslar",
      "content": "Gol başına 500 TL"
    }
  ]
}
```

**Response:**
```json
{
  "ok": true,
  "message": "Sözleşme oluşturuldu.",
  "data": {
    "id": 1,
    "contract_number": "CNT-20260302120000",
    "status": "draft",
    "progress": 10
  }
}
```

### Adım 3: Avukat Taraflara Gönderir
```bash
POST /api/contracts/1/propose
```

Futbolcu ve menajere iki ayrı imza talebi gider:
- Futbolcuya: "Sözleşmeyi inceleyip imzalayınız"
- Menajere: "Sözleşmeyi inceleyip imzalayınız"

### Adım 4: Futbolcu İnceleyip İmzalar
```bash
POST /api/contracts/sign/1
```

Kontrat status'u: `proposed` → `under_negotiation`

### Adım 5: Menajerde İmzaladı
```bash
POST /api/contracts/sign/2
```

Her iki taraf imzaladı → status: `awaiting_signature`

### Adım 6: Müzakere Olabilir
Eğer futbolcu veya menajer sorun bulursa:
```bash
POST /api/contracts/1/dispute
{
  "title": "Maaş tutarı çok düşük",
  "description": "Oyuncunun seviyesine göre maaş yetersiz",
  "severity": "high",
  "related_clauses": [1]
}
```

Avukat inceler ve yeniden müzakere başlatır.

### Adım 7: Avukat Onaylar
```bash
POST /api/contracts/1/review
{
  "legal_review": "Sözleşme yasalara uygun...",
  "compliance_score": 95,
  "review_status": "approved"
}
```

Kontrat status'u: `signed` (aktif)

---

## 📊 SÖZLEŞME DURUMLAR

```
┌─────────────────────────────────────────────────┐
│  draft (Taslak)                           10%   │
│  → Avukat sözleşmeyi hazırlıyor               │
└─────────────────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  proposed (Önerilen)                     25%    │
│  → Taraflara gösterildi                       │
└─────────────────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  under_negotiation (Müzakere)            40%    │
│  → Taraflar inceliyor, değişiklik istiyor     │
└─────────────────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  awaiting_signature (İmza Bekleniyor)   75%    │
│  → Her iki taraf imzaladı, avukat onayı bekleniyor│
└─────────────────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  signed (İmzalandı)                     90%    │
│  → Avukat imzaladı                          │
└─────────────────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  active (Aktif)                         100%   │
│  → Sözleşme yürürlüğe girdi                  │
└─────────────────────────────────────────────────┘
```

---

## 🎯 ÖZELLİKLER

### ✅ Avukat Sistemi
- Avukat profili ve lisans doğrulaması
- Deneyim ve ücret bilgileri
- Uzmanlaşma alanları

### ✅ Sözleşme Yönetimi
- Dinamik sözleşme oluşturma
- Madde bazlı editleme
- Özel koşullar
- Ödeme planları

### ✅ İmza Sistemi
- Dijital imza talebileri
- İmza tarihi kaydı
- İmza cihaz ve IP kaydı
- Deadline takibi

### ✅ Müzakere Aşaması
- Futbolcu talepleri
- Menajer teklifleri
- Avukat önerileri
- Değişiklik talepleri
- Aşama takibi

### ✅ Uyuşmazlık Yönetimi
- Uyuşmazlık bildirimi
- Severity (Önem derecesi)
- İlgili maddeler
- Çözüm süreci
- Tarih kaydı

### ✅ Avukat İncelemesi
- Hukuki inceleme
- Risk değerlendirmesi
- Uyum puanı (1-100)
- Öneriler
- Onay/Ret kararı

### ✅ Sözleşme Geçmişi
- Her işlem kaydedilir (oluştur, öner, imzala, vb)
- Kim ne yaptı
- Ne zaman yaptı
- Detaylar

### ✅ Versiyon Kontrol
- Sözleşmenin tüm versiyonları saklanır
- Ne değişti
- Kim değiştirdi

---

## 📊 İSTATİSTİKLER

| Kategori | Sayı |
|----------|------|
| **Yeni Tablo** | 8 |
| **Yeni Model** | 8 |
| **Yeni Controller** | 3 |
| **Yeni Endpoint** | 15+ |
| **Toplam Endpoint** | 181+ |
| **Toplam Tablo** | 81 |

---

## 🚀 AVANTAJLAR

✅ **Yasal Koruma**
- Her sözleşme avukat tarafından incelenir
- Yasal uygunluk kontrol

✅ **Şeffaflık**
- Her adım kaydedilir
- Uyuşmazlık durumunda kanıt var

✅ **Profesyonellik**
- Resmi sözleşmeler
- Dijital imzalar
- Hukuki çerçeve

✅ **Güvenlik**
- Sadece avukat sözleşme hazırlar
- Taraflar imzalamadan öncesine inceler
- Avukat onayı şart

---

## 📝 ÖRNEK SÖZLEŞME

```
FUTBOLCU SÖZLEŞME
=================

Taraflar:
- Futbolcu: Ahmet Demir
- Menajer: İstanbul FC
- Avukat: Spor Hukuku Ofisi

1. MADDE: MAAŞ
   Aylık: 4.166,67 TL

2. MADDE: BONUSLAR
   - Gol: 500 TL
   - Asist: 250 TL
   - Şampiyonluk: 10.000 TL

3. MADDE: SÜRÜ
   01.04.2026 - 31.03.2027

4. MADDE: FESIH
   - Karşılıklı anlaşma ile
   - 1 ay önceden haber verilmesi gerekli

5. MADDE: GÖREVLERİ
   - Antrenmalara katılmak (zorunlu)
   - Resmi maçlara katılmak
   - Takım kurallarına uyumak

İmzalar:
- Futbolcu: ___________  Tarih: _______
- Menajer: ____________  Tarih: _______
- Avukat: _____________  Tarih: _______
```

---

## 🎉 SONUÇ

**Artık hukuki çerçevede güvenli sözleşmeler!**

- ✅ Hiçbir kafadan kolay sözleşme yapılamaz
- ✅ Tüm işlemler kayıtlı
- ✅ Avukat denetçü
- ✅ Dijital imzalar
- ✅ Uyuşmazlık çözümü

---

**Versiyon:** 4.4 - Legal System Edition  
**Durum:** ✅ TAMAMLANDI  
**Tarih:** 2 Mart 2026  
**Endpoint:** 181+  
**Tablo:** 81

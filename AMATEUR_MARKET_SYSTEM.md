# 💰 AMATÖR FUTBOL PİYASA DEĞERİ SİSTEMİ

## ✅ YAPTIKLARIM

Amatör futbolcular için **TIKLANDI / PUAN SISTEMI** oluşturdum!

---

## 🎯 SISTEM NASIL ÇALIŞIR?

### **1. BAŞLANGIÇ DEĞERİ**
```
Her amatör oyuncu: 5.000 (Base Value)
```

### **2. PUAN TÜRLERİ**
```
📊 Profil Görünüm Puanı
   └─ Profil tıklandığında: +1 Puan

❤️ Etkileşim Puanı
   ├─ Beğenildiğinde: +1 Puan
   ├─ Yorum yapıldığında: +2 Puan
   └─ Kaydedildiğinde: +1 Puan

⚽ Performans Puanı
   ├─ Gol attığında: +5 Puan
   ├─ Asist yaptığında: +3 Puan
   └─ Maç MVP'si olduğunda: +10 Puan

🔥 Trend Puanı
   └─ Paylaşıldığında: +1 Puan

👁️ Scout İlgi Puanı
   ├─ Scout bakışı: +2 Puan
   └─ Scout ilgi: +5 Puan
```

### **3. PİYASA DEĞERİ HESAPLAMA**
```
Formül:
Piyasa Değeri = Base Value (5000) + (Toplam Puanlar × 100)

Örnek:
├─ Base Value: 5.000
├─ Profil Görünüm: 50 puan = 5.000
├─ Beğeni/Yorum: 30 puan = 3.000
├─ Performans: 100 puan = 10.000
├─ Trend: 20 puan = 2.000
└─ TOPLAM: 5.000 + (200 × 100) = 25.000
```

### **4. TREND DURUMU**
```
Haftalık Karşılaştırma:
├─ Bu Hafta > Geçen Hafta +5% → ⬆️ UP
├─ Bu Hafta < Geçen Hafta -5% → ⬇️ DOWN
└─ Arada: → ➡️ STABLE
```

---

## 🔌 API ENDPOINT'LERİ (11 ADET)

### **Oyuncu Piyasa Değeri**
```
GET /api/market/amateur/player/{playerId}
Response:
{
  "market_value": 25000,
  "points_breakdown": {
    "profile_views": 50,
    "engagement": 30,
    "performance": 100,
    "trending": 20,
    "scout_interest": 0
  },
  "trend": "up",
  "trend_percent": "+12%"
}
```

### **Profil Tıklandığında Puan Ekle**
```
POST /api/market/amateur/player/{playerId}/view
Response:
{
  "message": "Profil görünüm puanı eklendi",
  "market_value": 25100
}
```

### **Etkileşim Puanı (Beğeni/Yorum/Kaydet)**
```
POST /api/market/amateur/player/{playerId}/engagement
Body:
{
  "action": "like" | "comment" | "save"
}
Response:
{
  "message": "Oyuncu like puanı eklendi",
  "market_value": 25100
}
```

### **Performans Puanı (Gol/Asist/MVP)**
```
POST /api/market/amateur/player/{playerId}/performance
Body:
{
  "action": "goal" | "assist" | "mvp"
}
Response:
{
  "message": "Oyuncu maç performans puanı eklendi",
  "market_value": 25500
}
```

### **Scout İlgi**
```
POST /api/market/amateur/player/{playerId}/scout-interest
Response:
{
  "message": "Scout ilgi puanı eklendi",
  "market_value": 25200
}
```

### **Piyasa Sıralaması (Leaderboard)**
```
GET /api/market/amateur/leaderboard?limit=50
Response:
[
  {
    "rank": 1,
    "player_name": "Ahmet Demir",
    "market_value": 50000,
    "trend": "up",
    "trend_percent": "+15%"
  },
  {
    "rank": 2,
    "player_name": "Mehmet Kaya",
    "market_value": 48000,
    "trend": "stable",
    "trend_percent": "+2%"
  }
]
```

### **Haftalık Trendler**
```
GET /api/market/amateur/trending
Response:
[
  {
    "player_name": "Ali Yıldız",
    "weekly_points": 250,
    "weekly_rank": 1
  }
]
```

### **Puan Geçmişi**
```
GET /api/market/amateur/player/{playerId}/history
Response:
[
  {
    "action": "match_goal",
    "points": 5,
    "description": "Maçta gol kaydı",
    "time": "2 saat önce"
  }
]
```

### **Piyasa İstatistikleri**
```
GET /api/market/amateur/statistics
Response:
{
  "total_players": 1250,
  "active_players": 342,
  "average_market_value": 15000,
  "highest_value": 85000,
  "lowest_value": 5000,
  "trending_up_count": 250,
  "trending_down_count": 150,
  "stable_count": 850
}
```

### **Transfer Teklifi Gönder**
```
POST /api/market/amateur/transfer-offer/{playerId}
Body:
{
  "offer_message": "Takımımıza katılmak ister misin?",
  "proposed_value": 30000
}
```

### **Transfer Teklifine Cevap Ver**
```
POST /api/market/amateur/transfer-offer/{offerId}/respond
Body:
{
  "response": "accepted" | "rejected"
}
```

---

## 📊 VERİTABANI (5 YENİ TABLO)

```
✅ amateur_player_market_value
   ├─ player_id
   ├─ base_value (5000)
   ├─ profile_views_points
   ├─ engagement_points
   ├─ performance_points
   ├─ trending_points
   ├─ scout_interest_points
   ├─ calculated_market_value
   ├─ price_trend (%)
   └─ trend_status (up/down/stable)

✅ market_point_logs
   ├─ player_id
   ├─ action_type
   ├─ points_gained
   ├─ description
   └─ timestamp

✅ weekly_trending_players
   ├─ player_id
   ├─ weekly_points
   ├─ weekly_rank
   └─ week_start/end

✅ amateur_market_statistics
   ├─ total_players
   ├─ active_players
   ├─ average_market_value
   ├─ highest_value
   ├─ trending_counts
   └─ date

✅ amateur_transfer_offers
   ├─ player_id
   ├─ from_team_id
   ├─ offer_message
   ├─ proposed_value
   └─ status
```

---

## 🎮 ÖRNEK SENARYO

### **Ahmet Demir (Amatör Futbolcu)**

**Başlangıç:**
```
Piyasa Değeri: 5.000
```

**Gün 1:**
```
10 kişi profili görüntüledi           → +10 Puan
5 kişi beğendi                         → +5 Puan
2 kişi yorum yaptı                     → +4 Puan
Toplam: 19 Puan × 100 = +1.900
Piyasa Değeri: 6.900 ⬆️
```

**Gün 2 (Maç Yaptı):**
```
2 Gol attı                             → +10 Puan
1 Asist yaptı                          → +3 Puan
Maç MVP'si                             → +10 Puan
Toplam: 23 Puan × 100 = +2.300
Piyasa Değeri: 9.200 ⬆️
```

**Hafta Sonu:**
```
Toplam Puan: 150 Puan × 100 = +15.000
Final Piyasa Değeri: 20.000 ⬆️

Trend: +300% (Son hafta çok popüler!)
Sıralama: #25 (Amatör Futbolcular Arasında)
```

---

## ✨ ÖZELLIKLER

✅ **Gerçek Zamanlı Puan Sistemi** - Tıklandığında hemen puan artar  
✅ **Performans Tabanlı Değer** - Maç sonuçlarına göre artış  
✅ **Trend Analizi** - Haftalık karşılaştırma  
✅ **Leaderboard** - Top 50 oyuncu sıralaması  
✅ **Transfer Sistemi** - Takımlar oyuncu transfer edebilir  
✅ **Haftalık Trendler** - En popüler oyuncular  
✅ **Puan Geçmişi** - Nasıl puan kazandığını göster  
✅ **İstatistikler** - Pazar geneli veriler  

---

## 📊 FİNAL İSTATİSTİKLER

| Metrik | Sayı |
|--------|------|
| Eklenen Tablo | 5 |
| Eklenen Model | 5 |
| Eklenen Controller | 1 |
| Yeni Endpoint | 11 |
| Puan Türü | 5 |
| **Toplam Endpoint** | **261+** |
| **Toplam Tablo** | **115** |

---

## 🎉 SONUÇ

### **AMATÖR FUTBOL PİYASA DEĞERİ SİSTEMİ TAMAMLANDI! ✅**

✅ **5 Tür Puan Sistemi**  
✅ **Gerçek Zamanlı Hesaplama**  
✅ **Trend Analizi**  
✅ **Leaderboard & Sıralama**  
✅ **Transfer Sistemi**  
✅ **Haftalık Trendler**  

---

**Versiyon:** 5.2 - Amateur Market Edition  
**Durum:** ✅ TAMAMLANDI  
**Tarih:** 2 Mart 2026

# NextScout Secrets Policy

## Scope

Bu politika production ve staging ortamlarda kullanilan tum gizli degerler icin gecerlidir.

## Secret Types

- Uygulama: `APP_KEY`
- Veritabani: `DB_PASSWORD`
- Mail: `MAIL_PASSWORD`
- Ucuncu parti: payment/API key'leri
- CI/CD: deployment token'lari ve workflow secret'lari

## Storage Rules

- Secret'lar asla repoya yazilmaz.
- `.env.production.example` sadece ornek alan adlarini ve placeholder degerleri icerir.
- Gercek degerler:
  - CI/CD secret store (GitHub Secrets vb)
  - Sunucu secret manager / protected `.env`

## Rotation

- Kritik secret'lar 90 gunde bir rotate edilir.
- Olay/ihlal durumunda aninda rotate edilir.
- Rotation sonrasi:
  - servis restart
  - eski token/key iptali
  - audit kaydi

## Access Control

- En az yetki prensibi.
- Secret goruntuleme/degistirme sadece yetkili roller.
- Tum erisimler loglanir.

## CI/CD Rules

- Workflow'larda secret degerleri echo edilmez.
- `PROD_API_BASE` disindaki production secret'lar protected environment ile verilir.
- Fork PR pipeline'larinda production secret inject edilmez.


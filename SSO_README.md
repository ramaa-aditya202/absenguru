# Integrasi SSO OAuth2

Aplikasi ini sudah dikonfigurasi untuk menggunakan SSO OAuth2 dari `sso.domain.sch.id`.

## Konfigurasi

### 1. Environment Variables (.env)
Pastikan konfigurasi berikut sudah diatur di file `.env`:

```env
SSO_CLIENT_ID=your_client_id_here
SSO_CLIENT_SECRET=your_client_secret_here  
SSO_REDIRECT_URI=http://localhost/auth/sso/callback
SSO_BASE_URL=https://sso.domain.sch.id
SSO_AUTHORIZE_URL=https://sso.domain.sch.id/oauth/authorize
SSO_TOKEN_URL=https://sso.domain.sch.id/oauth/token
SSO_USER_URL=https://sso.domain.sch.id/api/user
```

### 2. Dapatkan Client ID dan Client Secret
Untuk mendapatkan `SSO_CLIENT_ID` dan `SSO_CLIENT_SECRET`:

1. Hubungi administrator SSO sekolah
2. Daftarkan aplikasi dengan informasi:
   - **Application Name**: Absensi Guru
   - **Redirect URI**: `http://your-domain.com/auth/sso/callback`
   - **Scopes**: read (atau sesuai yang disediakan)

### 3. Update Redirect URI untuk Production
Jika deploy ke production, update `SSO_REDIRECT_URI` dan `APP_URL` di file `.env`:

```env
APP_URL=https://your-domain.com
SSO_REDIRECT_URI=https://your-domain.com/auth/sso/callback
```

## Cara Kerja

1. User klik tombol "Login dengan SSO Sekolah" di halaman login
2. User diarahkan ke halaman login SSO sekolah
3. Setelah login berhasil, user diarahkan kembali ke aplikasi
4. Aplikasi membuat atau update user berdasarkan data dari SSO
5. User otomatis login ke aplikasi

## Fitur

- Auto-create user baru jika belum ada di database
- Update data user dari SSO setiap login
- Fallback login manual tetap tersedia
- Role default 'guru' untuk user SSO baru
- Avatar support dari SSO

## Testing

Untuk test integrasi SSO:

1. Pastikan server SSO berjalan dan dapat diakses
2. Konfigurasi Client ID dan Secret sudah benar  
3. Akses halaman login aplikasi
4. Klik tombol "Login dengan SSO Sekolah"
5. Login menggunakan akun SSO sekolah

## Troubleshooting

### Error "Client not found"
- Pastikan `SSO_CLIENT_ID` benar
- Verifikasi aplikasi sudah terdaftar di server SSO

### Error "Invalid redirect URI" 
- Pastikan `SSO_REDIRECT_URI` sesuai dengan yang didaftarkan
- Periksa domain dan path callback

### Error "Scope not allowed"
- Verifikasi scope 'read' tersedia di server SSO
- Sesuaikan scope di SsoProvider jika perlu
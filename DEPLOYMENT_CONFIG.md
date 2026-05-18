# VSTEP Deployment Configuration Guide

This guide explains how to configure environment variables for Render (backend) and Vercel (frontend).

## Backend (Render) - Environment Variables

Set these environment variables in Render's dashboard under **Environment**:

### Core Settings
```
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:B/ScOYdZCJTB13gmYqUxqVhEXdft3lSX6BvbCKiMX/0=
APP_DEBUG=false
APP_URL=https://vstep.onrender.com
```

### Database (NeonDB PostgreSQL)
```
DB_CONNECTION=pgsql
DB_SEED_ON_BOOT=true
DB_SSLMODE=require
DB_URL=postgresql://neondb_owner:npg_1cgxi3oODUed@ep-gentle-field-a1ldpcht-pooler.ap-southeast-1.aws.neon.tech/neondb?sslmode=require
DB_URL_DIRECT=postgresql://neondb_owner:npg_1cgxi3oODUed@ep-gentle-field-a1ldpcht.ap-southeast-1.aws.neon.tech/neondb?sslmode=require
```

### Session & Cache
```
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

### Redis (Upstash)
```
REDIS_URL=redis://default:gQAAAAAAAUzlAAIncDJlMDlmMzUxYzE1MjQ0NWExYmIxODI3NmZlNTJhOThlYXAyODUyMjE@main-newt-85221.upstash.io:6379
```

### CORS & Frontend
```
CORS_ALLOWED_ORIGINS=https://vstep.hamhochoi.com,https://vstep.vercel.app
CORS_ALLOWED_ORIGIN_PATTERNS=^https://.*\.vercel\.app$
FRONTEND_URL=https://vstep.hamhochoi.com
```

### Authentication
```
JWT_SECRET=01wW0Hc2srXnDjjl2oA70Z7GwtVZZMza7vCNuV8H7c95SLufVWt6DRu98MArkaNUQUEUE
```

### Storage (R2 Cloudflare)
```
AWS_ACCESS_KEY_ID=<your-r2-access-key>
AWS_SECRET_ACCESS_KEY=<your-r2-secret-key>
AWS_DEFAULT_REGION=auto
AWS_BUCKET=vstep
AWS_ENDPOINT=https://<account_id>.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
```

### AI Services
```
AZURE_SPEECH_KEY=<your-azure-speech-key>
AZURE_SPEECH_REGION=southeastasia
OPENAI_API_KEY=<your-openai-key>
OPENAI_BASE_URL=<your-openai-base-url>
```

### Logging
```
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug
```

### Octane (FrankenPHP)
```
OCTANE_SERVER=frankenphp
OCTANE_HTTPS=false
OCTANE_HOST=0.0.0.0
OCTANE_PORT=8000
OCTANE_WORKERS=auto
OCTANE_MAX_REQUESTS=500
```

---

## Frontend (Vercel) - Environment Variables

Set these environment variables in Vercel's dashboard under **Settings → Environment Variables**:

### API Configuration
```
VITE_API_URL=https://vstep.onrender.com
VITE_STORAGE_URL=https://pub-44427da338f348eca0451808ade7798e.r2.dev
```

---

## Step-by-Step Setup Instructions

### For Render Backend:

1. Go to your Render service dashboard
2. Click **Environment** in the left sidebar
3. Add each variable from the "Backend (Render)" section above
4. Click **Save** to apply changes
5. Render will automatically redeploy with the new environment variables

### For Vercel Frontend:

1. Go to your Vercel project dashboard
2. Click **Settings** → **Environment Variables**
3. Add each variable from the "Frontend (Vercel)" section above
4. Select which environments to apply to (Production, Preview, Development)
5. Click **Save**
6. Trigger a new deployment or wait for the next push to apply changes

---

## Verification Checklist

After setting up environment variables:

- [ ] Backend is running on Render without errors
- [ ] Frontend can reach the backend API at `https://vstep.onrender.com`
- [ ] CORS errors are resolved in browser console
- [ ] Exam start endpoint returns 200 (not 500)
- [ ] Database queries work (check Render logs)
- [ ] Authentication tokens are generated correctly

---

## Troubleshooting

### 500 Error on Exam Start
- Check Render logs for database connection errors
- Verify `DB_URL` is correct and NeonDB is accessible
- Ensure `JWT_SECRET` matches between frontend and backend

### CORS Errors
- Verify `CORS_ALLOWED_ORIGINS` includes your Vercel domain
- Check `CORS_ALLOWED_ORIGIN_PATTERNS` regex is correct
- Clear browser cache and hard refresh

### Database Connection Failed
- Test NeonDB connection string in Render logs
- Verify SSL mode is set to `require`
- Check NeonDB firewall allows Render IP

### Frontend Can't Reach Backend
- Verify `VITE_API_URL` points to correct Render URL
- Check network tab in browser DevTools
- Ensure backend is running and accessible

---

## Local Development

For local development, use `.env.local` files:

**Backend** (`apps/backend-v2/.env.local`):
```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=vstep
DB_USERNAME=postgres
DB_PASSWORD=
QUEUE_CONNECTION=sync
CACHE_STORE=file
```

**Frontend** (`apps/frontend/.env.local`):
```
VITE_API_URL=http://localhost:8000
VITE_STORAGE_URL=https://pub-44427da338f348eca0451808ade7798e.r2.dev
```

Then run:
```bash
# Backend
php artisan serve

# Frontend
bun run dev
```

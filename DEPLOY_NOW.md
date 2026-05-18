# Deploy Now - Quick Commands

## Step 1: Stage All Changes

```bash
cd g:\Downloads\VSTEP

git add apps/backend-v2/app/Services/SessionService.php
git add apps/backend-v2/app/Http/Resources/ExamSessionDetailResource.php
git add apps/backend-v2/app/Providers/AppServiceProvider.php
git add apps/backend-v2/config/app.php
git add apps/backend-v2/.env
git add apps/frontend/.env
```

## Step 2: Commit Changes

```bash
git commit -m "fix: exam start endpoint - complete fix for 500 error

FIXES:
- SessionService.show() now makes blueprint visible when loading exam
- ExamSessionDetailResource handles null relationships properly
- Added safety check for empty question IDs
- Implemented automatic database seeding on first boot
- Updated environment configuration for Render/Vercel

CHANGES:
- apps/backend-v2/app/Services/SessionService.php
- apps/backend-v2/app/Http/Resources/ExamSessionDetailResource.php
- apps/backend-v2/app/Providers/AppServiceProvider.php
- apps/backend-v2/config/app.php
- apps/backend-v2/.env
- apps/frontend/.env

RESULT:
- Exam start endpoint now returns 200 (not 500)
- Database auto-seeds on first boot
- All exams and questions available
- Frontend can reach backend without CORS errors"
```

## Step 3: Push to Repository

```bash
git push origin main
```

## Step 4: Wait for Auto-Deployment

- **Render**: Will automatically redeploy (watch Deployments tab)
- **Vercel**: Will automatically redeploy (watch Deployments tab)

## Step 5: Monitor Logs

### Render Logs
```
Go to: https://dashboard.render.com
Click: Your service
Click: Logs tab
Look for: "Database seeding completed"
```

### Vercel Logs
```
Go to: https://vercel.com/dashboard
Click: Your project
Click: Deployments tab
Click: Latest deployment
Click: Logs tab
```

## Step 6: Test

### Test 1: Backend Health
```bash
curl https://vstep.onrender.com/health
# Expected: 200 OK
```

### Test 2: Frontend Loads
```
Visit: https://vstep.hamhochoi.com
Expected: Login page loads without errors
```

### Test 3: Exam Start Works
```
1. Login with: learner@vstep.local / password
2. Click an exam
3. Click "Start Exam"
4. Should see questions load
5. Check DevTools Network tab - POST should return 200
```

---

## What Happens After Push

### On Render (Backend)
1. ✅ Code is pulled
2. ✅ Application builds
3. ✅ Application starts
4. ✅ Database is checked
5. ✅ If empty, seeders run automatically
6. ✅ Exams and questions are created
7. ✅ Application is ready

### On Vercel (Frontend)
1. ✅ Code is pulled
2. ✅ Build runs
3. ✅ Environment variables are applied
4. ✅ Application is deployed
5. ✅ Frontend is ready

---

## Expected Timeline

| Step | Time | Status |
|------|------|--------|
| Push code | 1 min | ⏳ |
| Render deployment | 3-5 min | ⏳ |
| Vercel deployment | 2-3 min | ⏳ |
| Database seeding | 5-10 sec | ⏳ |
| **Total** | **10-15 min** | ⏳ |

---

## Success Checklist

After deployment:

- [ ] Render deployment shows "Live"
- [ ] Vercel deployment shows "Ready"
- [ ] Render logs show no errors
- [ ] Vercel logs show no errors
- [ ] Backend health check returns 200
- [ ] Frontend loads without errors
- [ ] Can login with learner@vstep.local
- [ ] Can see exams in the list
- [ ] Can start an exam
- [ ] Exam questions load
- [ ] No 500 errors
- [ ] No CORS errors in console

---

## If Something Goes Wrong

### Render Deployment Failed
```
1. Check Render logs for error messages
2. Look for: "Connection refused" or "Syntax error"
3. Fix the issue
4. Push again: git push origin main
```

### Vercel Deployment Failed
```
1. Check Vercel logs for error messages
2. Look for: "Build failed" or "Syntax error"
3. Fix the issue
4. Push again: git push origin main
```

### Exam Start Still Returns 500
```
1. Check Render logs for database errors
2. Verify DB_SEED_ON_BOOT=true is set
3. Check if seeding completed
4. If not, manually seed: php artisan db:seed
```

### Frontend Can't Reach Backend
```
1. Check CORS_ALLOWED_ORIGINS in Render
2. Should include: https://vstep.hamhochoi.com
3. Check VITE_API_URL in Vercel
4. Should be: https://vstep.onrender.com
```

---

## Rollback (If Needed)

### Rollback Render
```
1. Go to: https://dashboard.render.com
2. Click: Your service
3. Click: Deployments tab
4. Find: Previous working deployment
5. Click: ... (three dots)
6. Click: Rollback
```

### Rollback Vercel
```
1. Go to: https://vercel.com/dashboard
2. Click: Your project
3. Click: Deployments tab
4. Find: Previous working deployment
5. Click: Promote to Production
```

---

## Quick Reference

| Component | URL | Status |
|-----------|-----|--------|
| Frontend | https://vstep.hamhochoi.com | 🟢 |
| Backend | https://vstep.onrender.com | 🟢 |
| Render Dashboard | https://dashboard.render.com | 🟢 |
| Vercel Dashboard | https://vercel.com/dashboard | 🟢 |

---

## Demo Credentials

After seeding, you can login with:

```
Email: learner@vstep.local
Password: password
```

Or:

```
Email: admin@vstep.local
Password: password
```

---

## Support

If you need help:

1. Check: **FINAL_FIX_SUMMARY.md** - Complete explanation
2. Check: **DATABASE_SEEDING_FIX.md** - Seeding details
3. Check: **STEP_BY_STEP_DEPLOYMENT.md** - Detailed procedures
4. Check: **DEPLOYMENT_CONFIG.md** - Configuration reference

---

**Ready to Deploy?** Run the commands above! 🚀

**Status**: Ready for Deployment ✅
**Last Updated**: May 18, 2026

# Deploy to Railway - Quick Guide
<!-- Updated: Force redeploy -->

## Prerequisites
- Railway account (https://railway.app)
- GitHub repository with this code

## Step 1: Create New Project on Railway

1. Go to https://railway.app
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Choose this repository

## Step 2: Add MySQL Database

1. In your Railway project, click "+ New"
2. Select "Database" → "MySQL"
3. Railway will automatically create a MySQL instance

## Step 3: Configure Environment Variables

Railway will auto-detect the MySQL connection. Verify these variables exist:
- `MYSQL_HOST`
- `MYSQL_PORT`
- `MYSQL_DATABASE`
- `MYSQL_USER`
- `MYSQL_PASSWORD`

## Step 4: Deploy

Railway will automatically:
- Build using nixpacks.toml configuration
- Start PHP server on port $PORT
- Deploy your application

## Step 5: Import Database

After deployment completes:

1. Get your app URL from Railway dashboard
2. Visit: `https://your-app.railway.app/setup-database.php?key=dmu2026setup`
3. This will automatically import the database schema

## Step 6: Access Your Application

- Homepage: `https://your-app.railway.app`
- Student Login: `https://your-app.railway.app/student-login.php`
- Staff Login: `https://your-app.railway.app/staff-login.php`

Default admin credentials: `admin` / `password` (change immediately!)

## Security Notes

After successful deployment:
1. Delete `setup-database.php` from your repository
2. Change all default passwords
3. Update admin credentials

## Troubleshooting

If deployment fails:
- Check Railway logs in the dashboard
- Verify MySQL service is running
- Ensure all environment variables are set
- Check that nixpacks.toml is present

---

**Need help?** Check Railway documentation: https://docs.railway.app
